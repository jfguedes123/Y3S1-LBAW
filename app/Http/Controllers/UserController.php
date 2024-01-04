<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Space;
use App\Models\Follow;
use Illuminate\Support\Facades\DB;
use App\Models\Block;
use App\Models\FollowsRequest;
use App\Models\Notification;
use App\Models\UserNotification;
use App\Models\Group;
use App\Models\Comment;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\FileController;


class UserController extends Controller
{

    public function show(int $id)
    {   
        $user = User::findOrFail($id);
        $this->authorize('show', [User::class,$user]);
        if (Auth::check()) {
            $isBlocked = Block::where('user_id', $id)->exists();
            $isFollowing = Auth::user()->isFollowing($user);
            $wants = FollowsRequest::whereIn('user_id2', [$user->id])->get();
            $countFollows = Follow::where('user_id1', $user->id)->count();
            $countFollowers = Follow::where('user_id2', $user->id)->count();
            $spaces = Space::where('user_id',$user->id)->get();
            return view('pages.user', [
                'user' => $user,
                'isFollowing' => $isFollowing,
                'isBlocked' => $isBlocked,
                'wants' => $wants,
                'countFollows' => $countFollows,
                'countFollowers' => $countFollowers,
                'spaces' => $spaces
            ]);
        } else {
            if ($user->is_public == 1) {
                return back()->withErrors([
                    'profile' => 'The provided profile is private.'
                ]);
            } else {
                return view('pages.user', [
                    'user' => $user,
                ]);
            }
        }
    }


    public function editUser()
    {
        $this->authorize('editUser', Auth::user());
        if (Auth::check()) {
            return view('pages.editUser', [
                'user' => Auth::user()->name,
                Auth::user()->email,
                Auth::user()->password
            ]);
        } else {
            return redirect('/homepage');
        }
    }


    public function follow(Request $request, $id)
    {
        $this->authorize('follow', User::class);
        Follow::insert([
            'user_id1' => Auth::user()->id,
            'user_id2' => $id,
        ]);
    }

    public function unfollow(Request $request, $id)
    {
        $this->authorize('unfollow', User::class);
        if (Auth::user()->id == $id) {
            Follow::where('user_id1', $request->input('id'))->where('user_id2', $id)->delete();
        } else {
            Follow::where('user_id1', Auth::user()->id)->where('user_id2', $id)->delete();
        }
    }

