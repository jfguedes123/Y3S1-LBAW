<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends User
{
    use HasFactory;
    protected $table = 'follows';
    public $timestamps  = false;

    protected $fillable = [
        'user_id1', 'user_id2'
    ];    
}


?>