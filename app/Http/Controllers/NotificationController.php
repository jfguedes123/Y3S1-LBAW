<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Space;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\SpaceNotification;
use App\Models\UserNotification;
use App\Models\CommentNotification;
use App\Models\GroupNotification;
use App\Models\User;
use App\Models\Group;

class NotificationController extends Controller
{
    public function list() 
    {
        $this->authorize('list', Notification::class);
        if(!Auth::check())
        {
            return redirect('/homepage')->with('error','qualquer coisa');
        }
        $notifications = Notification::where('received_user', Auth::user()->id)->where('viewed',false)->get();
        $notificationsIds = $notifications->pluck('id');
        $userNotifications = UserNotification::whereIn('id', $notificationsIds)->get();
        $spaceNotifications = SpaceNotification::whereIn('id', $notificationsIds)->get();
        $commentNotifications = CommentNotification::whereIn('id', $notificationsIds)->get();
        $groupNotifications = GroupNotification::whereIn('id', $notificationsIds)->get();
        $notifications = $userNotifications->merge($spaceNotifications)->merge($commentNotifications)->merge($groupNotifications);
        $final = [];
        foreach($notifications as $notification) 
        {
            if($notification->user_id) 
            {
                
                $who = User::select('name')->where('id',$notification->user_id)->first();
                $who = $who->name;
                $not = Notification::select('emits_user')->where('id',$notification->id)->first();
                $link = '/profile/'.$not->emits_user;
            }
            if($notification->group_id) 
            {
                $who = Group::select('name')->where('id',$notification->group_id)->first();
                $who = $who->name;
                $link = '/group/'.$notification->group_id;
            }
            if($notification->space_id) 
            {
                $who = Space::select('content')->where('id',$notification->space_id)->first();
                $who = $who->content;
                $link = '/space/'.$notification->space_id;
            }
            if($notification->comment_id) 
            {
                $who = Comment::select('content')->where('id',$notification->comment_id)->first();
                $who = strip_tags($who->content);
                $space = Comment::select('space_id')->where('id',$notification->comment_id)->first();
                $link = '/space/'.$space->space_id;
            }
            $type = $notification->notification_type;
            $aux = Notification::select('emits_user')->where('id',$notification->id)->first();
            $name = User::select('name')->where('id',$aux->emits_user)->first();
            array_push($final,[$type,$name,$who,$link,$notification->id]);
        }
        return response()->json($final);
    }

    public function edit(int $id) 
    {
        $notification = Notification::findOrFail($id);
        $this->authorize('edit', $notification);
        $notification->viewed = true;
        $notification->save();
    }


    public function delete(int $id) 
    {
        $notification = Notification::findOrFail($id);
        DB::beginTransaction();
        UserNotification::where('id', $id)->delete();
        SpaceNotification::where('id', $id)->delete();
        CommentNotification::where('id', $id)->delete();
        GroupNotification::where('id', $id)->delete();
        DB::commit();
        $notification->delete();   
    }

}