    public function edit(Request $request)
    {
        $this->authorize('edit', User::class);
        if (Auth::user()->isAdmin(Auth::user())) {

            $user = User::find($request->input('id'));

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            if ($request->name != null) {
                $user->name = $request->name;
            }
            if ($request->email != null) {
                $user->email = $request->email;
            }
            if ($request->is_public != null) {
                $user->is_public = $request->is_public;
            }
            if ($request->password != null) {
                $user->password = Hash::make($request->password);
            }
            if ($request->file('image') != null) {
                if (!in_array(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
                    return redirect('user/edit')->with('error', 'File not supported');
                }
                $request->validate([
                    'image' =>  'mimes:png,jpeg,jpg',
                ]);
                $enc = encrypt($user->id);
                FileController::update($enc, 'profile', $request);
            }
            $user->save();
            return redirect('/profile/' . $user->id)->withSuccess('User edited successfully!');
        } else {
            $user = Auth::user();
            if ($request->name == null) {
                $user->name = Auth::user()->name;
            } else if ($request->name != null) {
                $user->name = $request->name;
            }
            if ($request->email == null) {
                $user->email = Auth::user()->email;
            } else if ($request->email != null) {
                $user->email = $request->email;
            }
            if ($request->is_public == null) {
                $user->is_public = Auth::user()->is_public;
            } else if ($request->is_public != null) {
                $user->is_public = $request->is_public;
            }
            if ($request->file('image') != null) {
                if (!in_array(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
                    return redirect('user/edit')->with('error', 'File not supported');
                }
                $request->validate([
                    'image' =>  'mimes:png,jpeg,jpg',
                ]);
                $enc = encrypt($user->id);
                FileController::update($enc, 'profile', $request);
            }
            if($request->oldPassword != null) 
            {
                if(Hash::check($request->oldPassword,$user->password))
            {
            if($request->password == $request->password_confirmation) 
            {  
                $request->validate([
                    'password' => 'required|min:8|confirmed',
                    'password_confirmation' => 'required|min:8'
                ]);
                $user->password = Hash::make($request->password);
                $user->save();
                return redirect('/profile/' . $user->id)->withSuccess('Password edited successfully!');
            }
            else 
            {
                return redirect("/profile/$user->id")->withErrors([
                    'password' => 'Passwords do not match.'
                ]);
            }
            }
            else 
            {
                return redirect("/profile/$user->id")->withErrors([
                    'password' => 'Wrong password.'
                ]);
            }
            }
                $user->save();
                return redirect('/profile/' . $user->id)->withSuccess('User edited successfully!');
            }
        }

    public function editPassword()
    {
        return view('pages.editUserPassword');
    }

    public function editUserPassword(Request $request)
    {
        $user = Auth::user();
        
        if(Hash::check($request->oldPassword,$user->password))
        {
            if($request->password == $request->password_confirmation) 
            {
                $user->password = Hash::make($request->password);
                $user->save();
                return redirect('/profile/' . $user->id)->withSuccess('Password edited successfully!');
            }
            else 
            {
                return redirect("/profile/$user->id/editUser/password")->withErrors([
                    'password' => 'Passwords do not match.'
                ]);
            }
        }
        else 
        {
            return redirect("/profile/$user->id/editUser/password")->withErrors([
                'password' => 'Wrong password.'
            ]);
        }
        }

    public function update(int $id, string $type, Request $request)
    {
        die();
        if ($request->file('image')) {
            foreach (glob(public_path() . '/images/' . $type . '/' . $id . '.*', GLOB_BRACE) as $image) {
                if (file_exists($image)) unlink($image);
            }
        }
        $file = $request->file('image');
        $filename = $id . ".jpg";
        $file->move(public_path('images/' . $type . '/'), $filename);
    }


    public function updatePhoto(Request $request, int $id)
    {
        $user = User::find($id);
        echo("<script>console.log('IMAGE:');</script>");
        if ($request->hasFile('profile_picture')) {
            $filename = $user->id . '.jpg';
            $request->profile_picture->move(public_path('images/profile'), $filename);
        }

        return redirect('/profile/' . $id);
    }

    public function delete(Request $request, $id)
    {
        $user = User::find($id);
        $isAdmin = Auth::user()->isAdmin(Auth::user());
        $user->delete();
        redirect('/homepage')->withSuccess('User deleted successfully!');
        if ($isAdmin) {
            return redirect('/admin')->withSuccess('User deleted successfully!');
        } else {
            Auth::logout();
            return redirect('/')->withSuccess('User deleted successfully!')->with('logout', true);
        }
    }


    public function searchPage()
    {
        return view('pages.search');
    }

    public function search(Request $request)
    {
        $input = $request->get('search') ? $request->get('search') . ':*' : "*";
        $users = User::select('users.id', 'users.name', 'users.username')
            ->whereRaw("users.tsvectors @@ to_tsquery(?)", [$input])
            ->get();
        $searchType = 'users';


        return view('partials.searchUser', compact('users', 'searchType'))->render();

    }


    public function follow_request(Request $request)
    {
        $user = User::find($request->user_id2);
        $this->authorize('follow', $user);
        DB::beginTransaction();
        FollowsRequest::insert([
            'user_id1' => $request->user_id1,
            'user_id2' => $user->id
        ]);
        DB::commit();
    }

    public function accept_follow_request(Request $request)
    {
        $user1 = User::find($request->user_id1);
        $user2 = User::find($request->user_id2);
        $this->authorize('request',$user2);
        DB::beginTransaction();
        FollowsRequest::where([
            'user_id1' => $user1->id,
            'user_id2' => $user2->id

        ])->delete();

        Notification::insert([
            'received_user' => $user1->id,
            'emits_user' => $user2->id,
            'viewed' => false,
            'date' => now()
        ]);

        $lastNotification = Notification::orderBy('id', 'desc')->first();

        UserNotification::insert([
            'id' => $lastNotification->id,
            'user_id' => $user1->id,
            'notification_type' => 'accepted_follow'
        ]);


        Follow::insert([
            'user_id1' => $user1->id,
            'user_id2' => $user2->id
        ]);

        DB::commit();
    }

    public function decline_follow_request(Request $request)
    {
        $user1 = User::find($request->user_id1);
        $user2 = User::find($request->user_id2);
        $this->authorize('request',$user2);
        DB::beginTransaction();
        FollowsRequest::where([
            'user_id1' => $user1->id,
            'user_id2' => $user2->id
        ])->delete();

        DB::commit();
    }


    public function search_exact(Request $request)
    {
        $date = $request->input('date');
        $input = $request->input('search');
        $profileType = $request->input('profileType');
        
        $users = User::query();
        $spaces = Space::query();
        $groups = Group::query();
        $comments = Comment::query();

        if($input == null) {
            return view('pages.search', ['users' => [], 'spaces' => [], 'comments' => [], 'groups' => []]);
        }
        
        if($input != null)
        {
            $users->where('username', 'like', '%' . $input . '%');
            $spaces->where('content', 'like', '%' . $input . '%');
            $comments->where('content', 'like', '%' . $input . '%');
            $groups->where('name', 'like', '%' . $input . '%');
        }
    
        if ($date != null) {
            $spaces->where('date', $date);
            $comments->where('date', $date);
        }
    
        if($profileType != null) {
            if($profileType == 'anyone') {
                // No additional filters needed
            } else if($profileType == 'follow') {
                $following = Follow::select('user_id2')->where('user_id1',Auth::user()->id)->get()->pluck('user_id2');
                $users->whereIn('id', $following);
                $spaces->whereIn('user_id', $following);
                $comments->whereIn('author_id', $following);
                $groups->whereIn('user_id', $following);
            }
        }
    
        $users = $users->orderBy('username')->get();
        $spaces = $spaces->orderBy('content')->get();
        $comments = $comments->orderBy('content')->get();
        $groups = $groups->orderBy('name')->get();
    
        return view('pages.search', ['users' => $users, 'spaces' => $spaces, 'comments' => $comments, 'groups' => $groups]);
    }

    public function following()
    {
        $follows = Follow::where('user_id1', Auth::user()->id)->get();
        return view('pages.following', [
            'follows' => $follows
        ]);
    }
    public function followers()
    {
        $follows = Follow::where('user_id2', Auth::user()->id)->get();
        return view('pages.followers', [
            'follows' => $follows
        ]);
    }
}
