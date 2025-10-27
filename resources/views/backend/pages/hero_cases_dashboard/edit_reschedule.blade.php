<form id="rescheduleForm" method="POST" action="{{ route('hero-cases.reschedule.update', $heroCase->id) }}">
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