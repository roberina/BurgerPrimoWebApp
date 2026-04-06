<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'content' => 'required|string|max:85',
            'rating'  => 'required|integer|min:1|max:5',
        ]);

        Review::create($validated); // status defaults to 'pending'

        return back()->with('review_success', true);
    }
}