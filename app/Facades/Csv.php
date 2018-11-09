<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Csv extends Facade {
    protected static function getFacadeAccessor() {
        return 'Csv';
    }
}
