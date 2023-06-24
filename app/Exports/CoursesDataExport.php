<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CoursesDataExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        
        $courseid = $this->data['courseID'];
        $coursename = $this->data['coursename'];
        $subcourseid = $this->data['subcoursesid'];

        if ($subcourseid != null) {
            $subcoursename = $this->data['subcourses'];
            $programname = $this->data['programname'];
            $day = $this->data['day'];
            $timein = $this->data['timein'];
            $timeout = $this->data['timeout'];
            $timeformat = $this->data['timeformat'];

            $rows = [];

            foreach ($subcourseid as $index => $subcourseid) {
                $courseIDValue = $courseid;
                $coursenameValue = $coursename[0];
                $subcourseidValue = $subcourseid;
                $subcoursenameValue = isset($subcoursename[$index]) ? $subcoursename[$index] : null;
                $programnameValue = isset($programname[$index]) ? $programname[$index] : null;
                $dayValue = isset($day[$index]) ? $day[$index] : null;
                $timeinValue = isset($timein[$index]) ? $timein[$index] : null;
                $timeoutValue = isset($timeout[$index]) ? $timeout[$index] : null;
                $timeformatValue = isset($timeformat[$index]) ? $timeformat[$index] : null;

                $rows[] = [
                    $courseIDValue,
                    $coursenameValue,
                    $subcourseidValue['id'],
                    $subcoursenameValue[0],
                    $programnameValue[0],
                    $dayValue[0],
                    $timeinValue[0],
                    $timeoutValue[0],
                    $timeformatValue[0],
                ];
            }
        }
        else{
            $rows[] = [
                $courseid,
                $coursename[0],
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ];
        }
       


        $collection = new Collection($rows);

        return $collection;
    }

    public function headings(): array
    {
        return [
            'Course ID',
            'Course Name',
            'Sub Course ID',
            'Sub Course Name',
            'Program Name',
            'Day',
            'Time In',
            'Time Out',
            'Time Format',
        ];
    }
}