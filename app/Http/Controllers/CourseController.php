<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index(Request $r)
    {
        $q = Course::query()
            ->withCount(['enrollments as active_count' => fn ($q) => $q->where('status', 'enrolled')])
            ->orderBy('starts_at');
        if ($r->filled('from')) $q->whereDate('starts_at', '>=', $r->query('from'));
        if ($r->filled('to'))   $q->whereDate('starts_at', '<=', $r->query('to'));
        if ($r->boolean('free')) $q->withFreeSpots();
        return view('courses.index', ['courses' => $q->get()]);
    }

    public function store(Request $r)
    {
        $c = Course::create($this->data($r));
        return redirect()->route('courses.show', $c)->with('ok', 'Kurz byl vytvořen.');
    }

    public function show(Course $course)
    {
        $course->load('enrollments.participant');
        $taken = $course->enrollments->where('status', '!=', 'cancelled')->pluck('participant_id');
        return view('courses.show', [
            'course' => $course,
            'active' => $course->activeCount(),
            'available' => Participant::whereNotIn('id', $taken)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $r, Course $course)
    {
        $course->update($this->data($r, $course));
        return back()->with('ok', 'Kurz byl uložen.');
    }

    public function destroy(Course $course)
    {
        $course->delete(); // přihlášky se smažou kaskádou
        return redirect()->route('courses.index')->with('ok', 'Kurz byl smazán.');
    }

    private function data(Request $r, ?Course $c = null): array
    {
        $r->merge(['code' => Str::upper(trim((string) $r->input('code')))]);
        $min = $c ? max(1, $c->activeCount()) : 1;
        return $r->validate([
            'name' => 'required|string|max:100',
            'code' => ['required', 'string', 'max:20', Rule::unique('courses', 'code')->ignore($c?->id)],
            'capacity' => ['required', 'integer', 'min:' . $min],
            'starts_at' => 'required|date',
        ], [
            'code.unique' => 'Kód kurzu už existuje.',
            'capacity.integer' => 'Kapacita musí být celé číslo.',
            'capacity.min' => "Kapacita musí být celé číslo alespoň $min (větší než 0 a ne méně než aktivních přihlášek).",
        ]);
    }
}
