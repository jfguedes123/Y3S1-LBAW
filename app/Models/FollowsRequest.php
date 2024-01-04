<?php 

namespace App\Models; 

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowsRequest extends Model 
{
    use HasFactory;

    protected $table = 'follows_request';

    public $timestamps = false;

    protected $fillable = [
        'user_id1',
        'user_id2',
        'status'
    ];
}