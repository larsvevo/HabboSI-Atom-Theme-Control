<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\User;
use Atom\Theme\Http\Requests\AccountStoreRequest;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AccountSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('account-settings');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AccountStoreRequest $request): RedirectResponse
    {
        $user = User::find($request->user()->id);

        $user->update(
            array_filter($request->validated())
        );

        return redirect()->back();
    }
}
