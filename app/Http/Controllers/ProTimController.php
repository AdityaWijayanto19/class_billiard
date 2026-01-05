<?php

namespace App\Http\Controllers;

use App\Models\ProTeam;
use Illuminate\Http\Request;

class ProTimController extends Controller
{
    public function index()
    {
        $this->authorizeAdminOnly();
        $proTeams = ProTeam::orderBy('order')->get();
        return view('admin.manage-content.pro-tim', compact('proTeams'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdminOnly();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:0|max:100',
            'origin' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['order'] = $validated['order'] ?? (ProTeam::max('order') + 1);
        ProTeam::create($validated);
        return redirect()->route('admin.pro-tim.index')->with('success', 'Pro Tim berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $this->authorizeAdminOnly();
        $proTeams = ProTeam::orderBy('order')->get();
        $editData = ProTeam::findOrFail($id);
        return view('admin.manage-content.pro-tim', compact('proTeams', 'editData'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAdminOnly();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:0|max:100',
            'origin' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'boolean',
        ]);
        $proTeam = ProTeam::findOrFail($id);
        $proTeam->update($validated);
        return redirect()->route('admin.pro-tim.index')->with('success', 'Pro Tim berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->authorizeAdminOnly();
        $proTeam = ProTeam::findOrFail($id);
        $proTeam->delete();
        return redirect()->route('admin.pro-tim.index')->with('success', 'Pro Tim berhasil dihapus!');
    }

    private function authorizeAdminOnly()
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }
    }
}
