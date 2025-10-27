<?php

namespace App\Exports;

use App\Models\casesFiType;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Facades\Auth;

class CasesCountExport implements FromCollection, WithHeadings, WithStyles
{
    protected $dateTo;
    protected $dateFrom;

    public function __construct($filter)
    {
        $this->dateTo = $filter['dateTo'];
        $this->dateFrom = $filter['dateFrom'];
    }

    public function collection()
    {
        $dateFrom = $this->dateFrom;
        $dateTo = $this->dateTo;

        $query = casesFiType::with([
            'getCase:id,refrence_number,applicant_name,bank_id',
            'getUser:id,name'
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

        $userCasesCount = $query->get()
            ->groupBy('getUser.name')
            ->map(function ($cases, $userName) {
                return [
                    'user' => $userName,
                    'total_cases' => $cases->count(),
                ];
            });

        return collect($userCasesCount->values());
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Case Count',
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
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
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];

        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

        $rowCount = $this->collection()->count() + 1;
        $sheet->getStyle('A1:B' . $rowCount)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);
    }
}
