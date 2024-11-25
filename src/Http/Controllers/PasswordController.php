<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Theme\Http\Requests\PasswordStoreRequest;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('password');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PasswordStoreRequest $request): RedirectResponse
    {
        $request->user()
            ->update($request->validated());

        return redirect()->route('users.settings.password.index');
    }
}
