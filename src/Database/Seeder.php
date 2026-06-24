<?php

namespace App\Database;


abstract class Seeder extends Executable
{
    abstract public function run(): void;

}