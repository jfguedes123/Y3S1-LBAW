<?php 

namespace App\Policies;

use App\Models\Space;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class SpacePolicy
{
    use HandlesAuthorization;

    public function show(?User $user, Space $space)
{
    // Allow viewing the space if the space is public or if the user is the owner, an admin, or following the owner
    $getUser = User::findOrfail($space->user_id);
    return $space->is_public == false || ($user && ($user->isAdmin(Auth::user()) || $user->id == $space->user_id || $user->isFollowing($getUser)));
}

    public function list(User $user)
    {
        return Auth::check();
    }

    public function add(User $user) 
    {
        return $user->id == Auth::user()->id;
    }
    public function edit(User $user, Space $space)
    {
        return ((Auth::check() && Auth::user()->isAdmin(Auth::user())) || (Auth::check() && Auth::user()->id == $space->user_id));
    }
    public function delete(User $user, Space $space)
    {
        return (Auth::check() && Auth::user()->isAdmin(Auth::user())) || (Auth::check() && Auth::user()->id == $space->user_id);
    }
    public function search()
    {
        return Auth::check();
    }

    public function likes()
    {
        return true; 
    }

    public function unlikes(User $user, Space $space)
    {
        return Auth::check();
    }
}


?>