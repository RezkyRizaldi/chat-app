<?php

namespace App\Http\Controllers;

use App\Events\GroupChatEvent;
use App\Models\GroupMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupChatConversationController extends Controller
{
    public function index(): JsonResponse
    {
        $groupMessages = GroupMessage::with(['user'])->get();

        return response()->json($groupMessages);
    }

    public function store(Request $request): RedirectResponse
    {
        $groupMessage = GroupMessage::create([
            'user_id' => Auth::id(),
            'message' => $request->input('message'),
        ]);

        $groupMessage->load(['user']);

        GroupChatEvent::dispatch($groupMessage);

        return back();
    }
}
