<?php

namespace App\Http\Controllers;

use App\Enums\Qualities;
use App\Http\Resources\ItemResource;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Quality;
use App\Services\ClientsService;
use App\Services\ItemService;
use App\Services\ProfileService;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ItemApiController extends Controller
{
    private ItemService $service;
    private ProfileService $profileService;

    public function __construct(ItemService $service, ProfileService $profileService)
    {
        $this->service = $service;
        $this->profileService = $profileService;
    }

    public function items() {
        return ItemResource::collection($this->service->get());
    }

    public function index(Item $item_id) {
        return new ItemResource($item_id);
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => ["required", "string"],
            "description" => ["required", "string"],
            "price" => ["required", "integer"],
            "picture" => ["required"],
            "quality" => ["sometimes", new EnumValue(Qualities::class)]
        ]);
        if ($validator->fails()) {
            return response(status: 400);
        }
        $item = $this->service->create(
            $request["name"],
            $request["description"],
            $request["price"],
            $request["quality"],
            $request["picture"]
        );
        if ($item) {
            return new ItemResource($item);
        }
        return response(status: 422);
    }

    public function sell(Request $request) {
        if (!array_key_exists("item_ids", $request->all())) {
            return response(status: 400);
        }

        $profit = $this->profileService->sellItems(Auth::user()->id, $request["item_ids"]);

        return $profit != null ? ["profit" => $profit] : response(status: 422);
    }
}
