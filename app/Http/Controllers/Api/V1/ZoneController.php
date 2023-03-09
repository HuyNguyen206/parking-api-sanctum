<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ZoneResource;
use App\Models\Zone;
use App\Responsable\ResponseSuccess;

/**
 * @group Zones
 */
class ZoneController extends Controller
{
    public function index()
    {
        return new ResponseSuccess(ZoneResource::collection(Zone::paginate(5)));
    }
}
