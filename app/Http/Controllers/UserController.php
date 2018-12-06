<?php
/**
 * Created by PhpStorm.
 * User: geeknat
 * Date: 11/26/18
 * Time: 8:27 AM
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserController
{

    public function login(Request $request)
    {


        if ($request->has('username') && $request->has('password')) {

            $userName = $request->input('username');
            $password = $request->input('password');

            $user = DB::table('users')
                ->select('id', 'first_name', 'last_name', 'username', 'email', 'password')
                ->where('username', $userName)
                ->get();

            if ($user->count() == 0) {
                return response()->json(array('success' => 0, 'message' => 'Failed to log in'), 200);
            }

            if (Hash::check($password, $user[0]->password)) {
                unset($user[0]->password);
                return response()->json(array('success' => 1, 'message' => $user[0]), 200);
            }

            return response()->json(array('success' => 0, 'message' => 'Invalid credentials'), 200);

        }

        return response()->json(array('success' => 0, 'message' => 'Invalid request'), 200);

    }


    public function microsoft(Request $request)
    {


        if ($request->has('email') && $request->has('f_name')) {

            $email = $request->input('email');
            $firstName = $request->input('f_name');
            $lastName = $request->input('l_name');

            $user = DB::table('users')
                ->select('id', 'first_name', 'last_name', 'username', 'email')
                ->where('email', $email)
                ->get();

            if ($user->count() == 0) {

                //add user

                $userId = DB::table('users')->insertGetId(
                    [
                        'email' => $email,
                        'password' => Hash::make("0012345"),
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                    ]
                );

                $user = DB::table('users')
                    ->select('id', 'first_name', 'last_name', 'username', 'email')
                    ->where('id', $userId)
                    ->get();

                return response()->json(array('success' => 1, 'message' => $user[0]), 200);
            }

            return response()->json(array('success' => 1, 'message' => $user[0]), 200);

        }

        return response()->json(array('success' => 0, 'message' => 'Invalid request'), 200);

    }


}