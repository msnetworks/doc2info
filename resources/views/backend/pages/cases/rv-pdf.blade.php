@php
$path = public_path('images/sign.png');
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$sign = 'data:image/' . $type . ';base64,' . base64_encode($data);
$logopath = public_path('images/logo.jpg');
$logotype = pathinfo($logopath, PATHINFO_EXTENSION);
$logodata = file_get_contents($logopath);
$logo = 'data:image/' . $logotype . ';base64,' . base64_encode($logodata);
@endphp
<style>
    @import url('https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');

    .img-container {
        width: 80px;
        height: 80px;
        margin-bottom: 5px;
        margin-left: 5px;
        border: 2px solid #b06c1c;
        border-radius: 10px;
    }

    .bg-info {
        background-color: #17a2b8 !important;
    }

    .text-white {
        color: #fff !important;
    }

    .head-text {
        font-weight: 600;
        color: #0094ff
    }
</style>
<div class="w-100 col-xs-12 col-sm-12 col-md-12">
    <table class="table table-bordered" border="2">
        <tbody>
            <tr>
                <td style="width: 50%;!important" align="center" colspan="2">
                    <img style="width: 50%;" alt="CORE DOC2INFO SERVICES" src="{{ $logo }}">
                </td>
                <td align="center" style="width: 50%;!important" colspan="2">
                    <h2 style="color: #ff0000; margin-bottom: 0;"><u>
                            <i>CORE DOC2INFO SERVICES</i></u></h2>
                </td>
            </tr>
            <tr>
                <th colspan="4" class="text-center">
                    @php
                    $fiType = $case->getFiType->name ?? null;
                    $bank = $case->getCase->getBank->name ?? null;
                    $product = $case->getCase->getProduct->name ?? null;

                    $columnValue = null;

                    if($bank){
                    $columnValue = $bank;
                    }

                    if($product){
                    if($columnValue){
                    $columnValue .= ' ';
                    }
                    $columnValue .= $product;
                    }

                    if($fiType){
                    if($columnValue){
                    $columnValue .= ' ';
                    }
                    $columnValue .= $fiType == 'BV' ? 'BUSINESS VERIFICATION' : ($fiType == 'RV' ? 'RESIDENCE
                    VERIFICATION' : $fiType);
                    }
                    @endphp
                    {{ $columnValue }}
                    </td>
            </tr>
            <tr>
                <td style="width: 25%!important;" class="head-text">Branch Code</td>
                <td style="width: 25%!important;" class="BVstyle ng-binding">{{ $case->getCase->getBranch->branch_code
                    ?? '' }}</td>
                <td style="width: 25%!important;" class="head-text">Reference No.</td>
                <td style="width: 25%!important;" class="BVstyle ng-binding">{{ $case->getCase->refrence_number ?? '' }}
                </td>
            </tr>
            <tr>
                <td style="width: 25%!important;" class="head-text">Customer Name</td>
                <td style="width: 25%!important;" class="BVstyle ng-binding">{{ $case->getCase->applicant_name ?? '' }}
                </td>
                <td style="width: 25%!important;" class="head-text">Fi Type</td>
                <td>{{ $case->getFiType->name }}</td>
            </tr>
            <tr>
                <td class="head-text">Case Creation Login Details</td>
                <td class="BVstyle ng-binding">{{ $case->getCase->created_at ?? 'NA' }}</td>
                <td class="head-text">Bank</td>
                <td class="BVstyle ng-binding">{{ $case->getCase->getBank->name ?? 'NA' }}</td>
            </tr>
            <tr>
                <td class="head-text">Product Name</td>
                <td class="BVstyle ng-binding">{{ $case->getCase->getProduct->name ?? 'NA' }}</td>
                <td class="head-text">Loan Amount</td>
                <td class="BVstyle ng-binding">{{ $case->getCase->amount ?? 'NA' }}</td>
            </tr>
            <tr>
                <td class="head-text">Contact No.</td>
                <td colspan="3" class="BVstyle ng-binding">{{ $case->mobile ?? '' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Loan Amount</td>
                <td>{{ $case->getCase->amount ?? 'NA' }}</td>
                <td style="font-weight: 600; color:#0094ff">Dealer Code</td>
                <td>{{ $case->dealer_code ?? 'NA' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Landline</td>
                <td>{{ $case->landline ?? '' }}</td>
                <td style="font-weight: 600; color:#0094ff">Address</td>
                <td>{{ $case->address ?? '' }}</td>
            </tr>
            <tr class="bg-info text-white">
                <td colspan="4" class="subheading" style="text-align: center"><strong>Residence
                        Verification Format</strong>
                </td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Address Confirmed </td>
                <td>{{ $case->address_confirmed ?? '' }} &nbsp; </td>
                <td style="font-weight: 600; color:#0094ff">Address Confirmed By</td>
                <td class="BVstyle ng-binding ng-hide">{{ $case->address_confirmed_by ?? '' }}</td>
            </tr>
            <tr class="bg-info text-white">
                <td colspan="4" class="subheading" style="text-align: center"><strong>The
                        following information should be obtained if the applicant/colleagues are
                        contacted in the office </strong></td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Applicant Name</td>
                <td>
                    {{ $case->applicant_name ?? $case->getCase->applicant_name }}</td>
                <td style="font-weight: 600; color:#0094ff">Permanent Address/Phone</td>
                <td>{{ $case->permanent_address ?? 'NA' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Person Met</td>
                <td>{{ $case->person_met ?? 'NA' }} </td>
                <td style="font-weight: 600; color:#0094ff">Relationship</td>
                <td>{{ $case->relationship ?? 'NA' }} </td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">No of Residents in the House</td>
                <td>{{ $case->no_of_residents_in_house ?? 'NA' }}</td>
                <td style="font-weight: 600; color:#0094ff">Years at current Residence</td>
                <td>{{ $case->years_at_current_residence ?? 'NA' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">No of Earning Family Members</td>
                <td class="BVstyle ng-binding ng-hide">
                    {{ $case->no_of_earning_family_members ?? 'NA' }}</td>
                <td style="font-weight: 600; color:#0094ff">Residence Status</td>
                <td class="BVstyle ng-binding ng-hide">{{ $case->residence_status ?? 'NA' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Approx Rent</td>
                <td>{{ $case->approx_rent ?? 'NA' }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr class="bg-info text-white">
                <td colspan="4" class="subheading" style="text-align: center"><strong>Verifier's Observations</strong>
                </td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Location </td>
                <td>{{ $case->location ?? 'NA' }} </td>
                <td style="font-weight: 600; color:#0094ff">Locality</td>
                <td>{{ $case->locality ?? 'NA' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Accomodation Type</td>
                <td>{{ $case->accommodation_type ?? 'NA' }} </td>
                <td style="font-weight: 600; color:#0094ff">Interior Conditions</td>
                <td>{{ $case->interior_conditions ?? 'NA' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Assets Seen</td>
                <td class="BVstyle ng-binding ng-hide"> {{ $case->assets_seen ?? 'NA' }}</td>
                <td style="font-weight: 600; color:#0094ff">Nearest Landmark</td>
                <td>{{ $case->nearest_landmark ?? 'NA' }} </td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Area</td>
                <td class="BVstyle ng-binding ng-hide">{{ $case->area ?? 'NA' }}</td>
                <td style="font-weight: 600; color:#0094ff">Standard of Living</td>
                <td>{{ $case->standard_of_living ?? 'NA' }}</td>
            </tr>

            <tr class="bg-info text-white">
                <td colspan="4" class="subheading" style="text-align: center"><strong>If the
                        house is locked,the following information needs to be obtained from the
                        Neighbour/Third Party.</strong></td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Applicant Name</td>
                <td>{{ $case->applicant_name ?? 'NA' }}</td>
                <td style="font-weight: 600; color:#0094ff">Person Met</td>
                <td>{{ $case->person_met ?? 'NA' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Relationship</td>
                <td>{{ $case->locked_relationship ?? 'NA' }}</td>
                <td style="font-weight: 600; color:#0094ff">Applicant Age(Approx)</td>
                <td style="font-weight: 600; ">{{ $case->applicant_age ?? 'NA' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">No. of Residents in House</td>
                <td style="font-weight: 600;">
                    {{ $case->residence_status_others ?? 'NA' }}</td>
                <td style="font-weight: 600; color:#0094ff">Years Lived at this Residence</td>
                <td style="font-weight: 600;">
                    {{ $case->years_at_current_residence ?? 'NA' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Occupation</td>
                <td>{{ $case->occupation ?? '0000' }}</td>
                <td style="font-weight: 600; color:#0094ff">Years Lived at this Residence other</td>
                <td>{{ $case->years_at_current_residence_others ?? 'NA' }}</td>
            </tr>
            <tr class="bg-info text-white">
                <td colspan="4" class="subheading" style="text-align: center"><strong>If the
                        address is not confirmed then the following information needs to be
                        filled.</strong></td>
            </tr>
            <tr>
                <td>{{ $case->untraceable == 'true' ? 'Untraceable' : ''}}</td>
                <td style="font-weight: 600; color:#0094ff">Reason</td>
                <td>{{ $case->reason_of_untraceable }}</td>
                <td style="font-weight: 600; color:#0094ff">Result of Calling</td>
            </tr>
            <tr>
                <td>
                    '{{ $case->reason_of_calling }}'</td>
                <td></td>
                <td style="font-weight: 600; color:#0094ff">
                    <b>{{ $case->untraceable == 'false' ? 'Mismatch in Residence Address' : ''}}</b>
                </td>
                <td style="font-weight: 600; color:#0094ff">Is Applicant Known to the person</td>
            </tr>
            <tr>
                <td>{{ $case->is_applicant_know_to_person }} </td>
                <td></td>
                <td style="font-weight: 600; color:#0094ff">To Whom Does Address Belong ?</td>
                <td>{{ $case->to_whom_does_address_belong }}</td>
            </tr>
            <tr class="bg-info text-white">
                <td colspan="4" class="subheading" style="text-align: center"><strong>The
                        following is based on Verifier Observations</strong></td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Verifier's Name</td>
                <td>{{ optional($case->getUserVerifiersName)->name ?? 'NA' }}</td>
                <td style="font-weight: 600; color:#0094ff">Verification Conducted at</td>
                <td>{{ $case->verification_conducted_at ?? 'NA' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Proof attached</td>
                <td>{{ $case->proof_attached ?? 'NA' }}</td>
                <td style="font-weight: 600; color:#0094ff">Type of Proof</td>
                <td>{{ $case->type_of_proof ?? 'NA' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Date of Visit</td>
                <td>{{ $case->date_of_visit ?? 'NA' }}</td>
                <td style="font-weight: 600; color:#0094ff">Time of Visit</td>
                <td>{{ $case->time_of_visit ?? 'NA' }}</td>
            </tr>

            <tr class="bg-info text-white">
                <td colspan="4" class="subheading" style="text-align: center">
                    <strong>Updations</strong>
                </td>
            </tr>
            <tr>
                <td>Address</td>
                <td colspan="3"></td>
            </tr>
            <tr class="bg-info text-white">
                <td colspan="4" class="subheading" style="text-align: center"><strong>NEGATIVE
                        FEATURES</strong></td>
            </tr>
            <tr ng-hide="BVCase.VerifiedType==219 || BVCase.SubStatusId!=536">
                <td colspan="4" class="ng-binding">
                    {{ in_array($case->status, [2, 4]) ? 'Recommended : Address and Details Confirmed' :
                    (in_array($case->status, [3, 5]) ? 'Not Recommended : ' . ($case->negative_feedback_reason ??
                    (isset($case->getCaseStatus->name) ? $case->getCaseStatus->name : '')) : 'NA') }}
                </td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Visit Conducted </td>
                <td>
                    {{ $case->visit_conducted ?? (isset($case->getCaseStatus->parent_id) &&
                    $case->getCaseStatus->parent_id == 113 ? (isset($case->getCaseStatus->name) ?
                    isset($case->getCaseStatus->name) : '') : (isset($case->getStatus->name) ? $case->getStatus->name :
                    '')) }}
                </td>
                <td style="font-weight: 600; color:#0094ff">Reason </td>
                <td>{{ $case->negative_feedback_reason ?? 'NA' }}</td>
            </tr>
            <tr class="bg-info text-white">
                <td colspan="4" class="subheading" style="text-align: center">
                    <strong>Location</strong>
                </td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Latitude</td>
                <td>{{ $case->latitude ?? 'NA' }}</td>
                <td style="font-weight: 600; color:#0094ff">Longitude</td>
                <td>{{ $case->longitude ?? 'NA' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Address</td>
                <td>{{ $case->latlong_address ?? 'NA' }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr class="bg-info text-white">
                <td colspan="4" class="subheading" style="text-align: center"><strong>Cross
                        Verification Info</strong></td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Neighbour Check 1</td>
                <td>{{ $case->tcp1_name ?? 'NA' }} </td>
                <td style="font-weight: 600; color:#0094ff">Neighbour1 Checked With</td>
                <td>{{ $case->tcp1_checked_with ?? 'NA' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">TCP1 Negative Comments</td>
                <td>{{ $case->tcp1_negative_comments ?? 'NA' }} </td>
                <td style="font-weight: 600; color:#0094ff">Neighbour Check 2</td>
                <td>{{ $case->tcp2_name ?? 'NA' }} </td>
            </tr>
            <tr>
                <td style="font-weight: 600; color:#0094ff">Neighbour2 Checked With</td>
                <td>{{ $case->tcp2_checked_with ?? 'NA' }}</td>
                <td style="font-weight: 600; color:#0094ff">TCP2 Negative Comments</td>
                <td colspan="3">{{ $case->tcp2_negative_comments ?? 'NA' }} </td>
            </tr>

            <tr>
                <td style="font-weight: 600; color:#0094ff">Visited By </td>
                <td>{{ $case->visited_by ?? 'NA' }}</td>
                <td style="font-weight: 600; color:#0094ff">Verified By </td>
                <td>{{ $case->verified_by ?? 'NA' }}</td>
            </tr>
            <tr class="bg-info text-white">
                <td colspan="4" class="subheading" style="text-align: center"><strong>Office
                        CPV COMMENTS</strong></td>
            </tr>
            <tr>
                <td colspan="4" class="ng-binding">{{ $case->app_remarks }}</td>
            </tr>
            <tr class="bg-info text-white">
                <td colspan="4" class="subheading" style="text-align: center">
                    <strong>Supervisor Remarks</strong>
                </td>
            </tr>

            <tr>
                <td colspan="4">{{ $case->supervisor_remarks ?? 'NA' }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:center">
                    <img title="image"
                        style="width:150px;margin-bottom:5px; margin-left:5px;border:2px solid #b06c1c;border-radius:10px;"
                        src="{{ $sign }}" />
                    <br>
                    Signature of Agency Supervisor (With agency Seal)
                </td>
                <td colspan="2" style="text-align:center">
                    <img title="image"
                        style="width:150px;margin-bottom:5px; margin-left:5px;border:2px solid #b06c1c;border-radius:10px;"
                        src="{{ $sign }}" />
                    <br>
                    Audit Check Remarks by Agency With Stamp &amp; Sign
                </td>
            </tr>
            <tr class="bg-info text-white">
                <td colspan="4" class="subheading" style="text-align: center">
                    <strong>Applicant Photos</strong>
                </td>
            </tr>
            @php
            $images = []; // Create an array to hold the image data
            for ($i = 0; $i < 15; $i++) { $imageProperty='image_' . $i; $img=$case->$imageProperty ?? null; // Avoid
                undefined property errors
                if (!empty($img)) {
                $path = public_path($img);
                if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION); // Get the file extension
                $data = file_get_contents($path); // Get the file content
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data); // Encode to base64
                $images[] = $base64; // Add base64 image to array
                }
                }
                }
                @endphp

                @for ($i = 0; $i < count($images); $i +=4) <tr>
                    @for ($j = 0; $j < 4; $j++) @if (isset($images[$i + $j])) <td
                        style="width: 33%; text-align: center;">
                        <div class="col-md-4 col-lg-3">
                            <img class="zoomimg img-container" alt="img" data-img="{{ $images[$i + $j] }}"
                                src="{{ $images[$i + $j] }}" />
                        </div>
                        </td>
                        @endif
                        @endfor
                        </tr>
                        @endfor
        </tbody>
    </table>
    <br>
</div>