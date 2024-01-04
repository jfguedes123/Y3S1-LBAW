<?php 

namespace App\Policies;

use App\Models\Comment; 
use App\Models\User;
use App\Models\Space; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;



class CommentPolicy 
{
    use HandlesAuthorization;

    public function delete(User $user, Comment $comment)
    {
        return (Auth::check() && Auth::user()->isAdmin(Auth::user())) || (Auth::check() && Auth::user()->id == $comment->author_id);
    }

    public function edit(User $user, Comment $comment)
    {
        return (Auth::check() && Auth::user()->isAdmin(Auth::user())) || (Auth::check() && Auth::user()->id == $comment->author_id);
    }

    public function create(User $user, ?Space $space) 
    {
        $getUser = User::findOrFail($space->user_id);
        
        return (Auth::check() && (Auth::user()->isFollowing($getUser) || $getUser->is_public == false));
    }

    public function like(User $user, Comment $comment) 
    {
        $getUser = User::findOrFail($comment->author_id);
        return (Auth::check() && (Auth::user()->isFollowing($getUser) || $getUser->is_public == false)); 
    }
    public function unlike(User $user, Comment $comment)
    {
    return (Auth::check() && Auth::user()->likesComment(Auth::user(),$comment));
    }
    public function search(User $user, Comment $comment)
    {
        return Auth::check();
    }

}
