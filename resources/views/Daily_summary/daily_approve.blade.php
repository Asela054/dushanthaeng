@extends('layouts.app')

@section('content')

    <main>
        <div class="page-header shadow">
             <div class="container-fluid d-none d-sm-block shadow">
                   @include('layouts.attendant&leave_nav_bar')
             </div>
             <div class="container-fluid">
                 <div class="page-header-content py-3 px-2">
                     <h1 class="page-header-title ">
                         <div class="page-header-icon"><i class="fa-light fa-calendar-pen"></i></div>
                         <span>Daily Approvels</span>
                     </h1>
                 </div>
             </div>
         </div>

         {{-- OT Approve Table --}}
        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-12 d-flex justify-content-between align-items-center">
                              <h1 class="mb-0">OT Approve</h1>
                             <button class="btn btn-warning btn-sm filter-btn px-3" type="button"
                                    data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                                        class="fas fa-filter mr-1"></i> Filter
                                    Records</button>
                        </div>
                        <div class="col-12">
                            <hr class="border-dark">
                        </div>
                        <div class="col-6 mb-2">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input checkallocate" id="selectAll">
                                <label class="form-check-label" for="selectAll">Select All Records</label>
                            </div>
                        </div>
                        <div class="col-6 text-right">
                            <button type="button" class="btn btn-primary btn-sm float-right px-3" id="btn_approve_ot"><i
                                    class="fa-light fa-light fa-clipboard-check"></i>&nbsp;&nbsp;Approve OT</button><br>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                            <table class="table table-striped table-bordered table-sm small nowrap" id="ot_table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>EMP ID</th>
                                        <th>ETF NO</th>
                                        <th>EMPLOYEE</th>
                                        <th>DATE</th>
                                        <th>DAY</th>
                                        <th>FROM</th>
                                        <th>TO</th>
                                        <th>OT TIME</th>
                                        <th>D/OT TIME</th>
                                        <th>T/OT TIME</th>
                                        <th>IS HOLIDAY</th>
                                        <th>Holiday OT Time</th>
                                        <th>Holiday D/OT Time</th>
                                        <th>Sunday D/OT</th>
                                        <th>Poya Ex. OT</th>
                                        <th>Poya Days</th>
                                        <th>Mercantile Days</th>
                                        <th>Sundays</th>
                                    </tr>
                                </thead>
                                <tbody class="response">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
           
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
                    aria-labelledby="offcanvasRightLabel">
                    <div class="offcanvas-header">
                        <h2 class="offcanvas-title font-weight-bolder" id="offcanvasRightLabel">Records Filter Options</h2>
                        <button type="button" class="btn-close" data-dismiss="offcanvas" aria-label="Close">
                            <span aria-hidden="true" class="h1 font-weight-bolder">&times;</span>
                        </button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="list-unstyled">
                            <form class="form-horizontal" id="formFilter">
                                <li class="mb-2">
                                    <div class="col-md-12">
                                        <label class="small font-weight-bolder text-dark">Company</label>
                                        <select name="company" id="company" class="form-control form-control-sm">
                                        </select>
                                    </div>
                                </li>
                                <li class="mb-2">
                                    <div class="col-md-12">
                                        <label class="small font-weight-bolder text-dark">Department</label>
                                        <select name="department" id="department" class="form-control form-control-sm">
                                        </select>
                                    </div>
                                </li>
                                 <li class="mb-2">
                                    <div class="col-md-12">
                                        <label class="small font-weight-bolder text-dark">Location</label>
                                        <select name="location" id="location" class="form-control form-control-sm">
                                        </select>
                                    </div>
                                </li>
                                <li class="mb-2">
                                    <div class="col-md-12">
                                        <label class="small font-weight-bolder text-dark">Employee</label>
                                        <select name="employee" id="employee" class="form-control form-control-sm">
                                        </select>
                                    </div>
                                </li>
                                <li class="mb-2">
                                    <div class="col-md-12">
                                    <label class="small font-weight-bolder text-dark">Add/Deduct Type*</label>
                                        <select id="remuneration_name" name="remuneration_name" class="form-control form-control-sm" required>
                                        <option value="">Select Remuneration</option>
                                        @foreach ($remunerations as $remuneration){
                                            <option value="{{$remuneration->id}}" >{{$remuneration->remuneration_name}}</option>
                                        }  
                                        @endforeach
                                    </select>
                                    </div>
                                </li>
                                <li class="mb-2">
                                    <div class="col-md-12">
                                        <label class="small font-weight-bolder text-dark">Date</label>
                                        <input type="date" id="from_date" name="from_date" class="form-control form-control-sm"placeholder="yyyy-mm-dd">
                                    </div>
                                </li>
                                <li>
                                    <div class="col-md-12 d-flex justify-content-between">
                                        
                                        <button type="button" class="btn btn-danger btn-sm filter-btn px-3" id="btn-reset">
                                            <i class="fas fa-redo mr-1"></i> Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary btn-sm filter-btn px-3"
                                            id="btn-filter">
                                            <i class="fas fa-search mr-2"></i>Search
                                        </button>
                                    </div>
                                </li>
                            </form>
                        </ul>
                    </div>
            </div>
        </div>

         {{-- LATE Approve Table --}}
        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card mb-2">
                <div class="card-body p-0 p-2 ">
                     <div class="row">
                        <div class="col-md-12">

                            <div class="row align-items-center mb-4">
                                <div class="col-md-12">
                                   <h1 class="mb-0">Late Approve</h1>
                                </div>
                                 <div class="col-12">
                                    <hr class="border-dark">
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input checkallocate" id="selectAll_late">
                                        <label class="form-check-label" for="selectAll_late">Select All Records</label>
                                    </div>
                                </div>
                                <div class="col-6 text-right">
                                     <button id="approve_late" class="btn btn-primary float-right mt-2 btn-sm px-3"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp; Approve Late Attendance</button>
                                </div>
                            </div>
                           
                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap w-100"
                                    id="table_late">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>EMP ID</th>
                                            <th>EMPLOYEE</th>
                                            <th>DATE</th>
                                            <th>CHECK IN TIME</th>
                                            <th>CHECK OUT TIME</th>
                                            <th>WORKING HOURS</th>
                                            <th>LOCATION</th>
                                            <th>DEPARTMENT</th>
                                            <th>IS APPROVED ?</th>
                                            <th class="d-none">EMPNAME</th>
                                            <th class="d-none"> EMPID</th>
                                            <th class="d-none">CALLINGNAME</th>
                                        </tr>
                                    </thead>
                                    <tbody class="responselate">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </div>
            </div>

             <!-- approve modal -->
            <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Approve Late Attendances</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="message_modal"></div>
                            <form class="form-horizontal" id="formApprove">
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bolder text-dark">Leave Type</label>
                                    <select name="leave_type" id="leave_type" class="form-control form-control-sm">
                                        <option value="">Select Leave Type</option>
                                        @foreach($leave_types as $leave_type)
                                            <option value="{{ $leave_type->id }}">{{ $leave_type->leave_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm px-3" id="btn-approve-late"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp;Approve</button>
                        </div>
                    </div>
                </div>
           </div>
        </div>

        {{-- ABSENT Approve Table --}}
         <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2">
                    <div class="col-md-12">

                        <div class="row align-items-center mb-4">
                            <div class="col-md-12">
                                <h1 class="mb-0">Absent Nopay Approve</h1>
                            </div>
                            <div class="col-12">
                                <hr class="border-dark">
                            </div>
                            <div class="col-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input checkallocate" id="selectAllAbsent">
                                    <label class="form-check-label" for="selectAllAbsent">Select All Records</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button id="approve" class="btn btn-primary btn-sm float-right px-3"><i
                                        class="fas fa-plus mr-2"></i>Apply Nopay</button>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap w-100" style="width: 100%" id="dataTableAbsent">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>EMP ID </th>
                                            <th>EMPLOYEE NAME</th>
                                            <th class="d-none">Emp Auto ID</th>
                                        </tr>
                                    </thead>
                                    <tbody class="responsenopay">
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ATTENDANCE APPROVE TABLE --}}
        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2 main_card">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="row align-items-center mb-4">
                                    <div class="col-md-12">
                                         <h1 class="mb-0">Attendance Approve</h1>
                                    </div>
                                    <div class="col-12">
                                        <hr class="border-dark">
                                    </div>
                                    <div class="col-12 text-right">
                                        <button id="approve_att" class="btn btn-primary btn-sm px-3"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp;Approve All</button>
                                    </div>
                                </div>
                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap w-100" id="attendtable">
                                    <thead>
                                    <tr>
                                        <th>EMPLOYEE ID</th>
                                        <th>EMPLOYEE NAME</th>
                                        <th>WORK MONTH</th>
                                        <th>DEPARTMENT</th>
                                        <th>COMPANY</th>
                                        <th>WORKING WEEK DAYS</th>
                                        <th>WORKING HOURS</th>
                                        <th>LEAVE DAYS</th>
                                        <th>NO PAY DAYS</th>
                                        {{-- <th>Last Time Stamp</th> --}}
                                        {{-- <th>Action</th> --}}
                                    </tr>
                                    </thead>
                                    <tbody class="responseattendance_app"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Late Deduction Approve Modal --}}
        <div class="container-fluid mt-2 p-0 p-2">
            <div class="card">
                <div class="card-body p-0 p-2 main_card">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="row align-items-center mb-4">
                                 <div class="col-md-12">
                                         <h1 class="mb-0">Late Deduction Approval</h1>
                                    </div>
                                <div class="col-12">
                                    <hr class="border-dark">
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input checkallocate" id="selectAll_late_deduction">
                                        <label class="form-check-label" for="selectAll_late_deduction">Select All Records</label>
                                    </div>
                                </div>
                                <div class="col-6 text-right">
                                    <button id="approve_late_deduction" class="btn btn-primary btn-sm"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp;Approve All</button>
                                </div>
                            </div>

                            <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap display" style="width: 100%"  id="late_dedutable">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>EMPLOYEE ID</th>
                                        <th>EMPLOYEE NAME</th>
                                        <th>LATE MINITES TOTAL</th>
                                        <th>NOPAY AMOUNT</th>
                                        <th>TOTAL AMOUNT</th>
                                        <th class="d-none">EMPLOYEE auto ID</th>
                                    </tr>
                                    </thead>
                                    <tbody class="responselate_deduction">
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Salary Adjustment Approve Modal --}}
        <div class="container-fluid mt-2 p-0 p-2">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                                <div class="col-md-12">
                                    <div class="row align-items-center mb-4">
                                        <div class="col-md-12">
                                            <h1 class="mb-0">Salary Adjustments Approval</h1>
                                        </div>
                                        <div class="col-12">
                                            <hr class="border-dark">
                                        </div>

                                        <div class="col-6 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input checkallocate" id="selectAll_salary">
                                                <label class="form-check-label" for="selectAll_salary">Select All Records</label>
                                            </div>
                                        </div>
                                        <div class="col-6 text-right">
                                            <button id="approve_salary" class="btn btn-primary btn-sm px-3"><i class="fa-light fa-light fa-clipboard-check"></i>&nbsp;Approve All</button>
                                        </div>
                                    </div>
                                    
                                    <div class="center-block fix-width scroll-inner">
                                        <table class="table table-striped table-bordered table-sm small nowrap display"
                                            style="width: 100%" id="dataTable_salary">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>EMP ID </th>
                                                    <th>EMPLOYEE NAME</th>
                                                    <th>TOTAL WORKING DAYS</th>
                                                    <th>ALLOWANCE AMOUNT</th>
                                                    <th>ADDITION | DEDUCTION AMOUNT</th>
                                                    <th>REMAINING AMOUNT</th>
                                                    <th class="d-none">PAYROLL PROFILE</th>
                                                    <th class="d-none">TYPE</th>
                                                    <th class="d-none">REMUNITION</th>
                                                </tr>
                                            </thead>
                                            <tbody class="responseattendance_salary">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
        </div>

    </main>

