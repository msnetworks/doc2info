<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\casesFiType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Cases;
use DateTime;
use DB;
class DashboardController extends Controller
{
    public $user;
    public $FromDate;
    public $ToDate;
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            $this->FromDate = $this->ToDate =  date('Y-m-d');

            return $next($request);
        });
    }

    public function _group_by($array, $key)
    {
        $return = array();
        foreach ($array as $val) {
            $return[$val[$key]][] = $val;
        }
        return $return;
    }


public function index(Request $request)
{
    if (is_null($this->user) || !$this->user->can('dashboard.view')) {
        abort(403, 'Sorry !! You are Unauthorized to view dashboard!');
    }
    // if (Auth::guard('admin')->user()->role === 'Bank') {
    //     return redirect()->route('admin.case.caseStatus', ['status' => 'aaa','user_id' => 0]);
    // }

    if ($request->FromDate) {
        $FromDate = $request->FromDate;
        $ToDate = $request->ToDate;
    } else {
        $FromDate = $this->FromDate;
        $ToDate = $this->ToDate;
    }
    $currentAdminId = Auth::guard('admin')->user()->id;
    if ($this->user->role == 'Vendor') {
    $currentAdminId = Auth::guard('admin')->user()->id; // Define $currentAdminId

    $data = DB::table('cases_fi_types')
        ->select([
            'cases_fi_types.user_id',
            'users.name AS agent_name',
            'cases.created_by',
            DB::raw('COUNT(cases_fi_types.status) AS total_cases'),
            DB::raw('SUM(cases_fi_types.status IS NULL) AS unassigned'),
            DB::raw('SUM(cases_fi_types.status = "0") AS new'),
            DB::raw('SUM(cases_fi_types.status = "1") AS inprogress'),
            DB::raw('SUM(cases_fi_types.status = "2") AS positive_resolve'),
            DB::raw('SUM(cases_fi_types.status = "3") AS negative_resolve'),
            DB::raw('SUM(cases_fi_types.status = "4" AND DATE(cases_fi_types.updated_at) = CURDATE()) AS positive_verified'),
            DB::raw('SUM(cases_fi_types.status = "5" AND DATE(cases_fi_types.updated_at) = CURDATE()) AS negative_verified'),
            DB::raw('SUM(cases_fi_types.status = "6") AS hold'),
            DB::raw('SUM(cases_fi_types.status = "7" AND DATE(cases_fi_types.updated_at) = CURDATE()) AS closed'),
        ])
        ->leftJoin('cases', 'cases_fi_types.case_id', '=', 'cases.id')
        ->leftJoin('users', 'cases_fi_types.user_id', '=', 'users.id')
        ->whereRaw("FIND_IN_SET(?, users.admin_access)", [$currentAdminId]) // Raw SQL alternative
        ->groupBy('cases_fi_types.user_id', 'cases.created_by', 'users.name')
        ->get();

    return view('backend.pages.dashboard.index', compact('data'));
}

    $total_roles       = Role::count();
    $total_admins      = Admin::count();
    $total_permissions = Permission::count();

    $queryUnassigned = casesFiType::query();
    if (Auth::guard('admin')->user()->role != 'superadmin') {
        $assignBank = explode(',', Auth::guard('admin')->user()->banks_assign);
        $queryUnassigned->whereHas('getCase', function ($query) use ($assignBank) {
            if (Auth::guard('admin')->user()->role == 'Bank') {
                $query->where("created_by", $this->user->id);
            }
            $query->whereIn("bank_id", $assignBank);
        });
    }
    $total_Unassigned = $queryUnassigned->where('status', '0')->count();
    $total_dedup = casesFiType::where('status', '8')->count();

    $assignedBank = is_string($this->user->banks_assign) && strpos($this->user->banks_assign, ',') !== false
        ? explode(',', $this->user->banks_assign)
        : [$this->user->banks_assign];

    $allroles = [];
    foreach ($this->user->roles as $key => $value) {
        $allroles[$key] = $value->id;
    }

    if ($this->user->role == 'superadmin') {
        // Admin with role 1 sees all records.
        $getCases = casesFiType::with('getCase', 'getuser')
            ->where('user_id', '!=', '0')
            ->get();
    } else {
        // Other admins see only their assigned users' cases.
        $getCases = casesFiType::with('getCase', 'getuser')
            ->whereHas('getuser', function ($query) use ($currentAdminId) {
                $query->whereRaw("FIND_IN_SET(?, admin_access)", [$currentAdminId]);
            })
            ->where('user_id', '!=', '0')
            ->whereHas('getCase', function ($query) use ($assignedBank) {
                $query->whereIn('bank_id', $assignedBank);
            })
            ->get();
    }

    $getUserWithCase = User::with('getcasesWithFiType')->get();

    $userwise = [];
    if (!empty($getCases)) {
        $cases = $getCases->toArray();
        $userwise = $this->_group_by($cases, 'user_id');
    }

    $userlist = [];
    foreach ($userwise as $userId => $userDetails) {
        foreach ($userDetails as $detail) {
            $userlist[] = [
                'user_id' => $userId,
                'name' => isset($detail['getuser']['name']) ? $detail['getuser']['name'] : 'Unknown',
            ];
        }
    }

    $agentLists = array_column($userlist, 'name', 'user_id');

    $userCount = [];
    $totalSum = [];
    $total = $inprogressTotal = $positive_resolvedTotal = $negative_resolvedTotal = $positive_verifiedTotal = $negative_verifiedTotal = $holdTotal = $closeTotal = 0;

    if ($userwise) {
        foreach ($userwise as $key => $userData) {
            $inprogress = $positive_resolved = $negative_resolved = $positive_verified = $negative_verified = $hold = $close = 0;
            $agentName = isset($userData[0]['getuser']['name']) ? $userData[0]['getuser']['name'] : 'Unknown';
            $agentid = isset($userData[0]['getuser']['id']) ? $userData[0]['getuser']['id'] : null;

            $today = new DateTime();
            foreach ($userData as $data) {
                $updatedAt = isset($data['updated_at']) ? new DateTime($data['updated_at']) : null;

                switch ($data['status']) {
                    case 1:
                        $inprogress += 1;
                        break;

                    case 2:
                        $positive_resolved += 1;
                        break;

                    case 3:
                        $negative_resolved += 1;
                        break;

                    case 4:
                        if ($updatedAt && $updatedAt->format('Y-m-d') === $today->format('Y-m-d')) {
                            $positive_verified += 1;
                        }
                        break;

                    case 5:
                        if ($updatedAt && $updatedAt->format('Y-m-d') === $today->format('Y-m-d')) {
                            $negative_verified += 1;
                        }
                        break;

                    case 6:
                        $hold += 1;
                        break;

                    case 7:
                        if ($updatedAt && $updatedAt->format('Y-m-d') === $today->format('Y-m-d')) {
                            $close += 1;
                        }
                        break;

                    default:
                        break;
                }
            }

            $userCount[$key]['created_by'] = $key;
            $userCount[$key]['agentid'] = $agentid;
            $userCount[$key]['agentName'] = $agentName;
            $userCount[$key]['inprogress'] = $inprogress;
            $userCount[$key]['positive_resolved'] = $positive_resolved;
            $userCount[$key]['negative_resolved'] = $negative_resolved;
            $userCount[$key]['positive_verified'] = $positive_verified;
            $userCount[$key]['negative_verified'] = $negative_verified;
            $userCount[$key]['hold'] = $hold;
            $userCount[$key]['close'] = $close;
            $userCount[$key]['total'] = $inprogress + $positive_resolved + $negative_resolved + $positive_verified + $negative_verified + $hold + $close;

            $total += $inprogress + $positive_resolved + $negative_resolved + $positive_verified + $negative_verified + $hold + $close;
            $inprogressTotal += $inprogress;
            $positive_resolvedTotal += $positive_resolved;
            $negative_resolvedTotal += $negative_resolved;
            $positive_verifiedTotal += $positive_verified;
            $negative_verifiedTotal += $negative_verified;
            $holdTotal += $hold;
            $closeTotal += $close;
        }
    }

    $totalSum = [
        'total' => $total,
        'inprogressTotal' => $inprogressTotal,
        'positive_resolvedTotal' => $positive_resolvedTotal,
        'negative_resolvedTotal' => $negative_resolvedTotal,
        'positive_verifiedTotal' => $positive_verifiedTotal,
        'negative_verifiedTotal' => $negative_verifiedTotal,
        'holdTotal' => $holdTotal,
        'closeTotal' => $closeTotal,
    ];

    return view('backend.pages.dashboard.index', compact('totalSum', 'userCount', 'agentLists', 'total_Unassigned', 'total_dedup', 'getUserWithCase'));
}

