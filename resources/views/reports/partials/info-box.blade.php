<!-- Info Box Component -->
<div class="col-md-{{ $cols ?? 6 }}">
    <div class="info-box bg-{{ $color ?? 'info' }}">
        <span class="info-box-icon"><i class="{{ $icon }}"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">{{ $title }}</span>
            <span class="info-box-number">{{ $value }}</span>
            @if(isset($description))
                <div class="progress">
                    <div class="progress-bar" style="width: 100%"></div>
                </div>
                <span class="progress-description">{{ $description }}</span>
            @endif
        </div>
    </div>
</div>
