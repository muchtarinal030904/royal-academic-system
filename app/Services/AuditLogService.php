<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Log a security or administrative activity in the database.
     *
     * @param string $action Kategori aksi (e.g. LOGIN, LOGOUT, CHANGE_PASSWORD, UPDATE_TEMPLATE, etc.)
     * @param string $description Detail deskripsi aktivitas
     * @param int|null $userId User ID alternatif jika tidak mengambil dari Auth::user()
     */
    public static function log(string $action, string $description, ?int $userId = null)
    {
        $user = Auth::user();
        
        ActivityLog::create([
            'user_id' => $userId ?: ($user ? $user->id : null),
            'username' => $user ? $user->username : 'Guest',
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }
}
