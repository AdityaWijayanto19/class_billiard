<?php

namespace App\Http\Controllers;

use App\Models\Menu;

class MenuDetailController extends Controller
{
    /**
     * Show menu detail page
     */
    public function show($slug)
    {
        // Get menu by slug (optimized)
        $menu = Menu::select('id', 'name', 'slug', 'price', 'image_path', 'description', 'short_description', 'labels', 'category_menu_id')
            ->with('categoryMenu:id,name')
            ->where('slug', $slug)
            ->firstOrFail();

        // Get all menus sorted by id for prev/next navigation (optimized - hanya kolom yang diperlukan)
        $allMenus = Menu::select('id', 'name', 'slug', 'image_path')
            ->orderBy('id')
            ->get();

        // Find current position
        $currentIndex = $allMenus->search(function ($item) use ($menu) {
            return $item->id === $menu->id;
        });

        // Get prev and next items
        $prevItem = $currentIndex > 0 ? $allMenus[$currentIndex - 1] : $allMenus[$allMenus->count() - 1];
        $nextItem = $currentIndex < $allMenus->count() - 1 ? $allMenus[$currentIndex + 1] : $allMenus[0];

        // Prepare data for view
        $item = [
            'id' => $menu->id,
            'name' => $menu->name,
            'slug' => $menu->slug,
            'price' => $menu->price,
            'image' => $menu->image_path,
            'description' => $menu->description ?? $menu->short_description,
            'category' => $menu->categoryMenu?->name ?? 'Menu',
            'label' => json_decode($menu->labels, true)[0] ?? null,
            'servings' => '1',
            'prep_time' => '5m',
            'cook_time' => '10m',
        ];

        $prevItemData = [
            'id' => $prevItem->id,
            'name' => $prevItem->name,
            'slug' => $prevItem->slug,
            'image' => $prevItem->image_path,
        ];

        $nextItemData = [
            'id' => $nextItem->id,
            'name' => $nextItem->name,
            'slug' => $nextItem->slug,
            'image' => $nextItem->image_path,
        ];

        return view('menu.detail', [
            'item' => $item,
            'prevItem' => $prevItemData,
            'nextItem' => $nextItemData,
        ]);
    }
}
