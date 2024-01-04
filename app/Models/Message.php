<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Message extends Model 
{
    use HasFactory; 

    public $timestamps = false; 

    protected $table = 'message';

    protected $fillable = [
        'received_id',
        'emits_id',
        'content',
        'date',
        'is_viewed'
    ];


    public function messages() 
    {
        return $this->hasMany(Message::class);
    
    }

    public function received() 
    {
        return User::find($this->received_id);
    }

    public function emits() 
    {
        return User::find($this->emits_id);
    }

}