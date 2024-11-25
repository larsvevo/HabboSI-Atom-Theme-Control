<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\CameraWeb;
use Atom\Theme\Http\Requests\ReactionUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $photos = CameraWeb::with('user', 'reactions')
            ->latest('id')
            ->where('approved', true)
            ->paginate(12);

        return view('photos', compact('photos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReactionUpdateRequest $request, CameraWeb $cameraWeb): RedirectResponse
    {
        $reaction = $cameraWeb->reactions()
            ->where('user_id', $request->user()->id)
            ->where('reaction', $request->get('reaction'));

        match ($reaction->exists()) {
            true => $reaction->delete(),
            false => $reaction->create(['user_id' => $request->user()->id, 'reaction' => $request->get('reaction')]),
        };

        return redirect()
            ->back();
    }
}
