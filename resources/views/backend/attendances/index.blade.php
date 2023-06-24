{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Attendances  | ' . Config::get('adminlte.title'))

@section('content_header')
    <h1>Attendances</h1>
@stop

@section('content')
    {{--Show message if any--}}
    @include('layouts.flash-message')

    <div class="card">
        <div class="row">
            
            <div class="card-header" style="border-bottom: 0px">
                <h3 class="card-title">List</h3>
            </div>
            
            <!--<div class="card-header col-4" style="border-bottom: 0px">-->
            <!--    <select class="form-select form-control" onchange="location = this.value;" aria-label="Default select example">-->
            <!--        <option selected>Filter By Course</option>-->
            <!--        <option value="/hikma/attendances/teacher">Teacher</option>-->
            <!--        <option value="/hikma/attendances/student">Student</option>-->
            <!--        <option value="/hikma/attendances/">All Records</option>-->
            <!--    </select>-->
            <!--</div>-->
            
            <div class="card-header col-4" style="border-bottom: 0px">
                <select class="form-select form-control courses-dropdown" aria-label="Default select example">
                    <option selected>Filter By Course</option>
                    @foreach($courses as $course)
                    <option value="{{$course->id}}">{{$course->name}}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="card-header col-4" style="border-bottom: 0px">
                <select class="form-select form-control sub-courses-dropdown" aria-label="Default select example">
                </select>
            </div>
            
        </div>    

        <div class="card-body">
            <!-- Filtering -->
            <div id="date_filter" class="form-inline">
                <div class="form-group mb-2">
                    <label for="from"></label>
                    <div class="input-group">
                        <input type="text" name="dateFrom" class="form-control" id="min" placeholder="From Date" autocomplete="off">
                        <div class="input-group-append" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <label for="to"></label>
                    <div class="input-group">
                        <input type="text" name="dateTo" class="form-control" id="max" placeholder="To Date" autocomplete="off">
                        <div class="input-group-append" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <!-- Filtering -->

            <div class="table-responsive">
                {!! $html->table(['class' => 'table table-hover']) !!}
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables-plugins/buttons/css/buttons.bootstrap4.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker.css') }}">
@stop

@section('js')
    <!--Data tables-->
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/jszip/jszip.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/pdfmake/pdfmake.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/pdfmake/vfs_fonts.js') }}"></script>
    {{--Button--}}
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/dataTables.buttons.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.colVis.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.html5.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.print.js') }}"></script>
    {!! $html->scripts() !!}
    {{--Datepicker--}}
    <script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/main_index.js'). '?v=' . rand(99999,999999) }}"></script>
    <script>
        $(document).ready(function () {
            $('#min, #max').change(function () {
                window.LaravelDataTables["dataTableBuilder"].draw();
            });

            $('#min').datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: 'TRUE',
                autoclose: true,
                changeMonth: true,
                changeYear: true,
                onSelect: function () {
                    window.LaravelDataTables["dataTableBuilder"].draw();
                },
            });

            $("#max").datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: 'TRUE',
                autoclose: true,
                changeMonth: true,
                changeYear: true,
                onSelect: function () {
                    window.LaravelDataTables["dataTableBuilder"].draw();
                },
            });
            
            $('.courses-dropdown').on('change', function () {
                // alert(1);
                var idCourse = this.value;
                // alert(idCourse);
                $(".sub-courses-dropdown").html('');
                $.ajax({
                    url: "{{url('sub-course-user')}}",
                    type: "POST",
                    data: {
                        courses_id: idCourse,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('.sub-courses-dropdown').html('<option value="">-- Select Sub Course --</option>');
                        $.each(result.sub_courses, function (key, value) {
                            $(".sub-courses-dropdown").append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });
                    }
                });
            });
        });
    </script>
@stop
