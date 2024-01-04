<?php 

namespace App\Models; 

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class LikesComments extends Model 
{
    use HasFactory; 

    protected $table = 'likes_on_comments';

    public $timestamps = false; 

    protected $fillable = [
        'user_id',
        'comment_id'
    ];

    public function user() 
    {
        $this->belongsTo('App\Models\User');
    }
    public function comment() 
    {
        $this->belongsTo('App\Models\Comment');
    }

    
}