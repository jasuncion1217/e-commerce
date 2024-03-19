<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $users = User::select('name', 'email', 'id')
                ->orderByDesc('id')
                ->with('roles')
                ->when($request->searchTerm, function ($query, $searchTerm) {
                    return $query->where('name', 'like', '%' . $searchTerm . '%');
                })->paginate(5);

            return Inertia::render('Admin/Users', [
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return Redirect::back()->withErrors(['errorMessage' => 'Database getting users: ' . $e->getMessage()]);
        }

    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|max:255',
                'confirm_password' => 'same:password',
            ]);

            if ($validator->fails()) {
                return Redirect::route('users.index')
                    ->withErrors($validator);
            } else {
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->save();
                $user->assignRole('editor');

                return Redirect::route('users.index')
                    ->with('successMessage', 'User Added Successfully');
            }
        } catch (\Exception $e) {
            return Redirect::back()->withErrors('errorMessage', 'Error adding user: ' . $e->getMessage())->withInput();
        }
    }

    public function getUserData($id)
    {
        try {
            $user = User::find($id);

            return Inertia::render('Admin/EditUser', [
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return Redirect::back()->withErrors('errorMessage', 'Error getting user: ' . $e->getMessage());
        }

    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (empty ($request->password)) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email,' . $user->id,
                ]);

                if ($validator->fails()) {
                    return Redirect::route('user.get', ['id' => $id])
                        ->withErrors($validator);
                } else {
                    $user->name = $request->input('name');
                    $user->email = $request->input('email');
                    $user->updated_at = now();
                    $user->save();

                    return Redirect::route('user.get', ['id' => $id])
                        ->with('successMessage', 'User updated Successfully');
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email,' . $user->id,
                    'password' => 'string|max:255',
                    'confirm_password' => 'same:password',
                ]);

                if ($validator->fails()) {
                    return Redirect::route('user.get', ['id' => $id])
                        ->withErrors($validator);
                } else {
                    $user->name = $request->input('name');
                    $user->email = $request->input('email');
                    $user->password = $request->input('password');
                    $user->updated_at = now();
                    $user->save();

                    return Redirect::route('user.get', ['id' => $id])
                        ->with('successMessage', 'User updated Successfully');
                }
            }
        } catch (\Exception $e) {
            return Redirect::back()->withErrors('errorMessage', 'Error updating user: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find($id);
            $user_name = $user->name; 
            $user->delete();

            return Redirect::route('users.index')
                ->with('successMessage', 'User: '. $user_name .' delete Successfully' );
        } catch (\Exception $e) {
            return Redirect::back()->withErrors('errorMessage', 'Error deleting user: ' . $e->getMessage())->withInput();
        }
    }
}
