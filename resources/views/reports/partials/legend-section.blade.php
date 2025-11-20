<!-- Legend/Information Section -->
<div class="card">
    <div class="card-body">
        <h5>{{ $title }}</h5>
        @foreach($items as $item)
            <p class="{{ !$loop->last ? 'mb-1' : 'mb-0' }}">
                @if(isset($item['icon']))
                    <i class="{{ $item['icon'] }} {{ $item['colorClass'] ?? '' }}"></i>
                @endif
                <strong>{{ $item['label'] }}:</strong> {{ $item['description'] }}
            </p>
        @endforeach
    </div>
</div>
