<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;
    protected $guarded = [];

    // public function category(){
    //     return $this->belongsTo(Category::class);
    // }
    // public function authors(){
    //     return $this->belongsTo(User::class);
    // }
    // public function posts(){
    //     return $this->hasMany(Post::class);
    // }
}
