<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    /**
     * Relationships
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
    
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'performed_by');
    }
    
    public function actionedNotifications()
    {
        return $this->hasMany(Notification::class, 'actioned_by');
    }
    
    /**
     * Calculated Attributes
     */
    public function getTasksCompletedAttribute()
    {
        return $this->sales()->count();
    }
    
    public function getActiveProjectsAttribute()
    {
        return $this->sales()
            ->whereHas('loan', function($q) {
                $q->whereIn('status', ['active', 'overdue']);
            })
            ->count();
    }
    
    public function getEstimatedHoursAttribute()
    {
        // Calculate based on actual work
        $salesHours = $this->sales()->count() * 0.5; // 30 min per sale
        $stockHours = $this->stockMovements()->count() * 0.25; // 15 min per movement
        return round($salesHours + $stockHours);
    }
    
    public function getPerformanceScoreAttribute()
    {
        $score = 5.0; // Base score
        
        // Sales volume (0-2 points)
        $salesCount = $this->sales()->count();
        if ($salesCount > 100) $score += 2.0;
        elseif ($salesCount > 50) $score += 1.5;
        elseif ($salesCount > 20) $score += 1.0;
        elseif ($salesCount > 10) $score += 0.5;
        
        // Revenue generated (0-2 points)
        $totalRevenue = $this->sales()->sum('total_amount');
        if ($totalRevenue > 1000000) $score += 2.0;
        elseif ($totalRevenue > 500000) $score += 1.5;
        elseif ($totalRevenue > 200000) $score += 1.0;
        elseif ($totalRevenue > 50000) $score += 0.5;
        
        // Loan collection rate (0-1 point)
        $loanSales = $this->sales()->where('sale_type', 'loan')->count();
        if ($loanSales > 0) {
            $completedLoans = $this->sales()
                ->whereHas('loan', fn($q) => $q->where('status', 'completed'))
                ->count();
            $collectionRate = $completedLoans / $loanSales;
            $score += $collectionRate * 1.0;
        }
        
        return min(10.0, $score);
    }
    
    public function getTotalRevenueAttribute()
    {
        return $this->sales()->sum('total_amount');
    }
}

