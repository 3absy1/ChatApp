<?php

namespace App\Http\Controllers;

use App\Events\PusherBroadcast;
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
        $ReceivedMessages = Message::where('sender', $user_id)->where('receiver', Auth::user()->id)->orderBy('created_at')->get();

        $allMessages = $sendMessages->merge($ReceivedMessages)->sortBy('created_at');

        return view('index', compact('allMessages', 'user_id'));
    }

    // public function broadcast(Request $request)
    // {
    //     broadcast(new PusherBroadcast($request->get('message')))->toOthers();

    //     return view('broadcast', ['message' => $request->get('message')]);
    // }

    public function receive(Request $request)
    {
        return view('receive', ['message' => $request->get('message')]);
    }

    public function sendMessage($user_id,Request $request)
    {
        $data['sender']=Auth::user()->id;
        $data['receiver']=$user_id;
        $data['message']=$request->message;

        Message::create($data);
        $receiver=User::find($user_id);
        $message=$request->message;
        \broadcast(new PusherBroadcast($receiver, $message));
        return view('broadcast', ['message' => $request->get('message')]);

        // return view('receive', ['message' => $request->get('message')]);
    }

}
