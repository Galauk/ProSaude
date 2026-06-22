<?php

namespace App\Database;

use App\Database\Executable;

abstract class Migration extends Executable
{
    abstract public function up(): void;

    abstract public function down(): void;
}