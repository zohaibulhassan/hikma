{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Sub Courses | ' . Config::get('adminlte.title'))
<style>
    .card-body {
        padding: 10px !important;
    }
</style>
@section('content_header')

<div class="row">
    <div class="col-md-6">
        <h1>Bluk Upload Data Courses</h1>
    </div>
    
</div>

@stop
@section('content')
    {{--Show message if any--}}
    @include('layouts.flash-message')
    <div class="page_container">
        <div class="col-md-12">
            
            <form action="{{url('courses/BlukUploaddata')}}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="attachment">Upload Attachment:</label>
                <input type="file" name="attachment" id="attachment" accept=".xlsx, .xls">
            </div>

            
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        
    </div>
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