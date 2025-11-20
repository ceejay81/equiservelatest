<?php

namespace App\Helpers;

class ReportHelper
{
    /**
     * Get status badge styling based on movement type
     */
    public static function getMovementTypeBadge($type)
    {
        $badges = [
            'sale' => [
                'class' => 'danger',
                'icon' => 'fas fa-arrow-down',
                'text' => 'Sale'
            ],
            'receive' => [
                'class' => 'success',
                'icon' => 'fas fa-arrow-up',
                'text' => 'Receive'
            ],
            'adjustment' => [
                'class' => 'warning',
                'icon' => 'fas fa-exchange-alt',
                'text' => 'Adjustment'
            ]
        ];

        return $badges[$type] ?? ['class' => 'secondary', 'icon' => 'fas fa-question', 'text' => ucfirst($type)];
    }

    /**
     * Get stock status styling
     */
    public static function getStockStatus($stock, $lowThreshold = 10, $criticalThreshold = 5)
    {
        if ($stock < 0) {
            return [
                'class' => 'danger',
                'icon' => 'fas fa-minus-circle',
                'text' => 'Negative',
                'textClass' => 'text-danger'
            ];
        } elseif ($stock == 0) {
            return [
                'class' => 'secondary',
                'icon' => 'fas fa-ban',
                'text' => 'Out of Stock',
                'textClass' => 'text-secondary'
            ];
        } elseif ($stock <= $criticalThreshold) {
            return [
                'class' => 'danger',
                'icon' => 'fas fa-exclamation-circle',
                'text' => 'Critical',
                'textClass' => 'text-danger'
            ];
        } elseif ($stock <= $lowThreshold) {
            return [
                'class' => 'warning',
                'icon' => 'fas fa-exclamation-triangle',
                'text' => 'Low Stock',
                'textClass' => 'text-warning'
            ];
        }

        return [
            'class' => 'success',
            'icon' => 'fas fa-check-circle',
            'text' => 'In Stock',
            'textClass' => 'text-success'
        ];
    }

    /**
     * Get sale type badge styling
     */
    public static function getSaleTypeBadge($type)
    {
        return [
            'cash' => 'success',
            'loan' => 'primary'
        ][$type] ?? 'secondary';
    }

    /**
     * Get payment mode badge styling
     */
    public static function getPaymentModeBadge($mode)
    {
        return [
            'cash' => 'warning',
            'online' => 'info'
        ][$mode] ?? 'secondary';
    }

    /**
     * Get loan status badge styling
     */
    public static function getLoanStatusBadge($status)
    {
        return [
            'active' => 'primary',
            'overdue' => 'danger',
            'completed' => 'secondary'
        ][$status] ?? 'secondary';
    }

    /**
     * Format currency for display
     */
    public static function formatCurrency($amount)
    {
        return '₱' . number_format($amount, 2);
    }

    /**
     * Get ranking icon for top products
     */
    public static function getRankingIcon($index)
    {
        if ($index == 0) {
            return '<i class="fas fa-trophy text-warning"></i>';
        } elseif ($index == 1) {
            return '<i class="fas fa-medal text-secondary"></i>';
        } elseif ($index == 2) {
            return '<i class="fas fa-medal text-bronze"></i>';
        }

        return $index + 1;
    }

    /**
     * Get aging analysis category
     */
    public static function getAgingCategory($daysOverdue)
    {
        if ($daysOverdue <= 0) {
            return 'current';
        } elseif ($daysOverdue <= 30) {
            return 'overdue_1_30';
        } elseif ($daysOverdue <= 60) {
            return 'overdue_31_60';
        }

        return 'overdue_60_plus';
    }

    /**
     * Build transaction collection from sales and payments
     */
    public static function buildTransactionHistory($sales, $payments)
    {
        $transactions = collect();

        foreach ($sales as $sale) {
            $transactions->push([
                'date' => $sale->created_at,
                'description' => 'Sale ' . $sale->sale_number . ' (' . ucfirst($sale->sale_type) . ')',
                'debit' => $sale->total_amount,
                'credit' => 0,
            ]);

            if ($sale->sale_type === 'loan' && $sale->loan) {
                $transactions->push([
                    'date' => $sale->created_at,
                    'description' => 'Down Payment - ' . $sale->sale_number,
                    'debit' => 0,
                    'credit' => $sale->loan->down_payment,
                ]);
            } elseif ($sale->sale_type === 'cash') {
                $transactions->push([
                    'date' => $sale->created_at,
                    'description' => 'Payment - ' . $sale->sale_number,
                    'debit' => 0,
                    'credit' => $sale->total_amount,
                ]);
            }
        }

        foreach ($payments as $payment) {
            $transactions->push([
                'date' => $payment->payment_date,
                'description' => 'Loan Payment - ' . ($payment->loan->sale->sale_number ?? 'N/A'),
                'debit' => 0,
                'credit' => $payment->amount_paid,
            ]);
        }

        return $transactions->sortBy('date');
    }
}
