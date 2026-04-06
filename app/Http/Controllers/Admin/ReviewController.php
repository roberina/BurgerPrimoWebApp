<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Reviews/Index', [
            'pending'  => Review::where('status', 'pending')->latest()->get(),
            'approved' => Review::where('status', 'approved')->latest()->get(),
            'rejected' => Review::where('status', 'rejected')->latest()->get(),
        ]);
    }

    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);
        return back()->with('success', 'Arvustus kinnitatud!');
    }

    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);
        return back()->with('success', 'Arvustus tagasi lükatud!');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Arvustus kustutatud!');
    }
}