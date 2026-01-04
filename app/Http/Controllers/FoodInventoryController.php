<?php

namespace App\Http\Controllers;

use App\Models\FoodInventory;
use App\Models\Menu;
use Illuminate\Http\Request;

class FoodInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Optimized: Select specific columns and eager load menu
        $inventories = FoodInventory::select('id', 'menu_id', 'quantity', 'reorder_level', 'last_restocked_at', 'created_at')
            ->with('menu:id,name,image_path')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Cache menus karena jarang berubah
        $menus = cache()->remember('menus_list', 1800, function () {
            return Menu::select('id', 'name')
                ->orderBy('name')
                ->get();
        });

        return view('admin.inventory.index', compact('inventories', 'menus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id|unique:food_inventories,menu_id',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
        ]);

        $inventory = FoodInventory::create($validated);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory untuk '.$inventory->menu->name.' berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FoodInventory $inventory)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
        ]);

        $validated['last_restocked_at'] = now();
        $inventory->update($validated);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory '.$inventory->menu->name.' berhasil diperbarui');
    }

    /**
     * Delete the specified resource.
     */
    public function destroy(FoodInventory $inventory)
    {
        $menuName = $inventory->menu->name;
        $inventory->delete();

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory '.$menuName.' berhasil dihapus');
    }

    /**
     * Get inventory status (for API)
     */
    public function getStatus(Menu $menu)
    {
        $inventory = FoodInventory::where('menu_id', $menu->id)->first();

        if (! $inventory) {
            return response()->json(['status' => 'unknown']);
        }

        return response()->json([
            'status' => $inventory->isInStock() ? 'in_stock' : 'out_of_stock',
            'quantity' => $inventory->quantity,
            'below_reorder' => $inventory->isBelowReorderLevel(),
        ]);
    }
}
