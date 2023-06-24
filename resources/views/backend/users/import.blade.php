@extends('adminlte::page')
<!-- page title -->
@section('title', 'Import Data ' . Config::get('adminlte.title'))

@section('content_header')
    <h1>Import Data</h1>
@stop

@section('content')
    {{-- Show message if any --}}
    @include('layouts.flash-message')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Import</h3>
        </div>

        <form action="{{url('/users/blukUpload')}}" method="post" enctype="multipart/form-data"> 
@csrf
        <div class="card-body">

            <div class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Import Data</strong>
                </div>
                <div class="col-sm-10 col-content">
                    <label for="attachment">Upload Attachment:</label>
                    <input type="file" name="attachment" id="attachment" accept=".xlsx, .xls">
                </div>
            </div>
{{-- 
            <div class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Download Template</strong>
                </div>
                <div class="col-sm-10 col-content">
                    <a href="{{ asset('img/template.csv') }}" download=""><button type="button" class="btn btn-success">
                            Download Template CSV</button></a>
                </div>
            </div> --}}

            {{-- <div class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Instructions</strong>
                </div>
                <div class="col-sm-10 col-content">
                    <button type="button" class="btn btn-info collapsed" data-toggle="collapse" data-target="#instructions"
                        aria-expanded="false">Show Instructions </button>
                    <div id="instructions" class="collapse col-content" aria-expanded="false">
                        <img src="{{ asset('img/import_csv.png') }}" class="img-fluid">
                    </div>
                </div>
            </div> --}}

        </div>

        <div class="card-footer">
            <div id="form-button">
                <div class="col-sm-12 text-center top20">
                    <button type="submit" name="submit" id="btn-admin-member-submit"
                        class="btn btn-primary">{{ $data->button_text }}</button>
                </div>
            </div>
        </div>
       
    </div>
</form>
    <!-- /.card -->
    </div>
    <!-- /.row -->
    <!-- /.content -->
@stop

@section('css')

@stop

@section('js')
    <script>
        var typePage = "{{ $data->page_type }}";
    </script>

    <script src="{{ asset('js/backend/histories/form.js') . '?v=' . rand(99999, 999999) }}"></script>
@stop
