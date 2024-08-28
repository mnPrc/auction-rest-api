<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Services\AuctionService;
use App\Services\BuyNowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OffersController extends Controller
{
    protected $auction_service;
    protected $buy_now_service;

    public function __construct(AuctionService $auction_service, BuyNowService $buy_now_service)
    {
        $this->auction_service = $auction_service;
        $this->buy_now_service = $buy_now_service;
    }

    public function bidOnItem(Request $request, $id){
        $item = Item::findOrFail($id);
        $user = Auth::user();
        $bid_amount = $request->input('bid_amount');

        $place_bid = $this->auction_service->placeBid($item, $bid_amount, $user);
        return $place_bid;
    }

    public function buyItem($id){
        $item = Item::findOrFail($id);
        $user = Auth::user();

        $buy_now = $this->buy_now_service->buyNow($item, $user);
        return $buy_now;
    }
}
