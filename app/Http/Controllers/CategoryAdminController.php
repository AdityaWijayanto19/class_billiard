<?php

namespace App\Http\Controllers;

use App\Models\CategoryMenu;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryAdminController extends Controller
{
    /**
     * Display list of categories ordered by priority
     *
     * Optimization:
     * - Select only needed columns
     * - Order by priority for display
     */
    public function index()
    {
        $this->authorize('viewAny', CategoryMenu::class);

        $categories = CategoryMenu::select('id', 'name', 'slug', 'order_priority', 'created_at')
            ->orderBy('order_priority', 'asc')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store new category
     *
     * Validation:
     * - Name must be unique
     * - Order priority must be integer
     *
     * Optimization:
     * - Use single create() call instead of assign and save
     */
    public function store(Request $request)
    {
        $this->authorize('create', CategoryMenu::class);

        // Validation with custom messages
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:category_menus,name',
            ],
            'order_priority' => 'required|integer|min:0',
        ], [
            'name.unique' => 'Nama kategori sudah digunakan.',
            'name.required' => 'Nama kategori harus diisi.',
            'order_priority.required' => 'Urutan tampil harus ditentukan.',
        ]);

        // Create category
        CategoryMenu::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'order_priority' => $validated['order_priority'],
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Update category
     *
     * Validation:
     * - Name must be unique (exclude current record)
     * - Order priority must be integer
     */
    public function update(Request $request, CategoryMenu $category)
    {
        $this->authorize('update', $category);

        // Validation with Rule::unique to exclude current record
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('category_menus', 'name')->ignore($category->id),
            ],
            'order_priority' => 'required|integer|min:0',
        ], [
            'name.unique' => 'Nama kategori sudah digunakan.',
            'name.required' => 'Nama kategori harus diisi.',
            'order_priority.required' => 'Urutan tampil harus ditentukan.',
        ]);

        // Update category
        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'order_priority' => $validated['order_priority'],
        ]);

        return back()->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Delete category with authorization
     *
     * Validation:
     * - Check if category has any menus before deleting
     */
    public function destroy(CategoryMenu $category)
    {
        $this->authorize('delete', $category);

        // Check if category has any menus before deleting
        $menuCount = Menu::where('category_menu_id', $category->id)->count();
        if ($menuCount > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki '.$menuCount.' menu.');
        }

        // Delete category
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
