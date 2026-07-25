<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;

    protected $table = 'blog_posts';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'title',
        'content',
        'image',
    ];
}
