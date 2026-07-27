<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $r)
    {
        $q = Review::with(['product', 'user'])->latest();
        if ($r->filled('status')) {
            $q->where('status', $r->status);
        }

return view('admin.reviews.index', ['reviews' => $q->paginate(30)->withQueryString()]);
    }

    public function update(Request $r, Review $review)
    {
        $d = $r->validate(['status' => 'required|in:pending,approved,rejected,spam']);
        $review->update($d);

        return back()->with('success', 'Review status updated.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
