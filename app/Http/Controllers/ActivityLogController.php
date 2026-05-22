<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Tampilkan log aktivitas administratif dengan filter.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // 1. Pencarian deskripsi atau username
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // 2. Filter tipe aksi (LOGIN, UPDATE_TEMPLATE, dll.)
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->latest()->paginate(25)->withQueryString();

        // Ambil daftar tipe aksi unik untuk filter dropdown
        $actions = ActivityLog::distinct()->pluck('action');

        return view('admin.logs', compact('logs', 'actions'));
    }

    /**
     * Bersihkan log aktivitas lama untuk mengoptimalkan ruang penyimpanan.
     */
    public function purge(Request $request)
    {
        $request->validate([
            'retention_days' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $days = intval($request->retention_days);

            if ($days === 0) {
                // Hapus semua log
                $count = ActivityLog::count();
                ActivityLog::truncate();

                // Catat aktivitas pembersihan log sebagai satu-satunya log awal baru
                AuditLogService::log('PURGE_LOGS', "Membersihkan seluruh log aktivitas keamanan sebanyak {$count} baris secara permanen.");
            } else {
                // Hapus log lebih tua dari X hari
                $cutoffDate = Carbon::now()->subDays($days);
                $count = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

                AuditLogService::log('PURGE_LOGS', "Membersihkan log aktivitas keamanan yang berumur lebih dari {$days} hari secara permanen. Total dihapus: {$count} baris.");
            }

            return back()->with('success', "Berhasil membersihkan {$count} baris log audit secara permanen.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membersihkan log audit: '.$e->getMessage());
        }
    }
}
