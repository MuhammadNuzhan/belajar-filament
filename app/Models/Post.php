<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'thumbnail',
        'title',
        'color',
        'slug',
        'category_id',
        'author_id',
        'issue_id',
        'content',
        'tags',
        'published',
    ];
    protected $casts = [
        'tags' => 'array'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function author(){
        return $this->belongsTo(Author::class);
    }//Untuk Relation Manager
    public function authors(){
        return $this->belongsTo(Author::class);
    }

    public function issue(){
        return $this->belongsTo(Issue::class);
    }//Untuk Relation Manager
    public function issues(){
        return $this->belongsTo(Issue::class);
    }

    // public function authors(){
    //     return $this->belongsToMany(User::class, 'post_user')->withTimestamps();
    // }
}
