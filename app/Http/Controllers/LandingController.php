<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function home(): View
    {
        $schoolCount = Schema::hasTable('schools') ? School::query()->count() : 0;
        $userCount = Schema::hasTable('users') ? User::query()->whereNotNull('school_id')->count() : 0;

        return view('home', [
            'schoolCount' => $schoolCount,
            'userCount' => $userCount,
        ]);
    }

    public function join(): View
    {
        return view('pages.join');
    }

    public function platform(): View
    {
        return view('pages.platform');
    }

    public function howItWorks(): View
    {
        return view('pages.how-it-works');
    }
}
