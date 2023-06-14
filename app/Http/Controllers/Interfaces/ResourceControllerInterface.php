<?php

namespace App\Http\Controllers\Interfaces;

use Illuminate\Http\Request;

interface ResourceControllerInterface
{
    public function get(Request $request);
    public function getById(Request $request, $id);
    public function create(Request $request);
    public function update(Request $request, $id);
}
