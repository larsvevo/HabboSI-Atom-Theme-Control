<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\WebsiteOpenPosition;
use Atom\Theme\Http\Requests\StaffApplicationStoreRequest;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class StaffApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $positions = WebsiteOpenPosition::with('permission')
            ->where(fn (Builder $query) => $query->whereNull('apply_from')->orWhere('apply_from', '<=', now()))
            ->where(fn (Builder $query) => $query->whereNull('apply_to')->orWhere('apply_to', '>', now()))
            ->get();

        return view('staff-applications.index', compact('positions'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, WebsiteOpenPosition $staffApplication): View
    {
        $position = $staffApplication->load('permission');

        $applied = $request->user()
            ->staffApplications()
            ->firstWhere('rank_id', $position->permission->id)
            ->exists();

        return view('staff-applications.show', compact('position', 'applied'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StaffApplicationStoreRequest $request)
    {
        $position = WebsiteOpenPosition::findOrFail($request->get('position_id'));

        $request->user()
            ->staffApplications()
            ->create($request->validated());

        return redirect()
            ->route('community.staff-applications.show', $position);
    }
}
