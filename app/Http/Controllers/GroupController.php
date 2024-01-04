<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use App\Models\Space;
use App\Models\Comment;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use App\Models\GroupJoinRequest;
use App\Models\Notification;
use App\Models\GroupNotification;

class GroupController extends Controller 
{
    public function add(Request $request)
    {
        echo("<script>console.log('PHP:');</script>");
        $this->authorize('add', Group::class);
        echo("<script>console.log('PHP:');</script>");
        $group = new Group();
        $group->name = $request->input('name');
        $group->user_id = Auth::user()->id;
        $group->description = $request->input('description');
        $group->is_public = null !== $request->public;
        $group->save();
        return redirect('/group')->withSuccess('Group created successfully!');
    } 

    public function show(int $id) 
    {
        $group = Group::findOrFail($id);
        $this->authorize('show', $group);
        $members = $group->members;
        $joins = GroupJoinRequest::whereIn('group_id', [$group->id])->get();
        $spaces = Space::whereIn('group_id', [$group->id])->get();
        return view('pages.group',['group' => $group, 'members' => $members, 'joins' => $joins,'spaces' => $spaces]);
    }

    public function list() 
    {
        $user = Auth::user(); 
        $this->authorize('list', Group::class);
        $groups = Group::whereIn('user_id', [$user->id])->get();
        $publics = Group::where('is_public',false)->get();        
        $members = DB::table('groups')
             ->join('member', 'member.group_id', '=', 'groups.id')
             ->where('member.user_id', Auth::user()->id)
             ->select('groups.*')
             ->get();      
        $all = $groups->concat($publics)->concat($members);
        $all = $all->unique('id');
        $others = Group::all();
        $allIds = $all->pluck('id');
        $others = Group::whereNotIn('id', $allIds)->get(); 
        return view('pages.listGroups',[
        'all' => $all,
        'others' => $others
        ]);
    }

    public function edit(Request $request)
    {
        $group = Group::find($request->id);
        $this->authorize('edit', [Group::class,$group]);
        $group->name = $request->input('name');
        $group->description = $request->input('description');
        $group->save();
        }

    public function delete(int $id)
    {
        $group = Group::find($id);
        $this->authorize('delete', [Group::class,$group]);
        $group->delete();

        // Check if the user is an admin
        $isAdmin = Auth::check() && Auth::user()->isAdmin(Auth::user());

        // Return a JSON response
        return response()->json([
            'isAdmin' => $isAdmin
        ]);
    }


    public function join(Request $request) 
    {
        $group = Group::find($request->id);
        $this->authorize('join', Group::class);  
        Member::insert([
            'user_id' => Auth::user()->id,
            'group_id' => $group->id,
            'is_favorite' => false
        ]);
    }

