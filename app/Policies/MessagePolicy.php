<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class MessagePolicy
{
    use HandlesAuthorization;

    public function list()
    {
        return Auth::check();
    }
    public function show(User $user,User $received,User $emits)
    {

        return Auth::check() && ($user->id == $received->id || $user->id == $emits->id);
    }


    public function send() 
    {
        return Auth::check();
    }
}