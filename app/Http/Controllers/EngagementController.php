<?php

namespace App\Http\Controllers;

use App\Models\AvisArticle;
use App\Models\Post;
use Illuminate\Http\Request;

/** Fidélisation des lecteurs : « Cet article vous a-t-il aidé ? » */
class EngagementController extends Controller
{
    public function avis(Request $request, Post $post)
    {
        $data = $request->validate(['utile' => 'required|boolean']);
        abort_unless($post->published === 'public', 404);

        AvisArticle::updateOrCreate(
            ['post_id' => $post->id, 'visiteur' => hash('sha256', $request->session()->getId() . '|' . $post->id)],
            ['utile' => (bool) $data['utile']]
        );

        $request->session()->put("avis.{$post->id}", (bool) $data['utile']);

        return response()->json(['ok' => true]);
    }
}
