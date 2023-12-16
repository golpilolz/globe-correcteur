<?php

namespace App\Service;

use App\Entity\Record;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportService
{
    const BLUE = "7092be";
    const BLUE_ODD = "a6bbd7";
    const BLUE_EVEN = "dce4ef";
    const GREEN = "83b6b9";
    const GREEN_ODD = "a9ccce";
    const GREEN_EVEN = "cee2e3";

    const STYLE_TITLE = [
        'font' => [
            'bold' => true,
            'size' => 16,
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ];

    const STYLE_HEADER_CREDIT = [
        'font' => [
            'bold' => true,
            'size' => 12,
            'color' => [
                'rgb' => 'FFFFFF',
            ],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => 'solid',
            'color' => [
                'rgb' => self::GREEN,
            ],
        ],
    ];

    const STYLE_HEADER_DEBIT = [
        'font' => [
            'bold' => true,
            'size' => 12,
            'color' => [
                'rgb' => 'FFFFFF',
            ],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => 'solid',
            'color' => [
                'rgb' => self::BLUE,
            ],
        ],
    ];

    const STYLE_ROW_CREDIT_ODD = [
        'fill' => [
            'fillType' => 'solid',
            'color' => [
                'rgb' => self::GREEN_ODD,
            ],
        ],
    ];

    const STYLE_ROW_CREDIT_EVEN = [
        'fill' => [
            'fillType' => 'solid',
            'color' => [
                'rgb' => self::GREEN_EVEN,
            ],
        ],
    ];

    const STYLE_ROW_DEBIT_ODD = [
        'fill' => [
            'fillType' => 'solid',
            'color' => [
                'rgb' => self::BLUE_ODD,
            ],
        ],
    ];

    const STYLE_ROW_DEBIT_EVEN = [
        'fill' => [
            'fillType' => 'solid',
            'color' => [
                'rgb' => self::BLUE_EVEN,
            ],
        ],
    ];

    /**
     * @param Record[] $credits
     * @param Record[] $debits
     * @param string $month
     * @param string $year
     * @return Spreadsheet
     */
    public function generateExcel(array $credits, array $debits, string $month, string $year): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("Globe Correcteur")
            ->setLastModifiedBy("Globe Correcteur")
            ->setTitle("Globe Correcteur - Compta : $month/$year");
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1;

        $sheet->mergeCells("A$row:G$row");
        $sheet->setCellValue("A$row", "Globe Correcteur Compta : $month/$year");
        $sheet->getStyle("A$row")->applyFromArray(self::STYLE_TITLE);
        $row+=2;

        $sheet->setCellValue("A$row", 'Libellé');
        $sheet->getStyle("A$row")->applyFromArray(self::STYLE_HEADER_CREDIT);
        $sheet->setCellValue("B$row", 'Montant');
        $sheet->getStyle("B$row")->applyFromArray(self::STYLE_HEADER_CREDIT);
        $sheet->setCellValue("C$row", 'Date');
        $sheet->getStyle("C$row")->applyFromArray(self::STYLE_HEADER_CREDIT);
        $sheet->setCellValue("E$row", 'Libellé');
        $sheet->getStyle("E$row")->applyFromArray(self::STYLE_HEADER_DEBIT);
        $sheet->setCellValue("F$row", 'Montant');
        $sheet->getStyle("F$row")->applyFromArray(self::STYLE_HEADER_DEBIT);
        $sheet->setCellValue("G$row", 'Date');
        $sheet->getStyle("G$row")->applyFromArray(self::STYLE_HEADER_DEBIT);
        $row++;

        $startRow = $row;
        $rowCredit = $row;
        $rowDebit = $row;

        foreach ($credits as $credit) {
            $sheet->setCellValue("A$rowCredit", $credit->getLibelle());
            $sheet->setCellValue("B$rowCredit", $credit->getAmount());
            $sheet->setCellValue("C$rowCredit", $credit->getCreatedAt()->format('d/m/Y'));
            if($rowDebit % 2 != 0) {
                $sheet->getStyle("A$rowCredit:C$rowCredit")->applyFromArray(self::STYLE_ROW_CREDIT_ODD);
            } else {
                $sheet->getStyle("A$rowCredit:C$rowCredit")->applyFromArray(self::STYLE_ROW_CREDIT_EVEN);
            }
            $rowCredit++;
        }

        foreach ($debits as $debit) {
            $sheet->setCellValue("E$rowDebit", $debit->getLibelle());
            $sheet->setCellValue("F$rowDebit", $debit->getAmount());
            $sheet->setCellValue("G$rowDebit", $debit->getCreatedAt()->format('d/m/Y'));
            if($rowDebit % 2 != 0) {
                $sheet->getStyle("E$rowDebit:G$rowDebit")->applyFromArray(self::STYLE_ROW_DEBIT_ODD);
            } else {
                $sheet->getStyle("E$rowDebit:G$rowDebit")->applyFromArray(self::STYLE_ROW_DEBIT_EVEN);
            }
            $rowDebit++;
        }

        ($rowCredit > $rowDebit) ? $row = $rowCredit : $row = $rowDebit;
        $row++;

        $sheet->setCellValue("A$row", 'Total crédits');
        $sheet->getStyle("A$row")->applyFromArray(self::STYLE_HEADER_CREDIT);
        $sheet->setCellValue("B$row", "=SUM(B$startRow:B$rowCredit)");
        $sheet->getStyle("B$row")->applyFromArray(self::STYLE_ROW_CREDIT_EVEN);
        $sheet->setCellValue("E$row", 'Total Débits');
        $sheet->getStyle("E$row")->applyFromArray(self::STYLE_HEADER_DEBIT);
        $sheet->setCellValue("F$row", "=SUM(F$startRow:F$rowDebit)");
        $sheet->getStyle("F$row")->applyFromArray(self::STYLE_ROW_DEBIT_EVEN);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getColumnDimensionByColumn(1)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(2)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(3)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(4)->setAutoSize(false);
        $sheet->getColumnDimensionByColumn(4)->setWidth(5);
        $sheet->getColumnDimensionByColumn(5)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(6)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(7)->setAutoSize(true);

        return $spreadsheet;
    }
}