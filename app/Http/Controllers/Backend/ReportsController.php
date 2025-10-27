<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use App\Exports\CasesExport;
use App\Exports\CasesCountExport;
use App\Models\CaseStatus;
use App\Models\Cases;
use App\Models\casesFiType;

use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
class ReportsController extends Controller
{
    public $user;
    public $Auth;
    //
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }
    public function index()
    {
        // if (is_null($this->user) || !$this->user->can('role.view')) {
        //     abort(403, 'Sorry !! You are Unauthorized to view any role !');
        // }

        // $roles = Role::all();
        $status = CaseStatus::select('id', 'name')
        ->whereIn('name', ['New', 'Inprogress', 'Negative Resolved', 'Positive Resolved','Positive Verified', 'Negative Verified', 'Hold', 'close'])
        ->orderBy('name') // Example of adding ordering
        ->get();
        return view('backend.pages.reports.index', compact('status'));
    }

    public function fetchreport(Request $request)
    {
        $validated = $request->validate([
            'verify_type' => 'required',
            'dateFrom' => 'required|date',
            'dateTo' => 'required|date|after_or_equal:dateFrom',
        ]);

        $status = $request->input('verify_type');
        $dateFrom = $request->input('dateFrom');
        $dateTo = $request->input('dateTo');
        
        $query = casesFiType::with([
            'getCase:id,refrence_number,applicant_name,bank_id',
            'getFiType:id,name',
            'getUser:id,name',
            'getStatus:id,name',
            'getCaseStatus:id,name'
        ]);
        if(Auth::guard('admin')->user()->id != 1){
            $assignBank = explode(',', Auth::guard('admin')->user()->banks_assign);
            $query->whereHas('getCase', function ($query) use ($assignBank) {
                $query->whereIn("bank_id", $assignBank);
            });
        }
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        
        if ($status >= 0) {
            $query->where('status', $status);
        }
        
        $cases = $query->get();
        return view('backend.pages.reports.list', compact('cases'))->render();
    }
    public function export(Request $request)
    {
        $validated = $request->validate([
            'verify_type' => 'required',
            'dateFrom' => 'required|date',
            'dateTo' => 'required|date|after_or_equal:dateFrom',
        ]);
        $filter['status'] = $request->input('verify_type');
        $filter['dateFrom'] = $request->input('dateFrom');
        $filter['dateTo'] = $request->input('dateTo');
        
        return Excel::download(new CasesExport($filter), 'cases_report.xlsx');
    }

    public function countReport(Request $request)
    {
        return view('backend.pages.reports.countreport');
    }
    public function fetchcountreport(Request $request)
    {
        $validated = $request->validate([
            'dateFrom' => 'required|date',
            'dateTo' => 'required|date|after_or_equal:dateFrom',
        ]);
    
        $dateFrom = $request->input('dateFrom');
        $dateTo = $request->input('dateTo');
        
        $query = casesFiType::with([
            'getCase:id,refrence_number,applicant_name,bank_id', // Removed `status` here
            'getUser:id,name'
        ]);
    
        if (Auth::guard('admin')->user()->id != 1) {
            $assignBank = explode(',', Auth::guard('admin')->user()->banks_assign);
            $query->whereHas('getCase', function ($query) use ($assignBank) {
                $query->whereIn("bank_id", $assignBank);
            });
        }
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        $userCasesCount = $query->get()
            ->groupBy('getUser.name')
            ->map(function ($cases, $userName) {
                // Filtering cases by status within the map function
                $positiveVerifiedCount = $cases->filter(function ($case) {
                    return $case->getCase && $case->getCase->status == '2'; // Null check added
                })->count();
                
                $negativeVerifiedCount = $cases->filter(function ($case) {
                    return $case->getCase && $case->getCase->status == '3'; // Null check added
                })->count();

                return [
                    'user' => $userName,
                    'total_cases' => $cases->count(),
                    'positive_verified' => $positiveVerifiedCount,
                    'negative_verified' => $negativeVerifiedCount,
                ];
            });
                
        return view('backend.pages.reports.countlist', compact('userCasesCount'))->render();
    }

    public function exportcount(Request $request)
    {
        $validated = $request->validate([
            'dateFrom' => 'required|date',
            'dateTo' => 'required|date|after_or_equal:dateFrom',
        ]);
        $filter['dateFrom'] = $request->input('dateFrom');
        $filter['dateTo'] = $request->input('dateTo');
        
        return Excel::download(new CasesCountExport($filter), 'cases_report.xlsx');
    }
}
