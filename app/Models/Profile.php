<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function profileImage(){
        $imagePath =  ($this->image) ? $this->image : 'profile/8DyNG9m6T5C9sLweJwSQ2NuFBXRSwbxsTiwXEKjz.png';
        return '/storage/'. $imagePath;
    }

    public function followers(){
        return $this->belongsToMany(User::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
