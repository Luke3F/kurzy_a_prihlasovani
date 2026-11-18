<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Enrollment extends Model
{
    public const ENROLLED = 'enrolled';
    public const CANCELLED = 'cancelled';
    public const COMPLETED = 'completed';
    public const LABELS = ['enrolled' => 'Přihlášen', 'cancelled' => 'Zrušeno', 'completed' => 'Dokončeno'];

    protected $fillable = ['course_id', 'participant_id', 'enrolled_at', 'status'];
    protected $casts = ['enrolled_at' => 'datetime'];

    public function course() { return $this->belongsTo(Course::class); }
    public function participant() { return $this->belongsTo(Participant::class); }

    public function changeStatus(string $to): self
    {
        $allowed = [
            self::ENROLLED => [self::CANCELLED, self::COMPLETED],
            self::CANCELLED => [self::ENROLLED],
            self::COMPLETED => [],
        ];
        return DB::transaction(function () use ($to, $allowed) {
            $course = Course::whereKey($this->course_id)->lockForUpdate()->firstOrFail();
            $this->refresh();
            if (!in_array($to, $allowed[$this->status] ?? [], true)) {
                throw ValidationException::withMessages(['status' => 'Tento přechod stavu není povolen.']);
            }
            if ($to === self::ENROLLED && $course->activeCount() >= $course->capacity) {
                throw ValidationException::withMessages(['status' => 'Kurz je obsazen, přihlášku nelze obnovit.']);
            }
            $this->update(['status' => $to] + ($to === self::ENROLLED ? ['enrolled_at' => now()] : []));
            return $this;
        });
    }
}
