<?php

namespace Atom\Theme\Http\Controllers;

use Atom\Core\Models\WebsiteSetting;
use Atom\Core\Models\WebsiteArticle;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Atom\Core\Models\Permission;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Atom\Core\Models\User;

class IndexController extends Controller
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(): View
    {
        $articles = WebsiteArticle::with('user')
            ->where('is_published', true)
            ->latest('id')
            ->limit(5)
            ->get();


        $settings = WebsiteSetting::whereIn('key', ['min_staff_rank'])
        ->pluck('value', 'key');


        $credits = User::where('rank', '<', $settings->get('min_staff_rank'))
            ->orderBy('credits', 'desc')
            ->limit(1)
            ->get();
        

        $duckets = User::with(['currencies' => fn ($query) => $query->where('type', 0)])
            ->where('rank', '<', $settings->get('min_staff_rank'))
            ->whereHas('currencies', fn ($query) => $query->where('type', 0))
            ->join('users_currency', 'users.id', '=', 'users_currency.user_id')
            ->where('users_currency.type', 0)
            ->orderBy('users_currency.amount', 'desc')
            ->select('users.*')
            ->limit(1)
            ->get();
        

        $diamonds = User::with(['currencies' => fn ($query) => $query->where('type', 5)])
            ->where('rank', '<', $settings->get('min_staff_rank'))
            ->whereHas('currencies', fn ($query) => $query->where('type', 5))
            ->join('users_currency', 'users.id', '=', 'users_currency.user_id')
            ->where('users_currency.type', 5)
            ->orderBy('users_currency.amount', 'desc')
            ->select('users.*')
            ->limit(1)
            ->get();

        $onlineTimes = User::with('settings')
            ->where('rank', '<', $settings->get('min_staff_rank'))
            ->join('users_settings', 'users.id', '=', 'users_settings.user_id')
            ->orderBy('users_settings.online_time', 'desc')
            ->select('users.*')
            ->limit(1)
            ->get();

        $respects = User::with('settings')
            ->where('rank', '<', $settings->get('min_staff_rank'))
            ->join('users_settings', 'users.id', '=', 'users_settings.user_id')
            ->orderBy('users_settings.respects_received', 'desc')
            ->select('users.*')
            ->limit(1)
            ->get();

        
        $achievements = User::with('settings')
            ->where('rank', '<', $settings->get('min_staff_rank'))
            ->join('users_settings', 'users.id', '=', 'users_settings.user_id')
            ->orderBy('users_settings.achievement_score', 'desc')
            ->select('users.*')
            ->limit(1)
            ->get();

        $settingsStaff = WebsiteSetting::whereIn('key', ['staff_min_rank', 'min_rank_to_see_hidden_staff'])
            ->pluck('value', 'key');

        $permissions = Permission::with(['users' => fn (Builder $query) => 
            $query->where('hidden_staff', '0')
                ->orderBy('rank', 'DESC')
        ])->where('level', '>=', $settingsStaff->get('staff_min_rank', 4))
            ->orderBy('level', 'DESC')
            ->get();

        $users = $permissions
            ->flatMap(fn ($permission) => $permission->users)
            ->sortByDesc('rank') 
            ->take(4); 

        return view('index', compact('articles', 'credits', 'duckets', 'diamonds', 'onlineTimes', 'respects', 'achievements', 'users'));
    }
}
