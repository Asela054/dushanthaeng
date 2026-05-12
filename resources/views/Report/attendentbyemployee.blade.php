<?php $page_stitle = 'Report on Employee Attendance - Multi Offset'; ?>
@extends('layouts.app')

@section('content')

    <main>
         <div class="page-header">
        <div class="container-fluid d-none d-sm-block shadow">
             @include('layouts.reports_nav_bar')
        </div>
        <div class="container-fluid">
            <div class="page-header-content py-3 px-2">
                <h1 class="page-header-title ">
                    <div class="page-header-icon"><i class="fa-light fa-file-contract"></i></div>
                    <span>Attendance Report</span>
                </h1>
            </div>
        </div>
    </div>

        <div class="container-fluid  mt-2 p-0 p-2">
            <div class="card ">
                <div class="card-body p-0 p-2">
                    <div class="col-md-12">
                        <button class="btn btn-warning btn-sm filter-btn float-right px-3" type="button"
                            data-toggle="offcanvas" data-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fas fa-filter mr-1"></i> Filter
                            Options</button><br>
                    </div>
                    <div class="col-md-12">
                        <hr class="border-dark">
                    </div>
                    <div class="response">
                    </div>
                    {{ csrf_field() }}
                </div>
            </div>
            @include('layouts.filter_menu_offcanves')
        </div>

    </main>

@endsection

