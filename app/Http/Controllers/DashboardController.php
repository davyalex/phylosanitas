<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Auth::check()) {

        //nombre de post
        $post_count = Post::get()->count();
        //nombre de category
        $category_count = Category::get()->count();
        //nombre de user
        $user_count = User::get()->count();
        //5 derniers post
        $post_recent = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at','desc')->get()->take(10);


        //vue par pays
           //count by group

           $viewByCountry = DB::table('views')
           ->select('country','viewable_id', DB::raw('count(viewable_id) as vue ' ))
           // ->where('viewable_id',$post['id'])
           ->groupBy('country','viewable_id')
           ->get();

           $key =   $viewByCountry->keys();
           $data =   $viewByCountry->values();

        //    dd($data[0]->country);

           $count = [];

           for ($i=0; $i <count($viewByCountry) ; $i++) { 
                $country = $viewByCountry[$i]  ->country;
                $vue = $viewByCountry[$i]  ->vue;
                $viewable_id = $viewByCountry[$i]  ->viewable_id;
                array_push( $count,[  "country"=>$country,
                "vue"=>$vue,
                "post_vue"=> $viewable_id]);

           }
   
        //    $collection = new Collection( $viewByCountry);
        //    $viewByCountry =  $collection->map(function($row){
        //          return  $row;
        //    });

        // for ($i=0; $i <count($view) ; $i++) { 
        //     $viewByCountry = $view[$i]['country'];
        // }




       
           
           

            return view('admin.pages.index',compact(['post_count','category_count','user_count','post_recent','key','data']));
        } else {
           return redirect('login');
        }
        
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
