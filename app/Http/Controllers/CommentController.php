<?php

namespace App\Http\Controllers;
use App\Models\CommentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use App\Models\Comment;
use App\Models\Space;   
use App\Models\LikesComments;
use App\Models\LikesSpaces; 
use App\Models\Notification;
use App\Models\User;
use App\Models\Tag;
use App\Http\Controllers\TagController;


use Illuminate\Support\Facades\DB;


class CommentController extends Controller 
{

    public function create(Request $request) 
    {
        $getSpace = $request->space_id;
        $space = Space::find($getSpace);
        
        $this->authorize('create', [Comment::class,$space]);
        DB::beginTransaction();
        Comment::insert([
            'author_id' => Auth::user()->id,
            'space_id' => $request->space_id,
            'username' => Auth::user()->username,
            'content' => " ",
            'date' => date('Y-m-d H:i')
        ]);
        $lastComment = Comment::orderBy('id','desc')->first();
        $string = TagController::tag($request->input('content'), $lastComment);
        Comment::where('id', $lastComment->id)
            ->update([
                'content' => $string
            ]);
        DB::commit();
       

        return redirect('/space/'.$request->space_id)->withSuccess('Comment created successfully!');
    }

    

    public function edit(Request $request)
    {
        $comment = Comment::find($request->id);
        $this->authorize('edit', $comment);
        $comment->content = $request->input('content');
        $comment->save();
        return response()->json($comment);
    }

    public function delete($id)
    {
        $comment = Comment::find($id);
        $this->authorize('delete', $comment);

        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }

        $comment->delete();
        redirect('/space/'.$comment->space_id)->withSuccess('Comment deleted successfully!');
        return response()->json(['message' => 'Comment deleted successfully']);
    }


    public function like_on_comments(Request $request) 
    {
    $comment = Comment::find($request->id);
    $this->authorize('like', $comment);
    LikesComments::insert([
        'user_id' => Auth::user()->id,
        'comment_id' => $comment->id
    ]);
    }

public function unlike_on_comments(Request $request)
{
    
    $comment = Comment::find($request->id);
    $this->authorize('unlike', $comment);
    DB::beginTransaction();
    
    $commentNotification = DB::table('notification')
    ->join('comment_notification', 'notification.id', '=', 'comment_notification.id')
    ->where([
        'comment_id' => $comment->id,
        'notification_type' => 'liked_comment'
    ])
    ->select('notification.*')
    ->first();
    CommentNotification::where('id', $commentNotification->id)
        ->delete();
    LikesComments::where('user_id', Auth::user()->id)
        ->where('comment_id', $comment->id)
        ->delete();
    Notification::where('id', $commentNotification->id)
        ->delete();
    DB::commit();

}


public function searchPage() {
    return view('pages.search');
}
public function search(Request $request) 
{
    $this->authorize('search', [User::class,Comment::class]);
    $input = $request->get('search', '*');
    $comments = Comment::select('id', 'space_id', 'author_id', 'username', 'content','date')
        ->whereRaw("tsvectors @@ to_tsquery(?)", [$input])
        ->orderByRaw("ts_rank(tsvectors, to_tsquery(?)) ASC", [$input])
        ->get();

return view('partials.searchComment', compact('comments'))->render();
}


}

  



?>