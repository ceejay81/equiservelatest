<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('actionedBy')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = Notification::unread()->count();
        $urgentCount = Notification::urgent()->unactioned()->count();

        return view('notifications.index', compact('notifications', 'unreadCount', 'urgentCount'));
    }

    public function getUnread()
    {
        $notifications = Notification::unread()
            ->unactioned()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count(),
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAsActioned($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsActioned();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification marked as contacted');
    }

    public function markAllAsRead()
    {
        Notification::unread()->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read');
    }
}
