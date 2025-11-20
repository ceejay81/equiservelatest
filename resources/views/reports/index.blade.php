@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reports.css') }}">
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $pageTitle }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Search and Filter Controls -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" id="reportSearch" class="form-control" placeholder="Search reports by name or keyword...">
                </div>
            </div>
            <div class="col-md-6">
                <div class="btn-group float-right" role="group">
                    <button type="button" class="btn btn-outline-secondary btn-sm active" data-filter="all">
                        <i class="fas fa-th"></i> All Reports
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-filter="sales">
                        <i class="fas fa-chart-line"></i> Sales
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-filter="inventory">
                        <i class="fas fa-boxes"></i> Inventory
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-filter="financial">
                        <i class="fas fa-dollar-sign"></i> Financial
                    </button>
                </div>
            </div>
        </div>

        @php
            $reportGroups = [
                'sales' => [
                    'title' => 'Sales & Revenue',
                    'icon' => 'fas fa-chart-line',
                    'reports' => [
                        [
                            'title' => 'Sales Reports',
                            'icon' => 'fas fa-chart-line',
                            'color' => 'primary',
                            'description' => 'View sales transactions, revenue analysis, and sales trends over time',
                            'route' => 'reports.sales',
                            'category' => 'sales',
                            'keywords' => 'sales revenue transactions trends analysis'
                        ],
                        [
                            'title' => 'Top Products',
                            'icon' => 'fas fa-trophy',
                            'color' => 'warning',
                            'description' => 'View best-selling products by quantity and revenue',
                            'route' => 'reports.top-products',
                            'category' => 'sales',
                            'keywords' => 'products best selling top revenue quantity'
                        ]
                    ]
                ],
                'financial' => [
                    'title' => 'Financial & Accounts Receivable',
                    'icon' => 'fas fa-dollar-sign',
                    'reports' => [
                        [
                            'title' => 'Collection Reports',
                            'icon' => 'fas fa-money-bill-wave',
                            'color' => 'success',
                            'description' => 'Track loan payments, outstanding balances, and aging analysis',
                            'route' => 'reports.collections',
                            'category' => 'financial',
                            'keywords' => 'collections payments loans balances aging receivable'
                        ],
                        [
                            'title' => 'Daily Reconciliation',
                            'icon' => 'fas fa-calculator',
                            'color' => 'info',
                            'description' => 'Verify daily cash and online collections',
                            'route' => 'reports.reconciliation',
                            'category' => 'financial',
                            'keywords' => 'reconciliation daily cash online verify balance'
                        ],
                        [
                            'title' => 'Customer Statements',
                            'icon' => 'fas fa-file-invoice',
                            'color' => 'secondary',
                            'description' => 'Generate detailed transaction statements for customers',
                            'route' => 'reports.customer-statement',
                            'category' => 'financial',
                            'keywords' => 'customer statements transactions invoice account'
                        ]
                    ]
                ],
                'inventory' => [
                    'title' => 'Inventory & Stock',
                    'icon' => 'fas fa-boxes',
                    'reports' => [
                        [
                            'title' => 'Inventory Reports',
                            'icon' => 'fas fa-boxes',
                            'color' => 'success',
                            'description' => 'Monitor stock levels, inventory value, and stock movements',
                            'route' => 'reports.inventory',
                            'category' => 'inventory',
                            'keywords' => 'inventory stock levels value warehouse'
                        ],
                        [
                            'title' => 'Stock Movements',
                            'icon' => 'fas fa-exchange-alt',
                            'color' => 'warning',
                            'description' => 'Track inventory changes and stock adjustments',
                            'route' => 'reports.stock-movements',
                            'category' => 'inventory',
                            'keywords' => 'stock movements changes adjustments transfers'
                        ]
                    ]
                ]
            ];
        @endphp

        @foreach($reportGroups as $groupKey => $group)
            <div class="report-group" data-group="{{ $groupKey }}">
                <div class="row mb-2">
                    <div class="col-12">
                        <h5 class="text-muted">
                            <i class="{{ $group['icon'] }}"></i> {{ $group['title'] }}
                        </h5>
                        <hr class="mt-1 mb-3">
                    </div>
                </div>

                <div class="row mb-4">
                    @foreach($group['reports'] as $card)
                        <div class="col-xl-3 col-lg-4 col-md-6 report-card" 
                             data-category="{{ $card['category'] }}"
                             data-keywords="{{ strtolower($card['title'] . ' ' . $card['keywords']) }}">
                            <div class="card card-{{ $card['color'] }} card-outline h-100">
                                <div class="card-body py-3">
                                    <div class="text-center mb-2">
                                        <i class="{{ $card['icon'] }} fa-2x text-{{ $card['color'] }}"></i>
                                    </div>
                                    <h6 class="card-title text-center mb-2 font-weight-bold">{{ $card['title'] }}</h6>
                                    <p class="text-muted text-center small mb-3" style="min-height: 40px;">{{ $card['description'] }}</p>
                                    <div class="text-center">
                                        <a href="{{ route($card['route']) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-arrow-right"></i> View Report
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- No Results Message -->
        <div id="noResults" class="row" style="display: none;">
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> No reports found matching your search criteria.
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('reportSearch');
    const filterButtons = document.querySelectorAll('[data-filter]');
    const reportCards = document.querySelectorAll('.report-card');
    const reportGroups = document.querySelectorAll('.report-group');
    const noResults = document.getElementById('noResults');
    
    let currentFilter = 'all';
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        filterReports();
    });
    
    // Filter buttons
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            filterReports();
        });
    });
    
    function filterReports() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;
        
        reportCards.forEach(card => {
            const category = card.dataset.category;
            const keywords = card.dataset.keywords;
            
            const matchesFilter = currentFilter === 'all' || category === currentFilter;
            const matchesSearch = searchTerm === '' || keywords.includes(searchTerm);
            
            if (matchesFilter && matchesSearch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide groups based on visible cards
        reportGroups.forEach(group => {
            const visibleCardsInGroup = group.querySelectorAll('.report-card:not([style*="display: none"])').length;
            group.style.display = visibleCardsInGroup > 0 ? '' : 'none';
        });
        
        // Show/hide no results message
        noResults.style.display = visibleCount === 0 ? '' : 'none';
    }
});
</script>
@endpush
@endsection
