<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use App\Responsable\ResponseSuccess;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Vehicles
 */
class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new ResponseSuccess(VehicleResource::collection(Vehicle::query()->latest('id')->paginate(5)));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVehicleRequest $request)
    {
       return new ResponseSuccess(VehicleResource::make($request->user()->vehicles()->create($request->validated())));
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        return new ResponseSuccess(VehicleResource::make($vehicle));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreVehicleRequest $request, Vehicle $vehicle)
    {
        $vehicle->update($request->validated());

        return new ResponseSuccess(VehicleResource::make($vehicle), message: 'Update vehicle successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return new ResponseSuccess(statusCode: Response::HTTP_NO_CONTENT);
    }
}
