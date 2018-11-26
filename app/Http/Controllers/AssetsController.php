<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AssetsController extends Controller
{

    public function findByTag(Request $request, $tag)
    {

        $userId = $request->input('user_id');

        $assets = DB::table('assets')
            ->join('models', 'assets.model_id', '=', 'models.id')
            ->join('locations', 'assets.rtd_location_id', '=', 'locations.id')
            ->join('status_labels', 'assets.status_id', '=', 'status_labels.id')
            ->join('suppliers', 'assets.supplier_id', '=', 'suppliers.id')
            ->join('users', 'assets.assigned_to', '=', 'users.id')
            ->select('assets.*',
                'models.name AS model_name',
                'models.model_number',
                'locations.name AS location_name',
                'status_labels.name AS status_label',
                'suppliers.name AS supplier_name',
                'users.email AS email'
            )
            ->where('assets.asset_tag', $tag)
            ->get();

        //TODO -> STORE IN HISTORY

        if (sizeof($assets) == 0) {
            return response()->json(array('success' => 0, 'message' => "No such asset found"), 200);
        }


        foreach ($assets as $asset) {

            //check for iss_location_id

            $issueLocation = DB::table('locations')
                ->select('name')
                ->where('id', $asset->iss_location_id)
                ->get();

            if ($issueLocation->count() > 0) {
                $asset->issue_location_name = $issueLocation->name;
            } else {
                $asset->issue_location_name = "";
            }

            DB::table('user_history')->insert(
                ['user_id' => $userId, 'asset_id' => $asset->id, 'date_added' => date("Y-m-d H:i:s")]
            );

        }

        return response()->json(array('success' => 1, 'message' => $assets), 200);

    }

    public function getUserHistory($userId)
    {

        $history = DB::table('user_history')
            ->select('*')
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        //TODO -> STORE IN HISTORY

        if (sizeof($history) == 0) {
            return response()->json(array('success' => 0, 'message' => "No user history found"), 200);
        }

        $assetHistory = array();

        foreach ($history as $asset) {

            $assets = DB::table('assets')
                ->join('models', 'assets.model_id', '=', 'models.id')
                ->join('locations', 'assets.rtd_location_id', '=', 'locations.id')
                ->join('status_labels', 'assets.status_id', '=', 'status_labels.id')
                ->join('suppliers', 'assets.supplier_id', '=', 'suppliers.id')
                ->join('users', 'assets.assigned_to', '=', 'users.id')
                ->select('assets.*',
                    'models.name AS model_name',
                    'models.model_number',
                    'locations.name AS location_name',
                    'status_labels.name AS status_label',
                    'suppliers.name AS supplier_name',
                    'users.email AS email'
                )
                ->where('assets.id', $asset->asset_id)
                ->get();

            foreach ($assets as $item) {

                //check for iss_location_id

                $issueLocation = DB::table('locations')
                    ->select('name')
                    ->where('id', $item->iss_location_id)
                    ->get();

                if ($issueLocation->count() > 0) {
                    $asset->issue_location_name = $issueLocation->name;
                } else {
                    $asset->issue_location_name = "";
                }

            }


            $assetHistory[] = $assets[0];

        }

        return response()->json(array('success' => 1, 'message' => $assetHistory), 200);

    }


}
