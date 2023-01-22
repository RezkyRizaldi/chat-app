<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class GroupChatController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Chat/Group/Index');
    }
}
