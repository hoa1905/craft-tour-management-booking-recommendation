<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $hotTours = Tour::getHotTours();

        $north = Tour::getToursByRegion('Miền Bắc');
        $central = Tour::getToursByRegion('Miền Trung');
        $south = Tour::getToursByRegion('Miền Nam');

        return view('client.home', [
            'hotTours' => $hotTours,
            'northTours' => $north['tours'],
            'centralTours' => $central['tours'],
            'southTours' => $south['tours'],
            'totalNorthTours' => $north['total'],
            'totalCentralTours' => $central['total'],
            'totalSouthTours' => $south['total'],
        ]);
    }

    public function loadMoreTours(Request $request)
    {
        $offset = (int)$request->get('offset', 0);
        $limit = 4;
        $region = $request->get('region');

        $tours = Tour::loadMoreByRegion($region, $offset, $limit);

        return response()->json($tours);
    }

    public function showTourDetail($id)
    {
        $tour = Tour::getTourDetail($id);
        return view('client.tour_detail', compact('tour'));
    }
}
