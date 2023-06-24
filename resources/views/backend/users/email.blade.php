{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Users | ' . Config::get('adminlte.title'))

@section('content_header')
    <h1>Email</h1>
@stop

@section('content')
    {{-- Show message if any --}}
    @include('layouts.flash-message')

    <div class="card">
        <div class="card-body">
            <form id="emailForm">
                @csrf
                <div class="form-group">
                    <label for="recipients">Recipients</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="recipients" placeholder="Search for names or emails">
                        <div class="input-group-append">
                            <button type="button" id="cc-bcc-toggle" class="btn btn-primary">Add CC/BCC</button>
                        </div>
                    </div>
                </div>

                <div id="emailResults" class="email-results"></div>
                <div id="selectedEmailsContainer" class="selected-emails-container"></div>

                <div id="cc-bcc-wrapper" style="display: none;">
                    <div class="form-group">
                        <label for="cc">CC</label>
                        <textarea class="form-control" id="cc" rows="3" placeholder="Email addresses"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="bcc">BCC</label>
                        <textarea class="form-control" id="bcc" rows="3" placeholder="Email addresses"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="group">Group</label>
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search sub-courses">
                        <div class="input-group-append">
                            <button type="button" id="searchButton" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                    <select class="form-control" id="group" multiple>
                        @foreach ($courses as $course)
                            <optgroup label="{{ $course->name }}">
                                @foreach ($subCourses as $subCourse)
                                    @if ($subCourse->courses_id === $course->id)
                                        <option data-group="{{ $course->name }}" value="{{ $subCourse->id }}">
                                            {{ $subCourse->name }}</option>
                                    @endif
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" value="HIKMAH INSTITUTE APP | Login Credentials" class="form-control" id="subject"
                        placeholder="Email subject">
                </div>
                <div class="form-group">
                    <label for="body">Body</label>
                    <textarea id="body" name="body"></textarea>
                </div>
                {{-- <button id="sendEmailButton" class="btn btn-primary">Send Email</button> --}}
                <a onclick="sendemail()" class="btn btn-primary">Send Email </a>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.tiny.cloud/1/61ngj8fm356o7qnz204euonztkdxzxkpi3716clsljgwvvru/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>


    <script>
        tinymce.init({
            selector: '#body',
            height: 300,
            menubar: 'file edit format',
            plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
            toolbar: 'undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | formatselect fontselect fontsizeselect',
            content_style: 'body { font-family: Arial, Helvetica, sans-serif; font-size: 14px; }',
            setup: function(editor) {
                editor.on('change', function() {
                    var content = editor.getContent();
                    document.getElementById('hidden-body').value = content;
                });
            },
            init_instance_callback: function(editor) {
                editor.setContent(`
            <p style="font-size: 16px; line-height: 1.5;">
اسلام علیکم<br><br>
Thank you for registering at Hikmah Institute. This is a formal confirmation of your admission in the course <i>{course} // {subcourse}<br>
You will be required to download Hikmah Institute application from the following link:<br><br>
<a href="https://play.google.com/store/apps/details?id=com.hikmah.institute">https://play.google.com/store/apps/details?id=com.hikmah.institute</a><br><br>
You are required to mark your attendance through the application and check-out when leaving the premises.<br><br>
Your credentials are as follows:<br><br>
<strong>Username:</strong> {email}<br>
<strong>Password:</strong> {password}<br><br>
May Allah swt reward you abundantly and grant you consistency in learning Deen.<br><br>
Wassalam,<br>
Team Hikmah
</p>
        `);
            }
        });



        document.getElementById('cc-bcc-toggle').addEventListener('click', function() {
            var ccBccWrapper = document.getElementById('cc-bcc-wrapper');
            ccBccWrapper.style.display = (ccBccWrapper.style.display === 'none') ? 'block' : 'none';
        });
    </script>

    <script>
        const searchInput = document.getElementById('searchInput');
        const searchButton = document.getElementById('searchButton');
        const options = Array.from(document.querySelectorAll('#group option'));

        searchButton.addEventListener('click', function() {
            const searchTerm = searchInput.value.toLowerCase();

            options.forEach(function(option) {
                const subCourseName = option.textContent.toLowerCase();
                const group = option.getAttribute('data-group').toLowerCase();
                const matches = subCourseName.includes(searchTerm) || group.includes(searchTerm);
                option.style.display = matches ? 'block' : 'none';
            });
        });
    </script>
    <script>
        const emailData = {!! $emailDataJson !!};
        const searchEmailInput = document.getElementById('recipients');
        const selectedEmailsContainer = document.getElementById('selectedEmailsContainer');
        const maxDisplayedEmails = 4; // Maximum number of emails to display
        let selectedEmails = [];

        function filterEmails(searchTerm) {
            const filteredEmails = emailData.filter(emailDetail =>
                emailDetail.name.toLowerCase().includes(searchTerm) ||
                emailDetail.email.toLowerCase().includes(searchTerm)
            );
            return filteredEmails.slice(0,
                maxDisplayedEmails); // Limit the results to the maximum number of displayed emails
        }

        function displayEmailResults(results) {
            const emailResultsContainer = document.getElementById('emailResults');
            if (!emailResultsContainer) {
                console.error("Could not find emailResultsContainer element.");
                return;
            }

            // Clear previous results
            emailResultsContainer.innerHTML = '';

            results.forEach(emailDetail => {
                const option = document.createElement('div');
                option.textContent = `${emailDetail.name} - ${emailDetail.email} / ${emailDetail.subCourse}`;
                option.classList.add('email-result');
                option.addEventListener('click', () => {
                    // Toggle selection of the clicked email
                    const email = emailDetail.email;
                    const index = selectedEmails.indexOf(email);
                    if (index > -1) {
                        selectedEmails.splice(index, 1);
                        option.classList.remove('selected');
                    } else {
                        selectedEmails.push(email);
                        option.classList.add('selected');
                    }
                    updateSelectedEmails();
                });
                // Append the email option to the email results container
                emailResultsContainer.appendChild(option);
            });

            // Show email results container
            emailResultsContainer.style.display = 'block';

            // Hide email results container when clicking outside
            document.addEventListener('click', (event) => {
                const target = event.target;
                if (!emailResultsContainer.contains(target)) {
                    emailResultsContainer.style.display = 'none';
                }
            });
        }


        function updateSelectedEmails() {
            selectedEmailsContainer.innerHTML = '';

            selectedEmails.forEach(email => {
                const selectedEmail = document.createElement('div');
                selectedEmail.textContent = email;
                selectedEmail.classList.add('selected-email');
                selectedEmail.addEventListener('click', () => {
                    const index = selectedEmails.indexOf(email);
                    if (index > -1) {
                        selectedEmails.splice(index, 1);
                        updateSelectedEmails();
                    }
                });
                selectedEmailsContainer.appendChild(selectedEmail);
            });

            if (selectedEmails.length === 0) {
                selectedEmailsContainer.style.display = 'none';
            } else {
                selectedEmailsContainer.style.display = 'block';
            }
        }

        searchEmailInput.addEventListener('input', function() {
            const searchTerm = searchEmailInput.value.toLowerCase();
            const filteredResults = filterEmails(searchTerm);
            displayEmailResults(filteredResults);
        });
    </script>

    <script>
        const groupSelect = document.getElementById('group');

        groupSelect.addEventListener('change', function() {
            const selectedCourseId = this.value;

            // Make an AJAX request to the controller with the selected course ID
            fetch(`{{ route('users.fetch-emails') }}?courseId=${selectedCourseId}`)
                .then(response => response.json())
                .then(data => {
                    // Process the returned data
                    console.log(data);
                    var container = document.getElementById('selectedEmailsContainer');

                    for (var i = 0; i < data.length; i++) {
                        // Create a new child element for each item in the data array
                        var childElement = document.createElement('div');
                        childElement.textContent = data[i];

                        // Add the class name "selected-email" to the child element
                        childElement.classList.add('selected-email');

                        // Append the child element to the container
                        container.appendChild(childElement);
                    }


                    // Update the UI or perform other actions
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>
    <script>
function sendemail() {
    // Fetch all the selected emails
    const selectedEmails = Array.from(document.querySelectorAll('.selected-email')).map(emailElement =>
        emailElement.textContent);

    // Get the values of subject and editorContent
    var subject = document.getElementById('subject').value;
    var editorContent = tinymce.get('body').getContent();

    // Create an object with the data to send
    var data = {
        selectedEmails: selectedEmails,
        subject: subject,
        editorContent: editorContent
    };

    // Send the data to the Laravel controller using AJAX
    $.ajax({
        type: 'POST',
        url: '{{ url("users/emailsend") }}', // Replace with the actual URL of your Laravel controller function
        data: data, // Pass the data object
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log(response);
            console.log("Email sent successfully.");
            // Handle the success response if needed
        },
        error: function(xhr, status, error) {
            console.log("Error sending email:", error);
            // Handle the error response if needed
        }
    });
}


</script>





    <style>
        .email-results {
            position: absolute;
            background: #fff;
            border: 1px solid #ccc;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1;
            width: 100%;
            display: none;
        }

        .email-results .email-result {
            padding: 5px;
            cursor: pointer;
        }

        .email-results .email-result.selected {
            background: #f0f0f0;
        }

        .recipients-input-wrapper {
            position: relative;
        }

        .selected-emails-container {
            display: flex;
            flex-wrap: wrap;
            margin-top: 5px;
        }

        .selected-emails-container .selected-email {
            background: #f0f0f0;
            padding: 2px 5px;
            margin-right: 5px;
            margin-bottom: 5px;
            cursor: pointer;
        }

        .selected-emails-container {
            max-height: 8em;
            /* Set the maximum height for the div */
            overflow-y: auto;
            /* Add a vertical scroller if content exceeds the maximum height */
        }
    </style>

@stop
