<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * * Register New User
     */
    public function register (Request $request)
    {
        $input = $request->all();

        $rules = [
            'nik' => 'required|string|min:16|max:16',
            'name' => 'required|string',
            'address' => 'required|'
        ];
    }
}
