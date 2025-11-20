<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'related_type',
        'related_id',
        'priority',
        'is_read',
        'is_actioned',
        'actioned_by',
        'actioned_at',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_actioned' => 'boolean',
        'actioned_at' => 'datetime',
        'data' => 'array',
    ];

    public function actionedBy()
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }

    public function related()
    {
        return $this->morphTo();
    }

    // Scope for unread notifications
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // Scope for unactioned notifications
    public function scopeUnactioned($query)
    {
        return $query->where('is_actioned', false);
    }

    // Scope for urgent notifications
    public function scopeUrgent($query)
    {
        return $query->whereIn('priority', ['high', 'critical']);
    }

    // Mark as read
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    // Mark as actioned
    public function markAsActioned($userId = null)
    {
        $this->update([
            'is_actioned' => true,
            'actioned_by' => $userId ?? auth()->id(),
            'actioned_at' => now(),
        ]);
    }
}
