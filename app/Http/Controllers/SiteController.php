<?php

namespace App\Http\Controllers;


use App\Models\Post;
use App\Models\Category;
use App\Models\Actualite;
use App\Models\Soumission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stevebauman\Location\Facades\Location;

class SiteController extends Controller
{
    public function index()
    {
        $category_actualite = Category::whereSlug('actualites')->first();

        $post = Post::with(['category:id,title,slug', 'media'])
            ->withCount('commentaires')
            ->select(['id', 'title', 'slug', 'description', 'category_id', 'created_at', 'published'])
            ->where('published', 'public')
            ->whereNotIn('category_id', [$category_actualite['id']])
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        $slide = Actualite::with('media')->orderBy('created_at', 'desc')->limit(10)->get();

        return view('site.pages.accueil', compact(['post', 'slide']));
    }


    public function post(Request $request)
    {
        try {
            $slug_req = request('category');
            $category_req = Category::whereSlug($slug_req)->first();

            $post = Post::with(['category:id,title,slug', 'media'])
                ->withCount('commentaires')
                ->select(['id', 'title', 'slug', 'description', 'category_id', 'created_at', 'published', 'lien'])
                ->when($slug_req, function ($q) use ($category_req) {
                    return $q->where('category_id', $category_req['id'])
                        ->where('published', 'public');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('site.pages.post', compact(['post', 'category_req']));
        } catch (\Throwable $th) {
            return redirect()->action([SiteController::class, 'index']);
        }
    }


    public function detail(Request $request, Post $post)
    {
        try {
            $slug_req = request('slug');

            $post = Post::with(['category:id,title,slug', 'commentaires', 'media', 'optionSondages'])
                ->whereSlug($slug_req)
                ->first();

            $statistic_sondage = Soumission::with(['post', 'optionSondage'])
                ->where('post_id', $post['id'])
                ->selectRaw('post_id,option_sondage_id,count(*) as choice')
                ->groupBy(['post_id', 'option_sondage_id'])
                ->get();

            $sondage_total = Soumission::where('post_id', $post['id'])->count();

            if (config('app.env') == 'production') {
                $ip = $request->getClientIp();
                $currentUserInfo = Location::get($ip);
                $country = $currentUserInfo->countryName;
                $city = $currentUserInfo->cityName;

                views($post)->record();
                DB::table('views')->where('viewable_id', $post['id'])->update([
                    'ip' => $ip,
                    'country' => $country,
                    'city' => $city,
                ]);
            } elseif (config('app.env') == 'local') {
                $ip = $request->getClientIp();
                $currentUserInfo = Location::get('8.8.1.1');
                $country = $currentUserInfo->countryName;
                $city = $currentUserInfo->cityName;

                views($post)->record();
                DB::table('views')->where('viewable_id', $post['id'])->update([
                    'ip' => $ip,
                    'country' => $country,
                    'city' => $city,
                ]);
            }

            return view('site.pages.detail', compact(['post', 'statistic_sondage', 'sondage_total']));
        } catch (\Throwable $th) {
            return redirect()->action([SiteController::class, 'index']);
        }
    }


    public function search(Request $request)
    {
        try {
            $search = $request['query'];

            $post = Post::with(['category:id,title,slug', 'media'])
                ->withCount('commentaires')
                ->select(['id', 'title', 'slug', 'description', 'category_id', 'created_at', 'published'])
                ->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                })
                ->where('published', 'public')
                ->orderBy('created_at', 'desc')
                ->limit(30)
                ->get();

            return view('site.pages.searchPost', compact('post'));
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }


    public function contact()
    {
        return view('site.pages.contact');
    }
}