@endsection

@section('script')

    <script>
        $(document).ready(function () {
            $('#attendant_menu_link').addClass('active');
            $('#attendant_menu_link_icon').addClass('active');
            $('#dailysummry').addClass('navbtnactive');

            let company = $('#company');
            let department = $('#department');
            let employee = $('#employee');
            let location = $('#location');

            company.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("company_list_sel2")}}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        }
                    },
                    cache: true
                }
            });

            department.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("department_list_sel2")}}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company.val()
                        }
                    },
                    cache: true
                }
            });

            employee.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("employee_list_sel2")}}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company.val(),
                            department: department.val(),
                            location: location.val()
                        }
                    },
                    cache: true
                }
            });

            location.select2({
                placeholder: 'Select...',
                width: '100%',
                allowClear: true,
                ajax: {
                    url: '{{url("location_list_sel2")}}',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            company: company.val()
                        }
                    },
                    cache: true
                }
            });

            $('.response').html(
                '<tr>' +
                '<td colspan="19" class="text-center py-5">' +
                '<div class="d-flex flex-column align-items-center">' +
                '<i class="fas fa-filter fa-3x text-muted mb-3"></i>' +
                '<h4 class="text-muted mb-2">No Records Found</h4>' +
                '<p class="text-muted">Use the filter options to get records</p>' +
                '</div>' +
                '</td>' +
                '</tr>'
            );

            $('.responselate').html(
                '<tr>' +
                '<td colspan="10" class="text-center py-5">' +
                '<div class="d-flex flex-column align-items-center">' +
                '<i class="fas fa-filter fa-3x text-muted mb-3"></i>' +
                '<h4 class="text-muted mb-2">No Records Found</h4>' +
                '<p class="text-muted">Use the filter options to get records</p>' +
                '</div>' +
                '</td>' +
                '</tr>'
            );

            $('.responsenopay').html(
                '<tr>' +
                '<td colspan="3" class="text-center py-5">' +
                '<div class="d-flex flex-column align-items-center">' +
                '<i class="fas fa-filter fa-3x text-muted mb-3"></i>' +
                '<h4 class="text-muted mb-2">No Records Found</h4>' +
                '<p class="text-muted">Use the filter options to get records</p>' +
                '</div>' +
                '</td>' +
                '</tr>'
            );

            $('.responseattendance_app').html(
                '<tr>' +
                '<td colspan="9" class="text-center py-5">' + // Changed colspan to 9 to match your columns
                '<div class="d-flex flex-column align-items-center">' +
                '<i class="fas fa-filter fa-3x text-muted mb-2"></i>' +
                '<h4 class="text-muted mb-2">No Records Found</h4>' +
                '<p class="text-muted">Use the filter options to get records</p>' +
                '</div>' +
                '</td>' +
                '</tr>'
            );

            $('.responselate_deduction').html(
                '<tr>' +
                '<td colspan="6" class="text-center py-5">' + // Changed colspan to 9 to match your columns
                '<div class="d-flex flex-column align-items-center">' +
                '<i class="fas fa-filter fa-3x text-muted mb-2"></i>' +
                '<h4 class="text-muted mb-2">No Records Found</h4>' +
                '<p class="text-muted">Use the filter options to get records</p>' +
                '</div>' +
                '</td>' +
                '</tr>'
            );
        
            $('.responseattendance_salary').html(
                '<tr>' +
                '<td colspan="7" class="text-center py-5">' + // Changed colspan to 9 to match your columns
                '<div class="d-flex flex-column align-items-center">' +
                '<i class="fas fa-filter fa-3x text-muted mb-2"></i>' +
                '<h4 class="text-muted mb-2">No Records Found</h4>' +
                '<p class="text-muted">Use the filter options to get records</p>' +
                '</div>' +
                '</td>' +
                '</tr>'
            );

            $('#formFilter').on('submit',function(e) {
                e.preventDefault();
                $('.info_msg').html('');

                let company = $('#company').val();
                let department = $('#department').val();
                let location = $('#location').val();
                let employee = $('#employee').val();
                let from_date = $('#from_date').val();

                let date_obj = from_date ? new Date(from_date) : null;
                let month = date_obj ? date_obj.getFullYear() + '-' + String(date_obj.getMonth() + 1).padStart(2, '0') : '';

              
                load_table(department, employee, location, from_date);
                load_dt_atte_Approve(company,department, month, from_date);
                load_dt_late_deduction_Approve(company,department, month, from_date)
                

                load_dt_late(department,company, location, from_date);
                closeOffcanvasSmoothly();

            });

            // OT Appove Table Load Function
            function load_table(department, employee, location, from_date) {
                

                $('.response').html('');
                let btn = $('#btn-filter');
                btn.attr('disabled', true);
                btn.html('<i class="fa fa-spinner fa-spin"></i>');

                $.ajax({
                    url: "{{ route('get_ot_details') }}",
                    method: "POST",
                    data: {
                        department: department,
                        employee: employee,
                        location: location,
                        from_date: from_date,
                        to_date: from_date,
                        _token: '{{csrf_token()}}'
                    },
                    success: function (res) {
                        btn.html('Filter');
                        btn.prop('disabled', false);

                         if ($.fn.DataTable.isDataTable('#ot_table')) {
                        $('#ot_table').DataTable().clear().destroy();
                    }
                    $('#ot_table tbody').empty();

                        let ot_data = res.ot_data;
                        let ot_data_html = '';
                        
                        if(ot_data.length > 0) {
                            ot_data.forEach(function(key, data) {
                                let is_approved = key.is_approved;
                                let obj = key.ot_breakdown;
                                let is_holiday = obj.is_holiday == 1 ? 'Yes' : 'No';
                                
                                let h_class = obj.is_morning ? 'bg-teal-light' : '';
                                
                                ot_data_html += '<tr class="'+h_class+'" >';
                                
                                if(is_approved == false){
                                    ot_data_html += '<td><input type="checkbox" class="cb" ' +
                                        'data-emp_id="'+obj.emp_id+'" ' +
                                        'data-date="'+obj.date+'" ' +
                                        ' /></td>';
                                }else{
                                    ot_data_html += '<td> <i class="fa fa-check text-success"> </i> </td>';
                                }
                                
                                ot_data_html += '<td>'+obj.emp_id+'</td>';
                                ot_data_html += '<td>'+obj.etf_no+'</td>';
                                ot_data_html += '<td>'+obj.name+'</td>';
                                ot_data_html += '<td>'+obj.date+'</td>';
                                ot_data_html += '<td>'+obj.day_name+'</td>';
                                ot_data_html += '<td>'+obj.from_24+'</td>';
                                ot_data_html += '<td>'+obj.to_24+'</td>';
                                ot_data_html += '<td>'+obj.hours +'</td>';
                                ot_data_html += '<td>'+obj.double_hours +'</td>';
                                ot_data_html += '<td>'+obj.triple_hours+'</td>';
                                ot_data_html += '<td>'+is_holiday+'</td>';
                                ot_data_html += '<td>'+obj.holiday_ot_hours+'</td>';
                                ot_data_html += '<td>'+obj.holiday_double_hours+'</td>';
                                ot_data_html += '<td>'+obj.sunday_double_ot_hours+'</td>';
                                ot_data_html += '<td>'+obj.poya_extend_ot+'</td>';
                                ot_data_html += '<td>'+obj.poya_work_days+'</td>';
                                ot_data_html += '<td>'+obj.mercantile_work_days+'</td>';
                                ot_data_html += '<td>'+obj.sunday_work_days+'</td>';
                                ot_data_html += '</tr>';
                            });
                        }
                        $('#ot_table tbody').html(ot_data_html);
                        
                        // Initialize DataTable with export buttons
                        $('#ot_table').DataTable({
                            destroy: true,
                            responsive: true,
                              dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                            "buttons": [{
                                    extend: 'csv',
                                    className: 'btn btn-success btn-sm',
                                    title: 'OT Approve Information',
                                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                                },
                                { 
                                    extend: 'pdf', 
                                    className: 'btn btn-danger btn-sm', 
                                    title: 'OT Approve Information', 
                                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                    orientation: 'landscape', 
                                    pageSize: 'legal', 
                                    customize: function(doc) {
                                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                    }
                                },
                                {
                                    extend: 'print',
                                    title: 'OT Approve  Information',
                                    className: 'btn btn-primary btn-sm',
                                    text: '<i class="fas fa-print mr-2"></i> Print',
                                    customize: function(win) {
                                        $(win.document.body).find('table')
                                            .addClass('compact')
                                            .css('font-size', 'inherit');
                                    },
                                },
                            ],
                            pageLength: 25,
                            order: [[1, 'desc']]
                        });
                        
                        $('.date_time').datetimepicker({
                            format:'Y-m-d H:i',
                            mask:false,
                        });
                    },
                    error: function(xhr, status, error) {
                        btn.html('Filter');
                        btn.prop('disabled', false);
                        console.error("Error:", error);
                    }
                });
            }

            $(document).on('click', '#btn_approve_ot', async function (e) {
                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {

                    let btn = $(this);

                    btn.attr('disabled', true);
                    btn.html('<i class="fa fa-spinner fa-spin"></i>');

                    let cb = $('.cb');
                    let ot_data = [];

                    cb.each(function (e1) {
                        let cb_obj = $(this);
                        if (cb_obj.is(':checked')) {
                            let emp_id = cb_obj.data('emp_id');
                            let date = cb_obj.data('date');

                        // Get the row containing this checkbox
                        let row = cb_obj.closest('tr');
                        
                        // Get text cells
                        let from_cell = row.find('td:eq(6)');
                        let to_cell = row.find('td:eq(7)');
                        let hours_cell = row.find('td:eq(8)');
                        let double_hours_cell = row.find('td:eq(9)');
                        let triple_hours_cell = row.find('td:eq(10)');
                        let is_holiday_cell = row.find('td:eq(11)');
                        
                        // Get input fields - FIXED: use row.find instead of cb_obj.find
                        let holiday_ot_hours_input = row.find('td:eq(12)');
                        let holiday_doubleot_hours_input = row.find('td:eq(13)');
                        let sunday_dot_input = row.find('td:eq(14)');
                        let poya_exot_input = row.find('td:eq(15)');
                        let paya_days_input = row.find('td:eq(16)');
                        let mercantile_days_input = row.find('td:eq(17)');
                        let sunday_days_input = row.find('td:eq(18)');
                        
                        // Get text values
                        let from = from_cell.text().trim();
                        let to = to_cell.text().trim();
                        let hours = hours_cell.text().trim();
                        let double_hours = double_hours_cell.text().trim();
                        let triple_hours = triple_hours_cell.text().trim();
                        let is_holiday = is_holiday_cell.text().trim();
                        


                        // Get input values - with fallback to 0 if empty
                         let holiday_ot_hours =holiday_ot_hours_input.text().trim();
                        let holiday_double_hours = holiday_doubleot_hours_input.text().trim();
                        let sunday_dot = sunday_dot_input.text().trim();
                        let poya_exot = poya_exot_input.text().trim();
                        let paya_days = paya_days_input.text().trim();
                        let mercantile_days = mercantile_days_input.text().trim();
                        let sunday_days = sunday_days_input.text().trim();

                            let ot_data_obj = {
                                emp_id: emp_id,
                                date: date,
                                from: from,
                                to: to,
                                hours: hours,
                                //one_point_five_hours: one_point_five_hours,
                                double_hours: double_hours,
                                triple_hours: triple_hours,
                                holiday_ot_hours: holiday_ot_hours,
                                holiday_double_hours: holiday_double_hours,
                                is_holiday: is_holiday,
                                sunday_dot: sunday_dot,
                                poya_exot: poya_exot,
                                paya_days: paya_days,
                                mercantile_days: mercantile_days,
                                sunday_days: sunday_days
                            }


                            ot_data.push(ot_data_obj);

                        }

                    });

                    if (ot_data.length > 0) {
                        $(btn).html('<i class="fa fa-spinner fa-spin"></i>');
                        $(btn).prop('disabled', true);

                        $.ajax({
                            url: "{{ route('ot_approve_post') }}",
                            method: "POST",
                            data: JSON.stringify({
                                ot_data: ot_data,
                                _token: '{{csrf_token()}}'
                            }),
                            contentType: "application/json",
                            processData: false,
                            success: function (res) {
                                if (res.errors) {
                                    const actionObj = {
                                        icon: 'fas fa-warning',
                                        title: '',
                                        message: 'Record Error',
                                        url: '',
                                        target: '_blank',
                                        type: 'danger'
                                    };
                                    const actionJSON = JSON.stringify(actionObj, null, 2);
                                    action(actionJSON);
                                }
                                if (res.success) {
                                    const actionObj = {
                                        icon: 'fas fa-save',
                                        title: '',
                                        message: res.success,
                                        url: '',
                                        target: '_blank',
                                        type: 'success'
                                    };
                                    const actionJSON = JSON.stringify(actionObj, null, 2);
                                    action(actionJSON);
                                }
                                btn.html('Approve');
                                btn.attr('disabled', false);
                            }
                        });
                    } else {
                        $('.info_msg').html('<div class="alert alert-danger">Please select at least one attendance</div>');
                        $('html, body').animate({
                            scrollTop: 100
                        }, 'fast');
                         btn.attr('disabled', false);
                    }
                }
            });

            $('#selectAll').click(function (e) {
                $('#ot_table').closest('table').find('td input:checkbox').prop('checked', this.checked);
            });


            // Late Attendance Approve 

            let selected_cb = [];

            function load_dt_late(department, company, location, from_date) {
                $('#table_late').DataTable({
                    "destroy": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Late Attendance Approve Information',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        {
                            extend: 'pdf',
                            className: 'btn btn-danger btn-sm',
                            title: 'Late Attendance Approve Information',
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape',
                            pageSize: 'legal',
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Late Attendance Approve  Information',
                            className: 'btn btn-primary btn-sm',
                            text: '<i class="fas fa-print mr-2"></i> Print',
                            customize: function(win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                    ],
                    ajax: {
                        url: scripturl + "/late_attendance_approve_list.php",
                        type: "POST",
                        data: {
                            'department': department,
                            'company': company,
                            'location': location,
                            'from_date': from_date,
                            'to_date': from_date,
                        },
                    },

                    columns: [
                        { data: 'id',
                            render: function (data, type, row, meta) {
                                if (type === 'display') {
                                    return '<div class="custom-control custom-checkbox">' +
                                        '<input type="checkbox" ' +
                                        'data-id="' + data + '" ' +
                                        'data-emp_name_with_initial="' + row["emp_name_with_initial"] + '" ' +
                                        'data-date="' + row["date"] + '" ' +
                                        'data-check_in_time="' + row["check_in_time"] + '" ' +
                                        'data-check_out_time="' + row["check_out_time"] + '" ' +
                                        'data-working_hours="' + row["working_hours"] + '" ' +
                                        'data-location_id="' + row["location_id"] + '" ' +
                                        'data-dept_id="' + row["dept_id"] + '" ' +
                                        'class="form-check-input cb" id="cb_' + data + '">' +
                                        '<label class="form-check-label" for="cb_' + data + '"></label>' +
                                        '</div>';
                                }
                                return data;
                            }},
                        {data: 'emp_id'},
                        {data: 'employee_display'},
                        {data: 'date'},
                        {data: 'check_in_time'},
                        {data: 'check_out_time'},
                        {data: 'working_hours'},
                        {data: 'location'},
                        {data: 'dept_name'},
                        {data: 'is_approved'},
                        {data: "emp_name_with_initial", "visible": false},
                        {data: "calling_name", "visible": false},
                        {data: "emp_id", "visible": false}
                    ],
                    "bDestroy": true,
                    "order": [[3, "desc"]],

                    "drawCallback": function(settings) {
                        check_changed_text_boxes();
                    }
                });
            }

            // Helper: build the selected object from a checkbox element
            function buildCbObject(checkbox) {
                return {
                    id: checkbox.data('id'),
                    emp_name_with_initial: checkbox.data('emp_name_with_initial'),
                    date: checkbox.data('date'),
                    check_in_time: checkbox.data('check_in_time'),
                    check_out_time: checkbox.data('check_out_time'),
                    working_hours: checkbox.data('working_hours'),
                    location_id: checkbox.data('location_id'),
                    dept_id: checkbox.data('dept_id'),
                    is_approved_int: checkbox.data('is_approved_int'),
                };
            }

            $('.responselate').on('click', '.cb', function () {
                let id = $(this).data('id');
                let b = buildCbObject($(this));

                if ($(this).is(':checked')) {
                    // FIX: id eken compare karanawa, object reference eken nemei
                    if (!selected_cb.some(item => item.id === id)) {
                        selected_cb.push(b);
                        $(this).closest('tr').css('background-color', '#f7c8c8');
                    }
                } else {
                    removeA(selected_cb, id);
                }
            });

            $(document).on('click', '#approve_late', async function (e) {
                e.preventDefault();
                var r = await Otherconfirmation("You want to Approve this ? ");
                if (r == true) {
                    $('.message_modal').html('');
                    $('#approveModal').modal('show');

                    $('#btn-approve-late').on('click', function (e) {
                        e.preventDefault();
                        $('.error_msg').remove();

                        let save_btn = $(this);
                        let leave_type = $('#leave_type').val();

                        if (leave_type == '') {
                            Swal.fire({
                                position: "top-end",
                                icon: 'warning',
                                title: 'Please select leave type!',
                                showConfirmButton: false,
                                timer: 2500
                            });
                            return false;
                        }

                        save_btn.prop("disabled", true);
                        save_btn.html('<i class="fa fa-spinner fa-spin"></i> loading...');
                        $.ajax({
                            url: "lateAttendance_mark_as_late_approve",
                            method: "POST",
                            data: {
                                'selected_cb': selected_cb,
                                'leave_type': leave_type,
                                _token: $('input[name=_token]').val(),
                            },
                             success: function (data) {
                                if(data.status == true){
                                    const actionObj = {
                                                icon: 'fas fa-save',
                                                title: '',
                                                message: data.msg,
                                                url: '',
                                                target: '_blank',
                                                type: 'success'
                                            };
                                            const actionJSON = JSON.stringify(actionObj, null, 2);
                                            action(actionJSON);
                                    $('#approveModal').modal('hide');
                                }else{
                                const actionObj = {
                                                icon: 'fas fa-warning',
                                                title: '',
                                                message: data.msg,
                                                url: '',
                                                target: '_blank',
                                                type: 'danger'
                                            };
                                            const actionJSON = JSON.stringify(actionObj, null, 2);
                                            action(actionJSON);
                                }

                                save_btn.prop("disabled", false);
                                save_btn.html('Approve' );
                            }
                        });
                    });
                }
            });

            function removeA(arr, id) {
                for (let i = arr.length - 1; i >= 0; i--) {
                    if (arr[i].id == id) {
                        arr.splice(i, 1);
                    }
                }
                let selector = $('.cb[data-id="' + id + '"]');
                selector.closest('tr').css('background-color', 'inherit');
            }

            function check_changed_text_boxes() {
                for (let a = 0; a < selected_cb.length; a++) {
                    let id = selected_cb[a]['id'];
                    let selector = $('.cb[data-id="' + id + '"]');
                    selector.prop("checked", true);
                    selector.closest('tr').css('background-color', '#f7c8c8');
                }
            }

            $(document).on('click', '#selectAll_late', function () {
                $('#table_late').closest('table').find('td input:checkbox').prop('checked', this.checked);
                let isChecked = $(this).is(':checked');
                let table = $('#table_late').DataTable();

                table.rows().every(function () {
                    let row = this.node();
                    let checkbox = $(row).find('.cb');

                    if (checkbox.length > 0) {
                        let id = checkbox.data('id');

                        if (isChecked) {
                            checkbox.prop('checked', true);
                            let b = buildCbObject(checkbox);

                            if (!selected_cb.some(item => item.id === id)) {
                                selected_cb.push(b);
                            }
                            $(row).css('background-color', '#f7c8c8');
                        } else {
                            checkbox.prop('checked', false);
                            removeA(selected_cb, id);
                            $(row).css('background-color', '');
                        }
                    }
                });
            });


            // Absent Approve Table Load Function

            $('#formFilter').on('submit', function (event) {
                event.preventDefault();

                var action_url = "{{ route('getabsetnopay') }}";

                var department = $('#department').val();
                var from_date = $('#from_date').val();

                closeOffcanvasSmoothly();

                $.ajax({
                    url: action_url,
                    method: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        department: department,
                        from_date: from_date,
                    },
                    dataType: "json",
                    success: function (data) {
                        if ($.fn.DataTable.isDataTable('#dataTableAbsent')) {
                            $('#dataTableAbsent').DataTable().clear().destroy();
                        }

                        $('#dataTableAbsent tbody').empty();

                        let dataRows = '';
                        $.each(data.data, function (index, item) {
                            dataRows += `
                                        <tr>
                                            <td><input type="checkbox" class="row-checkbox selectCheck removeIt"></td>
                                            <td>${item.empid}</td>
                                            <td>${item.emp_name}</td>
                                            <td class="d-none">${item.emp_autoid}</td>
                                        </tr>
                                    `;
                        });
                        $('#dataTableAbsent tbody').html(dataRows);
                        $('#dataTableAbsent').DataTable({
                            destroy: true,
                            responsive: true,
                            dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                                "buttons": [{
                                        extend: 'csv',
                                        className: 'btn btn-success btn-sm',
                                        title: 'Absent Nopay Information',
                                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                                    },
                                    { 
                                        extend: 'pdf', 
                                        className: 'btn btn-danger btn-sm', 
                                        title: 'Absent Nopay Information', 
                                        text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                        orientation: 'landscape', 
                                        pageSize: 'legal', 
                                        customize: function(doc) {
                                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                        }
                                    },
                                    {
                                        extend: 'print',
                                        title: 'Absent Nopay  Information',
                                        className: 'btn btn-primary btn-sm',
                                        text: '<i class="fas fa-print mr-2"></i> Print',
                                        customize: function(win) {
                                            $(win.document.body).find('table')
                                                .addClass('compact')
                                                .css('font-size', 'inherit');
                                        },
                                    },
                                ],

                            columnDefs: [{
                                orderable: false,
                                targets: [0, 1]
                            }, ]
                        });
                    }
                });
            });

            var selectedRowIdsapprove = [];

            $('#approve').click(async function () {
                var r = await Otherconfirmation("You want to Approve this ? ");
                if (r == true) {
                    selectedRowIdsapprove = [];
                        $('#dataTableAbsent tbody .selectCheck:checked').each(function () {
                            var rowData = $('#dataTableAbsent').DataTable().row($(this).closest('tr')).data();
                            if (rowData) {
                                selectedRowIdsapprove.push({
                                    empid: rowData[1],
                                    emp_name: rowData[2], 
                                    emp_autoid: rowData[3],
                                });
                            }
                        });

                        if (selectedRowIdsapprove.length > 0) {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            })

                            var from_date = $('#from_date').val();
                            $.ajax({
                                url: '{!! route("applyabsentnopay") !!}',
                                type: 'POST',
                                dataType: "json",
                                data: {
                                    dataarry: selectedRowIdsapprove,
                                    from_date:from_date
                                },
                                success: function (data) {

                                if (data.errors) {
                                    const actionObj = {
                                        icon: 'fas fa-warning',
                                        title: '',
                                        message: 'Record Error',
                                        url: '',
                                        target: '_blank',
                                        type: 'danger'
                                    };
                                    const actionJSON = JSON.stringify(actionObj, null, 2);
                                    action(actionJSON);
                                }
                                if (data.success) {
                                    const actionObj = {
                                        icon: 'fas fa-save',
                                        title: '',
                                        message: data.success,
                                        url: '',
                                        target: '_blank',
                                        type: 'success'
                                    };
                                    const actionJSON = JSON.stringify(actionObj, null, 2);
                                    action(actionJSON);
                                }

                                
                                }
                            })

                        } else {
                            
                            Swal.fire({
                            position: "top-end",
                            icon: 'warning',
                            title: 'Select Rows to Final Approve!',
                            showConfirmButton: false,
                            timer: 2500
                            });
                        }


                }
            });

            $('#selectAllAbsent').click(function (e) {
                $('#dataTableAbsent').closest('table').find('td input:checkbox').prop('checked', this.checked);
            });


            // Attendance Approve Table Load Function

            function load_dt_atte_Approve(company,department, month, from_date){
                $('#attendtable').DataTable({
                "destroy": true,
                    "processing": true,
                    "serverSide": true,
                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    "buttons": [{
                            extend: 'csv',
                            className: 'btn btn-success btn-sm',
                            title: 'Attendance Approve Information',
                            text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                        },
                        { 
                            extend: 'pdf', 
                            className: 'btn btn-danger btn-sm', 
                            title: 'Attendance Approve Information', 
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            orientation: 'landscape', 
                            pageSize: 'legal', 
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Attendance Approve  Information',
                            className: 'btn btn-primary btn-sm',
                            text: '<i class="fas fa-print mr-2"></i> Print',
                            customize: function(win) {
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            },
                        },
                    ],
                    "order": [
                        [0, "desc"]
                    ],
                    ajax: {
                        "url": "{{url('/attendance_list_for_approve')}}",
                        "data": {'company':company, 'department':department, 'month':month, 'closedate':from_date},
                    },

                    columns: [
                        { data: 'uid', name: 'at1.uid' },
                        { data: 'emp_name_with_initial', name: 'employees.emp_name_with_initial' },
                        { data: 'date', name: 'at1.date' },
                        { data: 'dept_name', name: 'departments.name' },
                        { data: 'location', name: 'branches.location' },
                        { data: 'work_days', name: 'work_days' },
                        { data: 'working_hours', name: 'working_hours' },
                        { data: 'leave_days', name: 'leave_days' },
                        { data: 'no_pay_days', name: 'no_pay_days' },
                        // { data: 'uid' ,
                        //     render : function ( data, type, row, meta ) {

                        //         return type === 'display'  ?
                        //             ' <a href="Attendentdetails/'+row['uid']+ "/"+ row['date'] +'"class="view_button btn btn-outline-dark btn-sm ml-1 "><i class="fas fa-eye"></i></a> '
                        //             : data;
                        //     }},
                    ],
                    "bDestroy": true,
                    "order": [[ 0, "desc" ]],
                });
            }

            $(document).on('click', '#approve_att',async function (e) {
                e.preventDefault();
                
                let department = $('#department').val();
                let company = $('#company').val();
                 let from_date = $('#from_date').val();

                let date_obj = from_date ? new Date(from_date) : null;
                let month = date_obj ? date_obj.getFullYear() + '-' + String(date_obj.getMonth() + 1).padStart(2, '0') : '';

                var r = await Otherconfirmation("You want to Edit this ? ");

                if (r == true) {
                    $('#approve_att').html('<i class="fa fa-spinner fa-spin mr-2"></i> Processing').prop('disabled', true);
                    $.ajax({
                        url: "AttendentAprovelBatch",
                        method: "POST",
                        data: {
                            department: department,
                            company: company,
                            month: month,
                            closedate: from_date,
                            _token: $('input[name=_token]').val(),
                        },
                        success: function (data) {
                            if (data.errors) {
                                const actionObj = {
                                    icon: 'fas fa-warning',
                                    title: '',
                                    message: 'Record Error',
                                    url: '',
                                    target: '_blank',
                                    type: 'danger'
                                };
                                const actionJSON = JSON.stringify(actionObj, null, 2);
                                action(actionJSON);
                            }
                            if (data.success) {
                                const actionObj = {
                                    icon: 'fas fa-save',
                                    title: '',
                                    message:'Attendance Successfully Approved',
                                    url: '',
                                    target: '_blank',
                                    type: 'success'
                                };
                                const actionJSON = JSON.stringify(actionObj, null, 2);
                                 actionreload(actionJSON);
                            }
                        }
                    });

                }

            });

            // late deduction approve table load function

            function load_dt_late_deduction_Approve(company,department, month, from_date) {

                    $.ajax({
                            url: "{{url('/getlateminitesapprovel')}}",
                            method: "POST",
                            data: {
                                _token: '{{ csrf_token() }}',
                                department: department,
                                month: month,
                                closedate: from_date
                            },
                            dataType: "json",
                            success: function (data) {
                                if ($.fn.DataTable.isDataTable('#late_dedutable')) {
                                    $('#late_dedutable').DataTable().clear().destroy();
                                }
                                
                                $('#late_dedutable tbody').empty();
                                let dataRows = '';
                                $.each(data.data, function (index, item) {
                                    dataRows += `
                                                <tr>
                                                    <td><input type="checkbox" class="row-checkbox selectCheck removeIt"></td>
                                                    <td>${item.emp_id}</td>
                                                    <td>${item.emp_name_with_initial}</td>
                                                    <td>${item.late_hours_total}</td>
                                                    <td>${item.nopayAmount}</td>
                                                    <td>${item.late_day_amount}</td>
                                                    <td class="d-none">${item.emp_autoid}</td>
                                                </tr>`;
                                });
                                $('#late_dedutable tbody').html(dataRows);
                                $('#late_dedutable').DataTable({
                                    destroy: true,
                                    responsive: true,
                                    dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                                        "buttons": [{
                                                extend: 'csv',
                                                className: 'btn btn-success btn-sm',
                                                title: 'Late Deduction Approval Information',
                                                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                                            },
                                            { 
                                                extend: 'pdf', 
                                                className: 'btn btn-danger btn-sm', 
                                                title: 'Late Deduction Approval Information', 
                                                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                                orientation: 'landscape', 
                                                pageSize: 'legal', 
                                                customize: function(doc) {
                                                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                                }
                                            },
                                            {
                                                extend: 'print',
                                                title: 'Late Deduction Approval  Information',
                                                className: 'btn btn-primary btn-sm',
                                                text: '<i class="fas fa-print mr-2"></i> Print',
                                                customize: function(win) {
                                                    $(win.document.body).find('table')
                                                        .addClass('compact')
                                                        .css('font-size', 'inherit');
                                                },
                                            },
                                        ],
                                    columnDefs: [{
                                        orderable: false,
                                        targets: [0, 6]
                                    }, ]

                                
                                });
                                $('#btn-filter').html('Filter').prop('disabled', false);
                            }
                        });
            }

            var selectedRowIdsapprove_late = [];

            $('#approve_late_deduction').click(async function () {
                var r = await Otherconfirmation("You want to Edit this ? ");
                if (r == true) {

                    selectedRowIdsapprove_late = [];
                    $('#late_dedutable tbody .selectCheck:checked').each(function () {
                        var rowData = $('#late_dedutable').DataTable().row($(this).closest('tr')).data();

                        if (rowData) {
                            selectedRowIdsapprove_late.push({
                                empid: rowData[1],
                                emp_name: rowData[2],
                                late_hourstotal: rowData[3],
                                nopayamount: rowData[4],
                                total_amount: rowData[5],
                                autoid: rowData[6],
                            });
                        }
                    });

                    if (selectedRowIdsapprove_late.length > 0) {
                        console.log(selectedRowIdsapprove_late);


                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        })

                        let department = $('#department').val();
                        let company = $('#company').val();
                        let from_date = $('#from_date').val();

                        let date_obj = from_date ? new Date(from_date) : null;
                        let month = date_obj ? date_obj.getFullYear() + '-' + String(date_obj.getMonth() + 1).padStart(2, '0') : '';


                        $.ajax({
                            url: '{!! route("approvelatemintes") !!}',
                            type: 'POST',
                            dataType: "json",
                            data: {
                                dataarry: selectedRowIdsapprove,
                                department: department,
                                month: month,
                                closedate: from_date
                            },
                            success: function (data) {
                            if (data.errors) {
                                    const actionObj = {
                                        icon: 'fas fa-warning',
                                        title: '',
                                        message: 'Record Error',
                                        url: '',
                                        target: '_blank',
                                        type: 'danger'
                                    };
                                    const actionJSON = JSON.stringify(actionObj, null, 2);
                                    action(actionJSON);
                                }
                                if (data.success) {
                                    const actionObj = {
                                        icon: 'fas fa-save',
                                        title: '',
                                        message: data.success,
                                        url: '',
                                        target: '_blank',
                                        type: 'success'
                                    };
                                    const actionJSON = JSON.stringify(actionObj, null, 2);
                                    action(actionJSON);
                                }

                            }
                        })
                    } else {

                        Swal.fire({
                            position: "top-end",
                            icon: 'warning',
                            title: 'Select Rows to Final Approve!',
                            showConfirmButton: false,
                            timer: 2500
                            });
                    }
                }
            });

            $('#selectAll_late_deduction').click(function (e) {
                $('#late_dedutable').closest('table').find('td input:checkbox').prop('checked', this.checked);
            });

            // salary addition approve table load function


        $('#formFilter').on('submit', function (event) {
            event.preventDefault();
             closeOffcanvasSmoothly();

            var action_url = "{{ route('mealallowancecreate') }}";

            var department = $('#department').val();
            var from_date = $('#from_date').val();
            var remunerationtype = $('#remuneration_name').val();

            let date_obj = from_date ? new Date(from_date) : null;
            let month = date_obj ? date_obj.getFullYear() + '-' + String(date_obj.getMonth() + 1).padStart(2, '0') : '';


            $.ajax({
                url: action_url,
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    department: department,
                    from_date: from_date,
                    to_date: from_date,
                    selectedmonth: month,
                    remunerationtype: remunerationtype
                },
                dataType: "json",
                success: function (data) {
                    if ($.fn.DataTable.isDataTable('#dataTable_salary')) {
                        $('#dataTable_salary').DataTable().clear().destroy();
                    }
                    
                    $('#dataTable_salary tbody').empty();

                    let dataRows = '';
                    $.each(data.data, function (index, item) {
                        dataRows += `
                                    <tr>
                                        <td>
                                            ${item.approvedallowancestatus == 1 
                                                ? '<i class="fa fa-check-circle text-success"></i>' 
                                                : '<input type="checkbox" class="row-checkbox selectCheck removeIt">'
                                            }
                                        </td>
                                        <td>${item.empid}</td>
                                        <td>${item.emp_name}</td>
                                        <td>${item.working_Days}</td>
                                        <td>${item.allowance_amount}</td>
                                        <td>${item.total_amount}</td>
                                        <td>${item.monthly_remain}</td>
                                        <td class="d-none">${item.payroll_Profile}</td>
                                        <td class="d-none">${item.allowance_type}</td>
                                        <td class="d-none">${item.remuneration_id}</td>
                                    </tr>
                                `;
                    });
                    $('#dataTable_salary tbody').html(dataRows);
                    $('#dataTable_salary').DataTable({
                        destroy: true,
                        responsive: true,
                         dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
                                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                            "buttons": [{
                                    extend: 'csv',
                                    className: 'btn btn-success btn-sm',
                                    title: 'Salary Adjustments Approval Information',
                                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                                },
                                { 
                                    extend: 'pdf', 
                                    className: 'btn btn-danger btn-sm', 
                                    title: 'Salary Adjustments Approval Information', 
                                    text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                                    orientation: 'landscape', 
                                    pageSize: 'legal', 
                                    customize: function(doc) {
                                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                                    }
                                },
                                {
                                    extend: 'print',
                                    title: 'Salary Adjustments Approval  Information',
                                    className: 'btn btn-primary btn-sm',
                                    text: '<i class="fas fa-print mr-2"></i> Print',
                                    customize: function(win) {
                                        $(win.document.body).find('table')
                                            .addClass('compact')
                                            .css('font-size', 'inherit');
                                    },
                                },
                            ],
                        columnDefs: [{
                            orderable: false,
                            targets: [0, 9]
                        }, ]
                    });
                }
            });
        });

        var selectedRowIdsapprove_salary = [];

        $('#approve_salary').click( async function () {

              var r = await Otherconfirmation("You want to Edit this ? ");
            if (r == true) {

                selectedRowIdsapprove_salary = [];
                    $('#dataTable_salary tbody .selectCheck:checked').each(function () {
                        var rowData = $('#dataTable_salary').DataTable().row($(this).closest('tr')).data();

                        if (rowData) {
                            selectedRowIdsapprove_salary.push({
                                empid: rowData[1],
                                emp_name: rowData[2], 
                                working_Days: rowData[3],
                                allowance_amount: rowData[4], 
                                total_amount: rowData[5], 
                                monthly_remain: rowData[6], 
                                payroll_Profile: rowData[7],
                                allowance_type: rowData[8],
                                remuneration_id: rowData[9]
                            });
                        }
                    });

                    if (selectedRowIdsapprove_salary.length > 0) {
                     console.log(selectedRowIdsapprove_salary);
                       
                     $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        })

                        var department = $('#department').val();
                        var from_date = $('#from_date').val();
                        var remunerationtype = $('#remuneration_name').val();

                        let date_obj = from_date ? new Date(from_date) : null;
                        let month = date_obj ? date_obj.getFullYear() + '-' + String(date_obj.getMonth() + 1).padStart(2, '0') : '';


                        $.ajax({
                            url: '{!! route("mealallowancecreateapprove") !!}',
                            type: 'POST',
                            dataType: "json",
                            data: {
                                dataarry: selectedRowIdsapprove_salary,
                                selectedmonth: month,
                                from_date:from_date,
                                to_date:from_date
                            },
                            success: function (data) {
                                  if (data.errors) {
                                        const actionObj = {
                                            icon: 'fas fa-warning',
                                            title: '',
                                            message: 'Record Error',
                                            url: '',
                                            target: '_blank',
                                            type: 'danger'
                                        };
                                        const actionJSON = JSON.stringify(actionObj, null, 2);
                                        action(actionJSON);
                                    }
                                    if (data.success) {
                                        const actionObj = {
                                            icon: 'fas fa-save',
                                            title: '',
                                            message: data.success,
                                            url: '',
                                            target: '_blank',
                                            type: 'success'
                                        };
                                        const actionJSON = JSON.stringify(actionObj, null, 2);
                                        action(actionJSON);
                                    }
                            }
                        })

                    } else {
                        Swal.fire({
                        position: "top-end",
                        icon: 'warning',
                        title: 'Select Rows to Final Approve!',
                        showConfirmButton: false,
                        timer: 2500
                        });
                    }
            }
                    
        });


        $('#selectAll_salary').click(function (e) {
            $('#dataTable_salary').closest('table').find('td input:checkbox').prop('checked', this.checked);
        });
     });
    </script>

@endsection