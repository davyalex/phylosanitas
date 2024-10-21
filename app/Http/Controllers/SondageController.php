<?php

namespace App\Http\Controllers;

use App\Models\Soumission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;

class SondageController extends Controller
{


    public function index()
    {
        $statistic = Soumission::with(['post', 'optionSondage'])->get();

        dd($statistic->toArray());
        return view('site.pages.detail', compact('$statistic'));
    }




    //
    public function store(Request $request)
    {

        //recuperation la session currente
        $session = Session::getId();

        $verify = Soumission::where('user_session', $session)
            ->where('post_id', $request['post_id'])
            ->get();
        if (count($verify) < 1) {
            $sondage = Soumission::create([
                'user_session' => $session,
                'post_id' => $request['post_id'],
                'option_sondage_id' => $request['sondage_option'],
            ]);

            Alert::toast('Merçi d\'avoir participé au sondage ', 'success');
        } else {
            Alert::Error('Vous avez déjà participé au sondage!');
        }

        return back();
    }



    
}
