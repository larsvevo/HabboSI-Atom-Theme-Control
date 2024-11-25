<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\WebsiteShopVoucher;
use Atom\Theme\Http\Requests\RedeemVoucherRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class RedeemVoucherController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function __invoke(RedeemVoucherRequest $request): RedirectResponse
    {
        $voucher = WebsiteShopVoucher::firstWhere('code', $request->code);

        $request->user()->update([
            'website_balance' => $request->user()->website_balance + $voucher->amount,
        ]);

        $voucher->redeems()->create([
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('shop.index');
    }
}
