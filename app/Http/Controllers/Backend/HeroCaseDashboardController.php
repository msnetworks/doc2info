<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\HeroCase;
use App\Models\State; // Import the State model
use App\Models\Status; // Import the Status model
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class HeroCaseDashboardController extends Controller
{
    public function index()
    {
        $states = State::all();
        $stateWiseData = HeroCase::query()
            ->leftJoin('states', 'hero_cases.state', '=', 'states.id')
            ->select(
                'states.state as group_by',
                'states.id as group_by_id',
                DB::raw('COUNT(hero_cases.id) as total_count'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "1" THEN 1 ELSE 0 END) as inprogress'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "2" THEN 1 ELSE 0 END) as positive_resolved'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "3" THEN 1 ELSE 0 END) as negative_resolved'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "6" THEN 1 ELSE 0 END) as appointment_not_done'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "1156" THEN 1 ELSE 0 END) as appointment_rescheduled')
            )
            ->whereNotNull('hero_cases.state')
            ->groupBy('states.state', 'states.id') // Corrected to groupBy states.state
            ->orderBy('states.state') // Corrected to orderBy states.state
            ->get()
            ->toArray();

        $overallData = HeroCase::select(
            DB::raw('"Overall" as group_by'),
            DB::raw('COUNT(*) as total_count'),
            DB::raw('SUM(CASE WHEN status = "1" THEN 1 ELSE 0 END) as inprogress'),
            DB::raw('SUM(CASE WHEN status = "2" THEN 1 ELSE 0 END) as positive_resolved'),
            DB::raw('SUM(CASE WHEN status = "3" THEN 1 ELSE 0 END) as negative_resolved'),
            DB::raw('SUM(CASE WHEN status = "6" THEN 1 ELSE 0 END) as appointment_not_done'),
            DB::raw('SUM(CASE WHEN status = "1156" THEN 1 ELSE 0 END) as appointment_rescheduled')
        )
            ->first()
            ->toArray();


        return view('backend.pages.hero_cases_dashboard.index', compact('states', 'stateWiseData', 'overallData'));
    }
    
    public function getData(Request $request)
    {
        $baseQuery = HeroCase::query()
            ->leftJoin('states', 'hero_cases.state', '=', 'states.id');
    
        if ($request->filled('state')) {
            $baseQuery->where('hero_cases.state', $request->state);
        }
    
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $baseQuery->whereDate('hero_cases.created_at', '>=', $request->start_date)
                      ->whereDate('hero_cases.created_at', '<=', $request->end_date);
        } elseif ($request->filled('start_date')) {
            $baseQuery->whereDate('hero_cases.created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $baseQuery->whereDate('hero_cases.created_at', '<=', $request->end_date);
        }
    
        // Clone the base query for state-wise data
        $stateWiseQuery = clone $baseQuery;
        $stateWiseData = $stateWiseQuery
            ->select(
                'states.state as group_by',
                'states.id as group_by_id',
                DB::raw('COUNT(hero_cases.id) as total_count'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "1" THEN 1 ELSE 0 END) as inprogress'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "2" THEN 1 ELSE 0 END) as positive_resolved'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "3" THEN 1 ELSE 0 END) as negative_resolved'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "6" THEN 1 ELSE 0 END) as appointment_not_done'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "1156" THEN 1 ELSE 0 END) as appointment_rescheduled')
            )
            ->whereNotNull('hero_cases.state')
            ->groupBy('states.state', 'states.id')
            ->orderBy('states.state')
            ->get()
            ->toArray();
    
        // Clone the base query for overall data
        $overallQuery = clone $baseQuery;
        $overallData = $overallQuery
            ->select(
                DB::raw('"Overall" as group_by'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "1" THEN 1 ELSE 0 END) as inprogress'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "2" THEN 1 ELSE 0 END) as positive_resolved'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "3" THEN 1 ELSE 0 END) as negative_resolved'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "6" THEN 1 ELSE 0 END) as appointment_not_done'),
                DB::raw('SUM(CASE WHEN hero_cases.status = "1156" THEN 1 ELSE 0 END) as appointment_rescheduled')
            )
            ->first()
            ->toArray();
    
        return response()->json([
            'stateWiseData' => $stateWiseData,
            'overallData' => $overallData
        ]);
    }
    

    // public function getData(Request $request)
    // {
    //     $query = HeroCase::query()
    //         ->leftJoin('states', 'hero_cases.state', '=', 'states.id');

    //     if ($request->filled('state')) {
    //         $query->where('hero_cases.state', $request->state);
    //     }

    //     if ($request->filled('start_date') && $request->filled('end_date')) {
    //         $query->whereBetween('hero_cases.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
    //     } elseif ($request->filled('start_date')) {
    //         $query->where('hero_cases.created_at', '>=', $request->start_date . ' 00:00:00');
    //     } elseif ($request->filled('end_date')) {
    //         $query->where('hero_cases.created_at', '<=', $request->end_date . ' 23:59:59');
    //     }

    //     $stateWiseData = $query->clone()
    //         ->select(
    //             'states.state as group_by',
    //             'states.id as group_by_id',
    //             DB::raw('COUNT(hero_cases.id) as total_count'),
    //             DB::raw('SUM(CASE WHEN hero_cases.status = "1" THEN 1 ELSE 0 END) as inprogress'),
    //             DB::raw('SUM(CASE WHEN hero_cases.status = "2" THEN 1 ELSE 0 END) as positive_resolved'),
    //             DB::raw('SUM(CASE WHEN hero_cases.status = "3" THEN 1 ELSE 0 END) as negative_resolved'),
    //             DB::raw('SUM(CASE WHEN hero_cases.status = "6" THEN 1 ELSE 0 END) as appointment_not_done'),
    //             DB::raw('SUM(CASE WHEN hero_cases.status = "1156" THEN 1 ELSE 0 END) as appointment_rescheduled')
    //         )
    //         ->whereNotNull('hero_cases.state')
    //         ->groupBy('states.state', 'states.id')
    //         ->orderBy('states.state')
    //         ->get()
    //         ->toArray();

    //     $overallQuery = HeroCase::query();
    //     if ($request->filled('state')) {
    //         $overallQuery->where('state', $request->state);
    //     }
    //     if ($request->filled('start_date') && $request->filled('end_date')) {
    //         $overallQuery->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
    //     } elseif ($request->filled('start_date')) {
    //         $overallQuery->where('created_at', '>=', $request->start_date . ' 00:00:00');
    //     } elseif ($request->filled('end_date')) {
    //         $overallQuery->where('created_at', '<=', $request->end_date . ' 23:59:59');
    //     }

    //     $overallData = $overallQuery->select(
    //         DB::raw('"Overall" as group_by'),
    //         DB::raw('COUNT(*) as total_count'),
    //         DB::raw('SUM(CASE WHEN status = "1" THEN 1 ELSE 0 END) as inprogress'),
    //         DB::raw('SUM(CASE WHEN status = "2" THEN 1 ELSE 0 END) as positive_resolved'),
    //         DB::raw('SUM(CASE WHEN status = "3" THEN 1 ELSE 0 END) as negative_resolved'),
    //         DB::raw('SUM(CASE WHEN status = "6" THEN 1 ELSE 0 END) as appointment_not_done'),
    //         DB::raw('SUM(CASE WHEN status = "1156" THEN 1 ELSE 0 END) as appointment_rescheduled')
    //     )
    //         ->first()
    //         ->toArray();

    //     return response()->json(['stateWiseData' => $stateWiseData, 'overallData' => $overallData]);
    // }
    
    public function showCaseDetails(Request $request)
    {
        $state = $request->query('state');
        $status = $request->query('status');
    
        $cases = HeroCase::query()
            ->where('state', $state)
            ->where('status', $status)
            ->paginate(15);
    
        $statuses = Status::all(); // Fetch all statuses
    
        return view('backend.pages.hero_cases_dashboard.case_details', compact('cases', 'state', 'status', 'statuses'));
    }
    
    public function editVerification(HeroCase $heroCase, $id)
    {
        $heroCase = $heroCase->find($id);
        $statuses = Status::whereIn('id', [2, 3])->get(); // Fetch specific statuses
        return view('backend.pages.hero_cases_dashboard.edit_verification', compact('heroCase', 'statuses'));
    }

    public function updateVerification(Request $request, HeroCase $heroCase, $id)
    {
        $validator = Validator::make($request->all(), [
            'verification_status' => 'required|exists:case_status,id',
            'verification_pdf' => 'nullable|mimes:pdf|max:2048',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $heroCase = $heroCase->findOrFail($id); // Fix: properly fetch and assign
        $status  = $heroCase->status;
        $heroCase->status = $request->verification_status;
        $heroCase->verification_remarks = $request->verification_remarks;
        $heroCase->verification_status = $request->verification_status;
    
        if ($request->hasFile('verification_pdf')) {
            // Delete old PDF if it exists
            if ($heroCase->verification_pdf) {
                Storage::disk('public')->delete($heroCase->verification_pdf);
            }
    
            $path = $request->file('verification_pdf')->store('hero_case_pdfs', 'public');
            $heroCase->verification_pdf = $path;
        }
    
        $heroCase->save();
    
        return redirect()->route('admin.hero-cases.details', [
            'state' => $heroCase->state,
            'status' => $status,
        ])->with('success', 'Verification status updated successfully.');
    }



    public function editAppointment(HeroCase $heroCase, $id)
    {
        $heroCase = $heroCase->find($id);
        $statuses = Status::whereIn('parent_id', [6])->get(); // Fetch specific statuses
        return view('backend.pages.hero_cases_dashboard.edit_appointment', compact('heroCase', 'statuses'));
    }

    public function updateAppointment(Request $request, HeroCase $heroCase, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'appointment_status' => 'required|exists:case_status,id',
            'appointment_remarks' => 'required|string|max:1000',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Find the specific HeroCase instance (optional since route-model binding is used)
        $case = $heroCase->findOrFail($id);
    
        // Update the case
        $case->update([
            'status' => 6,
            'appointment_status' => $request->appointment_status,
            'appointment_remarks' => $request->appointment_remarks,
        ]);
    
        return redirect()->route('admin.hero-cases.details', [
            'state' => $case->state,
            'status' => $case->status,
        ])->with('success', 'Appointment status updated successfully.');
    }
    
    public function editReschedule(HeroCase $heroCase, $id)
    {
        $heroCase = $heroCase->find($id);
        $statuses = Status::whereIn('parent_id', [6])->get(); // Fetch specific statuses
        return view('backend.pages.hero_cases_dashboard.edit_reschedule', compact('heroCase', 'statuses'));
    }

    public function updateReschedule(Request $request, HeroCase $heroCase, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'reschedule_date' => 'required|date', // Add validation for the date
            'reschedule_remarks' => 'nullable|string|max:1000',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Find the specific HeroCase instance
        $case = $heroCase->findOrFail($id);
    
        // Update the case
        $case->update([
            'status' => 1156, // Set status to 1156 for rescheduled
            'reschedule_on' => $request->reschedule_date, // Store the date
            'reschedule_remarks' => $request->reschedule_remarks,
        ]);
    
        return redirect()->route('admin.hero-cases.details', [
            'state' => $case->state,
            'status' => $case->status,
        ])->with('success', 'Appointment rescheduled successfully.');
    }


    public function show(HeroCase $heroCase, $id)
    {
        $heroCase = $heroCase->find($id);
        return view('backend.pages.hero_cases_dashboard.show', compact('heroCase'));
    }

    public function downloadVerificationPdf(HeroCase $heroCase, $id)
    {
        $heroCase = $heroCase->findOrFail($id);
        if ($heroCase->verification_pdf) {
            $path = storage_path('app/public/' . $heroCase->verification_pdf);
            $name = 'verification_document_' . $heroCase->ref_no . '.pdf';
            $headers = [
                'Content-Type: application/pdf',
            ];

            return response()->download($path, $name, $headers, ResponseHeaderBag::DISPOSITION_INLINE);
        } else {
            return redirect()->back()->with('error', 'Verification PDF not found.');
        }
    }
}