<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\casesFiType;
use App\Models\FiType;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers\LogHelper;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;

// use App\Models\Cases;
// use App\Models\Bank;
// use App\Models\Product;
// use App\Models\FiType;
// use App\Models\ApplicationType;
// use App\Models\User;
// use App\Models\casesFiType;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Hash;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;

class CasesController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function ShowCaseCountWise($user_id)
    {
        // ALTER TABLE `cases`  ADD `status` ENUM('0','1','2','3') NOT NULL COMMENT '0->inprogress,1->resolve, 2->verified, 3->rejected'  AFTER `remarks`;

        $cases = DB::table('cases_fi_types as cft')
            ->join('fi_types as ft', 'ft.id', '=', 'cft.fi_type_id')
            ->join('cases as c', 'c.id', '=', 'cft.case_id')
            ->join('products as p', 'p.id', '=', 'c.product_id')
            ->join('banks as b', 'b.id', '=', 'c.bank_id')
            ->select(
                'p.id as product_id',
                'ft.name as fi_type',
                'b.name as bank_name',
                'p.name as product_name',
                DB::raw('COUNT(p.id) as total_count')
            )
            ->where('cft.user_id', $user_id)
            ->where('cft.status', '1')
            ->groupBy('ft.name', 'p.id', 'b.name', 'p.name')
            ->get();


        if ($cases !== null) {
            return response()->json(['ShowCaseCountWise' => $cases]);
        } else {
            return response()->json(['error' => 'Bank ID not provided.'], 400);
        }
    }

    public function showCasebyProductId($fi_type, $product_id, $user_id)
    {
        // dd($fi_type);
        // SELECT p.id as product_id, p.name as product_name, c.applicant_name, c.created_at, c.refrence_number, cft.address, cft.pincode
        // FROM products p 
        // INNER JOIN cases as c ON c.product_id = p.id
        // INNER JOIN cases_fi_types as cft ON cft.case_id = c.id
        // INNER JOIN fi_types ft on ft.id = cft.fi_type_id
        // WHERE p.id = 1 AND ft.name = 'BV'
        $cases = DB::table('products as p')
            ->join('cases as c', 'c.product_id', '=', 'p.id')
            ->join('cases_fi_types as cft', 'cft.case_id', '=', 'c.id')
            ->join('fi_types as ft', 'ft.id', '=', 'cft.fi_type_id')
            ->select(
                'p.id as product_id',
                'c.id as case_id',
                'cft.id as case_fi_type_id',
                'p.name as product_name',
                'c.applicant_name',
                'c.created_at',
                'c.refrence_number',
                'cft.address',
                'cft.pincode',
                'cft.mobile'
            )
            ->where('p.id', $product_id)
            ->where('ft.name', $fi_type)
            ->where('cft.user_id', $user_id)
            ->where('cft.status', '1')
            ->get();

        if ($cases !== null) {
            return response()->json(['CaseList' => $cases]);
        } else {
            return response()->json(['error' => 'Bank ID not provided.'], 400);
        }
    }

    public function showCasebyId($cft_id)
    {

        //SELECT c.* , cft.mobile, cft.address, cft.pincode, cft.land_mark, a.name as created_by
        // FROM cases_fi_types as cft
        // INNER JOIN cases as c ON c.id = cft.case_id
        // INNER JOIN admins as a ON a.id = c.created_by
        // WHERE cft.id = 2
        $cases = DB::table('cases_fi_types as cft')
            ->join('cases as c', 'c.id', '=', 'cft.case_id')
            ->leftJoin('admins as a', 'a.id', '=', 'c.created_by')
            ->select(
                'cft.id as case_fi_type_id',
                'c.*',
                'cft.mobile',
                'cft.address',
                'cft.pincode',
                'cft.land_mark',
                'a.name as created_by'
            )
            ->where('cft.id', $cft_id)
            ->get();

        if ($cases !== null) {
            return response()->json(['CaseList' => $cases]);
        } else {
            return response()->json(['error' => 'Case Fi type id is not vaild.'], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


         
    public function uploadImage(Request $request)
    {
        // Create New Cases
        $data = $request->all();
        
        DB::table('api_request')->insert(['request_data' => json_encode($data)]);
        
        $validator = Validator::make(
            request()->all(),
            array(
                'case_fi_type_id' => 'required',
            )
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 400]);
        }
        $case_fi_type_id = $data['case_fi_type_id'];

        $case = CasesFiType::findOrFail($case_fi_type_id);
        
        $year = date('Y');
        $month = date('m');
        $path = "images/cases/{$year}/{$month}/{$case_fi_type_id}";
        // Comment out original image storage functionality
        // $path2 = "images/cases/original/{$year}/{$month}/{$case_fi_type_id}";
        
        // Ensure the directory exists
        if (!file_exists(public_path($path))) {
            mkdir(public_path($path), 0777, true);
        }
        // Comment out original directory creation
        // if (!file_exists(public_path($path2))) {
        //     mkdir(public_path($path2), 0777, true);
        // }

        foreach ($request->file('image') as  $file) {
            // Get the first available image slot
            // $imgField = $this->getAvailableImageField($case);
            if(isset($data['name'])){
                $imgField = $data['name'];
            }else{
                $imgField = $this->getAvailableImageField($case);
            }

            if ($imgField) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($path), $filename);

                // Comment out original image backup functionality
                // Save a copy of the original image to $path2
                // $originalPath = public_path("{$path2}/{$filename}");
                // copy(public_path("{$path}/{$filename}"), $originalPath);

                // Save the original image to the `original_images` table
                // DB::table('original_images')->insert([
                //     'case_fi_id' => $case_fi_type_id,
                //     'images' => "{$path2}/{$filename}",
                //     'created_at' => now(),
                // ]);

                $latitude = $data['latitude'];
                $longitude = $data['longitude'];
                $image_name = "{$path}/{$filename}";
                $address = isset($data['latlong_address']) ? $data['latlong_address'] : '';
                $datetime = isset($data['date_of_visit']) 
                    ? date('d/m/Y', strtotime($data['date_of_visit'])) . ' ' . date('H:i', strtotime($data['time_of_visit'])) 
                    : '';
    
                $img = Image::make(public_path($image_name));
                $img->resize(1500, 2000);

                // Add simple text overlay instead of table
                $this->addSimpleTextToImage($img, $latitude, $longitude, $address, $datetime);
                $img->save(public_path($image_name));

                $case->$imgField = $image_name;
                $case->save();

                return response()->json(['success' => 'Image uploaded successfully'], 200);
            } else {
                return response()->json(['error' => 'Image Not uploaded'], 500);
            }
        }
    }

     
    
    // Comment out table-related methods and replace with simple text method
    /*
    private function drawLineWithWidth($img, $x1, $y1, $x2, $y2, $color, $thickness)
    {
        $angle = atan2($y2 - $y1, $x2 - $x1); // Calculate the angle of the line
        for ($i = -($thickness / 2); $i < ($thickness / 2); $i++) {
            $offsetX = $i * sin($angle);
            $offsetY = $i * cos($angle);
            $img->line(
                $x1 + $offsetX,
                $y1 - $offsetY,
                $x2 + $offsetX,
                $y2 - $offsetY,
                function ($draw) use ($color) {
                    $draw->color($color);
                }
            );
        }
    }
    
    private function drawTableBorders($img, $tableStartX, $tableStartY, $rowHeight, $rowCount, $col1Width, $col2Width)
    {
        $tableWidth = $col1Width + $col2Width;
        $tableHeight = $rowHeight * $rowCount;
    

        // Draw outer borders
        $this->drawLineWithWidth($img, $tableStartX, $tableStartY, $tableStartX + $tableWidth, $tableStartY, '#FFFFFF', 3); // Top
        $this->drawLineWithWidth($img, $tableStartX, $tableStartY, $tableStartX, $tableStartY + $tableHeight, '#FFFFFF', 3); // Left
        $this->drawLineWithWidth($img, $tableStartX + $tableWidth, $tableStartY, $tableStartX + $tableWidth, $tableStartY + $tableHeight, '#FFFFFF', 3); // Right
        $this->drawLineWithWidth($img, $tableStartX, $tableStartY + $tableHeight, $tableStartX + $tableWidth, $tableStartY + $tableHeight, '#FFFFFF', 3); // Bottom
    
        // Draw horizontal lines
        for ($i = 1; $i < $rowCount; $i++) {
            $y = $tableStartY + $rowHeight * $i;
            $this->drawLineWithWidth($img, $tableStartX, $y, $tableStartX + $tableWidth, $y, '#FFFFFF', 3);
        }
    
        // Draw vertical line between columns
        $this->drawLineWithWidth($img, $tableStartX + $col1Width, $tableStartY, $tableStartX + $col1Width, $tableStartY + $tableHeight, '#FFFFFF', 3);
    }
    
    private function addTextToImage($img, $tableStartX, $tableStartY, $rowHeight, $tableWidth, $latitude, $longitude,$latlong_address, $dateTime)
    {
        $col1Width = $img->width() * 0.25; // 25% for the first column
        $col2Width = $img->width() * 0.72; // 72% for the second column
        $padding = $img->width() * 0.03; // 3% for the second column
        $tableWidth = $col1Width + $col2Width - $padding;
        $tableHeight = $rowHeight * 4;
    
            // Draw background rectangle for the table
            $img->rectangle(
                $tableStartX,
                $tableStartY,
                $tableStartX + $tableWidth,
                $tableStartY + $tableHeight,
                function ($draw) {
                    $draw->background([0, 0, 0, 0.5]); // Semi-transparent black
                }
            );
        // Add Latitude
        $img->text('Address', $tableStartX + 10, $tableStartY + $rowHeight / 2, function ($font) {
            $font->file(public_path('fonts/ARIAL.TTF'));
            $font->size(40);
            $font->color('#fff'); // White text color
            $font->align('left');
            $font->valign('middle');
        });

        $img->text($latlong_address, $tableStartX + $col1Width + 10, $tableStartY + $rowHeight / 2, function ($font) {
            $font->file(public_path('fonts/ARIAL.TTF'));
            $font->size(40);
            $font->color('#fff'); // White text color
            $font->align('left');
            $font->valign('middle');
        });
        // Add Latitude
        $img->text("Latitude", $tableStartX + 10, $tableStartY + $rowHeight + $rowHeight / 2, function ($font) {
            $font->file(public_path('fonts/ARIAL.TTF'));
            $font->size(40);
            $font->color('#FFFFFF'); // White text color
            $font->align('left');
            $font->valign('middle');
        });
        $img->text($latitude, $tableStartX +$col1Width + 10, $tableStartY + $rowHeight+ $rowHeight / 2, function ($font) {
            $font->file(public_path('fonts/ARIAL.TTF'));
            $font->size(40);
            $font->color('#fff'); // White text color
            $font->align('left');
            $font->valign('middle');
        });
    
        // Add Longitude
        $img->text("Longitude", $tableStartX + 10, $tableStartY + $rowHeight + $rowHeight + $rowHeight / 2, function ($font) {
            $font->file(public_path('fonts/ARIAL.TTF'));
            $font->size(40);
            $font->color('#fff'); // White text color
            $font->align('left');
            $font->valign('middle');
        });
        $img->text($longitude, $tableStartX + $col1Width + 10, $tableStartY + $rowHeight + $rowHeight + $rowHeight / 2, function ($font) {
            $font->file(public_path('fonts/ARIAL.TTF'));
            $font->size(40);
            $font->color('#fff'); // White text color
            $font->align('left');
            $font->valign('middle');
        });
    
        $img->text('Date', $tableStartX + 10, $tableStartY + $rowHeight + $rowHeight + $rowHeight  + $rowHeight / 2, function ($font) {
            $font->file(public_path('fonts/ARIAL.TTF'));
            $font->size(40);
            $font->color('#fff'); // White text color
            $font->align('left'); // Center align text
            $font->valign('middle');
        });
        $img->text($dateTime, $tableStartX +$col1Width + 10, $tableStartY + $rowHeight + $rowHeight + $rowHeight  + $rowHeight / 2, function ($font) {
            $font->file(public_path('fonts/ARIAL.TTF'));
            $font->size(40);
            $font->color('#fff'); // White text color
            $font->align('left'); // Center align text
            $font->valign('middle');
        });
        // Draw table borders
        $this->drawTableBorders($img, $tableStartX, $tableStartY, $rowHeight, 4, $col1Width, $col2Width);
    
    }
    */

    // New simple text overlay method
    private function addSimpleTextToImage($img, $latitude, $longitude, $address, $dateTime)
    {
        // Calculate font size based on image resolution
        $imageWidth = $img->width();
        $imageHeight = $img->height();
        
        // Base font size calculation - scale with image width
        $baseFontSize = ($imageWidth / 1500) * 45; // For 1500px width, use 45px font
        $fontSize = max(32, min(60, $baseFontSize)); // Minimum 32px, Maximum 60px
        
        // Calculate positioning based on image height
        $backgroundStartY = $imageHeight - ($imageHeight * 0.15); // 15% from bottom
        $lineHeight = $fontSize + 10; // Add 10px spacing between lines
        $leftMargin = $imageWidth * 0.03; // 3% margin from left
        $backgroundHeight = ($lineHeight * 6) + 40; // Height for 6 lines + padding
        
        // Text starts 15px from background start
        $textStartY = $backgroundStartY + 30;

        // Create semi-transparent background for better text visibility
        $img->rectangle(
            0,
            $backgroundStartY - 30,
            $imageWidth,
            $backgroundStartY + $backgroundHeight,
            function ($draw) {
                $draw->background([0, 0, 0, 0.4]); // Slightly darker for better contrast
            }
        );

        // Current Y position for text
        $currentY = $textStartY;

        // Add Address
        if (!empty($address)) {
            $img->text("Address: {$address}", $leftMargin, $currentY, function ($font) use ($fontSize) {
                $font->file(public_path('fonts/ARIAL.TTF'));
                $font->size($fontSize);
                $font->color('#FFFFFF');
                $font->align('left');
            });
            $currentY += $lineHeight;
        }

        // Add Latitude
        $img->text("Latitude: {$latitude}", $leftMargin, $currentY, function ($font) use ($fontSize) {
            $font->file(public_path('fonts/ARIAL.TTF'));
            $font->size($fontSize);
            $font->color('#FFFFFF');
            $font->align('left');
        });
        $currentY += $lineHeight;

        // Add Longitude
        $img->text("Longitude: {$longitude}", $leftMargin, $currentY, function ($font) use ($fontSize) {
            $font->file(public_path('fonts/ARIAL.TTF'));
            $font->size($fontSize);
            $font->color('#FFFFFF');
            $font->align('left');
        });
        $currentY += $lineHeight;

        // Add Date/Time
        if (!empty($dateTime)) {
            $img->text("Date: {$dateTime}", $leftMargin, $currentY, function ($font) use ($fontSize) {
                $font->file(public_path('fonts/ARIAL.TTF'));
                $font->size($fontSize);
                $font->color('#FFFFFF');
                $font->align('left');
            });
        }
    }

    public function uploadSignature(Request $request)
    {
        // Create New Cases 
        $data = $request->all();
        // dump($data);
        $validator = Validator::make(
            request()->all(),
            array(
                'case_fi_type_id'  =>       'required',
            )
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 400]);
        }
        $case_fi_type_id = $data['case_fi_type_id'];

        $case = CasesFiType::findOrFail($case_fi_type_id);

        $year = date('Y');
        $month = date('m');
        $path = "images/cases/{$year}/{$month}/{$case_fi_type_id}";

        // Ensure the directory exists
        if (!file_exists(public_path($path))) {
            mkdir(public_path($path), 0777, true);
        }
        // Handle each uploaded file
        $agency_signature = $request->file('agency_signature');

        // Get the first available image slot
        if ($agency_signature) {
            $filename = time() . '_' . $agency_signature->getClientOriginalName();
            $agency_signature->move(public_path($path), $filename);

            // Save the filename to the current image field
            $case->signature_of_agency_supervisor = "{$path}/{$filename}";
            $case->save();
            return response()->json(['message' => 'Agency Signature uploaded successfully'], 200);
        } else {
            return response()->json(['message' => 'Image Not uploaded'], 500);
        }
    }
    private function getAvailableImageField($case)
    {
        for ($i = 1; $i <= 9; $i++) {
            $imgField = 'image_' . $i;
            if (is_null($case->$imgField)) {
                return $imgField;
            }
        }
        return null;
    }
    public function caseSubmit(Request $request)
    {
        // Create New Cases
        $data = $request->all();
        $jsonData = json_encode($data);

        // Insert the JSON data into the 'api_request' table
        DB::table('api_request')->insert(['request_data' => $jsonData]);
        $validator = Validator::make(
            request()->all(),
            array(
                'case_fi_type_id'   =>       'required',
                'fi_type_id'        =>       'required',
            )
        );
        if ($validator->fails()) {
        }

        $cases = casesFiType::findOrFail($data['case_fi_type_id']);

        // Count how many images are present in the database for this case
        $imageCount = 0;
        for ($i = 1; $i <= 9; $i++) {
            $imageField = 'image_' . $i;
            if (isset($cases->$imageField) && !empty($cases->$imageField)) {
                $imageCount++;
            }
        }

        // Check if at least 4 images are provided
        if ($imageCount < 4) {
            return response()->json(['error' => 'Minimum 4 images are required in the database'], 400);
        }

        if ($data['fi_type_id'] == '1') {
            $this->SubmitRVCase($data);
        } elseif ($data['fi_type_id'] == '2') {
            $this->SubmitBVCase($data);
        } else {
            return response()->json(['error' => 'Fi type is not vaild', 400]);
        }

        return response()->json(['message' => 'Case Submit Successfully'], 200);
    }

    private function SubmitRVCase($data)
    {

        // Create the path to store the image
        $case_fi_type_id = $data['case_fi_type_id'];
        $cases = casesFiType::findOrFail($case_fi_type_id);
        $cases->address_confirmed                   = $data['address_confirmed'];
        $cases->address_confirmed_by                = $data['address_confirmed_by'];
        $cases->applicant_name                      = $data['applicant_name'];
        $cases->person_met                          = $data['person_met'];
        $cases->relationship                        = $data['relationship'];
        $cases->year_of_establishment               = $data['year_of_establishment'];
        $cases->no_of_earning_family_members        = $data['no_of_earning_family_members'];
        $cases->residence_status                    = $data['residence_status'];
        $cases->name_of_employer                    = $data['name_of_employer'];
        $cases->employer_address                    = $data['employer_address'];
        $cases->telephone_no_residence              = $data['telephone_no_residence'];
        $cases->relationship_others                 = $data['relationship_others'];
        $cases->years_at_current_residence          = $data['years_at_current_residence'];
        $cases->years_at_current_residence_others   = $data['years_at_current_residence_others'];
        $cases->no_of_earning_family_members_others = $data['no_of_earning_family_members_others'];
        $cases->residence_status_others             = $data['residence_status_others'];
        $cases->verification_conducted_at_others    = $data['verification_conducted_at_others'];
        $cases->office                              = $data['office'];
        $cases->approx_value                        = $data['approx_value'];
        $cases->approx_rent                         = $data['approx_rent'];
        $cases->designation                         = $data['designation'];
        $cases->bank_name                           = $data['bank_name'];
        $cases->branch                              = $data['branch'];
        $cases->permanent_address                   = $data['permanent_address'];
        $cases->vehicles                            = $data['vehicles'];
        $cases->make_and_type                       = $data['make_and_type'];
        $cases->location                            = $data['location'];
        $cases->locality                            = $data['locality'];
        $cases->latlong_address                      = isset($data['latlong_address']) ? $data['latlong_address'] : 'NA';
        $cases->interior_conditions                 = $data['interior_conditions'];
        $cases->assets_seen                         = $data['assets_seen'];
        $cases->area                                = $data['area'];
        $cases->standard_of_living                  = $data['standard_of_living'];
        $cases->nearest_landmark                    = $data['nearest_landmark'];
        $cases->locked_person_met                   = $data['locked_person_met'];
        $cases->locked_relationship                 = $data['locked_relationship'];
        $cases->applicant_age                       = $data['applicant_age'];
        $cases->year_of_establishment               = $data['year_of_establishment'];
        $cases->status                              = $data['status'];
        $cases->occupation                          = $data['occupation'];
        $cases->untraceable                         = $data['untraceable'];
        $cases->reason_of_calling                   = $data['reason_of_calling'] ?? '';
        $cases->reason_of_untraceable               = $data['reason_of_untraceable'] ?? '';
        $cases->reason                              = $data['reason'] ?? '';
        $cases->verifiers_name                      = $cases['user_id'];
        $cases->verification_conducted_at           = $data['verification_conducted_at'];
        $cases->proof_attached                      = $data['proof_attached'];
        $cases->type_of_proof                       = $data['type_of_proof'];
        $cases->comments                            = $data['comments'];
        $cases->consolidated_remarks                = $data['consolidated_remarks'];
        $cases->app_remarks                         = $data['app_remarks'];
        $cases->recommended                         = $data['recommended'];
        $cases->accommodation_type                  = $data['accommodation_type'];
        // $cases->house_locked                     = $data['house_locked'];
        $cases->no_of_residents_in_house            = isset($data['no_of_residents_in_house']) ? $data['no_of_residents_in_house'] : null;
        // $cases->employement_details              = $data['employement_details'];
        $cases->date_of_visit                       = date('Y-m-d');
        $cases->time_of_visit                       = date('H:i:s');
        $cases->latitude                            = $data['latitude'];
        $cases->longitude                           = $data['longitude'];
        $cases->tcp1_name                           = $data['tcp1_name'];
        $cases->tcp1_checked_with                   = $data['tcp1_checked_with'];
        $cases->tcp1_negative_comments              = $data['tcp1_negative_comments'];
        $cases->tcp2_name                           = $data['tcp2_name'];
        $cases->tcp2_checked_with                   = $data['tcp2_checked_with'];
        $cases->tcp2_negative_comments              = $data['tcp2_negative_comments'];
        $cases->to_whom_does_address_belong         = $data['to_whom_does_address_belong'];
        $cases->is_applicant_know_to_person         = $data['is_applicant_know_to_person'];
        $cases->other_stability_year_details        = $data['other_stability_year_details'];
        $cases->negative_feedback_reason            = $data['negative_feedback_reason'];
        $cases->save();
        return $cases->id;
    }


    private function SubmitBVCase($data)
    {
        // Create the path to store the image
        $case_fi_type_id = $data['case_fi_type_id'];

        $caseFi = casesFiType::findOrFail($case_fi_type_id);
        $caseFi->employer_mobile                = $data['mobile'];
        $caseFi->latlong_address                = isset($data['latlong_address']) ? $data['latlong_address'] : 'NA';
        $caseFi->address_confirmed              = $data['address_confirmed'];
        $caseFi->employer_address               = $data['employer_address'];
        $caseFi->type_of_proof                  = $data['type_of_proof'];
        $caseFi->address_confirmed_by           = $data['address_confirmed_by'];
        $caseFi->name_of_employer               = $data['name_of_employer'];
        $caseFi->person_met                     = $data['person_met'];
        $caseFi->website_of_employer            = $data['website_of_employer'];
        $caseFi->email_of_employer              = $data['email_of_employer'];
        $caseFi->telephono_no_office            = $data['telephono_no_office'];
        $caseFi->ext                            = $data['ext'];
        $caseFi->telephone_no_residence         = $data['telephone_no_residence'];
        $caseFi->co_board_outside_bldg_office   = $data['co_board_outside_bldg_office'];
        $caseFi->type_of_employer               = $data['type_of_employer'];
        $caseFi->nature_of_employer             = $data['nature_of_employer'];
        $caseFi->line_of_business               = $data['line_of_business'];
        $caseFi->consolidated_remarks           = $data['consolidated_remarks'];
        $caseFi->app_remarks                    = $data['app_remarks'];
        $caseFi->nature_of_business             = isset($data['type_industry']) ? $data['type_industry'] : null;
        $caseFi->level_of_business_activity     = $data['level_of_business_activity'];
        $caseFi->no_of_employees                = $data['no_of_employees'];
        $caseFi->no_of_branches                 = $data['no_of_branches'];
        $caseFi->interior_conditions            = $data['office_ambience'];
        $caseFi->type_of_locality               = $data['type_of_locality'];
        $caseFi->ease_of_locating             = isset($data['ease_of_locating']) ? $data['ease_of_locating'] : null;
        $caseFi->area                           = $data['area'];
        $caseFi->nearest_landmark               = $data['nearest_landmark'];
        $caseFi->verifiers_name                 = $caseFi['user_id'];
        $caseFi->terms_of_employment            = $data['terms_of_employment'];
        $caseFi->grade                          = $data['grade'];
        $caseFi->year_of_establishment          = $data['year_of_establishment'];
        $caseFi->applicant_age                  = $data['applicant_age'];
        $caseFi->name_of_employer_co            = $data['name_of_employer_co'];
        $caseFi->established                    = $data['established'];
        $caseFi->designation                    = $data['designation'];
        $caseFi->date_of_visit                  = date('Y-m-d');
        $caseFi->time_of_visit                  = date('H:i:s');
        $caseFi->latitude                       = $data['latitude'];
        $caseFi->longitude                      = $data['longitude'];
        $caseFi->tcp1_name                      = $data['tcp1_name'];
        $caseFi->tcp1_checked_with              = $data['tcp1_checked_with'];
        $caseFi->tcp1_negative_comments         = $data['tcp1_negative_comments'];
        $caseFi->tcp2_name                      = $data['tcp2_name'];
        $caseFi->tcp2_checked_with              = $data['tcp2_checked_with'];
        $caseFi->tcp2_negative_comments         = $data['tcp2_negative_comments'];
        $caseFi->visited_by                     = $data['visited_by'];
        $caseFi->verified_by                    = $data['verified_by'];
        $caseFi->address_confirmed              = $data['address_confirmation_status'];
        $caseFi->employer_address               = $data['address_of_employer_co'];
        $caseFi->designation_other              = $data['designation_other'];
        $caseFi->residence_number               = $data['residence_number'];
        $caseFi->type_of_profession               = $data['type_of_profession'];
        $caseFi->other_stability_year_details   = $data['year_of_employment'];
        $caseFi->negative_feedback_reason       = $data['negative_feedback_reason'];
        $caseFi->status                         = $data['status'];
        $caseFi->residence_status               = $data['residence_status'];
        $caseFi->save();
        return $caseFi->id;
    }

    


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     $cases = Cases::all();
    //     $products  = Bank::all();
    //     return view('backend.pages.cases.index', compact('cases'));
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit($id)
    // {
    //     $cases = Cases::find($id);

    //     return view('backend.pages.cases.edit', compact('cases'));
    // }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     $cases = Cases::find($id);
    //     if (!is_null($cases)) {
    //         $cases->delete();
    //     }

    //     session()->flash('success', 'Cases has been deleted !!');
    //     return back();
    // }

    // public function getItem($bankId = null)
    // {
    //     $AvailbleProduct = Product::select('bpm.id', 'bpm.bank_id', 'bpm.product_id', 'products.name', 'products.product_code')
    //         ->leftJoin('bank_product_mappings as bpm', 'bpm.product_id', '=', 'products.id')
    //         ->where('bpm.bank_id', $bankId)
    //         ->where('products.status', '1')
    //         ->get()->toArray();


    //     if ($bankId !== null) {
    //         return response()->json(['AvailbleProduct' => $AvailbleProduct]);
    //     } else {
    //         return response()->json(['error' => 'Bank ID not provided.'], 400);
    //     }
    // }
    /**
     * @return \Illuminate\Support\Collection
     */
    // public function importExportView($bankId = 1)
    // {
    //     $cases = '';
    //     return view('backend.pages.cases.import', compact('cases'));
    // }

    /**
     * @return \Illuminate\Support\Collection
     */
    // public function import()
    // {
    //     dd('aaaaaaaaaaaaaaaaaaaaaa');
    //     return "ssuheee";
    //     // return Excel::download(new UsersExport, 'users.xlsx');
    // }

    /**
     * @return \Illuminate\Support\Collection
     */
    // public function export()
    // {
    //     dd('sssssssssssssssssssssssssssssss');

    //     // Excel::import(new UsersImport, request()->file('file'));

    //     return back();
    // }
}