<?php

namespace Atom\Theme\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Srmklive\PayPal\Services\PayPal;

class PayPalController extends Controller
{
    /**
     * Handle the payment request from PayPal.
     */
    public function success(PayPal $paypal, Request $request): RedirectResponse
    {
        $request->validate(['token' => 'required']);

        $transaction = $request->user()->transactions()
            ->firstWhere('transaction_id', $request->get('token'));

        if (! $transaction) {
            return redirect()->route('shop.index')
                ->withErrors(['amount' => __('Something went wrong, please try again later')]);
        }

        $response = $paypal->capturePaymentOrder($request->get('token'));

        $details = Arr::get($response, 'purchase_units.0.payments.captures.0');

        if (! $details) {
            return redirect()->route('shop.index')
                ->withErrors(['amount' => __('Something went wrong, please try again later')]);
        }

        $transaction->update([
            'status' => Arr::get($details, 'status'),
            'amount' => Arr::get($details, 'amount.value'),
            'currency' => Arr::get($details, 'amount.currency_code'),
        ]);

        if ($transaction->status !== 'COMPLETED') {
            return redirect()->route('shop.index')
                ->withErrors(['amount' => __('Something went wrong, please try again later')]);
        }

        $request->user()
            ->update(['website_balance' => $request->user()->website_balance + $transaction->amount]);

        return to_route('shop.index')
            ->withSuccess(__('Transaction successful'));
    }

    /**
     * Handle the cancelled payment from PayPal.
     */
    public function cancelled(Request $request): RedirectResponse
    {
        $request->validate(['token' => 'required']);

        $transaction = $request->user()->transactions()
            ->firstWhere('transaction_id', $request->get('token'));

        if (! $transaction) {
            return redirect()->route('shop.index')
                ->withErrors(['amount' => __('Something went wrong, please try again later')]);
        }

        $transaction->update([
            'status' => 'CANCELLED',
            'description' => 'The user cancelled the transaction',
        ]);

        return to_route('shop.index')
            ->withErrors(['amount' => __('You have canceled the transaction')]);
    }
}
