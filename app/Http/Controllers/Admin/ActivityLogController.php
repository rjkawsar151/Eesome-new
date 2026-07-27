<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminActivityLog::with('admin')->latest();
        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(fn ($q) => $q->where('action', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"));
        }

        return view('admin.activity.index', ['logs' => $query->paginate(30)->withQueryString()]);
    }
}
