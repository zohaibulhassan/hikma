@extends('adminlte::page')

@section('title', 'Courses  | ' . Config::get('adminlte.title'))

@section('content_header')
    <h1>Courses</h1>
@stop

@section('content')
     
    {{--Show message if any--}}
    @include('layouts.flash-message')

    <div class="card">
        <div class="row p-3">
        @foreach($Courses as $Course)
            <div class="col-3">
                <a href="{{url('/courses/card',$Course->id)}}">
                <div class="card border-info mb-3 border border-dark" style="max-width: 18rem;height: 9rem;">
                    <div class="card-body text-dark">
                    <h5 class="card-title font-weight-bold" style="padding: 7px;border-bottom: 1px solid #343a40;">{{ $Course->name }}</h5>
                    </div>
                </div>
                </a>
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
