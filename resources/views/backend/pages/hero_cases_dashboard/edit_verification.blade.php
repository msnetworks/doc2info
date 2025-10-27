<form id="verificationForm" method="POST" enctype="multipart/form-data" action="{{ route('hero-cases.verification.update', $heroCase->id) }}">
    @csrf
    @method('POST')
    <div class="form-group">
        <label for="verification_status">Verification Status</label>
        <select class="form-control" id="verification_status" name="verification_status" required>
            <option value="">Select Status</option>
            @if(isset($statuses))
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" {{ $heroCase->verification_status == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                @endforeach
            @endif
        </select>
    </div>
    <div class="form-group">
        <label for="verification_remarks">Verification Remarks</label>
        <textarea class="form-control" id="verification_remarks" name="verification_remarks" rows="3"></textarea>
    </div>
    <div class="form-group">
        <label for="verification_pdf">Upload PDF (Optional)</label>
        <input type="file" class="form-control-file" id="verification_pdf" name="verification_pdf" accept="application/pdf">
        @if($heroCase->verification_pdf)
            <small class="form-text text-muted">Current PDF: <a href="{{ route('hero-cases.download.pdf', $heroCase->id) }}" target="_blank">View</a></small>
        @endif
    </div>
    <button type="submit" class="btn btn-primary">Update Verification</button>
</form>