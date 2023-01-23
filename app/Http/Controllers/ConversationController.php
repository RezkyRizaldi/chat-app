<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $message = $conversation->messages()->create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'message' => $request->input('message'),
        ]);

        $conversation->update([
            'last_message' => $request->input('message'),
        ]);

        ChatEvent::dispatch($message);

        return back();
    }

    public function show(int $receiverId): JsonResponse
    {
        $conversation = Conversation::with(['messages'])
            ->where(fn (Builder $q) => $q->where(['sender_id' => Auth::id(), 'receiver_id' => $receiverId]))
            ->orWhere(fn (Builder $q) => $q->where(['sender_id' => $receiverId, 'receiver_id' => Auth::id()]))
            ->first();

        return response()->json($conversation);
    }
}
