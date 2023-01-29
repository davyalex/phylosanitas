<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use RealRashid\SweetAlert\Facades\Alert;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $post = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at','desc')->get();
        return view('admin.pages.post.index', compact('post'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $category = Category::with('posts')->get();
        return view('admin.pages.post.add', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $request->validate([
            'title' => 'required',
            'description' => '',
            'category' => 'required',
            'lien' => '',
        ]);

        $post = Post::create([
            'title' => $request['title'],
            'description' => $request['description'],
            'category_id' => $request['category'],
            'lien' => $request['lien'],
            // 'user_id' => Auth::user()->id,
        ]);

        if ($request->file('image')) {
            $post->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }


        Alert::toast('post inseré avec success', 'success');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post , $slug)
    {
        //
        $category = Category::with('posts')->get();
        $post = Post::with(['category', 'commentaires', 'media', 'user'])
        ->whereSlug($slug)
        ->first();
        return view('admin.pages.post.edit', compact(['post','category']));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
             'title' => 'required',
            'description' => '',
            'category' => 'required',
            'lien' => '',
        ]);

        $post = tap(Post::find($id))->update([
             'title' => $request['title'],
            'description' => $request['description'],
            'category_id' => $request['category'],
            'lien' => $request['lien'],
        ]);

        if ($request->hasFile('image')) {

            $post->clearMediaCollection('image');
            $post->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }
        $post = Post::with(['category', 'commentaires', 'media', 'user'])->get();

        Alert::toast('post modifié avec success', 'success');
        return view('admin.pages.post.index',compact('post'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $delete = Post::find($id)->delete();
        $delete = DB::table('media')->where('model_id', $id)->delete();
        Alert::toast('supprimé avec success', 'success');
        return back();
    }
}
