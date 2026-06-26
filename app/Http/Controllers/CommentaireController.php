<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Commentaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CommentaireController extends Controller
{
    public function store(Request $request)
    {
        if (Auth::check()) {
            $request->validate([
                'message' => 'required|string|max:2000',
                'post_id' => 'required|exists:posts,id',
            ]);

            $user_name = Auth::user()->name;
        } else {
            $request->validate([
                'name'    => 'required|string|max:100',
                'message' => 'required|string|max:2000',
                'post_id' => 'required|exists:posts,id',
            ]);

            $user_name = $request->name;
        }

        Commentaire::create([
            'user_name'  => $user_name,
            'user_email' => $request->email,
            'message'    => $request->message,
            'post_id'    => $request->post_id,
        ]);

        // Invalider le cache du post pour afficher le nouveau commentaire immédiatement
        $post = Post::find($request->post_id);
        if ($post) {
            Cache::forget("post_detail_{$post->slug}");
        }

        return back()->with('success_comment', 'Votre commentaire a été publié avec succès.');
    }
}
