@extends('backend.layouts.master')

@section('title')
Create Case - Admin Panel
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
                <h5 class="mb-0 h6">Create New Hero Case</h5>
            </div>
            <div class="card-body">
                <form id="createHeroCaseForm" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">Ref No. <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="ref_no" placeholder="Case Reference Number" required>
                            <div class="error ref_no-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Product <span class="text-danger">*</span></label>
                            <select class="form-control" name="product" required>
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->product_id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            <div class="error product_id-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">Profiling</label>
                            <input type="text" class="form-control" name="profiling" placeholder="Profiling">
                            <div class="error profiling-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Employer/Business Name</label>
                            <input type="text" class="form-control" name="employer_business_name" placeholder="Employer or Business Name">
                            <div class="error employer_business_name-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">FI/CPV Type</label>
                            @foreach ($fitypes as $key => $fitype)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="radio{{ $key }}" name="fi_cpv_type" value="{{ $fitype['id'] }}" rel-name="{{ $fitype['fi_code'] }}">
                                    <label class="form-check-label" for="radio{{ $key }}">
                                        {{ $fitype['name'] }}
                                    </label>
                                </div>
                            @endforeach
                            <div class="error fi_cpv_type-error"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="col-form-label">State <span class="text-danger">*</span></label>
                            <select class="form-control" name="state" required>
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->state }}</option>
                                @endforeach
                            </select>
                            <div class="error state-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="customer_name" placeholder="Customer Full Name" required>
                            <div class="error customer_name-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Customer Address</label>
                            <textarea class="form-control" name="customer_address" rows="1" placeholder="Customer Address"></textarea>
                            <div class="error customer_address-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">Mobile No. <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="mobile_no" placeholder="Primary Mobile Number" required>
                            <div class="error mobile_no-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Alt Mobile No.</label>
                            <input type="text" class="form-control" name="alt_mob_no" placeholder="Alternative Mobile Number">
                            <div class="error alt_mob_no-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">Email ID</label>
                            <input type="email" class="form-control" name="email_id" placeholder="Email Address">
                            <div class="error email_id-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Loan Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="loan_amount" placeholder="Loan Amount" required>
                            <div class="error loan_amount-error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="col-form-label">Ownership Type</label>
                            <select class="form-control" name="ownership_type">
                                <option value="">Select Ownership Type</option>
                                <option value="Individual">Individual</option>
                                <option value="Partnership">Partnership</option>
                                <option value="Company">Company</option>
                                <option value="Other">Other</option>
                            </select>
                            <div class="error ownership_type-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">Remarks</label>
                            <textarea class="form-control" name="remarks" rows="1" placeholder="Any additional remarks"></textarea>
                            <div class="error remarks-error"></div>
                        </div>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <button type="button" class="btn btn-primary" id="saveBtn">Save</button>
                        <a href="{{ route('admin.hero_cases.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#saveBtn').click(function(e) {
            e.preventDefault();
            $('.error').empty(); // Clear previous errors

            $.ajax({
                url: "{{ route('admin.hero_cases.store') }}",
                type: "POST",
                data: $('#createHeroCaseForm').serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: true, // Ensure the confirm button is shown
                        }).then((result) => {
                            /* Read more about isConfirmed, isDenied below */
                            if (result.isConfirmed) {
                                window.location.reload(); // Reload the page on OK
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong while saving.',
                        });
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            $('.' + key.replace('.', '_') + '-error').html(value[0]);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An unexpected error occurred.',
                        });
                    }
                }
            });
        });
    });
</script>
@endsection