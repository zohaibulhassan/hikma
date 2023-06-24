@extends('adminlte::page')
<!-- page title -->
@section('title', 'Schedule Courses' . Config::get('adminlte.title'))
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
    <h1>Schedule Courses</h1>
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
                    {{ Form::select('sub_courses_id', $subcourses, $data->subcourses, array('id' => 'subcourses', 'class' => 'form-control sub-courses-dropdown', 'required')) }}
                    <small class="form-text text-muted">
                        <i class="fa fa-question-circle" aria-hidden="true"></i>Sub Course.
                    </small>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Program</strong>
                </div>
                <div class="col-sm-10 col-content">
                    <select class="form-control" name="program" aria-label="Default select example">
                        <option selected>Select Program</option>
                        <option value="weekday-pm">Weekday-PM</option>
                        <option value="weekend-am">Weekend-AM</option>
                        <option value="weekend-pm">Weekend-PM</option>
                    </select>
                    <small class="form-text text-muted">
                        <i class="fa fa-question-circle" aria-hidden="true"></i> Program.
                    </small>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Day</strong>
                </div>
                <div class="col-sm-10 col-content">
                    <select id="choices-multiple-remove-button" placeholder="Select Day's" class="form-control" name="day[]" multiple>
                    <!--<option selected>Select Day</option>-->
                    <option value="monday">Monday</option>
                    <option value="tuesday">Tuesday</option>
                    <option value="wednesday">Wednesday</option>
                    <option value="thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="saturday">Saturday</option>
                    <option value="sunday">Sunday</option>
                    </select>
                    <small class="form-text text-muted">
                        <i class="fa fa-question-circle" aria-hidden="true"></i> Day.
                    </small>
                </div>
            </div>
            
            
            <!--<div class="form-group row">-->
            <!--    <div class="col-sm-2 col-form-label">-->
            <!--        <strong class="field-title">Day</strong>-->
            <!--    </div>-->
            <!--    <div class="col-sm-10 col-content">-->
            <!--        <select class="form-control" name="day" aria-label="Default select example">-->
            <!--            <option selected>Select Day</option>-->
            <!--            <option value="tuesday">Tuesday</option>-->
            <!--            <option value="wednesday">Wednesday</option>-->
            <!--            <option value="saturday">Saturday</option>-->
            <!--            <option value="sunday">Sunday</option>-->
            <!--        </select>-->
            <!--        <small class="form-text text-muted">-->
            <!--            <i class="fa fa-question-circle" aria-hidden="true"></i> Day.-->
            <!--        </small>-->
            <!--    </div>-->
            <!--</div>-->

            <!--<div class="form-group row">-->
            <!--    <div class="col-sm-2 col-form-label">-->
            <!--        <strong class="field-title">Time In</strong>-->
            <!--    </div>-->
            <!--    <div class="col-sm-10 col-content">-->
            <!--        {{ Form::text('time_in', $data->name, array('class' => 'form-control', 'required')) }}-->
            <!--        <small class="form-text text-muted">-->
            <!--            <i class="fa fa-question-circle" aria-hidden="true"></i>Time In.-->
            <!--        </small>-->
            <!--    </div>-->
            <!--</div>-->
            
            <div class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Time In</strong>
                </div>
                <div class="col-sm-8 col-content">
                    <select class="form-control" name="time_in" aria-label="Default select example">
                        <option selected>Select Time in</option>
                        <option value="01:00:00">01:00:00</option>
                        <option value="02:00:00">02:00:00</option>
                        <option value="03:00:00">03:00:00</option>
                        <option value="04:00:00">04:00:00</option>
                        <option value="05:00:00">05:00:00</option>
                        <option value="06:00:00">06:00:00</option>
                        <option value="07:00:00">07:00:00</option>
                        <option value="08:00:00">08:00:00</option>
                        <option value="09:00:00">09:00:00</option>
                        <option value="10:00:00">10:00:00</option>
                        <option value="11:00:00">11:00:00</option>
                        <option value="12:00:00">12:00:00</option>
                    </select>
                    <small class="form-text text-muted">
                        <i class="fa fa-question-circle" aria-hidden="true"></i> Time in.
                    </small>
                </div>
                <div class="col-sm-2 col-content">
                    <!--<input class="form-control" name="time_in_apm">-->
                    <select class="form-control" name="time_in_apm" aria-label="Default select example">
                        <option value="AM">AM</option>
                        <option value="PM">PM</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Time Out</strong>
                </div>
                <div class="col-sm-8 col-content">
                    <select class="form-control" name="time_out" aria-label="Default select example">
                        <option selected>Select Time Out</option>
                        <option value="01:00:00">01:00:00</option>
                        <option value="02:00:00">02:00:00</option>
                        <option value="03:00:00">03:00:00</option>
                        <option value="04:00:00">04:00:00</option>
                        <option value="05:00:00">05:00:00</option>
                        <option value="06:00:00">06:00:00</option>
                        <option value="07:00:00">07:00:00</option>
                        <option value="08:00:00">08:00:00</option>
                        <option value="09:00:00">09:00:00</option>
                        <option value="10:00:00">10:00:00</option>
                        <option value="11:00:00">11:00:00</option>
                        <option value="12:00:00">12:00:00</option>
                    </select>
                    <small class="form-text text-muted">
                        <i class="fa fa-question-circle" aria-hidden="true"></i> Time Out.
                    </small>
                </div>
                <div class="col-sm-2 col-content">
                    <select class="form-control" name="time_out_apm" aria-label="Default select example">
                        <option value="AM">AM</option>
                        <option value="PM">PM</option>
                    </select>
                </div>
            </div>

            <!--<div class="form-group row">-->
            <!--    <div class="col-sm-2 col-form-label">-->
            <!--        <strong class="field-title">Time Out</strong>-->
            <!--    </div>-->
            <!--    <div class="col-sm-10 col-content">-->
            <!--        {{ Form::text('time_out', $data->name, array('class' => 'form-control', 'required')) }}-->
            <!--        <small class="form-text text-muted">-->
            <!--            <i class="fa fa-question-circle" aria-hidden="true"></i>Time Out.-->
            <!--        </small>-->
            <!--    </div>-->
            <!--</div>-->
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
    <script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>var typePage = "{{ $data->page_type }}";</script>
    <script src="{{ asset('js/backend/time/form.js'). '?v=' . rand(99999,999999) }}"></script>
@stop
