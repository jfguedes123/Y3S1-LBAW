<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'group_id', 'inviter_id'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }
}