public function filter(Request $request)
{
    $agent = $request->input('agent');
    $fromDate = $request->input('fromDate');
    $toDate = $request->input('toDate');

    // Fetch cases with necessary relationships
    $getCases = casesFiType::with(['getCase', 'getuser']);

    // Apply filters
    if ($fromDate) {
        $getCases->whereDate('created_at', '>=', $fromDate);
    }
    if ($toDate) {
        $getCases->whereDate('created_at', '<=', $toDate);
    }
    if ($agent) {
        $getCases->where('user_id', $agent);
    }

    $getCases = $getCases->get();

    // Group data by user_id
    $userwise = $getCases->groupBy('user_id');
    $userCount = [];
    $totalSum = [
        'total' => 0,
        'inprogressTotal' => 0,
        'positive_resolvedTotal' => 0,
        'negative_resolvedTotal' => 0,
        'positive_verifiedTotal' => 0,
        'negative_verifiedTotal' => 0,
        'holdTotal' => 0,
        'closeTotal' => 0,
    ];

    foreach ($userwise as $key => $userData) {
        $agent = $userData->first()->getuser;
        if (!$agent) {
            continue;
        }

        $agentName = $agent->name ?? 'Unknown';
        $agentid = $agent->id ?? null;

        // Count cases by status
        $statusCounts = array_count_values($userData->pluck('status')->toArray());

        $userCount[$key] = [
            'created_by' => $key,
            'agentid' => $agentid,
            'agentName' => $agentName,
            'inprogress' => $statusCounts[1] ?? 0,
            'positive_resolved' => $statusCounts[2] ?? 0,
            'negative_resolved' => $statusCounts[3] ?? 0,
            'positive_verified' => $statusCounts[4] ?? 0,
            'negative_verified' => $statusCounts[5] ?? 0,
            'hold' => $statusCounts[6] ?? 0,
            'close' => $statusCounts[7] ?? 0,
            'total' => array_sum($statusCounts),
        ];

        // Update total summaries
        foreach (['inprogress', 'positive_resolved', 'negative_resolved', 'positive_verified', 'negative_verified', 'hold', 'close'] as $status) {
            $totalSum[$status . 'Total'] += $userCount[$key][$status];
        }
        $totalSum['total'] += $userCount[$key]['total'];
    }

    return view('backend.pages.dashboard.dashboardtable', compact('totalSum', 'userCount'));
}

