@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">Manage Pro Tim</h2>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif

    {{-- Form Tambah/Edit --}}
    <div class="mb-8">
        <form action="{{ isset($editData) ? route('admin.pro-tim.update', $editData->id) : route('admin.pro-tim.store') }}" method="POST">
            @csrf
            @if(isset($editData))
                @method('PUT')
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-semibold">Nama</label>
                    <input type="text" name="name" class="w-full border px-3 py-2 rounded" value="{{ old('name', $editData->name ?? '') }}" required>
                    @error('name')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block mb-1 font-semibold">Umur</label>
                    <input type="number" name="age" class="w-full border px-3 py-2 rounded" value="{{ old('age', $editData->age ?? '') }}">
                    @error('age')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block mb-1 font-semibold">Asal</label>
                    <input type="text" name="origin" class="w-full border px-3 py-2 rounded" value="{{ old('origin', $editData->origin ?? '') }}">
                    @error('origin')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block mb-1 font-semibold">Alamat</label>
                    <input type="text" name="address" class="w-full border px-3 py-2 rounded" value="{{ old('address', $editData->address ?? '') }}">
                    @error('address')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block mb-1 font-semibold">Urutan</label>
                    <input type="number" name="order" class="w-full border px-3 py-2 rounded" value="{{ old('order', $editData->order ?? '') }}">
                    @error('order')<div class="text-red-500 text-sm">{{ $message }}</div>@enderror
                </div>
                <div class="flex items-center mt-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1" class="mr-2" {{ old('is_active', $editData->is_active ?? true) ? 'checked' : '' }}> Aktif
                    </label>
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <button type="submit" class="bg-gold-400 text-black px-4 py-2 rounded hover:bg-gold-500">{{ isset($editData) ? 'Update' : 'Tambah' }}</button>
                @if(isset($editData))
                    <a href="{{ route('admin.pro-tim.index') }}" class="bg-gray-300 px-4 py-2 rounded">Batal Edit</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Data --}}
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Umur</th>
                    <th class="px-4 py-2">Asal</th>
                    <th class="px-4 py-2">Alamat</th>
                    <th class="px-4 py-2">Urutan</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proTeams as $team)
                <tr>
                    <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                    <td class="border px-4 py-2">{{ $team->name }}</td>
                    <td class="border px-4 py-2">{{ $team->age }}</td>
                    <td class="border px-4 py-2">{{ $team->origin }}</td>
                    <td class="border px-4 py-2">{{ $team->address }}</td>
                    <td class="border px-4 py-2">{{ $team->order }}</td>
                    <td class="border px-4 py-2">
                        @if($team->is_active)
                            <span class="text-green-600 font-bold">Aktif</span>
                        @else
                            <span class="text-red-600 font-bold">Nonaktif</span>
                        @endif
                    </td>
                    <td class="border px-4 py-2 flex gap-2">
                        <a href="{{ route('admin.pro-tim.edit', $team->id) }}" class="bg-blue-500 text-white px-2 py-1 rounded">Edit</a>
                        <form action="{{ route('admin.pro-tim.destroy', $team->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4">Belum ada data Pro Tim.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
