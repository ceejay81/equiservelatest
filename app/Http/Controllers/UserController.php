<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($role = $request->string('role')->toString()) {
            $query->where('role', $role);
        }

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function profile(Request $request): View
    {
        $user = $request->user();
        
        // Load relationships for performance
        $user->load([
            'sales' => fn($q) => $q->latest()->take(10),
            'sales.customer',
            'sales.loan',
            'stockMovements' => fn($q) => $q->latest()->take(10),
            'stockMovements.product',
            'actionedNotifications' => fn($q) => $q->latest('actioned_at')->take(10),
        ]);
        
        // Calculate real statistics
        $stats = [
            'tasks_completed' => $user->tasks_completed,
            'active_projects' => $user->active_projects,
            'hours_logged' => $user->estimated_hours,
            'performance_score' => $user->performance_score,
            'total_revenue' => $user->total_revenue,
        ];
        
        // Get recent activities
        $activities = $this->getRecentActivities($user);
        
        return view('users.profile', compact('user', 'stats', 'activities'));
    }
    
    private function getRecentActivities($user)
    {
        $activities = collect();
        
        // Recent sales
        foreach ($user->sales as $sale) {
            $activities->push([
                'type' => 'sale',
                'icon' => 'fa-shopping-cart',
                'color' => 'success',
                'title' => 'Processed Sale',
                'description' => "Sale {$sale->sale_number} for {$sale->customer->full_name} - ₱" . number_format($sale->total_amount, 2),
                'time' => $sale->created_at,
            ]);
        }
        
        // Recent stock movements
        foreach ($user->stockMovements as $movement) {
            $activities->push([
                'type' => 'stock',
                'icon' => 'fa-boxes',
                'color' => 'warning',
                'title' => ucfirst($movement->type) . ' Stock',
                'description' => "{$movement->product->name} (" . ($movement->quantity_change > 0 ? '+' : '') . "{$movement->quantity_change} units)",
                'time' => $movement->created_at,
            ]);
        }
        
        // Recent notifications actioned
        foreach ($user->actionedNotifications as $notification) {
            $activities->push([
                'type' => 'notification',
                'icon' => 'fa-bell',
                'color' => 'info',
                'title' => 'Handled Notification',
                'description' => $notification->title,
                'time' => $notification->actioned_at ?? $notification->created_at,
            ]);
        }
        
        return $activities->sortByDesc('time')->take(10)->values();
    }
}
