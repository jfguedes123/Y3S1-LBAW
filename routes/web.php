<?php

use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\UserController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\AdminController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MailController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home
Route::redirect('/', '/login');

// Homepage

Broadcast::routes(['middleware' => ['auth:sanctum']]);
Broadcast::routes();


Route::group(['middleware' => 'auth'], function () {

    Route::get('/homepage/search', [UserController::class, 'search_exact'])->name('search');
});

Route::get('/homepage', function () {
    return view('pages.home');
});

// About Us
Route::get('/about', function () {
    return view('pages.about');
});

Route::get('/features', function () {
    return view('pages.features');
}); 

// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

Route::controller(LoginController::class)->group(function () {
    Route::get('/login/resetPassword', 'showResetPassword')->name('resetPassword');
    Route::get('/login/createPassword', 'showCreatePasswordForm')->name('createPassword');
    Route::put('/login/createPassword', 'createPassword');
});



// Spaces
Route::controller(SpaceController::class) ->group(function() {
    Route::post('/space/add','add');
    Route::get('/space/{id}', 'show');
    Route::put('/space/{id}', 'edit');
    Route::get('/homepage','list');
    Route::delete('/api/space/{id}', 'delete');
    Route::get('/api/space', 'search');
    Route::post('/space/like','like_on_spaces');
    Route::delete('/space/unlike','unlike_on_spaces');
});

// Comments
Route::controller(CommentController::class) ->group(function() {
    Route::post('/comment/create', 'create');
    Route::delete('/api/comment/{id}', 'delete');
    Route::get('/api/comment', 'search');
    Route::post('/comment/like','like_on_comments');
    Route::delete('/comment/unlike','unlike_on_comments');
    Route::put('/comment/edit', 'edit');
});

// Messages
Route::controller(MessageController::class)->group(function(){
    Route::get('/messages','list');
    Route::post('/messages/send','send');
    Route::get('/messages/{emits_id}-{received_id}','show');
    Route::post('/messages/receive','receive'); 
    Route::get('/api/messages','search')->name('messages.search');
});

//Group
Route::controller(GroupController::class)->group(function () {
    Route::post('/group/add', 'add');
    Route::get('/api/group', 'search');
    Route::get('/group/{id}', 'show');
    Route::put('/group/favorite', 'favorite');
    Route::put('/group/unfavorite', 'unfavorite');
    Route::put('/group/edit', 'edit');
    Route::delete('/api/group/{id}', 'delete');
    Route::post('/group/join', 'join');
    Route::delete('/group/leave', 'leave_group');
    Route::delete('/api/group/member/{id}','remove_member');
    Route::get('/group', 'list');
    Route::post('/group/invite', 'invite');
    Route::post('/group/joinrequest', 'join_request');
    Route::post('/group/joinrequest/{id}', 'accept_join_request');
    Route::delete('/group/joinrequest', 'decline_join_request');
    Route::post('/group/acceptinvite', 'accept_invite');
    Route::delete('/group/declineinvite', 'decline_invite');
});

//Users
Route::controller(UserController::class)->group(function () {
    Route::get('/profile/{id}','show');
    Route::get('/api/profile','search');
    Route::get('/profile/{id}/editUser','editUser');
    Route::get('/profile/{id}/following','following');
    Route::get('/profile/{id}/followers','followers');
    Route::delete('api/profile/{id}', 'delete');
    Route::post('/profile/edit', 'edit')->name('edit');
    Route::post('/profile/follow/{id}', 'follow');
    Route::delete('/profile/unfollow/{id}', 'unfollow');
    Route::put('/profile/{id}/updatePhoto', 'updatePhoto');
    Route::post('/profile/followsrequest', 'follow_request');
    Route::post('/profile/followsrequest/{id}','accept_follow_request');
    Route::delete('/profile/followsrequest','decline_follow_request');
    Route::post('/profile/editUser/password', 'editUserPassword')->name('editUserPassword');
    Route::get('/profile/{id}/editUser/password', 'editPassword');
});




// Admin
Route::controller(AdminController::class) ->group(function() {
Route::get('/admin','show');
Route::post('/profile/block/{id}','block');
Route::delete('/profile/unblock/{id}','unblock');
});


//Notifications
Route::controller(NotificationController::class)->group(function () {
    Route::get('/notification', 'list');
    Route::delete('api/notification/{id}', 'delete');
    Route::put('/notification/{id}', 'edit');
});

Route::post('/send', [MailController::class, 'send']);










