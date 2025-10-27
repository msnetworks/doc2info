<form id="appointmentForm" method="POST" action="{{ route('hero-cases.appointment.update', $heroCase->id) }}">
    @csrf
    @method('POST')
    <div class="form-group">
        <label for="appointment_status">Appointment Status</label>
        <select class="form-control" id="appointment_status" name="appointment_status" required>
            <option value="">Select Status</option>
            @if(isset($statuses))
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" {{ $heroCase->appointment_status == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                @endforeach
            @endif
        </select>
    </div>
    <div class="form-group">
        <label for="appointment_remarks">Remarks</label>
        <textarea class="form-control" id="appointment_remarks" name="appointment_remarks" rows="3" required></textarea>
    </div>
    <button type="submit" class="btn btn-warning">Update Appointment</button>
</form>