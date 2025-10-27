<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\HeroCase;
use App\Models\FiType;
use App\Models\Product;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HeroCasesController extends Controller
{
    public function index()
    {
        $cases = HeroCase::latest()->get();
        return view('backend.pages.hero_cases.index', compact('cases'));
    }

    public function create()
    {
        $products = Product::select('bpm.id', 'bpm.bank_id', 'bpm.product_id', 'products.name', 'products.product_code')
            ->leftJoin('bank_product_mappings as bpm', 'bpm.product_id', '=', 'products.id')
            ->where('bpm.bank_id', 11)
            ->where('products.status', '1')
            ->get();
        $states = State::all();
        $fitypes = FiType::all();
        return view('backend.pages.hero_cases.create', compact('products', 'fitypes', 'states'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ref_no' => 'required|unique:hero_cases',
            'product' => 'required|exists:products,id',
            'profiling' => 'nullable|string|max:255',
            'employer_business_name' => 'nullable|string|max:255',
            'fi_cpv_type' => 'nullable|exists:fi_types,id',
            'state' => 'required|exists:states,id',
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'mobile_no' => 'required|string|max:20',
            'alt_mob_no' => 'nullable|string|max:20',
            'email_id' => 'nullable|email|max:255',
            'loan_amount' => 'required|numeric|min:0',
            'ownership_type' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        HeroCase::create($request->all());

        return response()->json(['success' => true, 'message' => 'Hero Case created successfully.']);
    }

    public function show($id)
    {
        $case = HeroCase::findOrFail($id);
        return view('backend.pages.hero_cases.show', compact('case'));
    }

    public function edit($id)
    {
        $case = HeroCase::findOrFail($id);
        $products = Product::select('bpm.id', 'bpm.bank_id', 'bpm.product_id', 'products.name', 'products.product_code')
            ->leftJoin('bank_product_mappings as bpm', 'bpm.product_id', '=', 'products.id')
            ->where('bpm.bank_id', 11)
            ->where('products.status', '1')
            ->get();
        $states = State::all();
        $fitypes = FiType::all();
        return view('backend.pages.hero_cases.edit', compact('case', 'products', 'fitypes', 'states'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ref_no' => 'required|unique:hero_cases,ref_no,' . $id,
            'product_id' => 'required|exists:products,id',
            'profiling' => 'nullable|string|max:255',
            'employer_business_name' => 'nullable|string|max:255',
            'fi_cpv_type' => 'nullable|exists:fi_types,id',
            'state' => 'required|exists:states,id',
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'mobile_no' => 'required|string|max:20',
            'alt_mob_no' => 'nullable|string|max:20',
            'email_id' => 'nullable|email|max:255',
            'loan_amount' => 'required|numeric|min:0',
            'ownership_type' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $case = HeroCase::findOrFail($id);
        $case->update($request->all());

        return response()->json(['success' => true, 'message' => 'Hero Case updated successfully.']);
    }

    public function destroy($id)
    {
        $case = HeroCase::findOrFail($id);
        $case->delete();

        return response()->json(['success' => true, 'message' => 'Hero Case deleted successfully.']);
    }
}