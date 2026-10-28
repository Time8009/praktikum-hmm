<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ControllerUser extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:tbl_user,email',
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();

        $header = rtrim(strtr(base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
        $payload = rtrim(strtr(base64_encode(json_encode([
            'id'    => $user->id,
            'email' => $user->email,
            'role'  => $user->role,
            'iat'   => time(),
            'exp'   => time() + (60 * 60 * 24),
        ])), '+/', '-_'), '=');

        $secret = env('JWT_SECRET', 'fallback_secret_key_123456');
        $signature = rtrim(strtr(base64_encode(hash_hmac('sha256', "$header.$payload", $secret, true)), '+/', '-_'), '=');

        return response()->json([
            'token' => "$header.$payload.$signature",
            'user'  => [
                'id'    => $user->id,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        if ($auth['role'] === 'A') {
            $users = User::all();
        } else {
            $users = User::where('id', $auth['id'])->get();
        }

        return response()->json($users);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        if ($auth['role'] !== 'A' && (int)$auth['id'] !== $id) {
            return response()->json(['error' => 'Zugriff verweigert'], 403);
        }

        $user = User::with('formulars')->findOrFail($id);
        return response()->json($user);
    }

    public function store(Request $request): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        $validated = $request->validate([
            'first_name' => 'required|string|min:2|max:50',
            'last_name'  => 'nullable|string|min:2|max:50',
            'email'      => 'required|email|unique:tbl_user,email|max:100',
            'birthdate'  => 'nullable|date',
            'adress'     => 'nullable|string|max:100',
            'zip_code'   => 'nullable|string|max:5',
            'city'       => 'nullable|string|max:100',
            'role'       => 'sometimes|in:A,U',
        ]);

        if (empty($auth) || $auth['role'] !== 'A') {
            $validated['role'] = 'U';
        } else {
            $validated['role'] = $validated['role'] ?? 'U';
        }

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        if ($auth['role'] !== 'A' && (int)$auth['id'] !== $id) {
            return response()->json(['error' => 'Zugriff verweigert'], 403);
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|string|min:2|max:50',
            'last_name'  => 'nullable|string|min:2|max:50',
            'role'       => 'sometimes|in:A,U',
            'email'      => "sometimes|email|unique:tbl_user,email,{$id},id|max:100",
            'birthdate'  => 'nullable|date',
            'adress'     => 'nullable|string|max:100',
            'zip_code'   => 'nullable|string|max:5',
            'city'       => 'nullable|string|max:100',
        ]);

        if ($auth['role'] !== 'A') {
            unset($validated['role']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    public function delete(Request $request, int $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        if ($auth['role'] !== 'A' && (int)$auth['id'] !== $id) {
            return response()->json(['error' => 'Zugriff verweigert'], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "Benutzer {$id} gelöscht."
        ]);
    }

    public function admin(Request $request, $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        if ((int)$auth['id'] === (int)$id) {
            return response()->json([
                'error' => 'Du kannst deine eigene Rolle nicht ändern.'
            ], 400);
        }

        $user = User::findOrFail((int)$id);
        $user->role = 'A';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "Benutzer {$id} wurde zum Admin ernannt.",
            'user'    => $user
        ]);
    }

    public function user(Request $request, $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        if ((int)$auth['id'] === (int)$id) {
            return response()->json([
                'error' => 'Du kannst deine eigene Rolle nicht ändern.'
            ], 400);
        }

        $user = User::findOrFail((int)$id);
        $user->role = 'U';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "Benutzer {$id} wurde zum normalen User herabgestuft.",
            'user'    => $user
        ]);
    }

    public function formulars(Request $request, $id): JsonResponse
    {
        $auth = $request->attributes->get('auth_user');

        if ($auth['role'] !== 'A' && (int)$auth['id'] !== (int)$id) {
            return response()->json(['error' => 'Zugriff verweigert'], 403);
        }

        $user = User::with([
            'formulars.subject:id,name,note',
            'formulars.answers:answer_id,form_id,message,created_at,updated_at'
        ])->findOrFail((int)$id);

        return response()->json([
            'user'      => [
                'id'         => $user->id,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'email'      => $user->email,
            ],
            'formulars' => $user->formulars
        ]);
    }

}