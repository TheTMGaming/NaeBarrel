<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Quality;
use App\Services\ClientService;
use App\Services\ItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ItemApiController extends Controller
{
    private ItemService $service;
    private ClientService $clientService;
    public function __construct(ItemService $service, ClientService $clientService)
    {
        $this->service = $service;
        $this->clientService = $clientService;
    }

    public function items() {
        return ItemResource::collection($this->service->GetAll());
    }

    public function index(Item $item_id) {
        return new ItemResource($item_id);
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => ["required"],
            "description" => ["required"],
            "price" => ["required"],
            "picture" => ["required"],
            "quality" => ["sometimes"]
        ]);
        if ($validator->fails()) {
            return response(status: 400);
        }
        $item = $this->service->CreateItem(
            $request["name"],
            $request["description"],
            $request["price"],
            $request["quality"],
            $request["picture"]
        );
        if ($item) {
            return new ItemResource($item);
        }
        return response(status: 400);
    }

    public function sell(Request $request) {
        if (!array_key_exists("item_ids", $request->all())) {
            return response(status: 400);
        }
        $profit = $this->clientService->SellItems(Auth::user()->id, $request["item_ids"]);

        return $profit;
    }
}
