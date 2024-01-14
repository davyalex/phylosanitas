<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\OptionSondage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use RealRashid\SweetAlert\Facades\Alert;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $request = request('type');
        $category = Category::whereTitle('sondage')->first();
        
        $sondage = Post::with(['category', 'commentaires', 'media', 'user'])
        ->when($request=='sondage',
        fn($q)=>$q->with('optionSondages')->where('category_id',$category['id'])
        )
        ->orderBy('created_at', 'desc')->get();

        $post = $post = Post::with(['category', 'commentaires', 'media', 'user','views'])
        ->where('category_id','!=',$category['id'])
        ->orderBy('created_at', 'desc')->get();
        
        // $actualite = Actualite::with('media')->orderBy('created_at', 'desc')->get();

        
// dd($post->toArray());

        return view('admin.pages.post.index', compact('post','sondage'));
    }


    public function published(Request $request)
    {

        $status = request('status');
        $post = request('post');

        if ($status & $post) {
            $published = Post::whereId($post)->update(['published' => $status]);
            $post = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')->get();
            Alert::Success('Status modifié avec success');

            return back();
        }

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
        $request = request('type');
        $category = Category::with('posts')
         ->when($request =='sondage',
         fn($q)=>$q->whereTitle('sondage')
         )
        ->get();
        return view('admin.pages.post.add', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //insertion des sondages
        if ($request['sondage']=='sondage') {
            $generate = Str::random(5);
            $request->validate([
                'description' => 'required',
                'category' => 'required',
                'option.*.title' => 'required',
            ]);
    
            $post = Post::firstOrCreate([
                'slug' =>'sondage'.$generate,
                'description' => $request['description'],
                'category_id' => $request['category'],
                'published' => 'prive',
                // 'user_id' => Auth::user()->id,
            ]);
            
            if ($request->file('image')) {
                $post->addMediaFromRequest('image')
                    ->toMediaCollection('image');
            }

            foreach ($request['option'] as $key => $value) {
               $option = OptionSondage::create([
                'post_id' => $post['id'],
                'title' => $value['title'],
               ]);
            }
    
    
            Alert::toast('Sondage inséré avec success', 'success');
            return back();



            //insertion des posts

        } else {
           

            $request->validate([
                'title' => 'required',
                'description' => '',
                'category' => 'required',
                'lien' => '',
            ]);
    
            $post = Post::firstOrCreate([
                'title' => $request['title'],
                'description' => $request['description'],
                'category_id' => $request['category'],
                'lien' => $request['lien'],
                'published' => 'prive',
                'user_id' => Auth::user()->id,
            ]);
    
            if ($request->file('image')) {
                $post->addMediaFromRequest('image')
                    ->toMediaCollection('image');
            }
    
    
            Alert::toast('post inseré avec success', 'success');
            return back();
        }
        

      
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post, $slug)
    {
        //
        $category = Category::with('posts')->get();
        $post = Post::with(['category', 'commentaires', 'media', 'user'])
            ->whereSlug($slug)
            ->first();
        return view('admin.pages.post.edit', compact(['post', 'category']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'title' => 'required',
            'description' => '',
            'category' => 'required',
            'lien' => '',
        ]);

        $post = tap(Post::find($id))->update([
            'title' => $request['title'],
            'description' => $request['description'],
            'category_id' => $request['category'],
            'lien' => $request['lien'],
            'user_id' => Auth::user()->id,

        ]);

        if ($request->hasFile('image')) {

            $post->clearMediaCollection('image');
            $post->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }
        $post = Post::with(['category', 'commentaires', 'media', 'user'])->orderBy('created_at', 'desc')->get();

        Alert::toast('post modifié avec success', 'success');
        return redirect()->route('post');
        // return view('admin.pages.post.index', compact('post'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $delete = Post::find($id)->delete();
        $delete = DB::table('media')->where('model_id', $id)->delete();
        Alert::toast('supprimé avec success', 'success');
        return back();
    }
}
