<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ApiUsersController extends Controller
{
    // GET /apiUsers  (admin only)
    public function index(Request $request)
    {
        if (! $request->user()?->isAdmin()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $rows = User::select('id', 'nama_lengkap', 'username', 'email', 'role', 'created_at')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return response()->json([
            'status' => 'success',
            'total'  => $rows->count(),
            'data'   => $rows,
        ]);
    }
}
