<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpaceNotification extends Model
{
    use HasFactory;
    protected $table = 'space_notification';
    public $timestamps  = false;

    protected $fillable = [
        'space_id', 'notification_type'
    ];
}