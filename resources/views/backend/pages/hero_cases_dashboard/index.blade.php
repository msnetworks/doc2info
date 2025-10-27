@extends('backend.layouts.master')

@section('title')
Hero Cases Dashboard - Admin Panel
@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('styles')
<style>
    .clickable {
        cursor: pointer;
        text-decoration: underline;
        color: blue;
    }
    .filter-section {
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 5px;
    }
</style>
@endsection

@section('admin-content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">Hero Cases Dashboard</h5>
            </div>
            <div class="card-body">
                <div class="filter-section">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <select class="form-control" id="state" name="state">
                                        <option value="">All States</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->id }}">{{ $state->state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">From Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">To Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top: 30px;">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <button type="button" id="resetFilter" class="btn btn-secondary">Reset</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>State</th>
                                <th>Total Count</th>
                                <th>Inprogress</th>
                                <th>Positive Resolved</th>
                                <th>Negative Resolved</th>
                                <th>Appointment Not Done</th>
                                <th>Appointment Reshedule</th>
                            </tr>
                        </thead>
                        <tbody id="dataTableBody">
                            @foreach ($stateWiseData as $data)
                                <tr>
                                    <td>{{ $data['group_by'] }}</td>
                                    <td>{{ $data['total_count'] }}</td>
                                    <td class="clickable" onclick="showCaseDetails('{{ $data['group_by_id'] }}', '1')">{{ $data['inprogress'] }}</td>
                                    <td class="clickable" onclick="showCaseDetails('{{ $data['group_by_id'] }}', '2')">{{ $data['positive_resolved'] }}</td>
                                    <td class="clickable" onclick="showCaseDetails('{{ $data['group_by_id'] }}', '3')">{{ $data['negative_resolved'] }}</td>
                                    <td class="clickable" onclick="showCaseDetails('{{ $data['group_by_id'] }}', '6')">{{ $data['appointment_not_done'] }}</td>
                                    <td class="clickable" onclick="showCaseDetails('{{ $data['group_by_id'] }}', '1156')">{{ $data['appointment_rescheduled'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot id="dataTableFooter">
                            <tr>
                                <th>Overall</th>
                                <th>{{ $overallData['total_count'] ?? 0 }}</th>
                                <th>{{ $overallData['inprogress'] ?? 0 }}</th>
                                <th>{{ $overallData['positive_resolved'] ?? 0 }}</th>
                                <th>{{ $overallData['negative_resolved'] ?? 0 }}</th>
                                <th>{{ $overallData['appointment_not_done'] ?? 0 }}</th>
                                <th>{{ $overallData['appointment_rescheduled'] ?? 0 }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showCaseDetails(state, status) {
        // Get filter values
        const stateFilter = document.getElementById('state').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        // Build URL with filters
        let url = '/admin/hero-cases/details?state=' + encodeURIComponent(state) + '&status=' + status;
        
        if (stateFilter) {
            url += '&filter_state=' + encodeURIComponent(stateFilter);
        }
        if (startDate) {
            url += '&start_date=' + encodeURIComponent(startDate);
        }
        if (endDate) {
            url += '&end_date=' + encodeURIComponent(endDate);
        }
        
        window.location.href = url;
    }

    $(document).ready(function() {
        // Handle form submission
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            filterData();
        });
    
        // Handle reset button
        $('#resetFilter').on('click', function() {
            $('#state').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            filterData();
        });
    
        function filterData() {
            const formData = $('#filterForm').serialize();
            
            $.ajax({
                url: "{{ route('admin.hero-cases.filter-data') }}",
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Update table body
                    let tableBodyHtml = '';
                    response.stateWiseData.forEach(data => {
                        tableBodyHtml += `
                            <tr>
                                <td>${data.group_by}</td>
                                <td>${data.total_count}</td>
                                <td class="clickable" onclick="showCaseDetails('${data.group_by_id}', '1')">${data.inprogress}</td>
                                <td class="clickable" onclick="showCaseDetails('${data.group_by_id}', '2')">${data.positive_resolved}</td>
                                <td class="clickable" onclick="showCaseDetails('${data.group_by_id}', '3')">${data.negative_resolved}</td>
                                <td class="clickable" onclick="showCaseDetails('${data.group_by_id}', '6')">${data.appointment_not_done}</td>
                                <td class="clickable" onclick="showCaseDetails('${data.group_by_id}', '1156')">${data.appointment_rescheduled}</td>
                            </tr>
                        `;
                    });
                    $('#dataTableBody').html(tableBodyHtml);
    
                    // Update footer
                    const overall = response.overallData;
                    $('#dataTableFooter').html(`
                        <tr>
                            <th>Overall</th>
                            <th>${overall.total_count || 0}</th>
                            <th>${overall.inprogress || 0}</th>
                            <th>${overall.positive_resolved || 0}</th>
                            <th>${overall.negative_resolved || 0}</th>
                            <th>${overall.appointment_not_done || 0}</th>
                            <th>${overall.appointment_rescheduled || 0}</th>
                        </tr>
                    `);
                },
                error: function(xhr) {
                    alert('Error filtering data. Please try again.');
                }
            });
        }
    });
</script>
@endsection