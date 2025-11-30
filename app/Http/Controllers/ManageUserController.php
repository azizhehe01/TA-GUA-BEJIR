<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ManageUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc'); 

        $users = User::query()
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                      ->orWhere('email', 'LIKE', "%$search%");
                });
            })
            ->orderBy($sort, $direction)
            ->get();

        return view('ManageUser', compact('users', 'search', 'sort', 'direction'));
    }


    public function updateStatus(User $user)
    {
        $user->active = !$user->active;
        $user->save();

        return back()->with('success', 'Status updated!');
    }

    public function updateRole(Request $request, User $user)
    {
        $user->is_admin = $request->is_admin;
        $user->save();

        return back()->with('success', 'Role updated!');
    }

    public function delete(User $user)
    {
        $user->delete();

        return back()->with('success', 'User deleted!');
    }

}
