<?php

namespace App\Http\Controllers;

use App\Events\PusherBroadcast;
use App\Events\UserTyping;
use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PusherController extends Controller
{
    public function index($user_id)
    {
        $sendMessages = Message::where('sender', Auth::user()->id)->where('receiver', $user_id)->orderBy('created_at')->get();
        $receivedMessages = Message::where('sender', $user_id)->where('receiver', Auth::user()->id)->orderBy('created_at')->get();

        $allMessages = $sendMessages->merge($receivedMessages)->sortBy('created_at');

        $userName = User::where('id', $user_id)->first()->name;
        $userStatus = User::where('id', $user_id)->first()->status;
        // dd($userStatus);
        return view('index', compact('allMessages', 'user_id', 'userName','userStatus'));
    }

    public function dashboard()
    {
        $authUserId = auth()->id();
        $users = User::where('id', '!=', $authUserId)->get();
        return view('dashboard', [
            'users' => $users
        ]);
    }

    public function receive(Request $request)
    {
        // return view('receive', ['message' => $request->get('message')]);
        return view('receive', ['message' => $request->get('message'),'attachment'=> $request->get('attachment')]);

    }

    public function sendMessage($user_id, Request $request)
    {
        $data['sender'] = Auth::user()->id;
        $data['receiver'] = $user_id;
        $data['message'] = $request->message;

        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment');
            $path = $attachment->store('attachments', 'public');
            $data['attachment'] = $path;
        }

        $message = Message::create($data);
        $receiver = User::find($user_id);

        \broadcast(new PusherBroadcast($receiver, $message->message, $message->attachment));

        return view('broadcast', ['message' => $message->message, 'attachment' => $message->attachment]);
    }

    public function typing(Request $request)
    {
        $userId = Auth::user()->id;
        $otherUserId = $request->user_id;
        \broadcast(new UserTyping($userId, $otherUserId))->toOthers();
    }
}
