<?php

namespace Atom\Theme\Http\Controllers;

use App\Events\UserClient;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(Request $request): View
    {
        $request->user()->auth_ticket = str()->uuid()->toString();

        $request->user()->save();

        $url = sprintf('%s?%s', config('nitro.client_url'), http_build_query([
            ...$request->query(),
            'sso' => $request->user()->auth_ticket,
        ]));

        UserClient::dispatch($request->user());

        return view('client', compact('url'));
    }
}
