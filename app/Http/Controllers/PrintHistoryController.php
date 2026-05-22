<?php

namespace App\Http\Controllers;

use App\Models\PrintHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PrintHistoryController extends Controller
{
    /**
     * Tampilkan riwayat pencetakan ijazah dengan penyaringan & pencarian.
     */
    public function index(Request $request)
    {
        $query = PrintHistory::with(['student.user', 'user']);

        // 1. Pencarian (Nama, NIM, Nomor Ijazah)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('nim', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($uq) use ($search) {
                                $uq->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // 2. Penyaringan Berdasarkan Tanggal Mulai
        if ($request->filled('start_date')) {
            $query->whereDate('printed_at', '>=', Carbon::parse($request->start_date));
        }

        // 3. Penyaringan Berdasarkan Tanggal Selesai
        if ($request->filled('end_date')) {
            $query->whereDate('printed_at', '<=', Carbon::parse($request->end_date));
        }

        $histories = $query->latest()->paginate(15)->withQueryString();

        // 4. Kalkulasi Metrik Ringkasan untuk Kartu Dasbor
        $totalToday = PrintHistory::whereDate('printed_at', Carbon::today())->count();
        $totalThisMonth = PrintHistory::whereMonth('printed_at', Carbon::now()->month)
            ->whereYear('printed_at', Carbon::now()->year)
            ->count();
        $totalAllTime = PrintHistory::count();

        return view('admin.history', compact('histories', 'totalToday', 'totalThisMonth', 'totalAllTime'));
    }
}
