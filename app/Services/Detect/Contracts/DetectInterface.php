<?php

namespace App\Services\Detect\Contracts;

interface DetectInterface {

    public function platform(): array;

    public function browser();

    public function ip();

    public function country();

}
