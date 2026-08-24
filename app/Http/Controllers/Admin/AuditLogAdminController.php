<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\ProductHistory;

class AuditLogAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $tab = $request->input('tab', 'products');

        $productHistories = ProductHistory::with(['product', 'user'])->latest()->paginate(25);
        $systemAudits = AuditLog::with('user')->latest()->paginate(25);

        return view('admin.system.audit_logs', compact('tab', 'productHistories', 'systemAudits'));
    }
}
