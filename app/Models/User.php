<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

// Added to define Eloquent relationships.
use Illuminate\Database\Eloquent\Relations\HasMany;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'is_public',
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function visibleSpaces() {
        
        $own = Space::select('*')->where('space.user_id', '=', $this->id);

        $null = Space::select('space.*')
            ->fromRaw('space,follows')
            ->where('follows.user_id2', '=', $this->id)
            ->whereColumn('follows.user_id1', '=', 'space.user_id')
            ->where('space.group_id', null);


        $group = Space::select('space.*')
            ->fromRaw('space,member')
            ->where('member.user_id', $this->id)
            ->whereColumn('space.group_id','member.group_id');
            

        return $own->union($null)->union($group)
            ->orderBy('date','desc');
    }

    public function isFollowing(User $user) {
        return Follow::where('user_id1', $this->id)->where('user_id2', $user->id)->exists();
    }

    public function isMember(User $user, Group $group)
    {
        return Member::where('user_id', $user->id)->where('group_id', $group->id)->exists();
    }

    public function isFavorite(User $user,Group $group)
    {
        return Member::where('user_id', $user->id)->where('group_id', $group->id)->where('is_favorite', true)->exists();
    }

     public function likesSpace(User $user, Space $space) {
        return LikeSpace::where('user_id', $user->id)->where('space_id', $space->id)->exists();
    }

    public function likesComment(User $user, Comment $comment) 
    {
        return LikesComments::where('user_id', $user->id)->where('comment_id', $comment->id)->exists();
    }
    

    public function getUsername(int $space_id) 
    {
        $space = Space::findOrFail($space_id);
        $user = User::findOrFail($space->user_id);
        return $user->username;
    }

    public function isAdmin(User $user) 
    {
        return count($this->hasOne(Admin::class,'id')->get());
    }

    public function isBlock()
    {
        return count($this->hasOne(Block::class,'id')->get());
    }
    public function hasSpaces() 
    {
        return $this->hasMany(Space::class,'user_id')->where('group_id',null)->orderBy('date','desc');
    }
    public function hasFollowers()
    {
        return $this->belongsToMany(User::class, 'follows', 'user_id2', 'user_id1');
    }
    public function hasFollowings()
    {
        return $this->belongsToMany(User::class, 'follows', 'user_id1', 'user_id2');
    }
    public function showFollows() 
    {
    return $this->follows()->get();
    }

    public function follows()
    {
        return $this->belongsToMany(User::class, 'follows', 'user_id1', 'user_id2');
    }

    public function getUserDMs() 
    {
        $users = Message::select('emits_id')
            ->where('received_id', $this->id)
            ->union(
                Message::select('received_id')
                        ->where('emits_id', $this->id)
                )
            ->distinct()->get();

        return $users;
    }

    public function hasSentFollowRequest($user) 
    {
        return FollowsRequest::where('user_id1', Auth::user()->id)->where('user_id2', $user->id)->exists();
    }

    public function hasSentJoinRequest($group) 
    {
        return GroupJoinRequest::where('user_id', Auth::user()->id)->where('group_id', $group)->exists();
    }

    public function media() { 
        $files = glob("images/profile/*", GLOB_BRACE);
        foreach($files as $file) 
        {
            $filename = basename($file, ".jpg");
            if($filename != "default"){
            $check = Crypt::decrypt($filename);
            if($check == $this->id)
            {
                
                return "/".$file;
            }
        }
        }
        $default = "/images/profile/default.jpg";
        if(sizeof($files) < 1) return $default;
        return "/".$files[0];
    }

    public function getFollowers() 
    {
        $followers = Follow::where('user_id2',Auth::user()->id)->get();

        return $followers;
    }

    public function getFollowings() 
    {
        $followings = Follow::where('user_id1',Auth::user()->id)->get();
        
        return $followings;
    }

    public function messages() 
    {
        return $this->hasMany(Message::class);
    }

    public function user() 
    {
        return $this->belongsTo(User::class);
    }

}
