<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $t) {
            $t->id();
            $t->string('name', 100);
            $t->string('code', 20)->unique();
            $t->unsignedInteger('capacity');
            $t->dateTime('starts_at');
            $t->timestamps();
        });
        Schema::create('participants', function (Blueprint $t) {
            $t->id();
            $t->string('name', 100);
            $t->string('email', 120)->unique();
            $t->timestamps();
        });
        Schema::create('enrollments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $t->dateTime('enrolled_at');
            $t->string('status', 20)->default('enrolled'); // enrolled | cancelled | completed
            $t->timestamps();
            $t->unique(['course_id', 'participant_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('participants');
        Schema::dropIfExists('courses');
    }
};
