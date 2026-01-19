@extends('layouts.app')

@section('content')

<main> 
   <div class="page-header shadow">
            <div class="container-fluid d-none d-sm-block shadow">
                 @include('layouts.meal_nav_bar')
            </div>
            <div class="container-fluid">
                <div class="page-header-content py-3 px-2">
                    <h1 class="page-header-title ">
                        <div class="page-header-icon"><i class="fa-light fa-utensils"></i></div>
                        <span>Meal Requests</span>
                    </h1>
                </div>
            </div>
        </div>
    <div class="container-fluid mt-2 p-0 p-2">
        <div class="card">
            <div class="card-body p-0 p-2">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-sm fa-pull-right mr-2" name="create_record"
                        id="create_record"><i class="fas fa-plus mr-2"></i>Add Employee Meal Request</button>
                    </div>
                    <div class="col-12">
                        <hr class="border-dark">
                    </div>
                    <div class="col-12">
                        <div class="center-block fix-width scroll-inner">
                        <table class="table table-striped table-bordered table-sm small nowrap" style="width: 100%" id="dataTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>EMPLOYEE</th>
                                    <th>DATE</th>
                                    <th>MEAL</th>
                                    <th>TYPE</th>
                                    <th class="text-right">ACTION</th>
                                    <th class="d-none">TYPE</th>
                                    <th class="d-none">TYPE</th>
                                    <th class="d-none">TYPE</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Area Start -->
    <div class="modal fade" id="formModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Employee Meal Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <span id="form_result"></span>
                            <form method="post" id="formTitle" class="form-horizontal">
                                {{ csrf_field() }}
                                <input type="hidden" name="action" id="action" />
                               
                                <input type="hidden" name="detailsid" id="detailsid" />
                                
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder">Date*</label>
                                        <input type="date" name="production_date" id="production_date"
                                            class="form-control form-control-sm" required />
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder">Meal Type*</label>
                                        <select name="mealtype" id="mealtype" class="form-control form-control-sm">
                                            <option value="">Please Select</option>
                                            @foreach ($meal_types as $meal_type)
                                                <option value="{{ $meal_type->id }}">{{ $meal_type->meal_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="row col-sm-12 col-md-6">
                                            <div class="col-12">
                                                <label class="small font-weight-bolder">Allocate Type*</label><br>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="allocatetype" id="free" value="1">
                                                    <label class="form-check-label small font-weight-bolder " for="free" required>Free Issue</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="allocatetype" id="paid" value="2">
                                                    <label class="form-check-label small font-weight-bolder " for="paid" required >Paid Issue</label>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder">Employee*</label>
                                        <select class="employee form-control form-control-sm" name="employee" id="employee" style="width:100%"></select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-4">
                                        <button type="button" id="addtolist" class="btn btn-primary btn-sm px-4" style="margin-top:30px;"><i class="fas fa-plus"></i>&nbsp;Add to list</button>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <button type="button" id="Btnupdatelist" class="btn btn-success btn-sm px-3" style="margin-top:30px; display:none;"><i class="fas fa-edit"></i>&nbsp;Update</button>
                                    </div>
                                </div>

                                <br>
                                <div class="center-block fix-width scroll-inner">
                                <table class="table table-striped table-bordered table-sm small nowrap display" id="allocationtbl" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>EMP ID</th>
                                            <th>EMPLOYEE NAME</th>
                                            <th style="white-space: nowrap;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody id="emplistbody">
                                    </tbody>
                                </table>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="button" name="action_button" id="action_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

 

    <!-- View Modal -->
    <div class="modal fade" id="viewconfirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="aviewmodal-title" id="staticBackdropLabel">Edit Employee Meal Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" id="editformTitle" class="form-horizontal">
                         <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder">Date*</label>
                                        <input type="date" name="edit_date" id="edit_date"
                                            class="form-control form-control-sm" required />
                                    </div> 
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder">Meal Type*</label>
                                        <select name="edit_mealtype" id="edit_mealtype" class="form-control form-control-sm">
                                            <option value="">Please Select</option>
                                            @foreach ($meal_types as $meal_type)
                                                <option value="{{ $meal_type->id }}">{{ $meal_type->meal_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="row col-sm-12 col-md-6">
                                            <div class="col-12">
                                                <label class="small font-weight-bolder">Allocate Type*</label><br>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="edit_allocatetype" id="editfree" value="1">
                                                    <label class="form-check-label small font-weight-bolder " for="editfree" required>Free Issue</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="edit_allocatetype" id="editpaid" value="2">
                                                    <label class="form-check-label small font-weight-bolder " for="editpaid" required >Paid Issue</label>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="col-sm-12 col-md-6">
                                        <label class="small font-weight-bolder">Employee*</label>
                                        <select class="employee form-control form-control-sm" name="editemployee" id="editemployee" style="width:100%" readonly></select>
                                    </div>
                                </div>
                                 <div class="form-group mt-3">
                                     <input type="hidden" name="hidden_id" id="hidden_id" />
                                    <button type="button" name="update_button" id="update_button" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="fas fa-plus"></i>&nbsp;Update</button>
                                </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Area End -->     
</main>
              
@endsection

@section('script')
<script>
$(document).ready(function(){
    $('#production_menu_link').addClass('active');
    $('#production_menu_link_icon').addClass('active');
    $('#dailyprocess').addClass('navbtnactive');

    // Modal close handlers
    $('#viewconfirmModal .close').click(function(){
        $('#viewconfirmModal').modal('hide');
    });

    // Employee Select2 initialization
    let employee = $('#employee');
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
                    page: params.page || 1
                }
            },
            cache: true
        }
    });

    // DataTable initialization
    $('#dataTable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "buttons": [{
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                title: 'Employee Meal Requests  Information',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
            },
            { 
                extend: 'pdf', 
                className: 'btn btn-danger btn-sm', 
                title: 'Employee Meal Requests Information', 
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape', 
                pageSize: 'legal', 
                customize: function(doc) {
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }
            },
            {
                extend: 'print',
                title: 'Employee Meal Requests  Information',
                className: 'btn btn-primary btn-sm',
                text: '<i class="fas fa-print mr-2"></i> Print',
                customize: function(win) {
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                },
            },
            // 'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        "order": [
            [0, "desc"]
        ],
        ajax: {
            url: scripturl + "/meal_requets_list.php",
            type: "POST",
            data: {},
        },
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'employee_display',
                name: 'employee_display'
            },
            {
                data: 'date',
                name: 'date'
            },
            {
                data: 'meal_name',
                name: 'meal_name'
            },
            {
                data: 'issue_type',
                name: 'issue_type',
                render: function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        if (data == 1) {
                            return 'Free Issue';
                        } else if (data == 2) {
                            return 'Paid Issue';
                        } else {
                            return data;
                        }
                    }
                    return data;
                }
            },
            {
                data: 'action',
                name: 'action',
                className: 'text-right',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    var buttons = '';

                    buttons += ' <button name="edit" id="'+row.id+'" class="edit btn btn-primary btn-sm mr-1" type="button" data-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt"></i></button>';

                    buttons += '<button name="delete" id="'+row.id+'" class="delete btn btn-danger btn-sm" data-toggle="tooltip" title="Delete"><i class="far fa-trash-alt"></i></button>';

                      return buttons;
                }
            },
            {   data: "emp_name_with_initial", 
                name: "emp_name_with_initial", 
                visible: false
            },
            {   data: "calling_name",
                name: "calling_name", 
                visible: false
            },
            {   data: "emp_id", 
                name: "emp_id", 
                visible: false
            },
        ],
        drawCallback: function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });




    // Create record button
    $('#create_record').click(function () {
        $('.modal-title').text('Add Employee Meal Request');
        $('#action').val('Add');
        $('#form_result').html('');
        $('#formTitle')[0].reset();
        $('#action_button').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
        $('#emplistbody').empty();
        $('#employee').val('').trigger('change');
        $('#Btnupdatelist').hide();
        $('#formModal').modal('show');
    });

    // Add to list functionality
    $('#addtolist').click(function () {
        if (!$('#employee').val()) {
             Swal.fire({
                position: "top-end",
                icon: 'warning',
                title: 'Please select an employee!',
                showConfirmButton: false,
                timer: 2500
            });


            return;
        }
        
        if (!$('#mealtype').val() || !$('#production_date').val()) {
              Swal.fire({
                position: "top-end",
                icon: 'warning',
                title: 'Please select Meal Type and product!',
                showConfirmButton: false,
                timer: 2500
            });
            return;
        }

        var emp_id = $('#employee').val();
        var selectedText = $('#employee option:selected').text();

        var exists = false;
        $('#emplistbody tr').each(function() {
            if ($(this).find('td:first').text() == emp_id) {
                exists = true;
                return false;
            }
        });

        if (exists) {
             Swal.fire({
                position: "top-end",
                icon: 'warning',
                title: 'Employee already added to the list!',
                showConfirmButton: false,
                timer: 2500
            });
            return;
        }

        $('#emplistbody').append('<tr class="pointer">' +
            '<td>' + emp_id + '</td>' +
            '<td>' + selectedText + '</td>' +
            '<td class="text-right">' +
                '<button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm">' +
                    '<i class="fas fa-trash-alt"></i>' +
                '</button>' +
            '</td>' +
            '<td class="d-none">NewData</td>' +
        '</tr>');

        $('#employee').val('').trigger('change');
    });

    // Form submission
    $('#action_button').click(function () {
        var action_url = '';
        
        if ($('#action').val() == 'Add') {
            action_url = "{{ route('mealrequestinsert') }}";
        }
        $('#action_button').prop('disabled', true).html(
            '<i class="fas fa-circle-notch fa-spin mr-2"></i> Processing');

        var tbody = $("#emplistbody");

        if (tbody.children().length > 0) {
            var jsonObj = [];
            $("#emplistbody tr").each(function () {
                var item = {};
                $(this).find('td').each(function (col_idx) {
                    if (col_idx !== 2) {
                        item["col_" + (col_idx + 1)] = $(this).text();
                    }
                });
                jsonObj.push(item);
            });

            var mealtype = $('#mealtype').val();
            var allocatetype = $('input[name="allocatetype"]:checked').val();
            var date = $('#production_date').val(); 
          
            $.ajax({
                method: "POST",
                dataType: "json",
                data: {
                    _token: '{{ csrf_token() }}',
                    tableData: jsonObj,
                    mealtype: mealtype,
                    allocatetype: allocatetype,
                    date: date
                },
                url: action_url,
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
                        actionreload(actionJSON);
                    }
                }
            });
        } else {
            Swal.fire({
                position: "top-end",
                icon: 'warning',
                title: 'Cannot Create..Table Empty!',
                showConfirmButton: false,
                timer: 2500
            });

            $('#action_button').prop('disabled', false).html('<i class="fas fa-plus"></i>&nbsp;Add');
        }
    });


    // Edit function
    $(document).on('click', '.edit',async function () {
        var r = await Otherconfirmation("You want to Edit this ? ");
         if (r == true) {
            var id = $(this).attr('id');
            $('#form_result').html('');
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

            $.ajax({
                url: '{!! route("mealrequestedit") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: id
                },
                success: function (data) {
                    $('#edit_date').val(data.result.date);
                    $('#edit_mealtype').val(data.result.meal_type);
                    $('#editemployee').val(data.result.date);
                     var issueType = data.result.issue_type;
                    if (issueType == 1) {
                        $('#editfree').prop('checked', true);
                    } else if (issueType == 2) {
                        $('#editpaid').prop('checked', true);
                    }

                     $('#editemployee').empty(); 
                      var empNameWithInitial = data.result.emp_name_with_initial || '';
                    var callingName = data.result.calling_name || '';
                    var displayName = empNameWithInitial + ' - ' + callingName;
                    var option = new Option(displayName, data.result.emp_id, true, true);
                    $('#editemployee').append(option).trigger('change');
                    $('#hidden_id').val(id);
                    $('#viewconfirmModal').modal('show');
                }
            })
         }
    });

     // Form submission
    $('#update_button').click(function () {
           var action_url = "{{ route('mealrequestupdate') }}";

            var edit_mealtype = $('#edit_mealtype').val();
            var edit_allocatetype = $('input[name="edit_allocatetype"]:checked').val();
            var edit_date = $('#edit_date').val(); 
            var hidden_id = $('#hidden_id').val(); 
          
            $.ajax({
                method: "POST",
                dataType: "json",
                data: {
                    _token: '{{ csrf_token() }}',
                    mealtype: edit_mealtype,
                    allocatetype: edit_allocatetype,
                    date: edit_date,
                    hidden_id: hidden_id,
                },
                url: action_url,
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
                        actionreload(actionJSON);
                    }
                }
            });
    });

    // Delete main record
    var user_id;
    $(document).on('click', '.delete',async function () {
        user_id = $(this).attr('id');
        var r = await Otherconfirmation("You want to remove this ? ");
        if (r == true) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            
            $.ajax({
                url: '{!! route("mealrequestsdelete") !!}',
                type: 'POST',
                dataType: "json",
                data: {
                    id: user_id
                },
                success: function (data) {
                    const actionObj = {
                        icon: 'fas fa-trash-alt',
                        title: '',
                        message: 'Record Remove Successfully',
                        url: '',
                        target: '_blank',
                        type: 'danger'
                    };
                    const actionJSON = JSON.stringify(actionObj, null, 2);
                    actionreload(actionJSON);
                }
            })
        }
    });


});

</script>

@endsection