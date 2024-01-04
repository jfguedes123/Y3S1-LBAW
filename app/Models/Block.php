<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Block extends Model
{
    use HasFactory;

    protected $table = 'blocked';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


?>