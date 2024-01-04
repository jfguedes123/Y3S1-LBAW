<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Space;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comment';

    public $timestamps = false;

    protected $fillable = [
        'content',
        'author_id',
        'space_id',
        'date',
    ];

    public function author() 
    {
        return User::find($this->author_id);
    }

    public function space() 
    {
        return Space::find($this->space_id)->get();
    }
    public function likes() 
    {
        return count($this->hasMany('App\Models\LikesComments')->get());
    }
}


?>