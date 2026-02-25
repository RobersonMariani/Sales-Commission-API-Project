<?php

namespace App\Api\Modules\Seller\Controllers;

use App\Api\Modules\Seller\Data\CreateSellerData;
use App\Api\Modules\Seller\Data\SellerQueryData;
use App\Api\Modules\Seller\Resources\SellerResource;
use App\Api\Modules\Seller\UseCases\CreateSellerUseCase;
use App\Api\Modules\Seller\UseCases\GetSellersUseCase;
use App\Api\Modules\Seller\UseCases\GetSellerUseCase;
use App\Api\Modules\Seller\UseCases\ResendCommissionUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class SellerController extends Controller
{
    public function store(Request $request, CreateSellerUseCase $useCase): Response
    {
        $data = CreateSellerData::validateAndCreate($request->all());

        return SellerResource::make($useCase->execute($data))
            ->response()
            ->setStatusCode(201);
    }

    public function index(Request $request, GetSellersUseCase $useCase): AnonymousResourceCollection
    {
        $query = SellerQueryData::validateAndCreate($request->query());

        return SellerResource::collection($useCase->execute($query));
    }

    public function show(int $seller, GetSellerUseCase $useCase): SellerResource
    {
        return SellerResource::make($useCase->execute($seller));
    }

    public function resendCommission(int $seller, Request $request, ResendCommissionUseCase $useCase): JsonResponse
    {
        $useCase->execute($seller, $request->input('date'));

        return response()->json(['message' => 'Commission email queued successfully.']);
    }
}
