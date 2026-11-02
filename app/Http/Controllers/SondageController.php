<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Soumission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class SondageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id'        => 'required|exists:posts,id',
            // La réponse doit appartenir à ce sondage
            'sondage_option' => ['required', Rule::exists('option_sondages', 'id')->where('post_id', $request->post_id)],
        ], [
            'sondage_option.required' => 'Choisissez une réponse avant de valider.',
            'sondage_option.exists'   => 'Cette réponse n\'existe pas pour ce sondage.',
        ]);

        $postId  = (int) $request->post_id;
        $session = Session::getId();

        $existing = Soumission::where('user_session', $session)->where('post_id', $postId)->value('option_sondage_id');

        if (!$existing) {
            Soumission::create([
                'user_session'      => $session,
                'post_id'           => $postId,
                'option_sondage_id' => $request->sondage_option,
            ]);

            // Résultats en cache sur la page publique : on les rafraîchit tout de suite
            Cache::forget("post_stats_{$postId}");
            Cache::forget("post_votes_{$postId}");

            Session::put("poll_votes.$postId", (int) $request->sondage_option);
            Session::flash('poll_status', 'success');
        } else {
            Session::put("poll_votes.$postId", (int) $existing);
            Session::flash('poll_status', 'already');
        }

        $slug = Post::whereKey($postId)->value('slug');

        return redirect()->to(route('post.detail', ['slug' => $slug]) . '#sondage');
    }
}
