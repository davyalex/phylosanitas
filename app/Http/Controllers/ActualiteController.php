<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\StoreActualiteRequest;
use App\Http\Requests\UpdateActualiteRequest;

class ActualiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $actualite = Actualite::with('media')->orderBy('created_at','desc')->get();
            return view('admin.pages.actualite.index',compact('actualite'));
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
     * @param  \App\Http\Requests\StoreActualiteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        
        $request->validate([
            'title' => '',
            'description' => '',
        ]);

        $actualite = Actualite::create([
            'title' => $request['title'],
            'description' => $request['description'],
        ]);

        if ($request->file('image')) {
            $actualite->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }


        Alert::toast('actualite inseré avec success', 'success');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Actualite  $actualite
     * @return \Illuminate\Http\Response
     */
    public function show(Actualite $actualite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Actualite  $actualite
     * @return \Illuminate\Http\Response
     */
    public function edit(Actualite $actualite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateActualiteRequest  $request
     * @param  \App\Models\Actualite  $actualite
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'title' => '',
            'description' => '',
        ]);

        $actualite = tap(Actualite::find($id))->update([
            'title' => $request->title,
            // 'description' => $request->description,
        ]);

        if ($request->file('image')) {
            $actualite->clearMediaCollection('image');
            $actualite->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }


        Alert::toast('actualite modifié avec success', 'success');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Actualite  $actualite
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $delete = Actualite::find($id)->delete();
        $delete = DB::table('media')->where('model_id', $id)->delete();
        Alert::toast('supprimé avec success', 'success');
        return back();
    }
}
