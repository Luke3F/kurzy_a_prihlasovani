<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Course extends Model
{
    protected $fillable = ['name', 'code', 'capacity', 'starts_at'];
    protected $casts = ['starts_at' => 'datetime', 'capacity' => 'integer'];

    public function enrollments() { return $this->hasMany(Enrollment::class); }

    /** Aktivní přihlášky = pouze stav enrolled. */
    public function activeCount(): int
    {
        return $this->enrollments()->where('status', Enrollment::ENROLLED)->count();
    }

    public function scopeWithFreeSpots($q)
    {
        return $q->whereRaw("capacity > (select count(*) from enrollments where enrollments.course_id = courses.id and enrollments.status = 'enrolled')");
    }

    /** Přihlásí účastníka; kapacita se kontroluje pod zámkem řádku kurzu (ochrana proti souběhu). */
    public function enroll(int $participantId): Enrollment
    {
        return DB::transaction(function () use ($participantId) {
            $c = static::whereKey($this->id)->lockForUpdate()->firstOrFail();
            $e = $c->enrollments()->where('participant_id', $participantId)->first();

            if ($e && $e->status !== Enrollment::CANCELLED) {
                throw ValidationException::withMessages(['participant_id' =>
                    $e->status === Enrollment::COMPLETED ? 'Účastník už tento kurz dokončil.' : 'Účastník už je na kurz přihlášen.']);
            }
            if ($c->activeCount() >= $c->capacity) {
                throw ValidationException::withMessages(['participant_id' => 'Kurz je obsazen.']);
            }
            // zrušená přihláška se obnoví, nevznikne duplicitní řádek
            return $c->enrollments()->updateOrCreate(
                ['participant_id' => $participantId],
                ['status' => Enrollment::ENROLLED, 'enrolled_at' => now()]
            );
        });
    }
}
