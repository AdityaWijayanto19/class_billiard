<?php

namespace App\Http\Controllers;

use App\Models\NotificationSound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NotificationSoundController extends Controller
{
    /**
     * Get all notification sounds
     */
    public function index()
    {
        // Optimized: Select specific columns
        $sounds = NotificationSound::select('id', 'name', 'filename', 'file_path', 'created_at')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($sounds);
    }

    /**
     * Get active notification sound (for kitchen)
     * Gets the sound that admin has selected in their settings
     */
    public function getActive()
    {
        // Get active sound from cache (set by admin)
        $activeSoundId = cache()->get('active_notification_sound_id');

        if ($activeSoundId) {
            $sound = NotificationSound::select('id', 'name', 'filename', 'file_path')->find($activeSoundId);
            if ($sound) {
                $filePath = $sound->file_path;
                if (str_starts_with($filePath, 'sounds/')) {
                    $url = url("/notification-sounds/{$sound->id}/file");
                } else {
                    $url = asset('assets/sounds/'.$sound->filename);
                }

                return response()->json([
                    'success' => true,
                    'sound' => [
                        'id' => $sound->id,
                        'name' => $sound->name,
                        'filename' => $sound->filename,
                        'url' => $url,
                    ],
                ]);
            }
        }

        // Fallback: get the most recently created sound (optimized)
        $sound = NotificationSound::select('id', 'name', 'filename', 'file_path')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($sound) {
            $filePath = $sound->file_path;
            if (str_starts_with($filePath, 'sounds/')) {
                $url = url("/notification-sounds/{$sound->id}/file");
            } else {
                $url = asset('assets/sounds/'.$sound->filename);
            }

            return response()->json([
                'success' => true,
                'sound' => [
                    'id' => $sound->id,
                    'name' => $sound->name,
                    'filename' => $sound->filename,
                    'url' => $url,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'sound' => null,
        ]);
    }

    /**
     * Set active notification sound
     */
    public function setActive(Request $request, $id)
    {
        try {
            $sound = NotificationSound::findOrFail($id);

            // Store active sound ID in session or cache
            // For now, we'll use cache with a key
            cache()->put('active_notification_sound_id', $id, now()->addDays(30));

            return response()->json([
                'success' => true,
                'message' => 'Audio aktif berhasil diubah',
                'sound' => $sound,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah audio aktif: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload new notification sound
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            // Allow common audio formats including m4a
            'audio' => 'required|file|mimes:mp3,wav,ogg,m4a|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $file = $request->file('audio');
            $extension = strtolower($file->getClientOriginalExtension() ?: 'mp3');
            $filename = time().'_'.Str::uuid()->toString().'.'.$extension;
            $path = $file->storeAs('sounds', $filename, 'public');

            $sound = DB::transaction(function () use ($request, $path, $filename) {
                $existing = NotificationSound::where('name', $request->name)->lockForUpdate()->first();

                if ($existing) {
                    $oldPath = $existing->file_path;

                    $existing->update([
                        'filename' => $filename,
                        'file_path' => $path,
                    ]);

                    if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }

                    return $existing->fresh();
                }

                return NotificationSound::create([
                    'name' => $request->name,
                    'filename' => $filename,
                    'file_path' => $path,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Audio berhasil diupload',
                'sound' => $sound,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload audio: '.$e->getMessage(),
            ], 500);
        }
    }

    public function file($id)
    {
        $sound = NotificationSound::find($id);

        if (! $sound) {
            abort(404);
        }

        if (! str_starts_with($sound->file_path, 'sounds/')) {
            abort(404);
        }

        if (! Storage::disk('public')->exists($sound->file_path)) {
            abort(404);
        }

        $fullPath = Storage::disk('public')->path($sound->file_path);
        $mime = Storage::disk('public')->mimeType($sound->file_path) ?: 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type' => $mime,
        ]);
    }

    /**
     * Delete notification sound
     */
    public function destroy($id)
    {
        try {
            $sound = NotificationSound::find($id);

            if (! $sound) {
                return response()->json([
                    'success' => false,
                    'message' => 'Audio tidak ditemukan atau sudah dihapus',
                ], 404);
            }

            // Delete file from storage
            if (Storage::disk('public')->exists($sound->file_path)) {
                Storage::disk('public')->delete($sound->file_path);
            }

            $sound->delete();

            return response()->json([
                'success' => true,
                'message' => 'Audio berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus audio: '.$e->getMessage(),
            ], 500);
        }
    }
}