/* 
    public function filter(Request $request)
    {
        $agent = $request->input('agent');
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        // Fetch all cases with necessary relationships
        $getCases = casesFiType::with(['getCase', 'getuser']);

        // Apply filters
        if ($fromDate) {
            $getCases->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $getCases->whereDate('created_at', '<=', $toDate);
        }
        if ($agent) {
            $getCases->where('user_id', $agent);
        }

        $getCases = $getCases->get();

        // Group data by user_id
        $userwise = $getCases->groupBy('user_id');
        $userCount = [];
        $totalSum = [
            'total' => 0,
            'inprogressTotal' => 0,
            'positive_resolvedTotal' => 0,
            'negative_resolvedTotal' => 0,
            'positive_verifiedTotal' => 0,
            'negative_verifiedTotal' => 0,
            'holdTotal' => 0,
        ];

        foreach ($userwise as $key => $userData) {
            $agent = $userData->first()->getuser;
            if (!$agent) {
                continue;
            }

            $agentName = $agent['name'];
            $agentid = $agent['id'];

            // Count cases by status
            $statusCounts = array_count_values($userData->pluck('status')->toArray());

            $userCount[$key] = [
                'created_by' => $key,
                'agentid' => $agentid,
                'agentName' => $agentName,
                'inprogress' => $statusCounts[1] ?? 0,
                'positive_resolved' => $statusCounts[2] ?? 0,
                'negative_resolved' => $statusCounts[3] ?? 0,
                'positive_verified' => $statusCounts[4] ?? 0,
                'negative_verified' => $statusCounts[5] ?? 0,
                'hold' => $statusCounts[6] ?? 0,
                'total' => array_sum($statusCounts),
            ];

            // Update total summaries
            $totalSum['total'] += $userCount[$key]['total'];
            $totalSum['inprogressTotal'] += $userCount[$key]['inprogress'];
            $totalSum['positive_resolvedTotal'] += $userCount[$key]['positive_resolved'];
            $totalSum['negative_resolvedTotal'] += $userCount[$key]['negative_resolved'];
            $totalSum['positive_verifiedTotal'] += $userCount[$key]['positive_verified'];
            $totalSum['negative_verifiedTotal'] += $userCount[$key]['negative_verified'];
            $totalSum['holdTotal'] += $userCount[$key]['hold'];
        }

        return view('backend.pages.dashboard.dashboardtable', compact('totalSum', 'userCount'));
    } */

}
