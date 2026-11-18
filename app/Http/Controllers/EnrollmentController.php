<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    public function store(Request $r, Course $course)
    {
        $d = $r->validate(['participant_id' => 'required|exists:participants,id']);
        $course->enroll((int) $d['participant_id']);
        return back()->with('ok', 'Účastník byl přihlášen.');
    }

    public function update(Request $r, Enrollment $enrollment)
    {
        $d = $r->validate(['status' => ['required', Rule::in(array_keys(Enrollment::LABELS))]]);
        $enrollment->changeStatus($d['status']);
        return back()->with('ok', 'Stav přihlášky byl změněn.');
    }
}
