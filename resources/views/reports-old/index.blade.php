@extends('layouts.app')

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
        <div class="row">
            <!-- Sales Reports -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-chart-line fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title text-center mb-3">Sales Reports</h5>
                        <p class="text-muted text-center small">
                            View sales transactions, revenue analysis, and sales trends over time
                        </p>
                        <div class="text-center">
                            <a href="{{ route('reports.sales') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-arrow-right"></i> View Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Reports -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-success card-outline">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-boxes fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title text-center mb-3">Inventory Reports</h5>
                        <p class="text-muted text-center small">
                            Monitor stock levels, inventory value, and stock movements
                        </p>
                        <div class="text-center">
                            <a href="{{ route('reports.inventory') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-arrow-right"></i> View Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Collection Reports -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-warning card-outline">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-money-bill-wave fa-3x text-warning"></i>
                        </div>
                        <h5 class="card-title text-center mb-3">Collection Reports</h5>
                        <p class="text-muted text-center small">
                            Track loan payments, outstanding balances, and aging analysis
                        </p>
                        <div class="text-center">
                            <a href="{{ route('reports.collections') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-arrow-right"></i> View Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Statements -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-info card-outline">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-file-invoice fa-3x text-info"></i>
                        </div>
                        <h5 class="card-title text-center mb-3">Customer Statements</h5>
                        <p class="text-muted text-center small">
                            Generate detailed transaction statements for customers
                        </p>
                        <div class="text-center">
                            <a href="{{ route('reports.customer-statement') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-arrow-right"></i> View Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Reconciliation -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-secondary card-outline">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-calculator fa-3x text-secondary"></i>
                        </div>
                        <h5 class="card-title text-center mb-3">Daily Reconciliation</h5>
                        <p class="text-muted text-center small">
                            Verify daily cash and online collections
                        </p>
                        <div class="text-center">
                            <a href="{{ route('reports.reconciliation') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-right"></i> View Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-trophy fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title text-center mb-3">Top Products</h5>
                        <p class="text-muted text-center small">
                            View best-selling products by quantity and revenue
                        </p>
                        <div class="text-center">
                            <a href="{{ route('reports.top-products') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-arrow-right"></i> View Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Movements -->
            <div class="col-lg-4 col-md-6">
                <div class="card card-warning card-outline">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-exchange-alt fa-3x text-warning"></i>
                        </div>
                        <h5 class="card-title text-center mb-3">Stock Movements</h5>
                        <p class="text-muted text-center small">
                            Track inventory changes and stock adjustments
                        </p>
                        <div class="text-center">
                            <a href="{{ route('reports.stock-movements') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-arrow-right"></i> View Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
