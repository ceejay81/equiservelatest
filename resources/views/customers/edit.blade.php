@extends('layouts.app')

@php($pageTitle = 'Edit Customer')

@section('content')
<a href="/customers/{{ $customer->id }}" class="btn btn-link px-0 mb-2"><i class="fas fa-arrow-left mr-1"></i> Back to Profile</a>

<div class="card">
  <div class="card-header bg-white border-0"><h5 class="mb-0">Edit Customer</h5></div>
  <div class="card-body">
    @if ($errors->any())
      <div class="alert alert-danger"><strong>There were problems with your input.</strong><ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="/customers/{{ $customer->id }}">
      @csrf
      @method('PUT')
      <div class="form-group">
        <label for="account_number">Account #</label>
        <input id="account_number" name="account_number" value="{{ old('account_number', $customer->account_number) }}" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="full_name">Full Name</label>
        <input id="full_name" name="full_name" value="{{ old('full_name', $customer->full_name) }}" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="contact">Contact</label>
        <input id="contact" name="contact" value="{{ old('contact', $customer->contact) }}" class="form-control">
      </div>
      <div class="form-group">
        <label for="address">Address</label>
        <input id="address" name="address" value="{{ old('address', $customer->address) }}" class="form-control">
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="/customers/{{ $customer->id }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
