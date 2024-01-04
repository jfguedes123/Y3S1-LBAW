<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Space;
use App\Models\User;
//add the crypt
use Illuminate\Support\Facades\Crypt;
class FileController extends Controller 
{
    public static function update(String $id, string $type, Request $request)
    {
    if ($request->file('image')) {
        foreach ( glob(public_path().'/images/'.$type.'/*',GLOB_BRACE) as $image){
            $filename = basename($image, ".jpg");
            if($filename != "default"){
            $check = Crypt::decrypt($filename);
            $check_2 = Crypt::decrypt($id);
            if($check == $check_2)
            {
                unlink($image);
            }
        }
    }
    }
    $file= $request->file('image');
    $filename= $id.".jpg";
    $file->move(public_path('images/'. $type. '/'), $filename);
    }
}