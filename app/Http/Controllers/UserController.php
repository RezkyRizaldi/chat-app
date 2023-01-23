<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = Conversation::with(['messages' => fn ($q) => $q->latest()->limit(1), 'user'])
            ->where('receiver_id', '=', Auth::id())
            ->get();

        return response()->json($users);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }
}
