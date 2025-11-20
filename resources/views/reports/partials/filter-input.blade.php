<!-- Reusable Filter Input Field -->
<div class="col-md-{{ $cols ?? 3 }}">
    <div class="form-group">
        <label>{{ $label }}</label>
        @if($type === 'date')
            <input type="date" name="{{ $name }}" class="form-control" value="{{ $value }}">
        @elseif($type === 'select')
            <select name="{{ $name }}" class="form-control">
                @if(isset($placeholder))
                    <option value="">{{ $placeholder }}</option>
                @endif
                @foreach($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" {{ $value == $optionValue ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
        @endif
    </div>
</div>
