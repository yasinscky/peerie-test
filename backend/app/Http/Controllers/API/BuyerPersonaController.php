<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BuyerPersonaController extends Controller
{
    public function __construct(private WorksheetController $worksheets)
    {
    }

    public function show(): JsonResponse
    {
        return $this->worksheets->show('buyer_persona');
    }

    public function update(Request $request): JsonResponse
    {
        return $this->worksheets->update($request, 'buyer_persona');
    }

    public function export(Request $request): Response|JsonResponse
    {
        return $this->worksheets->export($request, 'buyer_persona');
    }
}
