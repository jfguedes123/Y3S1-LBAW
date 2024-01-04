<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller; 
use App\Events\Messages;
use App\Models\Follow;

class MessageController extends Controller 
{
    public function list() 
    {
        $this->authorize('list', Message::class);
        $users = Auth::user()->getUserDMs();
        $emits_ids = collect($users)->pluck('emits_id');
        
        $followings = Follow::select('*')->where('user_id1',Auth::user()->id)->get();
        $followings = $followings->reject(function ($following) use ($emits_ids) {
            return $emits_ids->contains($following->user_id2);
        });
        return view('pages.messages', ['users' => $users,'followings' => $followings]);
    }

    public function show($received_id,$emits_id)
    {
        $user = Auth::user();
        $received = User::find($received_id);
        $emits = User::find($emits_id);
        $all_1 = Message::select('*')->where('received_id', $received_id)->where('emits_id', $emits_id);
        $all_2 = Message::select('*')->where('received_id', $emits_id)->where('emits_id',$received_id);
        $all = $all_1->union($all_2)->orderBy('id','asc')->get();
        $this->authorize('show', [Message::class,$received,$emits]);
        
        
        return view('pages.message', [
            'all' => $all
    ]);
    }

    public function send(Request $request) 
    {
        $message = new Message();
        $message->emits_id = Auth::user()->id;
        if($request->received_id == Auth::user()->id)
        {
            $message->received_id = $request->emits_id;
        }
        else
        {
            $message->received_id = $request->received_id;
        }
        $message->content = $request->input('content'); 
        $message->date = now();
        $message->save();
        broadcast(new Messages($message))->toOthers();
        return redirect()->back();
    }
    public function search(Request $request) 
    {
        $input = $request->get('search') ? $request->get('search') . ':*' : "*";
        $users = User::select('users.id', 'users.name', 'users.username')
            ->whereRaw("users.tsvectors @@ to_tsquery(?)", [$input])
            ->get();
        
        $searchType = 'messages';
 

        return view('partials.searchMessage', compact('users', 'searchType'))->render();
    }
}
