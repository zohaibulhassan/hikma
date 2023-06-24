{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Sub Courses  | ' . Config::get('adminlte.title'))
<style>
    .card-body {
    padding: 10px !important;
}
</style>
@section('content_header')

  <div class="row">
    <div class="col-md-6">
      <h1>Sub Courses</h1>
    </div>
    <div class="col-md-6 text-right">
      <a href="{{url('courses/subcourseExcel',request()->id)}}"  class="btn btn-success ml-2">Excel Download</a>
    </div>
  </div>

@stop

@section('content')
    {{--Show message if any--}}
    @include('layouts.flash-message')

    <div class="card">
        <div class="row p-3">
        @foreach($SubCourses as $SubCourse)
            <div class="col-3">
                <!--<a href="/hikma/courses/schedule/{{$SubCourse->id}}">-->
                <div class="card border-info mb-3 border border-dark" >
                    <div class="card-body text-dark">
                        <h5 class="card-title font-weight-bold" style="padding: 7px;border-bottom: 1px solid #343a40;height: 50px;">{{ $SubCourse->name }}</h5>
                    </div>
                    <h3 class="card-title text-dark" style="padding: 7px;"><span class="font-weight-bold">Program: </span>{{ $SubCourse->program }}</h3>
                        <h3 class="card-title text-dark" style="padding: 7px;"><span class="font-weight-bold" style="float: left;margin-right: 3px;">Day: </span>{{ $SubCourse->day }}</h3>
                        <h3 class="card-title text-dark" style="padding: 7px;"><span class="font-weight-bold">Time In: </span>{{ $SubCourse->time_in }}</h3>
                        <h3 class="card-title text-dark" style="padding: 7px;"><span class="font-weight-bold">Time Out: </span>{{ $SubCourse->time_out }}</h3>
                </div>
                <!--</a>-->
            </div>
            @endforeach
        </div>
    </div>
@stop

@section('css')
    <link href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables-plugins/buttons/css/buttons.bootstrap4.css') }}" rel="stylesheet">
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
    <script src="{{ asset('js/main_index.js') }}"></script>
@stop
