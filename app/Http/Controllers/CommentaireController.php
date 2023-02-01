<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Commentaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCommentaireRequest;
use App\Http\Requests\UpdateCommentaireRequest;

class CommentaireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCommentaireRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
      
        if (Auth::check()) {
            $request->validate([
                'name'=>'',
                'message'=>'required',
    
            ]);
          $user_name = Auth::user()->roles[0]->name;
        }else {
            $request->validate([
                'name'=>'required',
                'message'=>'required',
    
            ]);
            $user_name = $request['name'];
        }
        $commentaire = Commentaire::create([
            'user_name'=>$user_name,
            'user_email'=>$request['email'],
            'message'=>$request['message'],
            'post_id'=>$request['post_id']
        ]);

        // $post = Post::with(['category', 'commentaires', 'media', 'user'])
        // ->whereId($id)->first();
        
        return back();
        
        // return view('site.pages.detail', compact([ 'post', 'commentaire']));    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Commentaire  $commentaire
     * @return \Illuminate\Http\Response
     */
    public function show(Commentaire $commentaire)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Commentaire  $commentaire
     * @return \Illuminate\Http\Response
     */
    public function edit(Commentaire $commentaire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCommentaireRequest  $request
     * @param  \App\Models\Commentaire  $commentaire
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCommentaireRequest $request, Commentaire $commentaire)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Commentaire  $commentaire
     * @return \Illuminate\Http\Response
     */
    public function destroy(Commentaire $commentaire)
    {
        //
    }
}
