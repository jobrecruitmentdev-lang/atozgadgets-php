<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $customers = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.customers', compact('customers'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:20',
            'role_id' => 'nullable|integer',
            'is_active' => 'required|boolean'
        ]);

        // Security Guard: Only SuperAdmin (role_id === 1) can modify role assignments
        if (isset($validated['role_id']) && (int)$validated['role_id'] !== (int)$user->role_id) {
            if ((int)\Illuminate\Support\Facades\Auth::user()->role_id !== 1) {
                return redirect()->route('admin.customers')->with('error', 'Unauthorized: Only SuperAdmin can modify user roles.');
            }
        } else {
            unset($validated['role_id']);
        }

        $user->update($validated);

        return redirect()->route('admin.customers')->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if (in_array($user->role_id, [1, 2])) {
            return redirect()->route('admin.customers')->with('error', 'Cannot delete admin or superadmin accounts.');
        }

        try {
            $user->delete();
            return redirect()->route('admin.customers')->with('success', 'Customer deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.customers')->with('error', 'Unable to delete customer. They have associated orders or records.');
        }
    }
}
