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
                <h5 class="mb-0 h6">Hero Cases List</h5>
            </div>
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>Ref No.</th>
                            <th>Customer Name</th>
                            <th>Product</th>
                            <th>Mobile No.</th>
                            <th>Loan Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cases as $case)
                        <tr>
                            <td>{{ $case->ref_no }}</td>
                            <td>{{ $case->customer_name }}</td>
                            <td>{{ $case->product }}</td>
                            <td>{{ $case->mobile_no }}</td>
                            <td>{{ format_price($case->loan_amount) }}</td>
                            <td>
                                <a href="{{ route('admin.hero_cases.edit', $case->id) }}" title="Edit">
                                    <i class="las la-edit"></i>
                                </a>
                                <a href="{{ route('admin.hero_cases.show', $case->id) }}" title="View">
                                    <i class="las la-eye"></i>
                                </a>
                                <form action="{{ route('admin.hero_cases.destroy', $case->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #f64e60; cursor: pointer;">
                                        <i class="las la-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection