<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ControllerFile extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');
        $query = File::with(['user:id,first_name,last_name,email']);

        if ($auth['role'] !== 'A') {
            $query->where('user_id', (int)$auth['id']);
        }

        if ($request->filled('form_id')) {
            $query->where('form_id', (int)$request->input('form_id'));
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function show(Request $request, $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');
        $file = File::with(['user:id,first_name,last_name,email'])->find((int)$id);

        if (!$file) {
            return response()->json(['error' => 'Datei nicht gefunden'], 404);
        }

        if ($auth['role'] !== 'A' && (int)$file->user_id !== (int)$auth['id']) {
            return response()->json(['error' => 'Zugriff verweigert'], 403);
        }

        return response()->json($file);
    }

    public function getByUser(Request $request, $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');
        $targetUserId = (int)$id;

        if ($auth['role'] !== 'A') {
            return response()->json(['error' => 'Zugriff verweigert'], 403);
        }

        if (!User::where('id', $targetUserId)->exists()) {
            return response()->json(['error' => 'Benutzer nicht gefunden'], 404);
        }

        $files = File::where('user_id', $targetUserId)
            ->with(['user:id,first_name,last_name,email'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($files);
    }

    public function download(Request $request, $id): StreamedResponse|JsonResponse
    {
        $auth = $request->attributes->get('auth_user');
        $file = File::with(['formular', 'answer.formular'])->find((int)$id);

        if (!$file) {
            return response()->json(['error' => 'Datei nicht gefunden'], 404);
        }

        if (empty($file->path)) {
            return response()->json(['error' => 'Kein Dateipfad in der Datenbank hinterlegt'], 422);
        }

        if ($auth['role'] !== 'A') {
            $isUploader = (int)$file->user_id === (int)$auth['id'];
            $isTicketOwner = false;

            if ($file->formular) {
                $isTicketOwner = (int)$file->formular->user_id === (int)$auth['id'];
            } elseif ($file->answer && $file->answer->formular) {
                $isTicketOwner = (int)$file->answer->formular->user_id === (int)$auth['id'];
            }

            if (!$isUploader && !$isTicketOwner) {
                return response()->json(['error' => 'Zugriff verweigert'], 403);
            }
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($file->disk ?: 's3');

        if (!$disk->exists($file->path)) {
            return response()->json(['error' => 'Datei existiert nicht auf dem Speicher'], 404);
        }

        return $disk->download($file->path, $file->name);
    }

    public function delete(Request $request, $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');
        $file = File::find((int)$id);

        if (!$file) {
            return response()->json(['error' => 'Datei nicht gefunden'], 404);
        }

        if ($auth['role'] !== 'A' && (int)$file->user_id !== (int)$auth['id']) {
            return response()->json(['error' => 'Zugriff verweigert'], 403);
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($file->disk ?: 's3');

        if (!empty($file->path) && $disk->exists($file->path)) {
            $disk->delete($file->path);
        }

        $file->delete();

        return response()->json([
            'success' => true,
            'message' => "Datei {$id} erfolgreich gelöscht."
        ]);
    }
}