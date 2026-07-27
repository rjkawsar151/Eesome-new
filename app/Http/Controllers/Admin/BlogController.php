<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Services\SafeHtml;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        return view('admin.blog.index', ['posts' => BlogPost::latest('id')->paginate(20)]);
    }

    public function create()
    {
        return view('admin.blog.form', ['post' => new BlogPost]);
    }

    public function store(Request $r)
    {
        $p = BlogPost::create($this->data($r));
        $this->image($r, $p);

        return redirect()->route('admin.blog.index')->with('success', 'Post created.');
    }

    public function edit(BlogPost $blog)
    {
        return view('admin.blog.form', ['post' => $blog]);
    }

    public function update(Request $r, BlogPost $blog)
    {
        $blog->update($this->data($r));
        $this->image($r, $blog);

        return back()->with('success', 'Post updated.');
    }

    public function destroy(BlogPost $blog)
    {
        $blog->delete();

        return back()->with('success', 'Post deleted.');
    }

    private function data(Request $r)
    {
        $data = $r->validate(['title' => 'required|string|max:255', 'name' => 'nullable|string|max:255', 'content' => 'required|string|max:100000', 'image_upload' => 'nullable|image|mimes:png,webp,jpg,jpeg|max:5120']);
        $data['name'] = $data['name'] ?: $data['title'];
        $data['content'] = app(SafeHtml::class)->sanitize($data['content']);
        unset($data['image_upload']);

        return $data;
    }

    private function image(Request $r, BlogPost $p)
    {
        if ($f = $r->file('image_upload')) {
            $p->update(['image' => $f->store('blog', 'public')]);
        }
    }
}
