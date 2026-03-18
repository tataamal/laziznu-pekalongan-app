<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wilayah;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::with('wilayah')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telpon', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $wilayahs = Wilayah::all();
        $roles = ['pc', 'mwc', 'ranting', 'developer'];

        return view('developer.users.index', compact('users', 'wilayahs', 'roles', 'search'));
    }

    /**
     * Update wilayah for multiple users.
     */
    public function bulkUpdateWilayah(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'wilayah_id' => 'nullable|exists:wilayah,id',
        ]);

        User::whereIn('id', $request->user_ids)->update([
            'wilayah_id' => $request->wilayah_id
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'BULK_UPDATE_USER_WILAYAH',
            'description' => 'Mengubah wilayah untuk ' . count($request->user_ids) . ' user.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return redirect()->route('developer.users.index')->with('success', count($request->user_ids) . ' user berhasil diperbarui wilayahnya.');
    }

    /**
     * Delete multiple users.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = array_diff($request->user_ids, [auth()->id()]); // Prevent self-deletion

        if (empty($userIds)) {
            return redirect()->route('developer.users.index')->with('error', 'Tidak ada user valid yang dapat dihapus.');
        }

        User::whereIn('id', $userIds)->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'BULK_DELETE_USER',
            'description' => 'Menghapus ' . count($userIds) . ' user.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return redirect()->route('developer.users.index')->with('success', count($userIds) . ' user berhasil dihapus.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['pc', 'mwc', 'ranting', 'developer'])],
            'telpon' => 'nullable|string|max:20',
            'wilayah_id' => 'nullable|exists:wilayah,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE_USER',
            'description' => 'Menambahkan user baru: ' . $user->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return redirect()->route('developer.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['pc', 'mwc', 'ranting', 'developer'])],
            'telpon' => 'nullable|string|max:20',
            'wilayah_id' => 'nullable|exists:wilayah,id',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_USER',
            'description' => 'Memperbarui user: ' . $user->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return redirect()->route('developer.users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('developer.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE_USER',
            'description' => 'Menghapus user: ' . $userName,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->route('developer.users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Import users from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'IMPORT_USER',
                'description' => 'Mengimport data user dari Excel',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()->route('developer.users.index')->with('success', 'Data user berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('developer.users.index')->with('error', 'Terjadi kesalahan saat import data: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template.
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        $path = public_path('templates/template_import_user.xlsx');

        if (!file_exists($path)) {
            // Alternatively generate a dynamic export, but static file is often requested
            abort(404, 'Template file not found.');
        }

        return response()->download($path);
    }
}
