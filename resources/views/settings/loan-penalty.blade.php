@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $pageTitle }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Settings</a></li>
                    <li class="breadcrumb-item active">Loan & Penalty</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-percent"></i> Penalty Configuration</h3>
                    </div>
                    <form action="{{ route('settings.loan-penalty.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="card-body">
                            <div class="form-group">
                                <label for="grace_period_days">Grace Period (Days)</label>
                                <input type="number" 
                                       class="form-control @error('grace_period_days') is-invalid @enderror" 
                                       id="grace_period_days" 
                                       name="grace_period_days" 
                                       value="{{ old('grace_period_days', $settings['grace_period_days'] ?? 3) }}"
                                       min="0" 
                                       max="30" 
                                       required>
                                <small class="form-text text-muted">
                                    Number of days after due date before penalty is applied
                                </small>
                                @error('grace_period_days')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="late_penalty_rate">Late Payment Penalty (%)</label>
                                <input type="number" 
                                       class="form-control @error('late_penalty_rate') is-invalid @enderror" 
                                       id="late_penalty_rate" 
                                       name="late_penalty_rate" 
                                       value="{{ old('late_penalty_rate', $settings['late_penalty_rate'] ?? 3.00) }}"
                                       min="0" 
                                       max="100" 
                                       step="0.01" 
                                       required>
                                <small class="form-text text-muted">
                                    Percentage of installment amount charged for late payments
                                    <br>Example: 3% of ₱750 = ₱22.50
                                </small>
                                @error('late_penalty_rate')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="maturity_penalty_rate">Maturity Penalty (%)</label>
                                <input type="number" 
                                       class="form-control @error('maturity_penalty_rate') is-invalid @enderror" 
                                       id="maturity_penalty_rate" 
                                       name="maturity_penalty_rate" 
                                       value="{{ old('maturity_penalty_rate', $settings['maturity_penalty_rate'] ?? 5.00) }}"
                                       min="0" 
                                       max="100" 
                                       step="0.01" 
                                       required>
                                <small class="form-text text-muted">
                                    Percentage of remaining balance charged if loan unpaid by maturity date
                                    <br>Example: 5% of ₱2,250 = ₱112.50
                                </small>
                                @error('maturity_penalty_rate')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="rebate_on_time_only" 
                                           name="rebate_on_time_only"
                                           value="1"
                                           {{ old('rebate_on_time_only', $settings['rebate_on_time_only'] ?? 1) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="rebate_on_time_only">
                                        Rebates only apply to on-time payments
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    When enabled, customers can only use rebates if they pay within the grace period
                                </small>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="{{ route('settings.index') }}" class="btn btn-default">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Important Notes</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Changes apply to NEW loans only</strong></p>
                        <p class="text-sm">Existing loans will keep their original penalty rates to protect customers from retroactive changes.</p>
                        
                        <hr>
                        
                        <p><strong>How Penalties Work:</strong></p>
                        <ul class="text-sm">
                            <li><strong>Grace Period:</strong> No penalty if paid within this period</li>
                            <li><strong>Late Payment:</strong> Charged once per late payment</li>
                            <li><strong>Maturity:</strong> Charged if loan unpaid by final due date</li>
                        </ul>
                        
                        <hr>
                        
                        <p><strong>Rebate Rules:</strong></p>
                        <ul class="text-sm">
                            <li>Rebates reduce the payment amount</li>
                            <li>If "on-time only" is enabled, late payments forfeit rebates</li>
                            <li>Forfeited rebates remain available for future on-time payments</li>
                        </ul>
                    </div>
                </div>

                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calculator"></i> Example Calculation</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-sm"><strong>Scenario:</strong> Monthly payment of ₱750</p>
                        
                        <table class="table table-sm">
                            <tr>
                                <td>On-time payment:</td>
                                <td class="text-right"><strong>₱750.00</strong></td>
                            </tr>
                            <tr>
                                <td>Late payment (3%):</td>
                                <td class="text-right text-danger"><strong>₱772.50</strong></td>
                            </tr>
                            <tr>
                                <td colspan="2"><small class="text-muted">₱750 + ₱22.50 penalty</small></td>
                            </tr>
                        </table>
                        
                        <hr>
                        
                        <p class="text-sm"><strong>Maturity Penalty:</strong></p>
                        <p class="text-sm">If ₱2,250 remains unpaid by maturity date:</p>
                        <p class="text-danger"><strong>₱2,250 × 5% = ₱112.50</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
