@extends('backend.layouts.master')

@section('title')
View Hero Case: {{ $heroCase->ref_no }} - Admin Panel
@endsection

@section('admin-content')
<div class="row">
    <div class="col-lg-10 col-md-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6"><strong>Hero Case Details: {{ $heroCase->ref_no }}</strong></h5>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered" id="detailsTable">
                    <tbody>
                        <tr>
                            <th>ID</th>
                            <td>{{ $heroCase->id }}</td>
                        </tr>
                        <tr>
                            <th>Ref No</th>
                            <td>{{ $heroCase->ref_no }}</td>
                        </tr>
                        @if($heroCase->product)
                        <tr>
                            <th>Product</th>
                            <td>{{ $heroCase->products->name }}</td>
                        </tr>
                        @endif
                        @if($heroCase->profiling)
                        <tr>
                            <th>Profiling</th>
                            <td>{{ $heroCase->profiling }}</td>
                        </tr>
                        @endif
                        @if($heroCase->employer_business_name)
                        <tr>
                            <th>Employer Business Name</th>
                            <td>{{ $heroCase->employer_business_name }}</td>
                        </tr>
                        @endif
                        @if($heroCase->fi_cpv_type)
                        <tr>
                            <th>FI CPV Type</th>
                            <td>{{ $heroCase->fitypes->name }}</td>
                        </tr>
                        @endif
                        @if($heroCase->state)
                        <tr>
                            <th>State</th>
                            <td>{{ $heroCase->states->state ?? '' }}</td>
                        </tr>
                        @endif
                        @if($heroCase->customer_name)
                        <tr>
                            <th>Customer Name</th>
                            <td>{{ $heroCase->customer_name }}</td>
                        </tr>
                        @endif
                        @if($heroCase->customer_address)
                        <tr>
                            <th>Customer Address</th>
                            <td>{{ $heroCase->customer_address }}</td>
                        </tr>
                        @endif
                        @if($heroCase->mobile_no)
                        <tr>
                            <th>Mobile No</th>
                            <td>{{ $heroCase->mobile_no }}</td>
                        </tr>
                        @endif
                        @if($heroCase->alt_mob_no)
                        <tr>
                            <th>Alternate Mobile No</th>
                            <td>{{ $heroCase->alt_mob_no }}</td>
                        </tr>
                        @endif
                        @if($heroCase->email_id)
                        <tr>
                            <th>Email ID</th>
                            <td>{{ $heroCase->email_id }}</td>
                        </tr>
                        @endif
                        @if($heroCase->loan_amount)
                        <tr>
                            <th>Loan Amount</th>
                            <td>{{ $heroCase->loan_amount }}</td>
                        </tr>
                        @endif
                        @if($heroCase->ownership_type)
                        <tr>
                            <th>Ownership Type</th>
                            <td>{{ $heroCase->ownership_type }}</td>
                        </tr>
                        @endif
                        @if($heroCase->remarks)
                        <tr>
                            <th>Remarks</th>
                            <td>{{ $heroCase->remarks }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Status</th>
                            <td>
                                @if ($heroCase->status == '1') <span class="badge bg-info text-white">In Progress</span>
                                @elseif ($heroCase->status == '2') <span class="badge bg-success text-white">Positive Resolved</span>
                                @elseif ($heroCase->status == '3') <span class="badge bg-danger text-white">Negative Resolved</span>
                                @elseif ($heroCase->status == '6') <span class="badge bg-warning text-dark">Appointment Not Done</span>
                                @elseif ($heroCase->status == '1156') <span class="badge bg-primary text-white">Appointment Rescheduled</span>
                                @else <span class="badge bg-secondary text-white">{{ $heroCase->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @if($heroCase->verification_status)
                        <tr>
                            <th>Verification Status</th>
                            <td>{{ $heroCase->verificationStatus->name ?? 'N/A' }}</td>
                        </tr>
                        @endif
                        @if($heroCase->verified_by)
                        <tr>
                            <th>Verified By</th>
                            <td>{{ $heroCase->verified_by }}</td>
                        </tr>
                        @endif
                        @if($heroCase->verified_on)
                        <tr>
                            <th>Verified On</th>
                            <td>{{ \Carbon\Carbon::parse($heroCase->verified_on)->format('d-m-Y') }}</td>
                        </tr>
                        @endif
                        @if($heroCase->verification_remarks)
                        <tr>
                            <th>Verification Remarks</th>
                            <td>{{ $heroCase->verification_remarks }}</td>
                        </tr>
                        @endif
                        @if($heroCase->appointment_status)
                        <tr>
                            <th>Appointment Status</th>
                            <td>{{ $heroCase->appointmentStatus->name ?? 'N/A' }}</td>
                        </tr>
                        @endif
                        @if($heroCase->appointment_by)
                        <tr>
                            <th>Appointment By</th>
                            <td>{{ $heroCase->appointment_by }}</td>
                        </tr>
                        @endif
                        @if($heroCase->appointment_on)
                        <tr>
                            <th>Appointment On</th>
                            <td>{{ \Carbon\Carbon::parse($heroCase->appointment_on)->format('d-m-Y') }}</td>
                        </tr>
                        @endif
                        @if($heroCase->appointment_remarks)
                        <tr>
                            <th>Appointment Remarks</th>
                            <td>{{ $heroCase->appointment_remarks }}</td>
                        </tr>
                        @endif
                        @if($heroCase->reschedule_on)
                        <tr>
                            <th>Reschedule On</th>
                            <td>{{ \Carbon\Carbon::parse($heroCase->reschedule_on)->format('d-m-Y H:i:s') }}</td>
                        </tr>
                        @endif
                        @if($heroCase->reschedule_remarks)
                        <tr>
                            <th>Reschedule Remarks</th>
                            <td>{{ $heroCase->reschedule_remarks }}</td>
                        </tr>
                        @endif
                        @if($heroCase->verification_pdf)
                        <tr>
                            <th>Verification PDF</th>
                            <td>
                                <a href="{{ route('hero-cases.download.pdf', $heroCase->id) }}" target="_blank">Download PDF</a>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th>Created At</th>
                            <td>{{ \Carbon\Carbon::parse($heroCase->created_at)->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ \Carbon\Carbon::parse($heroCase->updated_at)->format('d-m-Y') }}</td>
                        </tr>
                        @if($heroCase->deleted_at)
                        <tr>
                            <th>Deleted At</th>
                            <td>{{ \Carbon\Carbon::parse($heroCase->deleted_at)->format('d-m-Y') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <div class="mt-3">
                    <a href="{{ back() }}" class="btn btn-secondary">Back to List</a>
                    <button class="btn btn-primary" onclick="printDetails()">Print</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printDetails() {
        var printContents = document.getElementById('detailsTable').outerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
</script>
@endsection