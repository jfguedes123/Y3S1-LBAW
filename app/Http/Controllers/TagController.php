<?php 

namespace App\Http\Controllers;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Models\Space;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Events\LikesSpaces;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\CommentNotification;

class TagController extends Controller 
{

    public static function tag(String $string, Comment $comment){
        $string = strip_tags($string);
        $modifiedString = $string;
    
        preg_match_all('/@(\w+)/', $string, $matches);
    
        foreach($matches[1] as $username)
        {
            if(User::where('username', $username)->exists())
            {
                $getUser = User::where('username', $username)->first();
                $replacement = "<a href='/profile/$getUser->id'>$getUser->username</a>";
                $modifiedString = str_replace('@'.$username, $replacement, $modifiedString);
    
                DB::beginTransaction();
                Notification::insert([
                    'received_user' => $getUser->id,
                    'emits_user' => Auth::user()->id,
                    'viewed' => false,
                    'date' => date('Y-m-d H:i')
                ]);
                $lastNotification = Notification::orderBy('id','desc')->first();
                CommentNotification::insert([
                    'id' => $lastNotification->id,
                    'comment_id' => $comment->id,
                    'notification_type' => 'comment_tagging'
                ]);
                DB::commit();
            }
        }
    
        return $modifiedString; 
    }
}