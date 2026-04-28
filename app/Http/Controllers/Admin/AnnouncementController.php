<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Announcements/Index', [
            'announcements' => Announcement::ordered()->get(),
        ]);
    }

    public function store(Request $request, PushNotificationService $push)
    {
        $data = $this->validated($request);
        $announcement = Announcement::create($data);

        if ($announcement->is_active) {
            rescue(fn () => $push->sendToAll(
                $announcement->title,
                $announcement->message,
            ));
        }

        return back()->with('success', 'Teadaanne lisatud!');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $this->validated($request);
        $announcement->update($data);
        return back()->with('success', 'Teadaanne uuendatud!');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Teadaanne kustutatud!');
    }

    public function toggle(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);
        return back()->with('success', 'Olek uuendatud!');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'      => 'required|string|max:255',
            'message'    => 'required|string|max:1000',
            'type'       => 'required|in:info,warning,success,promo',
            'bg_color'   => 'required|string|max:20',
            'text_color' => 'required|string|max:20',
            'is_active'  => 'boolean',
            'starts_at'  => 'nullable|date',
            'ends_at'    => 'nullable|date',
            'sort_order' => 'integer|min:0',
        ]);
    }
}