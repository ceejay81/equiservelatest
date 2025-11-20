@extends('layouts.app')

@php($pageTitle = 'Add Customer')

@section('content')
<a href="/customers" class="btn btn-link px-0 mb-2"><i class="fas fa-arrow-left mr-1"></i> Back to Customers</a>

<div class="card">
  <div class="card-header bg-white border-0"><h5 class="mb-0">New Customer</h5></div>
  <div class="card-body">
    @if ($errors->any())
      <div class="alert alert-danger"><strong>There were problems with your input.</strong><ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="/customers">
      @csrf
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="account_number">Account #</label>
          <input id="account_number" name="account_number" value="{{ old('account_number') }}" class="form-control" placeholder="e.g., ACCT-000123" required autofocus>
        </div>
        <div class="form-group col-md-6">
          <label for="full_name">Full Name</label>
          <input id="full_name" name="full_name" value="{{ old('full_name') }}" class="form-control" placeholder="e.g., Juan Dela Cruz" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="contact">Contact</label>
          <input id="contact" name="contact" value="{{ old('contact') }}" class="form-control" placeholder="e.g., 0917 123 4567">
        </div>
        <div class="form-group col-md-6">
          <label for="address">Address</label>
          <input id="address" name="address" value="{{ old('address') }}" class="form-control" placeholder="e.g., Brgy. 123, Quezon City">
        </div>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="/customers" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
