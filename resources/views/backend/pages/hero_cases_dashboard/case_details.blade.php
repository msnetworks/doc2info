@extends('backend.layouts.master')

@section('title')
Hero Cases - State: {{ $state }}, Status: {{ $status }} - Admin Panel
@endsection

@section('admin-content')
 @php
 $usr = Auth::guard('admin')->user();
 @endphp
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">Hero Cases - State: {{ $state }}, Status: {{ $status }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="padding-bottom: 150px;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ref No</th>
                                <th>Product</th>
                                <th>Employer Business Name</th>
                                <th>FI CPV Type</th>
                                <th>State</th>
                                <th>Customer Name</th>
                                <th>Mobile No</th>
                                <th>Email ID</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cases as $case)
                                <tr>
                                    <td>{{ $case->id }}</td>
                                    <td>{{ $case->ref_no }}</td>
                                    <td>{{ $case->product }}</td>
                                    <td>{{ $case->employer_business_name }}</td>
                                    <td>{{ $case->fi_cpv_type }}</td>
                                    <td>{{ $case->states->state }}</td>
                                    <td>{{ $case->customer_name }}</td>
                                    <td>{{ $case->mobile_no }}</td>
                                    <td>{{ $case->email_id }}</td>
                                    <td>
                                        @if ($case->status == '1') Inprogress
                                        @elseif ($case->status == '2') Positive Resolved
                                        @elseif ($case->status == '3') Negative Resolved
                                        @elseif ($case->status == '6') Appointment Not Done
                                        @elseif ($case->status == '1156') Appointment Rescheduled
                                        @else {{ $case->status }}
                                        @endif
                                    </td>
                                    <td>{{ $case->created_at }}</td>
                                    <td>
                                        <div class="dropdown" style="position: relative;">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="caseActions{{ $case->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-cog"></i> Actions
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="caseActions{{ $case->id }}" style="position: relative;">
                                                @if ($usr->can('hero.verification') && $case->status == '1')
                                                    <li><a class="dropdown-item" href="#" onclick="openVerificationModal({{ $case->id }})"><i class="fas fa-check-circle"></i> Verification</a></li>
                                                    @if($usr->can('hero.appointment'))
                                                        <li><a class="dropdown-item" href="#" onclick="openAppointmentModal({{ $case->id }})"><i class="fas fa-calendar-alt"></i> Appointment</a></li>
                                                    @endif
                                                @elseif ($case->status == '2' || $case->status == '3')
                                                    @if ($case->verification_pdf)
                                                        <li><a class="dropdown-item" href="{{ route('hero-cases.download.pdf', $case->id) }}" target="_blank"><i class="fas fa-download"></i> Download PDF</a></li>
                                                    @endif
                                                @elseif ($usr->can('hero.reschedule') && $case->status == '6')
                                                    <li><a class="dropdown-item" href="#" onclick="openRescheduleModal({{ $case->id }})"><i class="fas fa-calendar-plus"></i> Reschedule Appointment</a></li>
                                                @elseif ($case->status == '1156')
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="{{ route('hero-cases.show', $case->id) }}"><i class="fas fa-eye"></i> View Details</a></li>
                                            </ul>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12">No cases found for this state and status.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $cases->links() }}

                <div class="modal fade" id="verificationModal" tabindex="-1" role="dialog" aria-labelledby="verificationModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="verificationModalLabel">Edit Verification</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="verificationForm" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('POST')
                                    <div class="form-group">
                                        <label for="verification_status">Verification Status</label>
                                        <select class="form-control" id="verification_status" name="verification_status" required>
                                            <option value="">Select Status</option>
                                            @if(isset($statuses))
                                                @foreach($statuses as $status)
                                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="verification_pdf">Upload PDF (Optional)</label>
                                        <input type="file" class="form-control-file" id="verification_pdf" name="verification_pdf" accept="application/pdf">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Verification</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="appointmentModalLabel">Edit Appointment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="appointmentForm" method="POST">
                                    @csrf
                                    @method('POST')
                                    <div class="form-group">
                                        <label for="appointment_status">Appointment Status</label>
                                        <select class="form-control" id="appointment_status" name="appointment_status" required>
                                            <option value="">Select Status</option>
                                            @if(isset($statuses))
                                                @foreach($statuses as $status)
                                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-warning">Update Appointment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="rescheduleModal" tabindex="-1" role="dialog" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="rescheduleModalLabel">Reschedule Appointment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="rescheduleForm" method="POST">
                                    @csrf
                                    @method('POST')
                                    <div class="form-group">
                                        <label for="reschedule_date">Reschedule Date and Time</label>
                                        <input type="datetime-local" class="form-control" id="reschedule_date" name="reschedule_date" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="reschedule_remarks">Remarks</label>
                                        <textarea class="form-control" id="reschedule_remarks" name="reschedule_remarks" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-warning">Update Reschedule</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Optional: Prevent dropdown from closing on item click if needed
    // const dropdownItems = document.querySelectorAll('.dropdown-item');
    // dropdownItems.forEach(item => {
    //     item.addEventListener('click', function(event) {
    //         event.stopPropagation(); // Prevent dropdown from closing
    //     });
    // });
