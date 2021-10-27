<?php

namespace Chestnut\Contracts\Dashboard;

use Illuminate\Http\Request;

interface Resource
{
    public function newQuery();

    public function getOption($key);

    public function index(Request $request);

    public function detail($id);

    public function edit($id);

    public function store(Request $request);

    public function update(Request $request, $id);

    public function destroy($id);
}
