<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Member; 


class Group extends Model 
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'groups'; 

    protected $fillable = [
        'name',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function spaces() 
    {
        return $this->hasMany(Space::class);
    }
    
    public function members() 
    {
        return $this->hasMany(Member::class);
    }

    public function hasMember(User $user) 
    {
        return $this->members()->where('user_id',$user->id)->exists();
    }
}