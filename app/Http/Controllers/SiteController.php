<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class SiteController extends Controller
{

public function __construct()
{
    // $category = app('category');
    $this->category;
}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // first recent post
        $post_recent = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')->first();

        // recent post
        $post_last = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')->get()->take(4);
        $post = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')
            ->get()->take(12);

        $category = Category::with('posts')->get()->sortBy('title');


        return view('site.pages.accueil', compact(['post_recent', 'post_last', 'post', 'category']));
    }


    public function post(Request $request)
    {


        //liste des categories
        $category = Category::with('posts')->get()->sortBy('title');

        //liste recent post
        $post_last = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')->get()->take(4);


        /******* */

        $slug_req = request('category');

        // category si request
        $category_req = Category::whereSlug($slug_req)->first();

        $post = Post::with(['category', 'commentaires', 'media', 'user'])
        ->when($slug_req, function($q) use( $category_req){
            return $q->where('category_id', $category_req['id']);
        })->orderBy('created_at', 'desc')->paginate(5);
        
       
          
        return view('site.pages.post', compact(['post_last', 'post', 'category', 'category_req']));

    }


    public function detail(Request $request)
    {


        //liste des categories
        $category = Category::with('posts')->get()->sortBy('title');

        //liste recent post
        $post_last = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')->get()->take(4);


        /******* */

        $slug_req = request('slug');


        $post = Post::with(['category', 'commentaires', 'media', 'user'])
        ->whereSlug($slug_req)->first();
        
        return view('site.pages.detail', compact(['post_last', 'post', 'category']));

    }


public function contact(){
    $this->category;
    return view('site.pages.contact');
}
   
}
