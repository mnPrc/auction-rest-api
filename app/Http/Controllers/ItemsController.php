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
        $query = Item::with(['user', 'images' => function($query){
            $query->take(1);
        }]);

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
        $data['end_time'] = Carbon::now()->addDays((int)$data['end_time']);

        $item = Item::create($data);

        //Create images folder in storage/app/public if you don't have it
        if($request->hasFile('images')){
            foreach($request->file('images') as $image){
                $path = $image->store('images', 'public');
                $item->images()->create(['image_path' => $path]);
            }
        }

        return response()->json($item->load('images'));
    }

    public function show($id){
        $item = Item::with('user', 'images')->findOrFail($id);
        
        return response()->json($item);
    }

    public function destroy($id){
        $item = Item::findOrFail($id);
        $active_user =  Auth::user();

        if($item['user_id'] == $active_user['id'] && $item['buyer_id'] == 0){
            $item->delete();   
            return response()->json([
                'delete' => true
            ]);
        }else{
            return response()->json(['message' => 'Cannot delete item']);
        }
    }
}
