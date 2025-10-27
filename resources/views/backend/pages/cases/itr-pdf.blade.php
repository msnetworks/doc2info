@php
$path = public_path('images/cd2isigned.jpg');
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$sign = 'data:image/' . $type . ';base64,' . base64_encode($data);

$logopath = public_path('images/cd2i.jpg');
$logotype = pathinfo($logopath, PATHINFO_EXTENSION);
$logodata = file_get_contents($logopath);
$logo = 'data:image/' . $logotype . ';base64,' . base64_encode($logodata);
@endphp

<table class="table table-bordered" style="border: 2px solid #000; width: 100%; table-layout: fixed;">
    <tbody>
        <!-- Header Row -->
        <tr style="border: 2px solid #000;">
            <td style="width: 30%; text-align: center; vertical-align: middle; border: 2px solid #000;">
                <img alt="CORE DOC2INFO SERVICES" src="{{ $logo }}" style="max-width: 100%; height: auto;">
            </td>
            <td style="width: 70%; text-align: center; vertical-align: middle; border: 2px solid #000;">
                <strong>
                    <h1 style="color: #ff0000; margin: 0;"><u><i>CORE DOC2INFO SERVICES</i></u></h1>
                </strong>
            </td>
        </tr>

        <!-- Case Details -->
        <tr style="border: 2px solid #000;">
            <td style="border: 2px solid #000; text-align: center;" colspan="2"><strong>{{ $case->getCase->getBank->name ?? null }} <br> {{ optional($case->getCase->getProduct)->name }} {{ $case->getFiType->name }}</strong></td>
        </tr>
        <tr style="border: 2px solid #000;">
            <td style="border: 2px solid #000;">Sr No.</td>
            <td style="border: 2px solid #000;">{{ $case->getCase->refrence_number ?? 'N/A' }}</td>
        </tr>
        <tr style="border: 2px solid #000;">
            <td style="border: 2px solid #000;">Branch</td>
            <td style="border: 2px solid #000;">
                {{ optional($case->getCase->getBranch)->branch_name . ' (' . optional($case->getCase->getBranch)->branch_code . ')' ?? 'N/A' }}
            </td>
        </tr>
        <tr style="border: 2px solid #000;">
            <td style="border: 2px solid #000;">Product Name</td>
            <td style="border: 2px solid #000;">{{ $case->getCase->getProduct->name ?? 'N/A' }}</td>
        </tr>
        <tr style="border: 2px solid #000;">
            <td style="border: 2px solid #000;">Applicant Name</td>
            <td style="border: 2px solid #000;">{{ $case->getCase->applicant_name ?? 'N/A' }}</td>
        </tr>
        <tr style="border: 2px solid #000;">
            <td style="border: 2px solid #000;">Pan Card</td>
            <td style="border: 2px solid #000;">{{ $case->pan_number ?? ($case->pan_card ?? 'N/A') }}</td>
        </tr>
        <tr style="border: 2px solid #000;">
            <td style="border: 2px solid #000;">Assesment  Year</td>
            <td style="border: 2px solid #000;">{{ $case->assessment_year ?? 'N/A' }}</td>
        </tr>
        <tr style="border: 2px solid #000;">
            <td style="border: 2px solid #000;">Date Reported</td>
            <td style="border: 2px solid #000;">{{ $case->getCase->created_at ?? 'N/A' }}</td>
        </tr>
        <tr style="border: 2px solid #000;">
            <td style="border: 2px solid #000;">Overall Status</td>
            <td style="border: 2px solid #000;">{{ $case->overall_status == 2 ? 'Positive' : ($case->overall_status == 3 ? 'Negative' : $case->getStatus->name) }}</td>
            {{-- <td style="border: 2px solid #000;">{{ $case->consolidated_remarks ?? ($case->getStatus->name ?? 'N/A') }}</td> --}}
        </tr>
        <tr style="border: 0!important;">
            <td colspan="2" style="border: 0px!important">
                <br>
            </td>
        </tr>
        <!-- Remarks Section -->
        <tr style="border: 2px solid #000;">
            <td style="width: 30%; border: 2px solid #000; text-align: left;">Assessment Year</td>
            <td style="width: 70%; border: 2px solid #000; text-align: left;">Remarks</td>
        </tr>
        @if(!empty($case->itr_form_remarks))
            @php
                $itrFormRemarks = json_decode($case->itr_form_remarks, true);
            @endphp
            @if(is_array($itrFormRemarks) && count($itrFormRemarks) > 0)
                @foreach($itrFormRemarks as $remark)
                    <tr style="border: 2px solid #000;">
                        <td style="border: 2px solid #000;">{{ $remark['financial_year'] ?? 'N/A' }}</td>
                        <td style="border: 2px solid #000;">{{ $remark['fi_remark'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            @else
                <tr style="border: 2px solid #000;">
                    <td colspan="2" style="text-align: center;">No remarks found.</td>
                </tr>
            @endif
            <!--{{-- <tr style="border: 2px solid #000;">-->
            <!--    <td style="border: 2px solid #000;">{{ __('Resolve Remarks') }}</td>-->
            <!--    <td style="border: 2px solid #000;">{{ $case->consolidated_remarks ?? 'N/A' }}</td>-->
            <!--</tr> --}}-->
            <tr style="border: 2px solid #000;">
                <td style="border: 2px solid #000;">{{ __('Verified Remarks') }}</td>
                <td style="border: 2px solid #000;">{{ $case->supervisor_remarks ?? 'N/A' }}</td>
            </tr>
        @else
            <tr style="border: 2px solid #000;">
                <td colspan="2" style="text-align: center;">No remarks available for this case.</td>
            </tr>
        @endif

        <tr style="border: 0!important;">
            <td colspan="2" style="border: 0px!important">
                <br>
            </td>
        </tr>
        <!-- Signature Section -->
        <tr style="border: 0px;">
            <td colspan="2" style="text-align: left; border: 0px;">
                <img src="{{ $sign }}" style="width: 150px; margin-top: 15px;">
            </td>
        </tr>
    </tbody>
</table>
