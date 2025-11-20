<!-- Reusable Filter Card Component -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> {{ $title ?? 'Filters' }}</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ $action }}">
            <div class="row">
                {{ $slot }}
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="{{ $resetUrl }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                    @if(isset($exportUrl))
                        <a href="{{ $exportUrl }}" class="btn btn-success float-right">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
