@extends('backend.layouts.master')

@section('title')
Create Case Create - Admin Panel
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .form-check-label {
        text-transform: capitalize;
    }
    .error {
        color: #dc3545;
        font-size: 0.875em;
    }
    .is-invalid {
        border-color: #dc3545;
    }
</style>
@endsection

@section('admin-content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">Edit Hero Case: {{ $case->ref_no }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.hero_cases.update', $case->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">Ref No. <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="ref_no" value="{{ old('ref_no', $case->ref_no) }}" placeholder="Case Reference Number" required>
                            @error('ref_no')
                                <div class="alert alert-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Product <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="product" value="{{ old('product', $case->product) }}" placeholder="Product Name" required>
                            @error('product')
                                <div class="alert alert-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">Profiling</label>
                            <input type="text" class="form-control" name="profiling" value="{{ old('profiling', $case->profiling) }}" placeholder="Profiling">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Employer/Business Name</label>
                            <input type="text" class="form-control" name="employer_business_name" value="{{ old('employer_business_name', $case->employer_business_name) }}" placeholder="Employer or Business Name">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">FI/CPV Type</label>
                            <input type="text" class="form-control" name="fi_cpv_type" value="{{ old('fi_cpv_type', $case->fi_cpv_type) }}" placeholder="FI/CPV Type">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">State</label>
                            <input type="text" class="form-control" name="state" value="{{ old('state', $case->state) }}" placeholder="State">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="customer_name" value="{{ old('customer_name', $case->customer_name) }}" placeholder="Customer Full Name" required>
                            @error('customer_name')
                                <div class="alert alert-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Customer Address</label>
                            <textarea class="form-control" name="customer_address" rows="1" placeholder="Customer Address">{{ old('customer_address', $case->customer_address) }}</textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">Mobile No. <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="mobile_no" value="{{ old('mobile_no', $case->mobile_no) }}" placeholder="Primary Mobile Number" required>
                            @error('mobile_no')
                                <div class="alert alert-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Alt Mobile No.</label>
                            <input type="text" class="form-control" name="alt_mob_no" value="{{ old('alt_mob_no', $case->alt_mob_no) }}" placeholder="Alternative Mobile Number">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">Email ID</label>
                            <input type="email" class="form-control" name="email_id" value="{{ old('email_id', $case->email_id) }}" placeholder="Email Address">
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Loan Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="loan_amount" value="{{ old('loan_amount', $case->loan_amount) }}" placeholder="Loan Amount" required>
                            @error('loan_amount')
                                <div class="alert alert-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">Ownership Type</label>
                            <select class="form-control" name="ownership_type">
                                <option value="">Select Ownership Type</option>
                                <option value="Individual" {{ old('ownership_type', $case->ownership_type) == 'Individual' ? 'selected' : '' }}>Individual</option>
                                <option value="Partnership" {{ old('ownership_type', $case->ownership_type) == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                <option value="Company" {{ old('ownership_type', $case->ownership_type) == 'Company' ? 'selected' : '' }}>Company</option>
                                <option value="Other" {{ old('ownership_type', $case->ownership_type) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Remarks</label>
                            <textarea class="form-control" name="remarks" rows="1" placeholder="Any additional remarks">{{ old('remarks', $case->remarks) }}</textarea>
                        </div>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.hero_cases.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection