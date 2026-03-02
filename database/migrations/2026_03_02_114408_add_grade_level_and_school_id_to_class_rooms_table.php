<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('class_rooms', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('grade_level')->nullable()->after('description');
        });

        $classRooms = DB::table('class_rooms')->get();
        foreach ($classRooms as $cr) {
            $schoolId = DB::table('users')->where('id', $cr->user_id)->value('school_id');
            if ($schoolId) {
                DB::table('class_rooms')->where('id', $cr->id)->update(['school_id' => $schoolId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_rooms', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('grade_level');
        });
    }
};
