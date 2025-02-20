<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop foreign keys if they exist
        $this->dropForeignKeysIfExist();

        // Drop primary keys first
        $tables = ['teams', 'assets', 'categories', 'locations', 'tags'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropPrimary();
            });
        }

        // Now modify the columns to ULID
        Schema::table('teams', function (Blueprint $table) {
            $table->string('id', 26)->change();
            $table->primary('id');
        });

        // Modify other tables
        $tablesToModify = [
            'team_invitations' => ['team_id'],
            'team_user' => ['team_id'],
            'assets' => ['id', 'team_id', 'category_id', 'location_id'],
            'categories' => ['id', 'team_id'],
            'locations' => ['id', 'team_id'],
            'tags' => ['id', 'team_id'],
            'asset_tag' => ['asset_id', 'tag_id'],
            'users' => ['current_team_id'],
        ];

        foreach ($tablesToModify as $tableName => $columns) {
            Schema::table($tableName, function (Blueprint $table) use ($columns, $tableName) {
                foreach ($columns as $column) {
                    $table->string($column, 26)->nullable()->change();
                }
                // Re-add primary key for tables that need it
                if (in_array($tableName, ['assets', 'categories', 'locations', 'tags'])) {
                    $table->primary('id');
                }
            });
        }

        // Recreate foreign keys
        $this->createForeignKeys();
    }

    private function dropForeignKeysIfExist()
    {
        $constraints = DB::select("
            SELECT tc.table_name, tc.constraint_name
            FROM information_schema.table_constraints tc
            WHERE tc.constraint_type = 'FOREIGN KEY'
            AND tc.table_schema = 'public'
        ");

        foreach ($constraints as $constraint) {
            Schema::table($constraint->table_name, function (Blueprint $table) use ($constraint) {
                $table->dropForeign($constraint->constraint_name);
            });
        }
    }

    private function createForeignKeys()
    {
        Schema::table('team_invitations', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });

        Schema::table('team_user', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });

        Schema::table('asset_tag', function (Blueprint $table) {
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });

        // Add users table foreign key
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('current_team_id')->references('id')->on('teams')->onDelete('set null');
        });
    }

    public function down()
    {
        // ... implement the down method if needed ...
    }
};
