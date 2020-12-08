<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Rates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\RateResource;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ratesList = Rates::select('char_code', 'value', 'valcurs_date')->orderBy('valcurs_date', 'DESC')->paginate($request->get('per_page'));
        return response(['rates' => $ratesList, 'message' => 'Retrieved successfully'], 200);
    }

    public function history(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
          //  'date_from' => 'required',
          //  'date_to' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $conditions = [['char_code', '=', $request->get('id')]];

        $ratesList = Rates::select('char_code', 'value', 'valcurs_date')->where($conditions)->paginate();

        if (!empty($request->date_from)) {
            $ratesList = Rates::select('char_code', 'value', 'valcurs_date')->where($conditions)->whereBetween('valcurs_date', array($request->date_from, $request->date_to))->paginate();
        }

        return response(['rates' => $ratesList, 'message' => 'Retrieved successfully'], 200);
    }
}
