<?php

namespace App\Exports;

use App\Models\casesFiType;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Style;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class CasesExport implements FromCollection, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $dateFrom;
    protected $dateTo;
    protected $status;
    public function __construct($filter)
    {
        $this->dateFrom = $filter['dateFrom'];
        $this->dateTo = $filter['dateTo'];
        $this->status = $filter['status'];
    }
    public function collection()
    {
        $query = casesFiType::with([
            'getCase:id,refrence_number,applicant_name,bank_id',
            'getFiType:id,name',
            'getUser:id,name',
            'getStatus:id,name',
            'getCaseStatus:id,name'
        ]);
        if(Auth::guard('admin')->user()->role != 'superadmin'){
            $assignBank = explode(',', Auth::guard('admin')->user()->banks_assign);
            $query->whereHas('getCase', function ($query) use ($assignBank) {
                $query->whereIn("bank_id", $assignBank);
            });
        }
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->status > 1) {
            $query->where('status', $this->status);
        }
        return $query->get()->map(function ($case) {
            return [
                'PROPOSAL NO.' => optional($case->getCase)->refrence_number,
                'APPLICANT NAME' => optional($case->getCase)->applicant_name,
                'VERIFICATION TYPE' => optional($case->getFiType)->name,
                'ADDRESS' => $case->address,
                'Mobile Number' => $case->mobile,
                'Status' => ucfirst(optional($case->getStatus)->name),
                'Verifier Remark' => $case->supervisor_remarks,
                'SubStatus' => ucfirst(optional($case->getCaseStatus)->name),
                'Uploaded Date' => Carbon::parse($case->created_at)->format('d-m-Y'),
                'Uploaded Time' => Carbon::parse($case->created_at)->format('H:i:s'),
                'Date of Visit' => $case->date_of_visit,
                'Visit Time' => $case->time_of_visit,
                'Agent Name' => optional($case->getUser)->name,
                'Agent Remark' => $case->consolidated_remarks,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'PROPOSAL NO.',
            'APPLICANT NAME',
            'VERIFICATION TYPE',
            'ADDRESS',
            'Mobile Number',
            'Status',
            'Verifier Remark',
            'SubStatus',
            'Uploaded Date',
            'Uploaded Time',
            'Date of Visit',
            'Visit Time',
            'Agent Name',
            'Agent Remark'
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE], // Header text color
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4F81BD'], // Header background color
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK], // Ensure full namespace
                ],
            ],

        ];
        $lastColumn = 'O';
        
        $sheet->getStyle('A1:N1')->applyFromArray($headerStyle); // Adjust columns as necessary
        
        $sheet->getStyle('A1:N' . (count($this->collection())+1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);
    }
}
