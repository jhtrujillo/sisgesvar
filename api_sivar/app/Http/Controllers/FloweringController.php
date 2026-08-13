<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FloweringService;

class FloweringController extends Controller
{
    protected $floweringService;

    public function __construct(FloweringService $floweringService)
    {
        $this->floweringService = $floweringService;
    }

    public function floweringList(Request $request)
    {
        try {
            $historico = $request->query('historico') === 'true';
            $model = $this->floweringService->getFloweringList($historico);

            return response()->json($model);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
