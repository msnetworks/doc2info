@extends('backend.layouts.master')

@section('title')
Create Case Create - Admin Panel
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .form-check-label {
        text-transform: capitalize;
    }
    .error {
        color: #dc3545;
        font-size: 0.875em;
    }
    .is-invalid {
        border-color: #dc3545;
    }
</style>
@endsection

@section('admin-content')
<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Create Case Create</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.fitypes.index') }}">All Create Case</a></li>
                    <li><span>Create Case</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>
<!-- page title area end -->

<div class="main-content-inner">
    <div class="row">
        <!-- data table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Create Case</h4>
                    @include('backend.layouts.partials.messages')

                    <form action="{{ route('admin.case.store') }}" method="POST" id="caseForm">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="name">Bank <span class="text-danger">*</span></label>
                                <select class="custom-select selectBank" name="bank_id" id="selectBank" required>
                                    <option value="">--Select Option--</option>
                                    @foreach ($banks as $bank)
                                    <option value="{{ $bank['id'] }}">{{ $bank['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="name">Branch Code <span class="text-danger">*</span></label>
                                <select class="custom-select branchDropdown" name="branch_code" id="branchDropdown" required>
                                    <option value="">--Select Option--</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="name">Product <span class="text-danger">*</span></label>
                                <select id="productSelect" name="product_id" class="custom-select" required>
                                    <option value="">--Select Option--</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="name">FI Type <span class="text-danger">*</span></label>
                                @foreach ($fitypes as $key => $fitype)
                                <div class="form-check">
                                    @php $fiCode = strtolower($fitype['fi_code']); @endphp
                                    <input class="form-check-input" type="checkbox" 
                                           id="Checkbox{{ $key }}" 
                                           name="fi_type_id[{{ $key }}][id]" 
                                           value="{{ $fitype['id'] }}" 
                                           data-fi-code="{{ $fitype['fi_code'] }}">
                                    <label class="form-check-label" for="Checkbox{{ $key }}">
                                        {{ $fitype['name'] }}
                                    </label>
                                </div>
                                @endforeach

                                <span id="fitype-error" class="error d-none">Please select at least one FI Type</span>
                            </div>
                            {!! $fitypesFeild !!}
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="application_type">Application Type <span class="text-danger">*</span></label>
                                <select class="custom-select application_type" name="application_type" id="application_type" required>
                                    <option value="">--Select Option--</option>
                                    @foreach ($ApplicationTypes as $ApplicationType)
                                    <option value="{{ $ApplicationType['id'] }}">{{ $ApplicationType['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- FI Type Specific Fields -->
                        <div id="fieldrv" class="fieldSet" style="display:none;">
                            <h4>RV</h4>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="v_address7" type="text" placeholder="RV Address" data-fi-type="7">
                                    <span class="error d-none" id="v_address7-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="v_pincode7" type="number" placeholder="RV Pincode (6 digits)" data-fi-type="7" maxlength="6">
                                    <span class="error d-none" id="v_pincode7-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="mobile7" type="number" placeholder="RV Phone Number (10 digits)" data-fi-type="7" maxlength="10">
                                    <span class="error d-none" id="mobile7-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="v_landmark7" type="text" placeholder="RV Landmark" data-fi-type="7">
                                </div>
                            </div>
                        </div>
                        
                        <div id="fieldbv" class="fieldSet" style="display:none;">
                            <h4>BV</h4>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="v_address8" type="text" placeholder="BV Address" data-fi-type="8">
                                    <span class="error d-none" id="v_address8-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="v_pincode8" type="number" placeholder="BV Pincode (6 digits)" data-fi-type="8" maxlength="6">
                                    <span class="error d-none" id="v_pincode8-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="mobile8" type="number" placeholder="BV Phone Number (10 digits)" data-fi-type="8" maxlength="10">
                                    <span class="error d-none" id="mobile8-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="v_landmark8" type="text" placeholder="BV Landmark" data-fi-type="8">
                                </div>
                            </div>
                        </div>
                        
                        <div id="fielditr" class="fieldSet" style="display:none;">
                            <h4>ITR</h4>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="pan_number12" type="text" placeholder="ITR Pancard" data-fi-type="12">
                                    <span class="error d-none" id="pan_number12-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="assessment_year12" type="text" placeholder="ITR Assessment Year" data-fi-type="12">
                                    <span class="error d-none" id="assessment_year12-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="mobile12" type="number" placeholder="ITR Mobile Number (10 digits)" data-fi-type="12" maxlength="10">
                                    <span class="error d-none" id="mobile12-error"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div id="fieldbanking" class="fieldSet" style="display:none;">
                            <h4>Banking</h4>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="accountnumber13" type="text" placeholder="Banking Account Number" data-fi-type="13">
                                    <span class="error d-none" id="accountnumber13-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="mobile13" type="number" placeholder="Banking Mobile Number (10 digits)" data-fi-type="13" maxlength="10">
                                    <span class="error d-none" id="mobile13-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="bank_name13" type="text" placeholder="Verify Bank Name" data-fi-type="13">
                                    <span class="error d-none" id="bank_name13-error"></span>
                                </div>
                            </div>
                        </div>
                        <div id="fieldsalary" class="fieldSet" style="display:none;">
                            <h4>Salary</h4>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="accountnumber45" type="text" placeholder="Company Name" data-fi-type="45">
                                    <span class="error d-none" id="accountnumber45-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="mobile45" type="number" placeholder="Mobile Number (10 digits)" data-fi-type="45" maxlength="10">
                                    <span class="error d-none" id="mobile45-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="bank_name45" type="text" placeholder="Salary Slip/Salary Certificate" data-fi-type="45">
                                    <span class="error d-none" id="bank_name45-error"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div id="fieldform_16" class="fieldSet" style="display:none;">
                            <h4>Form 16</h4>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="pan_number14" type="text" placeholder="Pan Card" data-fi-type="14">
                                    <span class="error d-none" id="pan_number14-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="assessment_year14" type="text" placeholder="Assignment Years" data-fi-type="14">
                                    <span class="error d-none" id="assessment_year14-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="mobile14" type="number" placeholder="Applicant's Mobile Number (10 digits)" data-fi-type="14" maxlength="10">
                                    <span class="error d-none" id="mobile14-error"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div id="fieldpan_card" class="fieldSet" style="display:none;">
                            <h4>Pancard</h4>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="pan_number17" type="text" placeholder="Pan Card" data-fi-type="17">
                                    <span class="error d-none" id="pan_number17-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="aadhar_number17" type="number" placeholder="Aadhar Number" data-fi-type="17" maxlength="12">
                                    <span class="error d-none" id="aadhar_number17-error"></span>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-control" name="mobile17" type="number" placeholder="Mobile Number (10 digits)" data-fi-type="17" maxlength="10">
                                    <span class="error d-none" id="mobile17-error"></span>
                                </div>
                            </div>
                        </div>
                        
                        <!--<div class="form-row">-->
                        <!--    <div class="form-group col-md-6 col-sm-12">-->
                        <!--        <label for="name">Amount</label>-->
                        <!--        <input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount') }}" placeholder="Enter Amount">-->
                        <!--    </div>-->
                        <!--</div>-->
                        

                        <!--<div class="form-row">-->
                            
                        <!--    <div class="form-group col-md-6 col-sm-12">-->
                        <!--        <label for="name">Amount</label>-->
                        <!--        <input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount') }}" placeholder="Enter Amount">-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                        
                        <div class="form-row">
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="tat_start">TAT Start</label>
                                <input type="datetime-local" class="form-control" id="tat_start" name="tat_start" value="{{ old('tat_start') }}">
                                <span class="error d-none" id="tat_start-error"></span>
                            </div>
                            
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="tat_end">TAT End</label>
                                <input type="datetime-local" class="form-control" id="tat_end" name="tat_end" value="{{ old('tat_end') }}">
                                <span class="error d-none" id="tat_end-error"></span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="name">Reference Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="refrence_number" name="refrence_number" value="{{ old('refrence_number') }}" placeholder=" Enter Reference Number" required>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="name">Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount') }}" placeholder=" Enter Amount" required>
                            </div>
                        </div>
                        <!-- Name fields -->
                        <div class="form-row">
                            <div class="form-group col-md-6 col-sm-12 name Applicant co_applicant_name d-none">
                                <label for="applicant_name">Applicant Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="applicant_name" value="{{ old('applicant_name') }}" placeholder="Enter Applicant Name">
                                <span class="error d-none" id="applicant_name-error"></span>
                            </div>
                            <div class="form-group col-md-6 col-sm-12 name co_applicant_name d-none">
                                <label for="co_applicant_name">Co-Applicant Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="co_applicant_name" value="{{ old('co_applicant_name') }}" placeholder="Enter Co-Applicant Name">
                                <span class="error d-none" id="co_applicant_name-error"></span>
                            </div>
                            <div class="form-group col-md-6 col-sm-12 name Guranter d-none">
                                <label for="guarantee_name">Guarantee Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="guarantee_name" value="{{ old('guarantee_name') }}" placeholder="Enter Guarantee Name">
                                <span class="error d-none" id="guarantee_name-error"></span>
                            </div>

                            <div class="form-group col-md-6 col-sm-12 name Seller d-none">
                                <label for="seller_name">Seller Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="seller_name" value="{{ old('seller_name') }}" placeholder="Enter Seller Name">
                                <span class="error d-none" id="seller_name-error"></span>
                            </div>
                        </div>
                        <!-- ... -->
                        
                        <div class="form-row">
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="geo_limit">Geo Limit</label>
                                <select id="geo_limit" name="geo_limit" class="custom-select">
                                    <option value="">--Select Option--</option>
                                    <option value="Local">Local</option>
                                    <option value="Outstation">Outstation</option>
                                </select>
                                <span class="error d-none" id="geo_limit-error"></span>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="user_assign">Assign Agent User  <span class="text-danger">{{ auth()->check() && auth()->user()->role == 'Vendor' ? '*' : '' }}</span></label>
                                <select id="user_assign" name="user_assign" class="custom-select" {{ auth()->check() && auth()->user()->role == 'Vendor' ? 'required' : '' }}>
                                    <option value="">--Select Option--</option>
                                    @foreach($agentusers as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                                <span class="error d-none" id="user_assign-error"></span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="remarks">Remarks</label>
                                <textarea name="remarks" rows="2" cols="20" id="remarks" class="form-control" placeholder="Remarks"></textarea>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">Save Case</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- data table end -->
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.selectBank, .branchDropdown, #productSelect, #application_type, #geo_limit').select2();

        $('#application_type').on('change', function(e) {
            var selectedApplicationType = $(this).find("option:selected").text();
            $('.name').addClass('d-none');
            if (selectedApplicationType == 'Applicant/Co-Applicant') {
                $(".co_applicant_name").removeClass('d-none');
            } else {
                $('.' + selectedApplicationType).removeClass('d-none');
            }
        });

        // Branch dropdown handler
        $('#selectBank').on('change', function(e) {
            var bankId = $(this).val();
            var customGetPath = "{{ route('admin.case.item','ID')}}";
            customGetPath = customGetPath.replace('ID', bankId);
            $.ajax({
                url: customGetPath,
                type: 'GET',
                success: function(response) {
                    var select = $('#productSelect');
                    select.empty(); // Clear any existing options
                    select.append('<option value="">--Select Option--</option>'); // Add default option

                    $.each(response, function(key, products) {
                        $.each(products, function(index, product) {
                            console.log(product);
                            var option = $('<option></option>')
                                .attr('value', product.product_id)
                                .text(product.name + ' (' + product.product_code + ')');
                            select.append(option);
                        });
                    });
                },
                error: function() {
                    alert('Request failed');
                }
            });
        });
        // ...
        // Branch dropdown handler
        $('#selectBank').change(function() {
            var bankId = $(this).val();
            var branchDropdown = $('#branchDropdown');

            if (bankId) {
                branchDropdown.html('<option value="">Loading...</option>');

                $.ajax({
                    url: '{{ route('get.branches', ':bankId') }}'.replace(':bankId', bankId),
                    type: 'GET',
                    success: function(data) {
                        branchDropdown.empty().append('<option value="">--Select Option--</option>');
                        $.each(data, function(key, branch) {
                            branchDropdown.append('<option value="' + branch.id + '">' + branch.branch_code + '</option>');
                        });
                    },
                    error: function() {
                        alert('Unable to fetch branch codes. Please try again.');
                    }
                });
            } else {
                branchDropdown.html('<option value="">--Select Option--</option>');
            }
        });
        // Form validation
        $.validator.addMethod("panFormat", function(value, element) {
            return this.optional(element) || /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(value);
        }, "Please enter a valid PAN number");

        $.validator.addMethod("aadharFormat", function(value, element) {
            return this.optional(element) || /^\d{12}$/.test(value);
        }, "Please enter a valid 12-digit Aadhar number");

        $.validator.addMethod("mobileFormat", function(value, element) {
            return this.optional(element) || /^\d{10}$/.test(value);
        }, "Please enter a valid 10-digit mobile number");

        $.validator.addMethod("pincodeFormat", function(value, element) {
            return this.optional(element) || /^\d{6}$/.test(value);
        }, "Please enter a valid 6-digit pincode");

        $("#caseForm").validate({
            rules: {
                bank_id: "required",
                branch_code: "required",
                product_id: "required",
                application_type: "required",
                applicant_name: {
                    required: function() {
                        return $("#application_type").val() == '1' || $("#application_type").val() == '2';
                    }
                },
                co_applicant_name: {
                    required: function() {
                        return $("#application_type").val() == '2';
                    }
                },
                guarantee_name: {
                    required: function() {
                        return $("#application_type").val() == '3';
                    }
                },
                seller_name: {
                    required: function() {
                        return $("#application_type").val() == '4';
                    }
                }
            },
            messages: {
                bank_id: "Please select a bank",
                branch_code: "Please select a branch",
                product_id: "Please select a product",
                application_type: "Please select an application type",
                applicant_name: "Please enter applicant name",
                co_applicant_name: "Please enter co-applicant name",
                guarantee_name: "Please enter guarantee name",
                seller_name: "Please enter seller name",
                
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") === "fi_type_id[][id]") {
                    error.appendTo("#fitype-error").removeClass('d-none');
                } else {
                    var errorElement = $("#" + element.attr("name") + "-error");
                    if (errorElement.length) {
                        errorElement.text(error.text()).removeClass('d-none');
                    } else {
                        error.insertAfter(element);
                    }
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
                $("#" + $(element).attr("name") + "-error").removeClass('d-none');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
                $("#" + $(element).attr("name") + "-error").addClass('d-none');
            },
            submitHandler: function(form) {
                // Validate at least one FI Type is selected
                if ($('.form-check-input:checked').length === 0) {
                    $('#fitype-error').removeClass('d-none');
                    return false;
                }
                form.submit();
            }
        });

 

        // Custom method to validate TAT end date is after start date (only if both are filled)
        $.validator.addMethod("greaterThan", function(value, element, params) {
            if (!value || !$(params).val()) return true; // Skip if either field is empty
            if (!/Invalid|NaN/.test(new Date(value))) {
                return new Date(value) > new Date($(params).val());
            }
            return isNaN(value) && isNaN($(params).val()) 
                || (Number(value) > Number($(params).val())); 
        }, 'End date must be after start date');

        // Add TAT validation only if both fields have values
        $('input[name="tat_start"], input[name="tat_end"]').change(function() {
            if ($('input[name="tat_start"]').val() && $('input[name="tat_end"]').val()) {
                $("#caseForm").validate().element('input[name="tat_end"]');
            }
        });


        // Input formatting and validation
        $('input[name="pan_number12"], input[name="pan_number14"], input[name="pan_number17"], input[name="pan_card"]').on('input', function() {
            this.value = this.value.toUpperCase();
        });

        $('input[name="v_pincode7"], input[name="v_pincode8"]').on('input', function() {
            if (this.value.length > 6) {
                this.value = this.value.slice(0,6);
            }
        });

        $('input[name="mobile7"], input[name="mobile8"], input[name="mobile12"], input[name="mobile13"], input[name="mobile14"], input[name="mobile17"]').on('input', function() {
            if (this.value.length > 10) {
                this.value = this.value.slice(0,10);
            }
        });

        $('input[name="aadhar_number17"], input[name="aadhar_card"]').on('input', function() {
            if (this.value.length > 12) {
                this.value = this.value.slice(0,12);
            }
        });
    });

</script>
<script>
    $(document).ready(function() {
    // Handle FI Type checkbox changes
    $(document).on('change', '.form-check-input', function() {
        const fiCode = $(this).data('fi-code');
        const fieldSection = $('#field' + fiCode);
        
        if ($(this).is(':checked')) {
            fieldSection.show();
        } else {
            fieldSection.hide();
            // Clear all inputs in this section
            fieldSection.find('input').val('');
        }
    });

    // Initialize any pre-checked checkboxes (if needed)
    $('.form-check-input:checked').trigger('change');
});
</script>
@endsection