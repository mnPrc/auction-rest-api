<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Services\AuctionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EndAuction extends Command
{
    protected $signature = 'app:end-auction';
    protected $description = 'Command description';

    protected $auction_service;

    public function __construct(AuctionService $auction_service){
        parent::__construct();
        $this->auction_service = $auction_service;
    }
    public function handle()
    {
        $finished_auctions = Item::where('end_time', '<=', Carbon::now())
                                    ->where('active', 1)
                                    ->get();        
        
        foreach($finished_auctions as $item){
            $this->auction_service->handleItemAuction($item->id);
        }
    }
}
