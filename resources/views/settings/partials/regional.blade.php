<div class="form-section-title">
  <i class="fas fa-globe mr-2"></i>Regional Settings
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label class="form-label">Timezone <span class="text-danger">*</span></label>
      <select name="timezone" id="regionalTimezone" class="form-control" required>
        <option value="">Select Timezone</option>
        @php
          $currentTimezone = old('timezone', $settings['timezone'] ?? 'Asia/Manila');
          $timezones = [
            'Asia/Manila' => 'Asia/Manila (PHT, UTC+8)',
            'Asia/Singapore' => 'Asia/Singapore (SGT, UTC+8)',
            'Asia/Tokyo' => 'Asia/Tokyo (JST, UTC+9)',
            'Asia/Hong_Kong' => 'Asia/Hong Kong (HKT, UTC+8)',
            'Asia/Shanghai' => 'Asia/Shanghai (CST, UTC+8)',
            'Asia/Bangkok' => 'Asia/Bangkok (ICT, UTC+7)',
            'Asia/Jakarta' => 'Asia/Jakarta (WIB, UTC+7)',
            'Asia/Kuala_Lumpur' => 'Asia/Kuala Lumpur (MYT, UTC+8)',
            'America/New_York' => 'America/New York (EST, UTC-5)',
            'America/Los_Angeles' => 'America/Los Angeles (PST, UTC-8)',
            'America/Chicago' => 'America/Chicago (CST, UTC-6)',
            'Europe/London' => 'Europe/London (GMT, UTC+0)',
            'Europe/Paris' => 'Europe/Paris (CET, UTC+1)',
            'Australia/Sydney' => 'Australia/Sydney (AEDT, UTC+11)',
            'UTC' => 'UTC (Coordinated Universal Time)',
          ];
        @endphp
        @foreach($timezones as $value => $label)
          <option value="{{ $value }}" {{ $currentTimezone == $value ? 'selected' : '' }}>
            {{ $label }}
          </option>
        @endforeach
      </select>
      <small class="form-text text-muted">All timestamps will be displayed in this timezone</small>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="form-group">
      <label class="form-label">Currency <span class="text-danger">*</span></label>
      <select name="currency" id="regionalCurrency" class="form-control" required>
        <option value="">Select Currency</option>
        @php
          $currentCurrency = old('currency', $settings['currency'] ?? 'PHP');
          $currencies = [
            'PHP' => 'PHP - Philippine Peso (₱)',
            'USD' => 'USD - US Dollar ($)',
            'EUR' => 'EUR - Euro (€)',
            'GBP' => 'GBP - British Pound (£)',
            'JPY' => 'JPY - Japanese Yen (¥)',
            'CNY' => 'CNY - Chinese Yuan (¥)',
            'SGD' => 'SGD - Singapore Dollar (S$)',
            'HKD' => 'HKD - Hong Kong Dollar (HK$)',
            'THB' => 'THB - Thai Baht (฿)',
            'MYR' => 'MYR - Malaysian Ringgit (RM)',
            'IDR' => 'IDR - Indonesian Rupiah (Rp)',
            'AUD' => 'AUD - Australian Dollar (A$)',
            'CAD' => 'CAD - Canadian Dollar (C$)',
          ];
        @endphp
        @foreach($currencies as $value => $label)
          <option value="{{ $value }}" {{ $currentCurrency == $value ? 'selected' : '' }}>
            {{ $label }}
          </option>
        @endforeach
      </select>
      <small class="form-text text-muted">Currency symbol and format for all monetary values</small>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="form-group mb-0">
      <label class="form-label">Date Format <span class="text-danger">*</span></label>
      <select name="date_format" id="regionalDateFormat" class="form-control" required>
        <option value="">Select Date Format</option>
        @php
          $currentDateFormat = old('date_format', $settings['date_format'] ?? 'M d, Y');
          $dateFormats = [
            'M d, Y' => 'Nov 17, 2025 (M d, Y)',
            'F d, Y' => 'November 17, 2025 (F d, Y)',
            'd/m/Y' => '17/11/2025 (d/m/Y)',
            'm/d/Y' => '11/17/2025 (m/d/Y)',
            'Y-m-d' => '2025-11-17 (Y-m-d)',
            'd-m-Y' => '17-11-2025 (d-m-Y)',
            'd.m.Y' => '17.11.2025 (d.m.Y)',
          ];
        @endphp
        @foreach($dateFormats as $value => $label)
          <option value="{{ $value }}" {{ $currentDateFormat == $value ? 'selected' : '' }}>
            {{ $label }}
          </option>
        @endforeach
      </select>
      <small class="form-text text-muted">How dates will be displayed throughout the system</small>
    </div>
  </div>
</div>
