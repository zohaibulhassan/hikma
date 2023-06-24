<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Writer;

class StudentsDataExport implements FromCollection, WithHeadings, WithMultipleSheets
{
    use Exportable;

    protected $data;
    protected $legend;

    public function __construct($data, $legend)
    {
        $this->data = $data;
        $this->legend = $legend;
    }

    public function collection()
    {
        return new Collection($this->data);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            // 'Password',
            'Course Name',
            'Subcourse Name',
        ];
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new StudentsDataSheet($this->data);
        $sheets[] = new LegendSheet($this->legend);

        return $sheets;
    }
}

class StudentsDataSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return new Collection($this->data);
    }

    public function map($row): array
    {
        return [
            $row['id'],
            $row['Name'],
            $row['Email'],
            $row['Password'],
            $row['Course Name'],
            $row['Subcourse Name'],
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Password',
            'Course Name',
            'Subcourse Name',
        ];
    }

    public function title(): string
    {
        return 'Students Data';
    }
}
class LegendSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    protected $legend;

    public function __construct($legend)
    {
        $this->legend = $legend;
    }

    public function collection()
    {
        $data = collect();

        foreach ($this->legend as $course => $subcourses) {
            foreach ($subcourses as $subcourse) {
                $data->push([$course, $subcourse]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Course Name',
            'Subcourse Name',
        ];
    }

    public function title(): string
    {
        return 'Legend';
    }
}