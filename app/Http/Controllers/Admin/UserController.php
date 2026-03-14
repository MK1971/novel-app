<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index()
    {
        if (!Gate::allows('admin')) {
            abort(403);
        }

        $users = User::orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.users.index', compact('users'));
    }
}