    public function leave_group(Request $request) 
    {
        $group = Group::find($request->id);
        $this->authorize('leave_group', $group);
        DB::beginTransaction();
        Member::where('group_id', $group->id)->where('user_id', Auth::user()->id)->delete();
        Notification::insert([
            'received_user' => $group->user_id,
            'emits_user' => Auth::user()->id,
            'viewed' => false,
            'date' => date('Y-m-d H:i')
        ]);
        $lastNotification = Notification::orderBy('id','desc')->first();
        GroupNotification::insert([
            'id' => $lastNotification->id,
            'group_id' => $group->id,
            'notification_type' => 'leave group'
        ]);
        DB::commit();
    }
    
    
    public function remove_member(Request $request)
    {
        $group = Group::find($request->groupId);
        $user = User::find($request->userId);
        $this->authorize('remove', [$group,$user]);
        Member::where('group_id', $request->groupId)->where('user_id', $request->userId)->delete();  // Corrected line
        DB::beginTransaction();
        Notification::insert([
            'received_user' => $request->userId,
            'emits_user' => Auth::user()->id,
            'viewed' => false,
            'date' => date('Y-m-d H:i')
        ]);
        $lastNotification = Notification::orderBy('id','desc')->first();
        GroupNotification::insert([
            'id' => $lastNotification->id,
            'group_id' => $request->groupId,
            'notification_type' => 'remove'
        ]);
        DB::commit();
    }
    

public function join_request(Request $request)
{
    $group = Group::find($request->id); 
    $this->authorize('join', Group::class); 
    GroupJoinRequest::insert([
        'user_id' => Auth::user()->id,
        'group_id' => $group->id,
    ]);
}

public function accept_join_request(Request $request)
{
    $group = Group::find($request->group_id);
    $this->authorize('request', $group);
    DB::beginTransaction();
    GroupJoinRequest::where([
        'user_id' => $request->id,
        'group_id' => $group->id
    ])->delete();    
    
    Member::insert([
        'user_id' => $request->id,
        'group_id' => $group->id,
        'is_favorite' => false
    ]);

    Notification::insert([
        'received_user' => $request->id,
        'emits_user' => Auth::user()->id,
        'viewed' => false,
        'date' => date('Y-m-d H:i')
    ]);

    $lastNotification = Notification::orderBy('id','desc')->first();
    GroupNotification::insert([
        'id' => $lastNotification->id,
        'group_id' => $group->id,
        'notification_type' => 'accepted_join'
    ]);
    DB::commit();
}

public function decline_join_request(Request $request)
{
    $group = Group::find($request->group_id);
    $this->authorize('request', $group);
    DB::beginTransaction();
    GroupJoinRequest::where([
        'user_id' => $request->id,
        'group_id' => $group->id
    ])->delete();    
    DB::commit();
    
}

public function invite(Request $request)
{
    $group = Group::find($request->group_id);
    $this->authorize('invite', $group);
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->withErrors(['email' => 'No user found with this email']);
    }

    DB::beginTransaction();
    Notification::insert([
        'received_user' => $user->id,
        'emits_user' => $group->user_id,
        'viewed' => false,
        'date' => date('Y-m-d H:i')
    ]);

    $lastNotification = Notification::orderBy('id','desc')->first();

    GroupNotification::insert([
        'id' => $lastNotification->id,
        'group_id' => $group->id,
        'notification_type' => 'invite'
    ]);

    DB::commit();

    return back()->with('success', 'Invitation sent!');
}


public function accept_invite(Request $request) 
{
    $group = Group::find($request->group_id); 
    $this->authorize('invite_request',Group::class);
    DB::beginTransaction();
    Member::insert([
        'user_id' => Auth::user()->id,
        'group_id' => $group->id,
        'is_favorite' => false
    ]);
    DB::commit();
}


public function decline_invite(Request $request) 
{
    $group = Group::find($request->group_id);
    $this->authorize('invite_request',Group::class);
    DB::beginTransaction();
    $getId = DB::table('notification')->join('group_notification','notification.id','=','group_notification.id')->where([
        'received_user' => Auth::user()->id,
        'emits_user' => $group->user_id,
        'notification_type' => 'invite'
    ])->select('notification.id')->first();

    GroupNotification::where('id', $getId->id)->delete();
    Notification::where([
        'received_user' => Auth::user()->id,
        'emits_user' => $group->user_id,
        'viewed' => false,
        'date' => date('Y-m-d H:i')
    ])->delete();
    DB::commit();
}

public function searchPage() {
    return view('pages.search');
}
public function search(Request $request) 
{
$this->authorize('search', Group::class);  
$input = $request->get('search', '*');

    $groups = Group::select('id', 'user_id', 'name', 'is_public', 'description')
        ->whereRaw("tsvectors @@ to_tsquery(?)", [$input])
        ->orderByRaw("ts_rank(tsvectors, to_tsquery(?)) ASC", [$input])
        ->get();

return view('partials.searchGroup', compact('groups'))->render();
}

public function favorite(Request $request)
{
    $group = Group::find($request->group_id);
    Member::where('group_id', $group->id)->where('user_id', Auth::user()->id)->update(['is_favorite' => true]);
}

public function unfavorite(Request $request)
{
    $group = Group::find($request->group_id);
    Member::where('group_id', $group->id)->where('user_id', Auth::user()->id)->update(['is_favorite' => false]);
}

}
?>
