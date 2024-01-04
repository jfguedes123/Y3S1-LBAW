<?php 

namespace App\Models; 

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class LikeSpace extends Model 
{
    use HasFactory; 

    protected $table = 'likes_on_spaces';

    public $timestamps = false; 

    protected $fillable = [
        'user_id',
        'space_id'
    ];

    public function space() 
    {
        $this->belongsTo('App\Models\Space');
    }

    public function user() 
    {
        $this->belongsTo('App\Models\User');
    }
}
