<?php

namespace App\Helpers;

interface CRCInterface
{
    public function reset();
    public function update($data);
    public function finish();
}