</script>
<script>
    function openVerificationModal(caseId) {
        var url = "{{ route('hero-cases.verification.edit', ':id') }}".replace(':id', caseId);
        $('#verificationForm').attr('action', "{{ url('/') }}/" + caseId + "/verification"); // Set the form action dynamically
        // Fetch the verification edit form content via AJAX and populate the modal
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                $('#verificationModal .modal-body').html(data);
                $('#verificationModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Error fetching verification form:", error);
                alert("Error loading verification form.");
            }
        });
    }

    function openAppointmentModal(caseId) {
        var url = "{{ route('hero-cases.appointment.edit', ':id') }}".replace(':id', caseId);
        $('#appointmentForm').attr('action', "{{ url('/') }}/" + caseId + "/appointment"); // Set the form action dynamically
        // Fetch the appointment edit form content via AJAX and populate the modal
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                $('#appointmentModal .modal-body').html(data);
                $('#appointmentModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Error fetching appointment form:", error);
                alert("Error loading appointment form.");
            }
        });
    }
    
    function openRescheduleModal(caseId) {
        var url = "{{ route('hero-cases.reschedule.edit', ':id') }}".replace(':id', caseId);
        $('#rescheduleForm').attr('action', "{{ url('/') }}/" + caseId + "/reschedule"); // Set the form action dynamically
        // Fetch the appointment edit form content via AJAX and populate the modal
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data) {
                $('#rescheduleModal .modal-body').html(data);
                $('#rescheduleModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Error fetching Reschedule form:", error);
                alert("Error loading Reschedule form.");
            }
        });
    }
    // Handle form submission within the verification modal via AJAX
    $('#verificationModal').on('submit', '#verificationForm', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var formData = new FormData(this);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
                $('#verificationModal').modal('hide');
                // Optionally, update the table row or display a success message
                location.reload(); // Simple way to refresh the page
            },
            error: function(xhr, status, error) {
                console.error("Error updating verification:", error);
                alert("Error updating verification.");
                // Optionally, display error messages in the modal
            }
        });
    });

    // Handle form submission within the appointment modal via AJAX
    $('#appointmentModal').on('submit', '#appointmentForm', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action'); // This should be correctly set in the partial
        var formData = form.serialize();
    
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#appointmentModal').modal('hide');
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error("Error updating appointment:", error);
                alert("Error updating appointment.");
            }
        });
    });
    
      // Handle form submission within the Reschedule modal via AJAX
    $('#rescheduleModal').on('submit', '#rescheduleForm', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action'); // This should be correctly set in the partial
        var formData = form.serialize();

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#rescheduleModal').modal('hide');
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error("Error updating Reschedule:", error);
                alert("Error updating Reschedule.");
            }
        });
    });
</script>
@endsection