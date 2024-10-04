<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    public function index(User $user)
    {
        $follows = (auth()->user()) ? auth()->user()->following->contains($user->id) : 'false';
        $postsCount = Cache::remember(
            'count.posts.'. $user->id, 
            now()->addSeconds(30), 
            function() use ($user){
            return $user->posts->count();
        });
        $followersCount = Cache::remember(
            'count.followers.'. $user->id, 
            now()->addSeconds(30), 
            function() use ($user){
            $user->profile->followers->count();
        });
        $followingCount = Cache::remember(
            'count.following.'. $user->id, 
            now()->addSeconds(30), 
            function() use ($user){
            $user->following->count();
        });
        return view('profiles.index', compact('user','follows','postsCount','followersCount','followingCount'));
    }
    public function edit(User $user)
    {
        $this->authorize('update', $user->profile);
        return view('profiles.edit', compact('user'));
    }
    public function update(User $user)
    {
        $this->authorize('update', $user->profile);

        $data = request()->validate([
            'title' => 'required',
            'description' => 'required',
            'url' => 'url',
            'image' => '',
        ]);

        

        if (request('image')) {
            $imagePath = request('image')->store('profile', 'public');


            $manager = new ImageManager(new Driver());


            $image = $manager->read("storage/{$imagePath}");


            $image->resize(800, 800);

            $image->save();
            $imageArray = ['image' => $imagePath];

        }
        // dd($data);
        auth()->user()->profile->update(array_merge(
            $data,
            $imageArray ?? [],
        ));
        return redirect("/profile/{$user->id}");
    }
}
