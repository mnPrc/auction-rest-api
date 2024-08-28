<?php

namespace App\Services;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BuyNowService{

    public function buyNow(Item $item, User $buyer){
        if($buyer->money < $item->buy_now_price){
            return response()->json(['error' => 'Not enough money'], 400);
        }
        
        $seller = $item->user;

        DB::transaction(function () use ($item, $buyer, $seller) {
            $item->buyer_id = $buyer->id;
            $item->buy_price = $item->buy_now_price;
            
            $buyer->money -= $item->buy_now_price;
            
            $seller->money += $item->buy_now_price;
            
            $buyer->save();
            $seller->save();
        });
        
        $item->active = 0;
        $item->save();

        return response()->json(['message' => 'Successfully bought item']);
    }
}