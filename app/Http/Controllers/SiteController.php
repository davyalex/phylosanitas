<?php

namespace App\Http\Controllers;


use App\Models\Post;
use  InteractsWithViews;
use App\Models\Category;
use App\Models\Actualite;
use App\Models\Soumission;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Stevebauman\Location\Facades\Location;
use CyrildeWit\EloquentViewable\Contracts\Visitor;
use Illuminate\Database\Eloquent\Relations\Relation;

// use App\Services\Views\Visitor;

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
        // $post_recent = Post::with(['category', 'commentaires', 'media', 'user'])
        //     ->where('published', 'public')
        //     ->orderBy('created_at', 'desc')->first();

        // recent post  /**get from appservice provider */
        // $post_last = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')
        //     ->where('published', 'public')
        //     ->get()->take(4);

        //liste de quelque article pour la page accueil sans categorie actualite
        $category_actualite = Category::whereSlug('actualites')->first();

        $post = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')
            ->where('published', 'public')
            ->whereNotIn('category_id',[$category_actualite['id']])
            ->get()->take(12);

            
        // $category = Category::with('posts')->get()->sortBy('title');

        //actualite sous forme de slider //publicite
        $actualite = Actualite::with('media')->orderBy('created_at', 'desc')->get();


        return view('site.pages.accueil', compact(['post','actualite']));
    }


    public function post(Request $request)
    {


        //liste des categories
        $category = Category::with('posts')->get()->sortBy('title');

        //liste recent post
        $post_last = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')
            ->where('published', 'public')
            ->get()->take(4);


        /******* */

        $slug_req = request('category');

        // category si request
        $category_req = Category::whereSlug($slug_req)->first();

        $post = Post::with(['category', 'commentaires', 'media', 'user'])
            ->when($slug_req, function ($q) use ($category_req) {
                return $q->where('category_id', $category_req['id'])
                    ->where('published', 'public');
            })->orderBy('created_at', 'desc')->paginate(5);



        return view('site.pages.post', compact(['post_last', 'post', 'category', 'category_req']));
    }


    public function detail(Request $request, Post $post)
    {


        // config('app.env');


        //liste des categories
        $category = Category::with('posts')->get()->sortBy('title');

        //liste recent post
        $post_last = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')
            ->where('published', 'public')
            ->get()->take(4);


        /******* */

        $slug_req = request('slug');


        $post = Post::with(['category', 'commentaires', 'media', 'user', 'optionSondages'])
            ->whereSlug($slug_req)->first();


        // statistics des sondages
        $statistic_sondage = Soumission::with(['post', 'optionSondage'])
            ->where('post_id', $post['id'])
            ->selectRaw('post_id,option_sondage_id,count(*) as choice')
            ->groupBy([
                'post_id',
                'option_sondage_id'
            ])->get();

        //total votant
        $sondage_total = Soumission::get()
            ->where('post_id', $post['id'])
            ->count();


        // dd($statistic_sondage->toArray());


        // verifier si le serveur est en production ou developpement

        if (config('app.env') == 'production') {
            // dd($post ->toArray());
            $ip = $request->getClientIp();

            $currentUserInfo = Location::get($ip);
            $country =  $currentUserInfo->countryName;
            $city =  $currentUserInfo->cityName;


            views($post)->record();
            DB::table('views')->where('viewable_id', $post['id'])->update([
                'ip' => $ip,
                'country' => $country,
                'city' => $city,
            ]);
            // $post->visitsCounter()->increment();


        } elseif (config('app.env') == 'local') {
            $ip = $request->getClientIp();

            $currentUserInfo = Location::get('8.8.1.1');
            $country =  $currentUserInfo->countryName;
            $city =  $currentUserInfo->cityName;


            views($post)->record();
            DB::table('views')->where('viewable_id', $post['id'])->update([
                'ip' => $ip,
                'country' => $country,
                'city' => $city,
            ]);
            // $post->visitsCounter()->increment();
        }


        return view('site.pages.detail', compact(['post_last', 'post', 'category', 'statistic_sondage', 'sondage_total']));
    }


    public function search(Request $request)
    {
        try {
            $search = $request['query'];
            $post = Post::where('title', 'Like', "%{$search}%")
                ->Orwhere('description', 'Like', "%{$search}%")
                ->where('published', 'public')
                ->orderBy('created_at', 'desc')->get();

            return view('site.pages.searchPost', compact('post'));
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }


    public function contact()
    {
        $this->category;
        return view('site.pages.contact');
    }
}
