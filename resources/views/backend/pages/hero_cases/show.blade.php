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
                <h5 class="mb-0 h6">Hero Case Details: {{ $case->ref_no }}</h5>
                <div class="float-right">
                    <a href="{{ route('admin.hero_cases.edit', $case->id) }}" class="btn btn-sm btn-primary">
                        <i class="las la-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.hero_cases.index') }}" class="btn btn-sm btn-light">
                        <i class="las la-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Basic Information</h6>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th width="30%">Ref No.</th>
                                    <td>{{ $case->ref_no }}</td>
                                </tr>
                                <tr>
                                    <th>Product</th>
                                    <td>{{ $case->product }}</td>
                                </tr>
                                <tr>
                                    <th>Profiling</th>
                                    <td>{{ $case->profiling ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>FI/CPV Type</th>
                                    <td>{{ $case->fi_cpv_type ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>State</th>
                                    <td>{{ $case->state ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Customer Details</h6>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th width="30%">Customer Name</th>
                                    <td>{{ $case->customer_name }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $case->customer_address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile No.</th>
                                    <td>{{ $case->mobile_no }}</td>
                                </tr>
                                <tr>
                                    <th>Alt Mobile No.</th>
                                    <td>{{ $case->alt_mob_no ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email ID</th>
                                    <td>{{ $case->email_id ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6>Financial Information</h6>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th width="30%">Loan Amount</th>
                                    <td>{{ format_price($case->loan_amount) }}</td>
                                </tr>
                                <tr>
                                    <th>Ownership Type</th>
                                    <td>{{ $case->ownership_type ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Employer/Business</th>
                                    <td>{{ $case->employer_business_name ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Additional Information</h6>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th width="30%">Remarks</th>
                                    <td>{{ $case->remarks ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $case->created_at->format('d-m-Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $case->updated_at->format('d-m-Y H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($case->trashed())
                <div class="alert alert-warning mt-3">
                    <i class="las la-exclamation-triangle"></i> This case was deleted on {{ $case->deleted_at->format('d-m-Y H:i:s') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection