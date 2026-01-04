<?php

namespace App\Http\Controllers;

use App\Models\meja_billiard;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TableController extends Controller
{
    /**
     * Display list of all tables
     *
     * Optimization:
     * - Select only needed columns
     * - Order by name for consistency
     */
    public function index()
    {
        $this->authorize('viewAny', meja_billiard::class);

        $tables = meja_billiard::select('id', 'name', 'room', 'qrcode', 'created_at')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.tables.index', compact('tables'));
    }

    /**
     * Show create table form
     */
    public function create()
    {
        $this->authorize('create', meja_billiard::class);

        return view('admin.tables.create');
    }

    /**
     * Store new table
     *
     * Validation:
     * - Name must be unique
     * - Room is required
     *
     * Actions:
     * - Create QR code for table
     * - Store QR code to storage
     */
    public function store(Request $request)
    {
        $this->authorize('create', meja_billiard::class);

        // Validation with custom messages
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:meja_billiards,name',
            ],
            'room' => 'required|string|max:255',
        ], [
            'name.unique' => 'Nama meja sudah digunakan.',
            'name.required' => 'Nama meja harus diisi.',
            'room.required' => 'Ruangan harus dipilih.',
        ]);

        $slug = Str::slug($validated['name']);

        // Extract table number from name (e.g., "Meja 01" -> "01")
        $tableNumber = preg_replace('/[^0-9]/', '', $validated['name']);
        if (empty($tableNumber)) {
            $tableNumber = $validated['name']; // Fallback to name if no numbers
        }

        // Generate URL for QR Code
        $url = route('orders.create').'?table='.urlencode($tableNumber).'&room='.urlencode($validated['room']);

        // Generate QR Code
        $result = (new Builder(
            writer: new PngWriter,
            writerOptions: [],
            validateResult: false,
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 500,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        ))->build();

        // Store QR Code to storage
        $qrCodePath = 'qrcodes/'.$slug.'.png';
        Storage::disk('public')->put($qrCodePath, $result->getString());

        // Create table
        $table = meja_billiard::create([
            'name' => $validated['name'],
            'room' => $validated['room'],
            'slug' => $slug,
            'qrcode' => $qrCodePath,
        ]);

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja berhasil ditambahkan dan QR Code telah di-generate!');
    }

    /**
     * Generate QR Code untuk meja yang sudah ada
     */
    public function generateQR($id)
    {
        $table = meja_billiard::findOrFail($id);

        // Extract nomor meja dari nama (misalnya "Meja 01" -> "01")
        $tableNumber = preg_replace('/[^0-9]/', '', $table->name);
        if (empty($tableNumber)) {
            $tableNumber = $table->name; // Fallback ke nama jika tidak ada angka
        }

        // Generate URL untuk QR Code dengan parameter table (nomor saja) dan room
        $url = route('orders.create').'?table='.urlencode($tableNumber).'&room='.urlencode($table->room ?? '');

        // Generate QR Code menggunakan package endroid/qr-code
        $result = (new Builder(
            writer: new PngWriter,
            writerOptions: [],
            validateResult: false,
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 500,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        ))->build();

        // Simpan QR Code ke storage
        $qrCodePath = 'qrcodes/'.$table->slug.'.png';
        Storage::disk('public')->put($qrCodePath, $result->getString());

        // Update table dengan path QR code
        $table->qrcode = $qrCodePath;
        $table->save();

        return redirect()->route('admin.tables.index')
            ->with('success', 'QR Code berhasil di-generate untuk '.$table->name);
    }

    /**
     * Menampilkan halaman print QR Code untuk meja tertentu
     */
    public function showBarcode($id)
    {
        $table = meja_billiard::findOrFail($id);

        // Authorize viewing the barcode
        $this->authorize('view', $table);

        // Extract nomor meja dari nama (misalnya "Meja 01" -> "01")
        $tableNumber = preg_replace('/[^0-9]/', '', $table->name);
        if (empty($tableNumber)) {
            $tableNumber = $table->name; // Fallback ke nama jika tidak ada angka
        }

        $url = route('orders.create').'?table='.urlencode($tableNumber).'&room='.urlencode($table->room ?? '');

        return view('admin.tables.print-barcode', compact('table', 'url'));
    }

    /**
     * Delete table with authorization
     *
     * Actions:
     * - Delete QR Code file from storage
     * - Delete table record
     */
    public function destroy($id)
    {
        $this->authorize('delete', meja_billiard::class);

        $table = meja_billiard::findOrFail($id);

        // Delete QR Code file if exists
        if ($table->qrcode && Storage::disk('public')->exists($table->qrcode)) {
            Storage::disk('public')->delete($table->qrcode);
        }

        // Delete table record
        $table->delete();

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja berhasil dihapus!');
    }
}
