<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistsController extends Controller
{
    public function index(){
        $user_id = Auth::id();

        $wishlist = Wishlist::with('item')->where(['user_id' => $user_id])->get();

        return response()->json($wishlist);
    }

    public function store($id){
        $item = Item::with('wishlists')->find($id);
        $active_user = Auth::user();


        $wishlist = new Wishlist;
        $wishlist->user()->associate($active_user);
        $wishlist->item()->associate($item->id);

        $wishlist->save();
        $wishlist->load('item');

        return response()->json($wishlist);
    }

    public function destroy($id){
        $wishlist = Wishlist::findOrFail($id);
        $active_user = Auth::user();

        if($wishlist['user_id'] == $active_user['id']){
            $wishlist->delete();   
            return response()->json([
                'delete' => true
            ]);
        }else{
            return response()->json(['message' => 'Not your wishlist']);
        }
    }
}
