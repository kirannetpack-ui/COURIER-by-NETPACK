<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OverseasPartnerController extends Controller
{
    public function index()
    {
        return redirect()->route('international.agencies.index');
    }

    public function create()
    {
        return redirect()->route('international.agencies.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('international.agencies.index');
    }

    public function show($id)
    {
        return redirect()->route('international.agencies.edit', $id);
    }

    public function edit($id)
    {
        return redirect()->route('international.agencies.edit', $id);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('international.agencies.index');
    }

    public function destroy($id)
    {
        return redirect()->route('international.agencies.index');
    }

    public function resetPassword($id)
    {
        return redirect()->route('international.agencies.index');
    }
}