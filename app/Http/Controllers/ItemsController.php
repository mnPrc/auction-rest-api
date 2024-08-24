<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemRequest;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function index(Request $request){
        $query = Item::with('user');

        if($search = $request->input('search')){
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', '%' . $search .'%')
                            ->orWhere('last_name', 'LIKE', '%' . $search . '%');
                    });
            });
        }

        $items = $query->where('active', true)
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

        return response()->json($items);
    }

    public function store(CreateItemRequest $request){
        $data = $request->validated();
        $data['end_time'] = Carbon::now()->addDays($data['end_time']);

        $item = Item::create($data);

        return response()->json($item);
    }

    public function show($id){
        $item = Item::with('user')->findOrFail($id);
        
        return response()->json($item);
    }

    public function destroy($id){
        $item = Item::findOrFail($id);
        $activeUser =  Auth::user();

        if($item['user_id'] == $activeUser['id']){
            $item->delete();   
            return response()->json([
                'delete' => true
            ]);
        }else{
            return response()->json(['message' => 'Not your item']);
        }
    }
}
