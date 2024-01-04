<?php 


namespace App\Policies;

use App\Models\User; 
use App\Models\Space;
use App\Models\Group;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{

    use HandlesAuthorization;

    public function add()
    {
        return Auth::check();
    }

    public function show(User $user, Group $group)
    {
        return (Auth::check());
    }

    public function list()
    {
        return Auth::check();
    }

    public function edit(User $user, Group $group)
    {
        echo("<script>console.log('PHP:');</script>");
        return (Auth::check() && Auth::user()->isAdmin(Auth::user())) || (Auth::check() && Auth::user()->id == $group->user_id);
    }

    public function delete(User $user, Group $group)
    {
        echo("<script>console.log('PHP:');</script>");
        return (Auth::check() && Auth::user()->isAdmin(Auth::user())) || (Auth::check() && Auth::user()->id == $group->user_id);
    }

    public function join() 
    {
        return Auth::check();
    }

    public function leave_group(User $user,Group $group)
    {
        return Auth::check() && Auth::user()->id != $group->user_id;
    }


    public function remove(User $user, Group $group)
    {
        return (Auth::check() && Auth::user()->isAdmin(Auth::user())) || (Auth::check() && Auth::user()->id == $group->user_id);
    }

    public function request(User $user, Group $group)
    {
        return (Auth::check() && Auth::user()->isAdmin(Auth::user())) || (Auth::check() && Auth::user()->id == $group->user_id);
    }

    public function invite(User $user, Group $group)
    {
        return (Auth::check() && Auth::user()->isAdmin(Auth::user())) || (Auth::check() && Auth::user()->id == $group->user_id);
    }

    public function invite_request()
    {
        return Auth::check();
    }

    public function search()
    {
        return Auth::check();
    }


}