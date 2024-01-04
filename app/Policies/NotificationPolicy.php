<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class NotificationPolicy
{
    use HandlesAuthorization;

   public function list() 
   {
         return Auth::check();
   }

   public function edit(User $user,Notification $notification) 
   {
         return (Auth::check() && Auth::user()->id == $notification->received_user);
   }

   public function delete(User $user,Notification $notification) 
   {
         return (Auth::check() && Auth::user()->id == $notification->received_user);
   }
}