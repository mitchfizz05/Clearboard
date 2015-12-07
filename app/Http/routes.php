<?php
namespace App;

use App\PostProcessor\PostProcessor;
use \Route;

/*
 * Clearboard Routes
 */

Route::get('/', function() {
    return view('clearboard.pages.index', ['forums' => Forum::all()]);
});

Route::get('/forum/{fid}-{_}', 'ForumController@getForum');
Route::get('/thread/{tid}-{_}', 'ThreadController@getThread');

// Route for processing markdown to HTML.
Route::post('/ajax/markdown', 'MarkdownController@postParse');
Route::post('/ajax/markdown_inline', 'MarkdownController@postInlineParse'); // for parsing inline markdown

// Posting routes
Route::post('/ajax/new_post', 'PostController@newPost');
Route::post('/ajax/new_thread', 'ThreadController@newThread');
Route::get('/newthread/{forumid}', 'ThreadController@createThread')->middleware('auth');

Route::get('test', function() {
    $test = \App\Facades\PostProcessor::postProcess('yay https://www.youtube.com/watch?v=fNOchIIms5w');
    echo $test;
});

// Authentication routes
Route::group(array('prefix' => '/auth'), function() {
    Route::post('login', 'LoginController@postLogin');
    Route::get('logout', 'Auth\AuthController@getLogout');
});


// Introduction route. Probably will be a way to disable at some point.
Route::get('/welcome', function () {
    return view('clearboard.welcome');
});

