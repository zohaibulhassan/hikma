{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Users  | ' . Config::get('adminlte.title'))

@section('content_header')
    <h1>Courses</h1>
@stop

@section('content')
    {{--Show message if any--}}
    @include('layouts.flash-message')

    <div class="card">
        <div class="row p-3">
        @foreach($Times as $Time)
            <div class="col-3">
            <a href="/time/edit/{{$Time->id}}">
                <div class="card border-info mb-3 border border-primary" style="max-width: 18rem;height: 13rem;">
                    <div class="card-body text-info">
                    <h5 class="card-title" style="padding: 7px;border-bottom: 1px solid #17a2b8;">{{ $Time->sub_courses_id }}</h5>
                    </div>
                    <div style="padding: 13px;">
                        <h6>{{ $Time->program }}</h6>
                        <h6>{{ $Time->day }}</h6>
                        <h6>{{ $Time->time_in }}</h6>
                        <h6>{{ $Time->time_out }}</h6>
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
