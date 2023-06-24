<?php


namespace App\Http\Controllers;

use App\attachmentscourse as AppAttachmentscourse;
use App\Models\Courses;
use Illuminate\Http\Request;
use App\Models\SubCourses;
use App\Models\Users;
use App\Models\Base\Attachmentscourse;
use App\Models\Base\Subcourse as AttachmentSubCourse;
use App\Models\Base\Courses as BaseCourses;




class AttachmentController extends Controller
{
    public function index()
    {

        $courses = Courses::all();
        return view('backend.attachments.index', compact('courses'));
    }

    public function SubCourseUser(Request $request)
    {
        // echo 'SubCourseUser';
        // exit;
        $data['sub_courses'] = SubCourses::where("courses_id", $request->courses_id)
            ->get(["name", "id"]);

        return response()->json($data);
    }

    public function uploadAttachment(Request $request)
    {
        $validatedData = $request->validate([
            'files.*' => 'required|file|max:16384', // Maximum file size of 16 MB (16 * 1024)
        ]);

        // Process the uploaded files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = $originalName . '_' . date('YmdHis') . '.' . $extension;

                // Store the file with the modified name and retrieve its path
                $file->move(public_path('assets/attachments'), $fileName);

                // Perform database operations based on subcourse data
                if ($request->input('sub_courses_id')[0] != null) {
                    // Sub course data exists
                    $subCourseId = $request->input('sub_courses_id')[0];

                    // Create a new instance of the model for each file
                    $subCourseAttachment = new AttachmentSubCourse();
                    $subCourseAttachment->subcourseid = $subCourseId;
                    $subCourseAttachment->link = 'assets/attachments/' . $fileName;
                    $subCourseAttachment->name = $originalName;
                    $subCourseAttachment->uploaddate = now();
                    $subCourseAttachment->save();
                } else if ($request->input('sub_courses_id')[0] == null) {
                    // Sub course data is null or not present
                    $courseId = $request->input('courses_id');
                    $courseAttachment = new Attachmentscourse();
                    $courseAttachment->courseid = $courseId;
                    $courseAttachment->link = 'assets/attachments/' . $fileName;
                    $courseAttachment->name = $originalName;
                    $courseAttachment->uploaddate = now();
                    $courseAttachment->save();
                }
            }
        }

        // Redirect or return a response
        return redirect()->back()->with('success', 'Attachments uploaded successfully');
    }
}
