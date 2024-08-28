<?php

namespace App\Services;
use App\Models\Item;
use App\Models\Offer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AuctionService{
    
    public function hasAuctionEnded(Item $item): bool{
        return Carbon::now()->greaterThanOrEqualTo($item->end_time);
    }

    public function placeBid(Item $item, float $bid_amount, User $user){
        if(Carbon::now() >= $item->end_time){
            return response()->json(['error' => 'The auction has expired.'], 400);
        }
        
        if($item->user_id === $user->id){
            return response()->json(['error' => 'You cannot bid on your item'], 400);
        }

        $last_bid = Offer::where('item_id', $item->id)->orderBy('price', 'desc')->first();

        $user_highest_bid = Offer::where('item_id', $item->id)
            ->where('user_id', $user->id)
            ->orderBy('price', 'desc')
            ->first();

        if ($user_highest_bid && $last_bid && $user_highest_bid->id === $last_bid->id) {
            return response()->json(['error' => 'You already have the highest bid.'], 400);
        }

        if($bid_amount >= $item->buy_now_price){
            return response()->json(['error' => 'Bid is bigger than buy now price of ' . $item->buy_now_price], 400);
        }

        if($bid_amount <= $item->current_price){
            return response()->json(['error' => 'Bid must be higher than the current price of ' . $item->current_price], 400);
        }

        Offer::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'price' => $bid_amount,
        ]);

        $item->current_price = $bid_amount;
        $item->save();

        return response()->json(['message' => 'Bid placed successfully']);
    }

    public function checkWinner(Item $item): ?User{
        $highestBid = Offer::where('item_id', $item->id)->orderBy('price', 'desc')->first();
    
        if($highestBid){
            return $highestBid->user;
        }else{
            return null;
        }

    }

    public function sellItemToWinner(Item $item){
        $winner = $this->checkWinner($item);
        $seller = $item->user;
        
        if($winner){
            DB::transaction(function () use ($item, $winner, $seller){
                $item->buyer_id = $winner->id;
                $item->buy_price = $item->current_price;
                
                $winner->money -= $item->buy_price;                
                $seller->money += $item->buy_price;
                
                $winner->save();
                $seller->save();
            });
        }
        $item->active = 0;
        $item->save();
    }

    public function handleItemAuction($id) {
        $item = Item::findOrFail($id);
        
        if(!$this->hasAuctionEnded($item)) {
            return response()->json(['error' => 'The auction is still ongoing.'], 400);
        }

        $winner = $this->checkWinner($item);

        if($winner){
            $this->sellItemToWinner($item);
        }else{
            $item->active = 0;
            $item->save();
            return response()->json(['error' => 'No winner could be determined for the auction.'], 400);
        }

        return response()->json(['message' => 'Auction completed successfully.'], 200);
    }
}

