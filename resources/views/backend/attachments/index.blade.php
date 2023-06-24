@extends('adminlte::page')

@section('title', 'Attendances | ' . Config::get('adminlte.title'))

<style>
    .mt-100{margin-top: 100px}body{background: #00B4DB;background: -webkit-linear-gradient(to right, #0083B0, #00B4DB);background: linear-gradient(to right, #0083B0, #00B4DB);color: #514B64;min-height: 100vh}
    .choices__inner {
        background: #fff !important;
        border-radius: 7px !important;
    }
    .choices{
        margin-bottom:5px !important;
    }

    .file-container {
  white-space: nowrap;
  overflow-x: auto;
  padding: 10px;
}

.file-item {
  display: inline-block;
  margin-right: 10px;
  text-align: center;
}

.file-item {
  margin-bottom: 10px;
}

.pdf-preview {
  max-width: 70%;
  height: auto;
}

.pptx-preview {
  max-width: 70%;
  height: auto;/* Adjust the height as needed */
  background-color: #eee; /* Placeholder background color */
  border: 1px solid #ccc; /* Placeholder border */
  margin-bottom: 5px; /* Spacing between preview and file name */
}

.video-preview {
  width: 200px; /* Adjust the width as needed */
  height: auto;
}

.file-name {
  font-size: 14px; /* Adjust the font size as needed */
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.loader {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: translate(-50%, -50%) rotate(0deg); }
  100% { transform: translate(-50%, -50%) rotate(360deg); }
}


    /* Updated file upload button style */
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
  

@section('content_header')
    <h1>Attendances</h1>
@stop

@section('content')
    {{-- Show message if any --}}
    @include('layouts.flash-message')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Add Attachments</h3>
        </div>
{{-- 
            {{ Form::open(array('url' => route($data->form_action), 'method' => 'POST','autocomplete' => 'off', 'files' => true)) }}
            {{ Form::hidden('id', $data->id, array('id' => 'user_id')) }} --}}

            <form action="{{ url('upload-attachment')}}" method="POST" enctype="multipart/form-data">
@csrf
                <div class="card-body">
                    
            <div class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Course</strong>
                </div>
                <div class="col-sm-10 col-content">
                    <select id="" placeholder="Select Courses" class="form-control courses-dropdown" name="courses_id">
                        <!--<option selected>Select Day</option>-->
                        @foreach($courses as $course)
                        <option value="{{ $course->id  }}">{{ $course->name }}</option>
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
                        <option value="">---Select Course---</option>
                    </select>
                    <small class="form-text text-muted">
                        <i class="fa fa-question-circle" aria-hidden="true"></i> Sub Course.
                    </small>
                </div>
            </div>
            
            <div class="form-group row">
                <div class="col-sm-2 col-form-label">
                    <strong class="field-title">Add Attachments</strong>
                </div>
                <div class="col-sm-10 col-content">
                    <input type="file" id="fileInput" name="files[]" multiple>
                </div>
            </div>
            <div class="container card w-auto">    
                <div id="fileContainer" class="file-container"></div>
            </div>
            
            
            <button class="btn btn-primary" type="submit">Add Attachment</button>
          </div>
          
        </form>
            {{-- {{ Form::close() }} --}}



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
    {{-- Button --}}
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/dataTables.buttons.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.colVis.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.html5.js') }}"></script>
    <script src="{{ asset('vendor/datatables-plugins/buttons/js/buttons.print.js') }}"></script>

    {{-- Datepicker --}}
    <script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/main_index.js') . '?v=' . rand(99999, 999999) }}"></script>
    

<script>
    
        $(document).ready(function(){
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

        const fileInput = document.getElementById('fileInput');
fileInput.addEventListener('change', handleFileSelect, false);

function handleFileSelect(event) {
  const fileContainer = document.getElementById('fileContainer');
  const files = event.target.files; // Get the selected files

  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    const fileName = file.name;
    const fileExtension = fileName.split('.').pop().toLowerCase();
    const fileSizeInMB = file.size / (1024 * 1024);

    // Check if the file size exceeds 16 MB
    if (fileSizeInMB > 16) {
      const fileItem = document.createElement('div');
      fileItem.className = 'file-item';

      const errorMessageElement = document.createElement('p');
      errorMessageElement.className = 'error-message';
      errorMessageElement.textContent = `Error: ${fileName} exceeds the maximum file size limit of 16 MB.`;
      fileItem.appendChild(errorMessageElement);

      fileContainer.appendChild(fileItem);
      continue; // Skip the file and move to the next one
    }

    // Check if the file extension is allowed
    if (fileExtension === 'pdf') {
      // Process PDF file
      const fileItem = document.createElement('div');
      fileItem.className = 'file-item';

      const embedElement = document.createElement('embed');
      embedElement.src = URL.createObjectURL(file);
      embedElement.className = 'pdf-preview';
      fileItem.appendChild(embedElement);

      const fileNameElement = document.createElement('p');
      fileNameElement.className = 'file-name';

      if (fileName.length > 20) {
        fileNameElement.textContent = fileName.substring(0, 20) + '...';
      } else {
        fileNameElement.textContent = fileName;
      }

      fileItem.appendChild(fileNameElement);

      fileContainer.appendChild(fileItem);
    } else if (fileExtension === 'pptx') {
      // Process PPTX file
      const fileItem = document.createElement('div');
      fileItem.className = 'file-item';

      const pptxPreview = document.createElement('div');
      pptxPreview.className = 'pptx-preview';
      fileItem.appendChild(pptxPreview);

      const fileNameElement = document.createElement('p');
      fileNameElement.className = 'file-name';

      if (fileName.length > 20) {
        fileNameElement.textContent = fileName.substring(0, 20) + '...';
      } else {
        fileNameElement.textContent = fileName;
      }

      fileItem.appendChild(fileNameElement);

      fileContainer.appendChild(fileItem);
    } else if (fileExtension === 'mp4' || fileExtension === 'mov' || fileExtension === 'avi') {
      // Process video file
      const fileItem = document.createElement('div');
      fileItem.className = 'file-item';

      const loaderElement = document.createElement('div');
      loaderElement.className = 'loader';
      fileItem.appendChild(loaderElement);

      const videoElement = document.createElement('video');
      videoElement.src = URL.createObjectURL(file);
      videoElement.className = 'video-preview';
      videoElement.controls = true;
      videoElement.addEventListener('loadeddata', function() {
        fileItem.removeChild(loaderElement); // Remove the loader when the video is loaded
      });
      fileItem.appendChild(videoElement);

      const fileNameElement = document.createElement('p');
      fileNameElement.className = 'file-name';

      if (fileName.length > 20) {
        fileNameElement.textContent = fileName.substring(0, 20) + '...';
      } else {
        fileNameElement.textContent = fileName;
      }

      fileItem.appendChild(fileNameElement);

      fileContainer.appendChild(fileItem);
    }
  }
}



        
</script>


<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
 <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
<script src="{{ asset('js/backend/users/form.js'). '?v=' . rand(99999,999999) }}"></script>
@stop
