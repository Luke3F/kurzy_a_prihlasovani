<?php
namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ParticipantController extends Controller
{
    public function index()
    {
        return view('participants.index', [
            'participants' => Participant::withCount(['enrollments as active_count' => fn ($q) => $q->where('status', 'enrolled')])->orderBy('name')->get(),
        ]);
    }

    public function store(Request $r)
    {
        Participant::create($this->data($r));
        return back()->with('ok', 'Účastník byl přidán.');
    }

    public function edit(Participant $participant) { return view('participants.edit', compact('participant')); }

    public function update(Request $r, Participant $participant)
    {
        $participant->update($this->data($r, $participant));
        return redirect()->route('participants.index')->with('ok', 'Účastník byl uložen.');
    }

    public function destroy(Participant $participant)
    {
        $participant->delete();
        return back()->with('ok', 'Účastník byl smazán.');
    }

    private function data(Request $r, ?Participant $p = null): array
    {
        $r->merge(['email' => Str::lower(trim((string) $r->input('email')))]);
        return $r->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:120', Rule::unique('participants', 'email')->ignore($p?->id)],
        ], ['email.unique' => 'Tento e-mail už je zaregistrovaný.', 'email.email' => 'Zadejte platný e-mail.']);
    }
}
