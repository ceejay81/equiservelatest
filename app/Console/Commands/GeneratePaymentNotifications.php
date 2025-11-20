<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Models\Notification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GeneratePaymentNotifications extends Command
{
    protected $signature = 'notifications:generate-payments';
    protected $description = 'Generate notifications for upcoming and overdue loan payments';

    public function handle()
    {
        $this->info('Generating payment notifications...');

        // Clear old notifications (optional - keep last 30 days)
        Notification::where('type', 'payment_due')
            ->where('created_at', '<', now()->subDays(30))
            ->delete();

        $generated = 0;

        // Get active loans
        $loans = Loan::with('sale.customer')
            ->whereIn('status', ['active', 'overdue'])
            ->whereNotNull('next_due_date')
            ->get();

        foreach ($loans as $loan) {
            $customer = $loan->sale->customer ?? null;
            if (!$customer) continue;

            $dueDate = $loan->next_due_date;
            $daysUntilDue = now()->diffInDays($dueDate, false);

            // Skip if already has notification for this loan and due date
            $exists = Notification::where('type', 'payment_due')
                ->where('related_type', 'App\Models\Loan')
                ->where('related_id', $loan->id)
                ->whereDate('created_at', today())
                ->exists();

            if ($exists) continue;

            $priority = 'medium';
            $title = '';

            if ($daysUntilDue < 0) {
                // Overdue
                $daysOverdue = abs($daysUntilDue);
                $priority = $daysOverdue > 7 ? 'critical' : 'high';
                $title = "{$customer->full_name} - Payment overdue ({$daysOverdue} days)";
            } elseif ($daysUntilDue == 0) {
                // Due today
                $priority = 'high';
                $title = "{$customer->full_name} - Payment due TODAY";
            } elseif ($daysUntilDue <= 3) {
                // Due in 1-3 days
                $priority = 'high';
                $title = "{$customer->full_name} - Payment due in {$daysUntilDue} days";
            } elseif ($daysUntilDue <= 7) {
                // Due in 4-7 days
                $priority = 'medium';
                $title = "{$customer->full_name} - Payment due in {$daysUntilDue} days";
            } else {
                // More than 7 days away - skip
                continue;
            }

            Notification::create([
                'type' => 'payment_due',
                'title' => $title,
                'message' => "Payment of ₱" . number_format($loan->monthly_amount, 2) . " due on " . $dueDate->format('M d, Y'),
                'related_type' => 'App\Models\Loan',
                'related_id' => $loan->id,
                'priority' => $priority,
                'data' => [
                    'customer_name' => $customer->full_name,
                    'customer_phone' => $customer->contact,
                    'amount' => $loan->monthly_amount,
                    'due_date' => $dueDate->format('Y-m-d'),
                    'sale_number' => $loan->sale->sale_number ?? null,
                    'days_until_due' => $daysUntilDue,
                ],
            ]);

            $generated++;
        }

        $this->info("Generated {$generated} payment notifications.");
        return 0;
    }
}
