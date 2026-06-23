<?php

namespace App\Http\Controllers;

use App\Models\Soumission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;

class SondageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id'        => 'required|exists:posts,id',
            'sondage_option' => 'required|exists:option_sondages,id',
        ]);

        $session = Session::getId();

        $dejaVote = Soumission::where('user_session', $session)
            ->where('post_id', $request->post_id)
            ->exists();

        if (!$dejaVote) {
            Soumission::create([
                'user_session'     => $session,
                'post_id'          => $request->post_id,
                'option_sondage_id' => $request->sondage_option,
            ]);

            Alert::toast('Merci d\'avoir participé au sondage !', 'success');
        } else {
            Alert::toast('Vous avez déjà participé à ce sondage.', 'warning');
        }

        return back();
    }
}
