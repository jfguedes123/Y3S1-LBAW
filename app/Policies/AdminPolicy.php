<?php

namespace App\Policies;

use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Space;
use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    public function show() 
    {
        return Auth::check() && Auth::user()->isAdmin(Auth::user());
    }
    public function block()
    {
        return Auth::check() && Auth::user()->isAdmin(Auth::user());
    }
    public function unblock()
    {
        return Auth::check() && Auth::user()->isAdmin(Auth::user());
    }
    public function delete_space() 
    {
        return Auth::check() && Auth::user()->isAdmin(Auth::user());
    }
}








?>