@section('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- autoTable plugin for jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script>
        // Make jsPDF available globally
    window.jsPDF = window.jspdf.jsPDF;
$(document).ready(function () {

        $('#report_menu_link').addClass('active');
        $('#report_menu_link_icon').addClass('active');
        $('#employeereportmaster').addClass('navbtnactive');

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
                data: function(params) {
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
                data: function(params) {
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
                        department: department.val()
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
                url: '{{url("location_list_from_attendance_sel2")}}',
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


        let today = new Date().toISOString().split('T')[0];
        $('#from_date').val(today);
        $('#to_date').val(today);
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt('', '', '', from_date, to_date);

    $('#formFilter').on('submit',function(e) {
        e.preventDefault();
        let department = $('#department').val();
        let employee = $('#employee').val();
        let location = $('#location').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();

        load_dt(department, employee, location, from_date, to_date);
        closeOffcanvasSmoothly();

    });


    function load_dt(department, employee, location, from_date, to_date) {
    $('.response').html('');

    $.ajax({
        url: "{{ route('get_attendance_by_employee_data') }}",
        method: "POST",
        data: {
            department: department,
            employee: employee,
            location: location,
            from_date: from_date,
            to_date: to_date,
            _token: '{{csrf_token()}}'
        },
        success: function (res) {
            let html = '';

            html += `
        <div class="row mb-2">
            <div class="col-md-4">
            <button id="export_excel" class="btn btn-sm btn-success d-none"><i class="fas fa-file-excel mr-2"></i>Export To Excel</button>
            </div>
            <div class="col-md-4">
            <label class="mr-2">
                <badge class="badge badge-pill " style="border: solid 1px black"> &nbsp; </badge> : Present
            </label>
            <label class="mr-2">
                <badge class="badge badge-pill " style="background-color: #ffeaea"> &nbsp; </badge> : Absent
            </label>
            <label class="mr-2">
                <badge class="badge badge-pill " style="background-color: rgb(247, 200, 200)"> &nbsp; </badge> : Incomplete
            </label>
            </div>
        </div>
        <div class="center-block fix-width scroll-inner" >
        <table class="table table-striped table-bordered table-sm table-hover" id="attendance_report_table">
            <thead>
                <tr>
                    <th>EMP ID</th>
                    <th>NAME</th>
                    <th>DEPARTMENT</th>
                    <th>LOCATION</th>
                    <th>DATE</th>
                    <th>DATE TYPE</th>
                    <th>CHECK IN</th>
                    <th>CHECK OUT</th>
                    <th>WORK HOURS</th>
                    <th>DAY SALARY</th>
                    <th>OT HOURS</th>    
                    <th>D.OT HOURS</th>    
                    <th>LEAVE TYPE</th>
                </tr>
            </thead>
            <tbody>
        `;

            // Function to convert 24-hour format to 12-hour format
            function convertTo12HourFormat(time) {
                if (!time || time === '-') return time;
                const [hour, minute] = time.split(':');
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const formattedHour = hour % 12 || 12;
                return `${formattedHour}:${minute} ${ampm}`;
            }

            res.data.forEach(function (datalist) {
                datalist.attendanceinfo.forEach(function (emp_data) {
                    let tr = '<tr>';
                    if (emp_data.workhours === '00:00:00') {
                        tr = '<tr style="background-color: rgb(247, 200, 200)">';
                    } else if (emp_data.workhours === '-') {
                        tr = '<tr style="background-color: #ffeaea">';
                    }

                    const checkInTime = convertTo12HourFormat(emp_data.timestamp);
                    const checkOutTime = convertTo12HourFormat(emp_data.lasttimestamp);

                    html += tr;
                    html += `<td>${emp_data.emp_id}</td>`;
                    html += `<td>${emp_data.emp_name_with_initial} - ${emp_data.calling_name}</td>`;
                    html += `<td>${emp_data.dept_name}</td>`;
                    html += `<td>${emp_data.location}</td>`;
                    html += `<td>${emp_data.date}</td>`;
                    html += `<td>${emp_data.day_type}</td>`;
                    html += `<td>${checkInTime}</td>`;
                    html += `<td>${checkOutTime}</td>`;
                    html += `<td>${emp_data.workhours}</td>`;
                    html += `<td>${emp_data.day_salary}</td>`;
                    html += `<td>${emp_data.normal_ot_hours}</td>`;
                    html += `<td>${emp_data.double_ot_hours}</td>`;
                    html += `<td>${emp_data.leave_type}</td>`;
                    
                    html += '</tr>';
                });
            });
            html += `
            </tbody>
        </table>
         </div>
        `;

            $('.response').html(html);

            // Check if DataTable already exists and destroy it first
            if ($.fn.DataTable.isDataTable('#attendance_report_table')) {
                $('#attendance_report_table').DataTable().destroy();
            }

            // Initialize DataTable with client-side processing
            $('#attendance_report_table').DataTable({
                "processing": false, 
                "serverSide": false, 
                "searching": true,
                dom: "<'row'<'col-sm-4 mb-sm-0 mb-2'B><'col-sm-2'l><'col-sm-6'f>>" + 
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                "buttons": [
                    {
                        extend: 'csv',
                        className: 'btn btn-success btn-sm',
                        title: 'Attendance Reports',
                        text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    },
                    {
                            text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                            className: 'btn btn-danger btn-sm',
                            action: function (e, dt, node, config) {
                                generatePDF();
                            }
                    },
                    {
                        extend: 'print',
                        title: 'Attendance Reports',
                        className: 'btn btn-primary btn-sm',
                        text: '<i class="fas fa-print mr-2"></i> Print',
                        customize: function(win) {
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        },
                    },
                ],
                "order": [[0, "asc"]]
            });
        }
    });
}
});

function generatePDF() {
    // Get current filter values for PDF header
    const fromDate = $('#from_date').val() || 'Not specified';
    const toDate = $('#to_date').val() || 'Not specified';
    const department = $('#department').val() || 'All';
    const employee = $('#employee').val() || 'All';
    const location = $('#location').val() || 'All';
    const currentDate = new Date().toLocaleDateString();

    // Get DataTable instance
    const table = $('#attendance_report_table').DataTable();
    const tableData = table.rows({ filter: 'applied' }).data();

    // Initialize PDF in landscape mode
    const doc = new jsPDF('l', 'mm', 'a4');

    // Add report title
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('Attendance Report', doc.internal.pageSize.getWidth() / 2, 15, { align: 'center' });

    // Add filter information
    doc.setFontSize(8);
    doc.setFont('helvetica', 'normal');

    let yPos = 25;
    doc.text(`Date Range: ${fromDate} to ${toDate}`, 15, yPos);
    doc.text(`Generated on: ${currentDate}`, doc.internal.pageSize.getWidth() - 15, yPos, { align: 'right' });

    yPos += 5;
    doc.text(`Department: ${department}   |   Location: ${location}`, 15, yPos);

    if (employee !== 'All') {
        yPos += 5;
        doc.text(`Employee: ${employee}`, 15, yPos);
    }

    // Add a line separator
    yPos += 8;
    doc.setLineWidth(0.3);
    doc.line(15, yPos, doc.internal.pageSize.getWidth() - 15, yPos);
    yPos += 5;

    // Prepare table headers matching attendance_report_table
    const headers = [[
        'EMP ID', 'NAME', 'DEPARTMENT', 'LOCATION', 'DATE',
        'DATE TYPE', 'CHECK IN', 'CHECK OUT', 'WORK HOURS',
        'DAY SALARY', 'OT HRS', 'D.OT HRS', 'LEAVE TYPE'
    ]];

    const body = [];
    let rowCount = 0;

    // Check if there's data
    if (!tableData || tableData.length === 0) {
        doc.setFontSize(8);
        doc.setTextColor(255, 0, 0);
        doc.text('No data available for the selected filters', doc.internal.pageSize.getWidth() / 2, yPos + 20, { align: 'center' });
        doc.save('Attendance_Report_No_Data.pdf');
        return;
    }

    // Extract data from DOM rows (since attendance table is built from HTML, not JSON objects)
    table.rows({ filter: 'applied' }).nodes().each(function(row) {
        const cells = $(row).find('td');
        const rowData = [];
        cells.each(function() {
            rowData.push($(this).text().trim());
        });
        body.push(rowData);

        // Apply row background color based on attendance status
        const style = $(row).attr('style') || '';
        if (style.includes('rgb(247, 200, 200)')) {
            // Incomplete - light red
            body[body.length - 1]._rowStyle = [247, 200, 200];
        } else if (style.includes('#ffeaea')) {
            // Absent - very light red
            body[body.length - 1]._rowStyle = [255, 234, 234];
        }

        rowCount++;
    });

    const margin = 5;

    // Generate table using autoTable
    doc.autoTable({
        startY: yPos,
        head: headers,
        body: body,
        theme: 'grid',
        styles: {
            fontSize: 5.5,
            cellPadding: 2,
            overflow: 'linebreak',
            valign: 'middle'
        },
        headStyles: {
            fillColor: [41, 128, 185],
            textColor: 255,
            fontStyle: 'bold',
            halign: 'center',
            fontSize: 5,
            cellPadding: 3
        },
        columnStyles: {
            0:  { cellWidth: 14, halign: 'center' },  // EMP ID
            1:  { cellWidth: 45, halign: 'left'   },  // NAME
            2:  { cellWidth: 35, halign: 'left'   },  // DEPARTMENT
            3:  { cellWidth: 20, halign: 'left'   },  // LOCATION
            4:  { cellWidth: 18, halign: 'center' },  // DATE
            5:  { cellWidth: 18, halign: 'center' },  // DATE TYPE
            6:  { cellWidth: 18, halign: 'center' },  // CHECK IN
            7:  { cellWidth: 18, halign: 'center' },  // CHECK OUT
            8:  { cellWidth: 24, halign: 'center' },  // WORK HOURS
            9:  { cellWidth: 18, halign: 'center' },  // DAY SALARY
            10: { cellWidth: 18, halign: 'center' },  // NORMAL OT HRS
            11: { cellWidth: 18, halign: 'center' },  // DOUBLE OT HRS
            12: { cellWidth: 20, halign: 'left'   },  // LEAVE TYPE
        },
        bodyStyles: {
            fontSize: 5.5
        },
        alternateRowStyles: {
            fillColor: [245, 245, 245]
        },
        // Preserve row color coding for attendance status
        didParseCell: function(data) {
            if (data.section === 'body') {
                const rowStyle = body[data.row.index]?._rowStyle;
                if (rowStyle) {
                    data.cell.styles.fillColor = rowStyle;
                }
            }
        },
        margin: { left: margin, right: margin },
        pageBreak: 'auto',
        tableWidth: 'auto',
        showHead: 'everyPage',
        willDrawPage: function(data) {
            const companyName = $('#company_name').val() || 'Company Name';
            doc.setFontSize(7);
            doc.setFont('helvetica', 'normal');
            doc.text(companyName, margin, 10);
            doc.text(`Page ${data.pageNumber}`, doc.internal.pageSize.getWidth() - margin, 10, { align: 'right' });

            if (data.pageNumber > 1) {
                doc.setFontSize(9);
                doc.setFont('helvetica', 'bold');
                doc.text('Attendance Report (Continued)', doc.internal.pageSize.getWidth() / 2, 18, { align: 'center' });
            }
        }
    });

    // Save the PDF
    const safeDept = department.replace(/[^a-zA-Z0-9]/g, '_') || 'Report';
    const fileName = `Attendance_Report_${safeDept}_${currentDate.replace(/[^0-9]/g, '')}.pdf`;
    doc.save(fileName);
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

@endsection

