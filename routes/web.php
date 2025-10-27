<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\BranchCode;
use App\Models\HeroCase;
use App\Http\Controllers\Backend\HeroCaseDashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', 'HomeController@redirectAdmin')->name('index');
Route::get('/home', 'HomeController@index')->name('home');

/**
 * Admin routes
 */
// Route::get('/admin/cases/getProducts', [CaseController::class, 'getProducts']);

Route::group(['prefix' => 'admin'], function () {
    Route::get('/', 'Backend\DashboardController@index')->name('admin.dashboard');
    Route::post('/filter', 'Backend\DashboardController@filter')->name('admin.filter');
    Route::resource('cases', 'Backend\CasesController', ['names' => 'admin.case']);

    // Route::get('cases/create', 'Backend\CasesController@create')->name('admin.case.create');
    // Route::post('cases/store', 'Backend\CasesController@store')->name('admin.case.store');
    // Route::get('cases', 'Backend\CasesController@index')->name('admin.case.index');

    Route::get('cases/getList/{id}', 'Backend\CasesController@getItem')->name('admin.case.item');
    Route::get('cases/getcase/{id}', 'Backend\CasesController@getCase')->name('admin.case.getCase');
    Route::get('cases/getcaseHistory/{id}', 'Backend\CasesController@getcaseHistory')->name('admin.case.getcaseHistory');
    Route::get('cases/importExportView/{id}', 'Backend\CasesController@importExportView')->name('admin.case.import.view');
    Route::get('cases/export', 'Backend\CasesController@export')->name('admin.case.export');
    Route::post('cases/import', 'Backend\CasesController@import')->name('admin.case.import');

    // Document Import
    Route::get('cases/importDocumentView/{id?}', 'Backend\CasesController@importDocumentView')->name('admin.case.importDocument.view');
    Route::post('cases/importDocument', 'Backend\CasesController@importDocumentCases')->name('admin.case.importDocument');

    Route::get('cases/reinitatiate-case/{id}', 'Backend\CasesController@reinitatiateCaseNew')->name('admin.case.reinitatiateCaseNew');
    Route::post('cases/reinitatiate-case/store', 'Backend\CasesController@reinitatiateNew')->name('admin.case.reinitatiate.store');
    Route::get('cases/upload-image/{id}', 'Backend\CasesController@uploadCaseImage')->name('admin.case.upload.image');
    Route::post('cases/upload-image/{id}', 'Backend\CasesController@uploadImage')->name('admin.case.upload.image');
    Route::post('cases/delete-image/{id}', 'Backend\CasesController@deleteImage')->name('admin.case.delete.image');
    Route::get('cases/original-image/{id}', 'Backend\CasesController@originalCaseImage')->name('admin.case.original.image');

    Route::post('cases/assignAgent', 'Backend\CasesController@assignAgent')->name('admin.case.assignAgent');
    Route::post('cases/resolveCase', 'Backend\CasesController@resolveCase')->name('admin.case.resolveCase');
    Route::post('cases/verifiedCase', 'Backend\CasesController@verifiedCase')->name('admin.case.verifiedCase');
    Route::post('cases/updateConsolidated', 'Backend\CasesController@updateConsolidated')->name('admin.case.updateConsolidated');
    Route::get('cases/caseClose/{id}', 'Backend\CasesController@closeCase')->name('admin.case.close');
    Route::get('cases/clone/{id}', 'Backend\CasesController@cloneCase')->name('admin.case.clone');
    Route::get('cases/clone-full/{case_id}', 'Backend\CasesController@cloneFullCase')->name('admin.case.clone.full');
    Route::get('cases/hold/{id}', 'Backend\CasesController@holdCase')->name('admin.case.hold');
    Route::get('cases/delete/{id}', 'Backend\CasesController@deleteCase')->name('admin.case.delete');
    Route::get('cases/case-status/{status}/{user_id?}', 'Backend\CasesController@caseStatus')->name('admin.case.caseStatus');
    Route::get('cases/dedup-case/{case_id?}', 'Backend\CasesController@dedupCase')->name('admin.case.dedup-case');
    Route::get('cases/view/{id}', 'Backend\CasesController@viewCaseByCftId')->name('admin.case.viewCase');
    Route::get('cases/update/{id}', 'Backend\CasesController@viewCaseByCftId')->name('admin.case.updateCase');
    Route::get('cases/getdetail/{id}', 'Backend\CasesController@viewCase')->name('admin.case.viewCase');
    Route::get('cases/{id}/editCase', 'Backend\CasesController@editCase')->name('admin.case.editCase');
    Route::post('cases/update-case/{id}', 'Backend\CasesController@modifyCase')->name('admin.case.modifyCase');
    Route::get('cases/view-form/{id}', 'Backend\CasesController@getForm')->name('admin.case.viewForm');
    Route::get('cases/addTextToImage/{long}/{lati}', 'Backend\CasesController@addTextToImage')->name('admin.case.addTextToImage');
    Route::get('cases/fitypeUpdate/{id}', 'Backend\CasesController@updatefitype')->name('admin.case.fitype');
    Route::get('cases/branchcodeUpdate/{id}', 'Backend\CasesController@updatebranchcode')->name('admin.case.branchcode');

    
    Route::get('cases/view-form-edit/{id}', 'Backend\CasesController@modifyForm')->name('admin.case.viewForm.modify');
    // Route::post('cases/update-view-form-case/{id}', 'Backend\CasesController@modifyRVCase')->name('admin.case.modifyCase.viewCase');
    Route::post('cases/update-bv-form-case/{id}', 'Backend\CasesController@modifyBVCase')->name('admin.case.modifyBVCase');
    Route::post('cases/update-rv-form-case/{id}', 'Backend\CasesController@modifyRVCase')->name('admin.case.modifyRVCase');
    Route::post('cases/update-form16-case/{id}', 'Backend\CasesController@modifyForm16Case')->name('admin.case.modifyForm16Case');
    Route::get('cases/zip-download/{id}', 'Backend\CasesController@zipDownload')->name('admin.case.zip.download');
    Route::get('cases/export-excel/{status}/{user_id?}', 'Backend\CasesController@exportCase')->name('admin.case.export.excel');
    Route::get('cases/export-pdf/{id}', 'Backend\CasesController@generatePdf')->name('admin.case.export.pdf');



    Route::get('cases/assigned/{status}/{user_id?}', 'Backend\CasesController@assigned')->name('admin.case.assigned');
    Route::get('cases/detail/{id}', 'Backend\CasesController@viewCaseAssign')->name('admin.case.viewCaseAssign');

    Route::resource('reports', 'Backend\ReportsController', ['names' => 'admin.reports']);
    Route::post('/fetchreport', 'Backend\ReportsController@fetchreport')->name('fetchreport');
    Route::post('/fetchcountreport', 'Backend\ReportsController@fetchcountreport')->name('fetchcountreport');
    Route::get('/export-cases', 'Backend\ReportsController@export')->name('export.cases');
    Route::get('/export-casescount', 'Backend\ReportsController@exportcount')->name('export.casescount');
    Route::get('/count_report', 'Backend\ReportsController@countReport')->name('countReport');
    Route::resource('fitypes', 'Backend\FITypesController', ['names' => 'admin.fitypes']);
    Route::resource('products', 'Backend\ProductsController', ['names' => 'admin.products']);
    Route::resource('banks', 'Backend\BanksController', ['names' => 'admin.banks']);
    Route::resource('roles', 'Backend\RolesController', ['names' => 'admin.roles']);
    Route::resource('users', 'Backend\UsersController', ['names' => 'admin.users']);
    Route::get('users/agent/{id}', 'Backend\UsersController@getAgent')->name('admin.users.agent');
    Route::get('users/status/{type}/{parent_id?}', 'Backend\UsersController@getCaseStatus')->name('admin.users.caseStatus');

    Route::get('/branchcodes', 'Backend\BranchCodeController@index')->name('admin.branchcodes.index');
    Route::get('/branchcodes/create', 'Backend\BranchCodeController@create')->name('admin.branchcodes.create');
    Route::post('/branchcodes', 'Backend\BranchCodeController@store')->name('admin.branchcodes.store');
    Route::get('/branchcodes/edit/{id}', 'Backend\BranchCodeController@edit')->name('admin.branchcodes.edit');
    Route::put('/branchcodes/{id}', 'Backend\BranchCodeController@update')->name('admin.branchcodes.update');
    Route::delete('/branchcodes/{id}', 'Backend\BranchCodeController@destroy')->name('admin.branchcodes.destroy');
    Route::get('/get-branches/{bank_id}', function ($bank_id) {
        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->role === 'Bank') {
            $assignBranch = explode(',', Auth::guard('admin')->user()->branch_assign);
            $branches = BranchCode::where('bank_id', $bank_id)->whereIn('id', $assignBranch)->get();
        }else{
            $branches = BranchCode::where('bank_id', $bank_id)->get();
        }
        return response()->json($branches);
    })->name('get.branches');
    // Route::get('users/agent/{id}', 'Backend\UsersController@getAgent')->name('admin.users.agent');
    Route::resource('admins', 'Backend\AdminsController', ['names' => 'admin.admins']);

    // Login Routes
    Route::get('/login', 'Backend\Auth\LoginController@showLoginForm')->name('admin.login');
    Route::post('/login/submit', 'Backend\Auth\LoginController@login')->name('admin.login.submit');

    // Logout Routes
    Route::post('/logout/submit', 'Backend\Auth\LoginController@logout')->name('admin.logout.submit');

    // Forget Password Routes
    Route::get('/password/reset', 'Backend\Auth\ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::post('/password/reset/submit', 'Backend\Auth\ForgotPasswordController@reset')->name('admin.password.update');

    // Hero Case
    Route::resource('hero_cases', 'Backend\HeroCasesController', ['names' => 'admin.hero_cases']);
    Route::get('/hero-cases-dashboard', 'Backend\HeroCaseDashboardController@index')->name('admin.hero_cases_dashboard.index');
    Route::post('/hero-cases/filter-data', [HeroCaseDashboardController::class, 'getData'])->name('admin.hero-cases.filter-data');
    // Route for displaying the edit form for a specific Hero Case
    Route::get('/{hero_case}/edit', 'Backend\HeroCaseDashboardController@edit')->name('hero-cases.edit');

    // Route for handling the submission of the edit form
    Route::put('/{hero_case}', 'Backend\HeroCaseDashboardController@update')->name('hero-cases.update');
    Route::get('/hero-cases/details', [HeroCaseDashboardController::class, 'showCaseDetails'])->name('admin.hero-cases.details');
    Route::get('/{id}/verification', [HeroCaseDashboardController::class, 'editVerification'])->name('hero-cases.verification.edit');
    Route::post('/{id}/verification', [HeroCaseDashboardController::class, 'updateVerification'])->name('hero-cases.verification.update');
    Route::get('/{id}/appointment', [HeroCaseDashboardController::class, 'editAppointment'])->name('hero-cases.appointment.edit');
    Route::post('/{id}/appointment', [HeroCaseDashboardController::class, 'updateAppointment'])->name('hero-cases.appointment.update');
    Route::get('/{id}/reschedule', [HeroCaseDashboardController::class, 'editReschedule'])->name('hero-cases.reschedule.edit');
    Route::post('/{id}/reschedule', [HeroCaseDashboardController::class, 'updateReschedule'])->name('hero-cases.reschedule.update');
    Route::get('/{id}/show', [HeroCaseDashboardController::class, 'show'])->name('hero-cases.show');
    Route::get('/{id}/download-pdf', [HeroCaseDashboardController::class, 'downloadVerificationPdf'])->name('hero-cases.download.pdf');
    Route::get('/hero-cases/update-rescheduled/direct', function () {
        HeroCase::where('status', '1156')->update(['status' => '1']);

        return redirect()->route('admin.hero_cases_dashboard.index')->with('success', 'Successfully updated all "Appointment Rescheduled" cases to "Inprogress".');
        })->name('admin.hero-cases.update-rescheduled-direct');
    

});
