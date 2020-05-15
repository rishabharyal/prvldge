<?php

namespace App\Http\Controllers;

use App\FeedDates;
use App\Memory;
use App\User;
use App\UserRelationships;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;


class FeedController extends Controller
{
    private Memory $memoryModel;

    public function __construct(Memory $memory)
    {
        $this->memoryModel = $memory;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * This method provides the feeds for the provided date
     */
    public function index(Request $request) {
        $dates = $request->get('date');

        if (!is_array($dates)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided data seems invalid.'
            ]);
        }

        $friendList = Auth::user()->friendIdList; // for this we will use Cache later

        // Yes it does not look safe & optimized, because we will implement GraphQL instead of JSON API response...
        $feeds = $this->memoryModel->whereIn('user_id', $friendList)
            ->whereIn('memory_at', $dates)
            ->where('visibility', 1)
            ->get([
            'user_id',
            'caption',
            'type',
            'created_at'
        ]);

        return response()->json([
            'success' => true,
            'data' => $feeds
        ]);

    }

    /**
     * This responds the dates on which posts
     * has been posted from the requested
     * user's friends
     */
    public function getFeedDates() {
        $friendList = Auth::user()->friendIdList; // for this we will use Cache later
        $dates = FeedDates::whereIn('user_id', $friendList)->get(); // for this we will use Cache later

        $responseDate = [];
        foreach ($dates as $date) {
            $responseDate = [...$responseDate, ...$date->post_dates]; // You will need PHP 7.4 for this (avoid array_merge in loop, performance issue)
        }

        return response()->json([
            'success' => true,
            'data' => $responseDate
        ]);
    }
}
