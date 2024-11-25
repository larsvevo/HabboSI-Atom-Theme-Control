<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\WebsiteShopArticle;
use Atom\Rcon\Services\RconService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PurchaseController extends Controller
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(RconService $rconService, Request $request, WebsiteShopArticle $article)
    {
        if ($request->user()->website_balance < $article->costs) {
            return redirect()->route('shop.index')
                ->withErrors(['message' => __('You need to top-up your account with another $:amount to purchase this package', ['amount' => ($article->costs - $request->user()->website_balance)])]);
        }

        $this->deductBalance($request, $article);

        $this->giveRank($rconService, $request, $article);

        $this->giveCredits($rconService, $request, $article);

        $this->giveDuckets($rconService, $request, $article);

        $this->giveDiamonds($rconService, $request, $article);

        $this->giveBadges($rconService, $request, $article);

        $this->giveFurniture($rconService, $request, $article);

        return redirect()->route('shop.index')
            ->withSuccess(__('You have successfully purchased the package'));
    }

    /**
     * Take away the balance from the user.
     */
    protected function deductBalance(Request $request, WebsiteShopArticle $article): void
    {
        $request->user()->decrement('website_balance', $article->costs);
    }

    /**
     * Give the user a rank.
     */
    protected function giveRank(RconService $rconService, Request $request, WebsiteShopArticle $article): void
    {
        if ($request->user()->rank > $article->give_rank) {
            return;
        }

        if (! $rconService->connected) {
            $request->user()->update(['rank' => $article->give_rank]);

            return;
        }

        $rconService->setRank($request->user()->id, $article->give_rank);
    }

    /**
     * Give the user credits.
     */
    protected function giveCredits(RconService $rconService, Request $request, WebsiteShopArticle $article): void
    {
        if ($article->credits === 0) {
            return;
        }

        if (! $rconService->connected) {
            $request->user()->increment('credits', $article->credits);

            return;
        }

        $rconService->giveCredits($request->user()->id, $article->credits);
    }

    /**
     * Give the user duckets.
     */
    protected function giveDuckets(RconService $rconService, Request $request, WebsiteShopArticle $article): void
    {
        if ($article->duckets === 0) {
            return;
        }

        if (! $rconService->connected) {
            $request->user()->currencies()->where('type', 0)->increment('amount', $article->duckets);

            return;
        }

        $rconService->giveDuckets($request->user()->id, $article->duckets);
    }

    /**
     * Give the user diamonds.
     */
    protected function giveDiamonds(RconService $rconService, Request $request, WebsiteShopArticle $article): void
    {
        if ($article->diamonds === 0) {
            return;
        }

        if (! $rconService->connected) {
            $request->user()->currencies()->where('type', 5)->increment('amount', $article->diamonds);

            return;
        }

        $rconService->giveDiamonds($request->user()->id, $article->diamonds);
    }

    /**
     * Give the user badges.
     */
    protected function giveBadges(RconService $rconService, Request $request, WebsiteShopArticle $article): void
    {
        foreach ($article->badgeItems as $badge) {
            if (! $rconService->connected) {
                $request->user()->badges()->updateOrCreate(['badge_code' => $badge], ['slot_id' => 0]);

                continue;
            }

            $rconService->giveBadge($request->user()->id, $badge);
        }
    }

    /**
     * Give the user furniture.
     */
    protected function giveFurniture(RconService $rconService, Request $request, WebsiteShopArticle $article): void
    {
        if ($article->items->isEmpty()) {
            return;
        }

        $items = $article->items
            ->map(fn ($item) => collect(range(1, $item->amount))->map(fn () => ['item_id' => $item->id, 'room_id' => 0, 'wall_pos' => '', 'x' => 0, 'y' => 0, 'z' => 0, 'rot' => 0, 'extra_data' => 0, 'wired_data' => '', 'limited_data' => '0:0', 'guild_id' => 0]))
            ->flatten(1);

        foreach ($items as $item) {
            if (! $rconService->connected) {
                $request->user()->items()->create($item);

                continue;
            }

            $rconService->sendGift($request->user()->id, $item['item_id'], $article->name);
        }
    }
}
