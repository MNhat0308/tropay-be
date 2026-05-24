<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('User/List', [
            'users' => User::all(),
        ]);
    }

    public function show(User $user): \Inertia\Response
    {
        return Inertia::render('User/Edit', [
            'user' => $user,
        ]);
    }
}
