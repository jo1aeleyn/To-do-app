<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIscompleteAndIsarchivedToTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->boolean('isComplete')->default(false)->after('title'); // Add isComplete column
            $table->boolean('isArchived')->default(false)->after('isComplete'); // Add isArchived column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('isComplete'); // Remove isComplete column
            $table->dropColumn('isArchived'); // Remove isArchived column
        });
    }
}
