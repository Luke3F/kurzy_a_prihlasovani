<?php
namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/** Automatická kontrola: vytvoří kurz, ověří jeho uložení a pravidla přihlašování. */
class CourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    private function course(array $o = []): array
    {
        return $o + ['name' => 'Laravel základy', 'code' => 'LAR-1', 'capacity' => 2, 'starts_at' => '2099-01-01 10:00'];
    }

    public function test_course_is_created_and_persisted(): void
    {
        $this->post('/courses', $this->course())->assertSessionHasNoErrors();
        $this->assertDatabaseHas('courses', ['code' => 'LAR-1', 'capacity' => 2]);
        $this->assertSame('Laravel základy', Course::where('code', 'LAR-1')->firstOrFail()->name);
    }

    public function test_course_validation(): void
    {
        Course::create($this->course());
        $this->post('/courses', $this->course())->assertSessionHasErrors('code');
        $this->post('/courses', $this->course(['code' => 'X', 'capacity' => 0]))->assertSessionHasErrors('capacity');
        $this->post('/courses', $this->course(['code' => 'Y', 'capacity' => '1.5']))->assertSessionHasErrors('capacity');
    }

    public function test_participant_email_validation(): void
    {
        $this->post('/participants', ['name' => 'A', 'email' => 'neplatny@'])->assertSessionHasErrors('email');
        $this->post('/participants', ['name' => 'A', 'email' => 'a@example.com'])->assertSessionHasNoErrors();
        $this->post('/participants', ['name' => 'B', 'email' => 'A@Example.com'])->assertSessionHasErrors('email');
    }

    public function test_no_double_enrollment_and_capacity_and_statuses(): void
    {
        $c = Course::create($this->course());
        [$a, $b, $d] = collect(['a', 'b', 'd'])->map(fn ($n) => Participant::create(['name' => $n, 'email' => "$n@example.com"]));

        $ea = $c->enroll($a->id);
        $this->assertThrows(fn () => $c->enroll($a->id), ValidationException::class);   // duplicita
        $c->enroll($b->id);
        $this->assertThrows(fn () => $c->enroll($d->id), ValidationException::class);   // plno

        $ea->changeStatus(Enrollment::CANCELLED);                                        // uvolní místo
        $this->assertSame(1, $c->activeCount());
        $c->enroll($d->id);
        $this->assertThrows(fn () => $ea->changeStatus(Enrollment::ENROLLED), ValidationException::class); // plno

        $eb = Enrollment::where('participant_id', $b->id)->first();
        $eb->changeStatus(Enrollment::COMPLETED);
        $this->assertThrows(fn () => $eb->changeStatus(Enrollment::CANCELLED), ValidationException::class); // completed je konečný
        $this->assertSame(1, Enrollment::where('course_id', $c->id)->where('participant_id', $a->id)->count());
    }
}
