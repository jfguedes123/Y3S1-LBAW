<?php 

namespace App\Http\Controllers;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Space;
use App\Models\User;
use App\Events\LikesSpaces;
use App\Models\LikeSpace;
use App\Models\Notification;
use App\Models\SpaceNotification;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\FileController;
use App\Models\LikesOnSpaces;
//add Crypt
use Illuminate\Support\Facades\Crypt;
class SpaceController extends Controller 
{

    public function show(int $id)
{
    $space = Space::findOrFail($id);
    $user = User::findOrFail($space->user_id);
    if ($space->is_public || Auth::check()) {
        // Only authorize if the space is not public or the user is authenticated
        $this->authorize('show', [Space::class, $space]);
    }

    return view('pages.space',[
        'space' => $space
    ]);
}
    


  public function list()
  { 
      $publics = Space::publicSpaces()->get();
  
      if (!Auth::check()) {
          return view('pages.home', ['publics' => $publics, 'spaces' => $publics]);
      }
      $this->authorize('list', Space::class);
      $followingIds = Auth::user()->showFollows()->pluck('id');
      $spaces = Space::whereIn('user_id', $followingIds)->get(); 
      $all = $publics->concat($spaces);
      $all = $all->unique('id');
      return view('pages.home', [
          'publics' => $publics,
          'spaces' => $spaces      ]);
  }

 
    public function edit(Request $request)
    {
        $space = Space::find($request->id);
        $this->authorize('edit', [Space::class,$space]);
        $space->content = $request->input('content');
        $space->is_public = $request->input('is_public', false);
        $space->save();
    }


    public function add(Request $request)
    {
    $this->authorize('add', Space::class);
    $space = new Space();
      $space->user_id = Auth::user()->id;
      if($request->input('group_id') != null) 
      {
        $space->group_id = $request->input('group_id');
        $space->is_public = true;
      }
      if($request->input('public') != null) 
      {
        $space->is_public = true;
      }
      else {
        $space->is_public = false;
      }
      $space->content = $request->input('content');
      $space->date = date('Y-m-d H:i');
      $space->save();
      if($request->file('image') != null)
    {
        if( !in_array(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION),['jpg','jpeg','png'])) {
            return redirect('/homepage')->with('error', 'File not supported');
        }
        $request->validate([
            'image' =>  'mimes:png,jpeg,jpg',
        ]);
        $enc = Crypt::encrypt($space->id);
        Filecontroller::update($enc,'space',$request);
    }
      if($space->group_id == null){
      return redirect('/homepage')->withSuccess('Space created successfully!');}
      else {
        return redirect('/group/'.$space->group_id)->withSuccess('Space created successfully!');}
    }

    

    public function delete($id)
    {

        $space = Space::find($id);
        $this->authorize('delete', [Space::class,$space]);

        if (!$space) {
            return response()->json(['error' => 'Space not found'], 404);
        }

        $space->delete();
        
        return response()->json([
            'isAdmin' => Auth::user()->isAdmin(Auth::user()),
        ]);    
    }

    public function searchPage() {
        return view('pages.search');
}
public function search(Request $request) 
{
    $this->authorize('search', Space::class);
    $input = $request->get('search', '*');
    
        $spaces = Space::select('id', 'content', 'date', 'user_id', 'group_id')
            ->whereRaw("tsvectors @@ to_tsquery(?)", [$input])
            ->orderByRaw("ts_rank(tsvectors, to_tsquery(?)) ASC", [$input])
            ->get();

    return view('partials.searchSpace', compact('spaces'))->render();
}


public function like_on_spaces(Request $request) 
{
    $space = Space::find($request->id);
    event(new LikesSpaces($space->id));
    LikeSpace::insert([
        'user_id' => Auth::user()->id,
        'space_id' => $space->id
    ]);
    return response()->json(['success' => 'You liked this space!']);
}

public function unlike_on_spaces(Request $request)
{
    $space = Space::find($request->id);
    LikeSpace::where('user_id', Auth::user()->id)
        ->where('space_id', $space->id)
        ->delete();
}
}





?>