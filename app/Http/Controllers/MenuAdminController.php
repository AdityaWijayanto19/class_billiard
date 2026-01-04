<?php

namespace App\Http\Controllers;

use App\Models\CategoryMenu;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MenuAdminController extends Controller
{
    /**
     * Display paginated list of menus
     *
     * Optimization:
     * - Eager load categoryMenu to prevent N+1
     * - Select specific columns
     * - Paginate for performance
     */
    public function index()
    {
        $this->authorize('viewAny', Menu::class);

        // Eager load category to prevent N+1 queries
        $menus = Menu::select('id', 'category_menu_id', 'name', 'slug', 'price', 'image_path', 'labels', 'short_description', 'created_at')
            ->with('categoryMenu:id,name')
            ->paginate(12);

        // Get categories for filtering (optional enhancement)
        $categories = CategoryMenu::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.menus.index', compact('menus', 'categories'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Menu::class);

        // Get categories for dropdown
        $categories = CategoryMenu::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.menus.create', compact('categories'));
    }

    /**
     * Store new menu
     *
     * Validation:
     * - Category must exist
     * - Name is required
     * - Price must be numeric
     * - Description is required
     * - Image is required (JPEG/PNG, max 2MB)
     */
    public function store(Request $request)
    {
        $this->authorize('create', Menu::class);

        // Validation with custom messages
        $validated = $request->validate([
            'category_menu_id' => 'required|exists:category_menus,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|min:10',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'labels' => 'nullable|string',
        ], [
            'category_menu_id.required' => 'Kategori harus dipilih.',
            'category_menu_id.exists' => 'Kategori tidak valid.',
            'name.required' => 'Nama menu harus diisi.',
            'price.required' => 'Harga harus diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'description.required' => 'Deskripsi harus diisi.',
            'description.min' => 'Deskripsi minimal 10 karakter.',
            'image.required' => 'Gambar harus diunggah.',
            'image.mimes' => 'Gambar harus format JPEG atau PNG.',
        ]);

        // Store image using Storage facade
        $imageName = time().'-'.Str::slug($validated['name']).'.'.$request->image->extension();
        $imagePath = $request->image->storeAs('menus', $imageName, 'public');

        // Parse labels from comma-separated string
        $labels = $validated['labels']
            ? array_filter(array_map('trim', explode(',', $validated['labels'])))
            : [];

        // Create menu
        Menu::create([
            'category_menu_id' => $validated['category_menu_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'price' => $validated['price'],
            'short_description' => Str::limit(strip_tags($validated['description']), 120),
            'description' => $validated['description'],
            'image_path' => $imagePath,
            'labels' => ! empty($labels) ? $labels : null,
        ]);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil dibuat!');
    }

    /**
     * Show edit form
     */
    public function edit(Menu $menu)
    {
        $this->authorize('update', $menu);

        // Get categories for dropdown
        $categories = CategoryMenu::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    /**
     * Update menu
     *
     * Validation:
     * - Category must exist
     * - Name is required
     * - Price must be numeric
     * - Description is required
     * - Image is optional (if provided, must be valid image)
     *
     * Actions:
     * - Update menu data
     * - Delete old image if new one provided
     */
    public function update(Request $request, Menu $menu)
    {
        $this->authorize('update', $menu);

        // Validation with custom messages
        $validated = $request->validate([
            'category_menu_id' => 'required|exists:category_menus,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|min:10',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'labels' => 'nullable|string',
        ], [
            'category_menu_id.required' => 'Kategori harus dipilih.',
            'category_menu_id.exists' => 'Kategori tidak valid.',
            'name.required' => 'Nama menu harus diisi.',
            'price.required' => 'Harga harus diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'description.required' => 'Deskripsi harus diisi.',
            'description.min' => 'Deskripsi minimal 10 karakter.',
            'image.mimes' => 'Gambar harus format JPEG atau PNG.',
        ]);

        // Prepare data for update
        $updateData = [
            'category_menu_id' => $validated['category_menu_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'price' => $validated['price'],
            'short_description' => Str::limit(strip_tags($validated['description']), 120),
            'description' => $validated['description'],
        ];

        // Parse and update labels
        if ($validated['labels']) {
            $labels = array_filter(array_map('trim', explode(',', $validated['labels'])));
            $updateData['labels'] = ! empty($labels) ? $labels : null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($menu->image_path && Storage::disk('public')->exists($menu->image_path)) {
                Storage::disk('public')->delete($menu->image_path);
            }

            // Store new image
            $imageName = time().'-'.$updateData['slug'].'.'.$request->image->extension();
            $imagePath = $request->image->storeAs('menus', $imageName, 'public');
            $updateData['image_path'] = $imagePath;
        }

        // Update menu
        $menu->update($updateData);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Delete menu
     *
     * Actions:
     * - Delete image file from storage
     * - Delete menu record
     */
    public function destroy(Menu $menu)
    {
        $this->authorize('delete', $menu);

        // Delete image file if exists
        if ($menu->image_path && Storage::disk('public')->exists($menu->image_path)) {
            Storage::disk('public')->delete($menu->image_path);
        }

        // Delete menu record
        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus.');
    }
}
