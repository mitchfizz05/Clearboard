<?php

namespace App\Http\Controllers;

use App\Forum;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Thread;
use App\Post;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;

class ThreadController extends Controller
{
    /**
     * Get thread page
     * @param integer $tid Thread ID
     * @return \Illuminate\View\View
     */
    public function view($tid)
    {
        $thread = Thread::where('id', $tid)->firstOrFail();

        if ($thread->hidden) {
            // Thread hidden, abort request
            abort(403);
        }

        return view('clearboard.thread.viewthread', [
            'thread' => $thread,
            'posts' => Post::where('thread_id', $thread->id)->paginate(20)
        ]);
    }

    /**
     * New thread (not the create thread page)
     * @param Request $request
     * @return string
     */
    public function createApi(Request $request)
    {
        // Validate input
        $this->validate($request, [
            'title' => 'required|max:255',
            'forum' => 'required|numeric',
            'body' => 'required|max:30000'
        ]);

        // Verify forum is a valid forum that can be posted in
        $forum = null;
        try {
            $forum = Forum::findOrFail($request->input('forum'));
        } catch (ModelNotFoundException $e) {
            abort(400); // 400 Bad Request - invalid forum id
        }
        if ($forum->type != 0) {
            abort(400); // 400 Bad Request - not correct forum type
        }

        // Create thread
        $thread = Thread::newThread(
            $request->input('title'),
            $request->input('forum')
        );

        // Create opening post
        $post = post::newPost($request->input('body'), $thread->id);

        // Respond
        return [
            'status' => true,
            'link' => $thread->getUserFriendlyURL()
        ];
    }

    /**
     * Create thread page
     * @param integer $forumid Forum ID
     * @return \Illuminate\View\View
     */
    public function create($forumid)
    {
        $forum = Forum::findOrFail($forumid);
        return view('clearboard.thread.newthread', ['forum' => Forum::find($forumid)]);
    }
}
