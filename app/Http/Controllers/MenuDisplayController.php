<?php

namespace App\Http\Controllers;

use App\Models\CategoryMenu;
use App\Models\Menu;

class MenuDisplayController extends Controller
{
    /**
     * Menampilkan daftar semua menu yang dikelompokkan berdasarkan kategori.
     */
    public function index()
    {
        // Mengambil kategori berdasarkan urutan prioritas dan meload menu di dalamnya
        $categories = CategoryMenu::with(['menus' => function ($query) {
            $query->orderBy('name', 'asc');
        }])->orderBy('order_priority', 'asc')->get();

        return view('pages.menu.index', compact('categories'));
    }

    /**
     * Menampilkan detail menu saat diklik.
     */
    public function show($slug)
    {
        // Mencari menu berdasarkan slug untuk SEO-friendly URL
        $menu = Menu::with('categoryMenu')->where('slug', $slug)->firstOrFail();

        return view('pages.menu.show', compact('menu'));
    }
}
