<button id="exportPdfBtn" type="button" class="btn btn-danger btn-sm float-right mt-2 mb-2"><i class="fa fa-file-pdf"></i> Export PDF</button>
@foreach ($employeeData as $employee)
    @php
        $estimate_salary = 0;
    @endphp
<table class="table table-striped table-bordered table-sm small exporttable">
    <thead>
        <tr>
            <th colspan="9" class="text-center">Estimate Salary Report</th>
        </tr>
        <tr>
            <th colspan="9" class="text-center">{{ $employee['emp_fullname'] }}</th>
        </tr>
        <tr>
            <th>Date</th>
            <th>Place</th>
            <th class="text-right">Basic Salary</th>
            <th class="text-right">Advance</th>
            <th>In</th>
            <th>Out</th>
            <th class="text-center">OT Hours</th>
            <th class="text-right">OT Amount</th>
            <th class="text-right">Estimate Salary</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($employee['attendance'] as $record)
            @php
                // Match advance record for this employee on this specific date
                $advance = collect($employee['salaryadvance'])->first(function ($item) use ($employee, $record) {
                    $itemEmpId = is_object($item) ? ($item->emp_id ?? null) : ($item['emp_id'] ?? null);
                    $itemDate  = is_object($item) ? ($item->date ?? null)   : ($item['date'] ?? null);

                    // $record only has in_date/out_date, not "date" — compare against in_date
                    return $itemEmpId == $employee['emp_id'] && $itemDate == $record->in_date;
                });

                // Safely extract paid_amount
                $paidAdvanceAmount = 0;
                if ($advance) {
                    $paidAdvanceAmount = is_object($advance) ? ($advance->paid_amount ?? 0) : ($advance['paid_amount'] ?? 0);
                }

                if ($record->leave_type === 'No Pay Leave') {
                    $estimate_salary -= abs($employee['extra']['nopay_base_rate']);
                } elseif ($record->leave_type == '' && ($record->in_time != '' || $record->out_time != '')) {
                    $estimate_salary += $employee['extra']['basic_base_rate']
                        + ($record->ot_hours * $employee['extra']['othrs1_base_rate'])
                        - $paidAdvanceAmount;
                }

                $leavetype = $record->leave_type === 'No Pay Leave' ? $record->leave_type : 'Leave';
            @endphp
            <tr>
                <td>{{ $record->in_date }}</td>
                @if($record->leave_type === 'No Pay Leave')
                    <td colspan="2" class="text-center">{{ $leavetype }}</td>
                @endif
                @if($record->leave_type != 'No Pay Leave' && $record->leave_type != '')
                    <td colspan="7" class="text-center">{{ $leavetype }}</td>
                @else
                    @if($record->leave_type == '')
                    <td>&nbsp;</td>
                    <td class="text-right">
                        @if($record->in_time != '' || $record->out_time != '')
                            {{ number_format($employee['extra']['basic_base_rate'], 2) }}
                        @endif
                    </td>
                    @endif
                    <td class="text-right">
                        @if($record->leave_type === 'No Pay Leave')
                            {{ number_format(abs($employee['extra']['nopay_base_rate']), 2) }}
                        @endif
                        @if($paidAdvanceAmount > 0)
                            {{ number_format($paidAdvanceAmount, 2) }}
                        @endif
                    </td>
                    @if($record->leave_type != '')
                        <td colspan="4" class="text-center">{{ $leavetype }}</td>
                    @else
                    <td>{{ $record->in_time }}</td>
                    <td>{{ $record->out_time }}</td>
                    <td class="text-center">
                        @if($record->ot_hours > 0)
                            {{ $record->ot_hours }}
                        @endif    
                    </td>
                    <td class="text-right">
                        @if($record->ot_hours > 0)
                            {{ number_format(($record->ot_hours * $employee['extra']['othrs1_base_rate']), 2) }}
                        @endif
                    </td>
                    @endif
                @endif
                <td class="text-right">{{ number_format($estimate_salary, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endforeach
<script>
$('#exportPdfBtn').click(function () {
    var { jsPDF } = window.jspdf;
    var doc = new jsPDF('p', 'pt', 'A4');

    var tables = $('.exporttable');

    if (!tables.length) {
        alert('No report data to export. Generate a report first.');
        return;
    }

    tables.each(function (index, table) {
        doc.autoTable({
            html: table,
            startY: 40,
            margin: { top: 40, left: 10, right: 10 },
            theme: 'striped',
            styles: {
                fontSize: 8,
                cellPadding: 3,
                overflow: 'linebreak',
                valign: 'middle'
            },
            headStyles: {
                fillColor: [200, 200, 200],
                textColor: [0, 0, 0],
                fontSize: 8
            },
            bodyStyles: {
                textColor: [0, 0, 0]
            },
            didParseCell: function (data) {
                // data.cell.raw is the original DOM element for this cell
                var cellEl = data.cell.raw;
                if (cellEl && cellEl.classList) {
                    if (cellEl.classList.contains('text-right')) {
                        data.cell.styles.halign = 'right';
                    } else if (cellEl.classList.contains('text-center')) {
                        data.cell.styles.halign = 'center';
                    } else if (cellEl.classList.contains('text-left')) {
                        data.cell.styles.halign = 'left';
                    }
                }
            }
        });

        if (index < tables.length - 1) {
            doc.addPage();
        }
    });

    var departmenttext = $("#department option:selected").text();
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();

    var doctitle = 'estimate_salary_' + departmenttext + '_from_' + from_date + '_to_' + to_date;

    doc.save(doctitle + '.pdf');
});
</script>