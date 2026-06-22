<?php

namespace App\Database;

abstract class Migration extends Executable
{
    abstract public function up(): void;

    abstract public function down(): void;
}