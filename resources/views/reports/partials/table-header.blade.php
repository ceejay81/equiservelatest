<!-- Reusable Table Header with Responsive Container -->
<div class="card">
    <div class="card-header{{ isset($headerBg) ? ' bg-' . $headerBg : '' }}">
        <h3 class="card-title">{{ $title }}</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    {{ $slot }}
                </tr>
            </thead>
            <tbody>
                {{ $body }}
            </tbody>
            @if(isset($footer))
                <tfoot>
                    {{ $footer }}
                </tfoot>
            @endif
        </table>
    </div>
    @if(isset($pagination) && $pagination->hasPages())
        <div class="card-footer clearfix">
            {{ $pagination->links() }}
        </div>
    @endif
</div>
