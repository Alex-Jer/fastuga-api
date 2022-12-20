<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function stats(Request $request)
    {
        switch ($request->user()->type) {
            case 'C':
                return response(['message' => 'Only employees can view statistics'], 403);
            case 'EC':
                return $this->chefStats();
            case 'ED':
                return
                    $this->delivererStats();
            case 'EM':
                return $this->managerStats();
        }
    }

    public function chefStats()
    {
        return response(['message' => 'Not implemented yet'], 501);
    }

    public function delivererStats()
    {
        return response(['message' => 'Not implemented yet'], 501);
    }

    public function managerStats()
    {
        return response(['message' => 'Not implemented yet'], 501);
    }
}
