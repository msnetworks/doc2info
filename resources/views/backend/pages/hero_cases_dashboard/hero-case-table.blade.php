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
        <tbody>
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
        <tfoot>
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