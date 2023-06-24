@extends('adminlte::page')
<!-- page title -->
@section('title', 'Create and Update Users ' . Config::get('adminlte.title'))

<style>
    .mt-100{margin-top: 100px}body{background: #00B4DB;background: -webkit-linear-gradient(to right, #0083B0, #00B4DB);background: linear-gradient(to right, #0083B0, #00B4DB);color: #514B64;min-height: 100vh}
    .choices__inner {
        background: #fff !important;
        border-radius: 7px !important;
    }
    .choices{
        margin-bottom:5px !important;
    }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
    
@section('content_header')
    <h1>Users</h1>
@stop

@section('content')
    {{--Show message if any--}}
    @include('layouts.flash-message')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Add or Update</h3>
        </div>

        {{ Form::open(array('url' => route($data->form_action), 'method' => 'POST','autocomplete' => 'off', 'files' => true)) }}
        {{ Form::hidden('id', $data->id, array('id' => 'user_id')) }}

        <div class="card-body">
            
            <div class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Name</strong>
                </div>
                <div class="col-sm-10 col-content">
                    {{ Form::text('name', $data->name, array('class' => 'form-control', 'required')) }}
                    <small class="form-text text-muted">
                        <i class="fa fa-question-circle" aria-hidden="true"></i> User name.
                    </small>
                </div>
            </div>
            
            <div class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Course</strong>
                </div>
                <div class="col-sm-10 col-content">
                    <select id="" placeholder="Select Courses" class="form-control courses-dropdown" name="courses_id">
                    <!--<option selected>Select Day</option>-->
                    @foreach($courses as $id => $course)
                        <option value="{{ $id }}">{{ $course }}</option>
                    @endforeach
                    </select>
                    <small class="form-text text-muted">
                        <i class="fa fa-question-circle" aria-hidden="true"></i> Course.
                    </small>
                </div>
            </div>
            
            <div class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Sub Course</strong>
                </div>
                <div class="col-sm-10 col-content">
                    <select id="" placeholder="Select Sub Courses" class="form-control sub-courses-dropdown" name="sub_courses_id[]">
                    <!--<option selected>Select Day</option>-->
                    <!--@foreach($subcourses as $subcourse)-->
                    <!--    <option value="{{ $subcourse }}">{{ $subcourse }}</option>-->
                    <!--@endforeach-->
                    </select>
                    <small class="form-text text-muted">
                        <i class="fa fa-question-circle" aria-hidden="true"></i> Sub Course.
                    </small>
                </div>
            </div>
            
            <!--<div class="form-group row">-->
            <!--    <div class="col-sm-2 col-form-label">-->
            <!--        <strong class="field-title">Sub Courses</strong>-->
            <!--    </div>-->
            <!--    <div class="col-sm-10 col-content">-->
            <!--        <select class="form-control" name="editor" aria-label="Default select example">-->
            <!--            <option value="teacher">QS 1</option>-->
            <!--            <option value="student">QHD 1</option>-->
            <!--            <option value="student">Prep Oula 1</option>-->
            <!--            <option value="student">QS 2</option>-->
                        
            <!--        </select>-->
            <!--    </div>-->
            <!--</div>-->
            
            <div class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Email</strong>
                </div>
                <div class="col-sm-10 col-content">
                    {{ Form::email('email',$data->email, array('class' => 'form-control', 'required')) }}
                    <small class="form-text text-muted">
                        <i class="fa fa-question-circle" aria-hidden="true"></i> User email, this email for login.
                    </small>
                </div>
            </div>

            <div id="form-password" class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Password</strong>
                </div>
                <div class="col-sm-10 col-content">
                    {{ Form::password('password', array('id' => 'password', 'class' => 'form-control', 'autocomplete' => 'new-password')) }}
                    @if($data->page_type === 'edit')
                        <small id="passwordHelpBlock" class="form-text text-muted">
                            <i class="fa fa-question-circle" aria-hidden="true"></i> Leave it blank if you don't want to change
                        </small>
                    @else
                        <small class="form-text text-muted">
                            <i class="fa fa-question-circle" aria-hidden="true"></i> User password, this password for login.
                        </small>
                    @endif
                    <label class="reset-field-password" for="show-password"><input id="show-password" type="checkbox" name="show-password" value="1"> Show Password</label>
                </div>
            </div>

            {{--  image  --}}
            <div id="form-image" class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Image</strong>
                </div>
                <div class="col-sm-10 col-content">
                    <input class="custom-file-input" name="image" type="file"
                           accept="image/gif, image/jpeg,image/jpg,image/png" data-max-width="800"
                           data-max-height="400">
                    <label class="custom-file-label" for="customFile">Choose file</label>
                    <span
                        class="image-upload-label"><i class="fa fa-question-circle" aria-hidden="true"></i> Please upload the image (Recommended size: 160px × 160px, max 5MB)</span>
                    <div class="image-preview-area">
                        <div id="image_preview" class="image-preview">
                            @if ($data->page_type == 'edit')
                                <img src="{{ asset('uploads/'.$data->image) }}" width="160" title="image"
                                     class="img-circle elevation-2">
                            @else
                                <img src="{{ asset('img/default-user.png') }}" width="160" title="image"
                                     class="img-circle elevation-2">
                            @endif
                        </div>
                        {{-- only image has main image, add css class "show" --}}
                        <p class="delete-image-preview @if ($data->image != null && $data->image != 'default-user.png') show @endif"
                           onclick="deleteImagePreview(this);"><i class="fa fa-window-close"></i></p>
                        {{-- delete flag for already uploaded image in the server --}}
                        <input name="image_delete" type="hidden">
                    </div>
                </div>
            </div>

            <div class="form-group row d-none">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Role</strong>
                </div>
                <!--<div class="col-sm-10 col-content">-->
                <!--    {{ Form::select('role', $role, $data->role, array('id' => 'role', 'class' => 'form-control', 'required')) }}-->
                <!--    <small class="form-text text-muted">-->
                <!--        <i class="fa fa-question-circle" aria-hidden="true"></i> User role.-->
                <!--    </small>-->
                <!--</div>-->
                <div class="col-sm-10 col-content">
                    <select class="form-control" name="role" aria-label="Default select example">
                        <!--<option selected>Select As User...</option>-->
                        <!--<option value="teacher">Teacher</option>-->
                        <option value="3">Staff</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Role</strong>
                </div>
                <div class="col-sm-10 col-content">
                    <select class="form-control" name="editor" aria-label="Default select example">
                        <option selected>Select As User...</option>
                        <!--<option value="teacher">Teacher</option>-->
                        <option value="student">Student</option>
                    </select>
                </div>
            </div>

        </div>

        <div class="card-footer">
            <div id="form-button">
                <div class="col-sm-12 text-center top20">
                    <button type="submit" name="submit" id="btn-admin-member-submit"
                            class="btn btn-primary">{{ $data->button_text }}</button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>

    <!-- /.card -->
    </div>
    <!-- /.row -->
    <!-- /.content -->
@stop

@section('css')
@stop

@section('js')
<script>
    $(document).ready(function(){
    
    var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
       removeItemButton: true,
       maxItemCount:50,
       searchResultLimit:50,
       renderChoiceLimit:50
     }); 
    
    /*------------------------------------------
    --------------------------------------------
    sUB cOURSES
    --------------------------------------------
    --------------------------------------------*/
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
     <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>var typePage = "{{ $data->page_type }}";</script>
    <script src="{{ asset('js/backend/users/form.js'). '?v=' . rand(99999,999999) }}"></script>
@stop
