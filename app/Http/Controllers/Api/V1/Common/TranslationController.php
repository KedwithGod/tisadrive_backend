<?php

namespace App\Http\Controllers\Api\V1\Common;

use GuzzleHttp\Client;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Languages;
use App\Transformers\LanguagesTransformer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

/**
 * @group Translation
 * translation api
 */
class TranslationController extends BaseController
{
    /**
    * Translation api
     * @return \Illuminate\Http\JsonResponse
     * @responseFile responses/translation/translation.json
    */
    public function index()
    {
        $client = new Client();
        $get_api_key = get_map_settings('google_map_key_for_distance_matrix');

        $sheet_id = get_map_settings('google_sheet_id');

        // $response = $client->get("https://sheets.googleapis.com/v4/spreadsheets/{$sheet_id}/values:batchGet", [
        //     'query' => [
        //         'ranges' => [
        //             'Settings!A:Z',
        //             'Sheet1!A:Z',
        //             'Update-Config!A:Z'
        //         ],
        //         'key' => $get_api_key
        //     ]
        // ]);
        $ranges = [
            'Settings!A:Z',
            'Sheet1!A:Z',
            'Update-Config!A:Z',
        ];
        
        $rangeQuery = implode('&', array_map(fn($r) => 'ranges=' . urlencode($r), $ranges));
        
        $query = $rangeQuery . '&key=' . urlencode($get_api_key);
        
        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$sheet_id}/values:batchGet?$query";
        
        $response = Http::get($url);
        
        $data = json_decode($response->getBody()->getContents(), true);

        $settings = [];
        $language = [];
        $lang = [];
        $update_sheet = false;


        for ($i = 1; $i < count($data["valueRanges"][0]['values']); $i++) {
            $sett = $data["valueRanges"][0]['values'][$i];

            if($data["valueRanges"][2]['values'][1][1]=="TRUE"){
                $update_sheet = true;
            }

            if ($sett[0] != '') {
                $settings[$sett[0]][$sett[1]] = array_key_exists(2, $sett) ? $sett[2] : "TRUE";
            }
        }

        foreach ($data["valueRanges"][1]['values'] as $key => $value) {
            for ($i = 1; $i < count($value); $i++) {
                if ($key == 0) {
                    if ($value[$i] != "") {
                        if (key_exists($value[$i], $settings) && key_exists("show", $settings[$value[$i]])) {
                            if ($settings[$value[$i]]['show'] == "TRUE") {
                                $lang[$i] = array(
                                    "name" => $value[$i],
                                    "state" => true,
                                );
                            } else {
                                $lang[$i] = array(
                                    "name" => $value[$i],
                                    "state" => false,
                                );
                            }
                        } else {
                            $lang[$i] = array(
                                "name" => $value[$i],
                                "state" => true,
                            );
                        }
                    }
                } else {
                    if ($value[0] != "" && $lang[$i]["state"]) {
                        $language[$lang[$i]['name']][$value[0]] = $value[$i];
                    }
                }
            }
        }

        return response()->json(['success'=>true,'update_sheet'=>$update_sheet,'data'=>$language]);
        
    }

    /**
    * Translation api
     * @return \Illuminate\Http\JsonResponse
     * @responseFile responses/translation/translation.json
    */
    public function userIndex()
    {
        $client = new Client();
        $get_api_key = get_map_settings('google_map_key_for_distance_matrix');

        $sheet_id = '1R5FZvcrzX9zvu6E-_dOEXgFTEhbRIiHwrBXRiFpQPUU';

        
        $response = $client->get('https://sheets.googleapis.com/v4/spreadsheets/'.$sheet_id.'/values:batchGet?ranges=Settings!A:Z&key='.$get_api_key.'&ranges=Sheet1!A:Z&ranges=Update-Config!A:Z');


        $data = json_decode($response->getBody()->getContents(), true);

        $settings = [];
        $language = [];
        $lang = [];
        $update_sheet = false;


        for ($i = 1; $i < count($data["valueRanges"][0]['values']); $i++) {
            $sett = $data["valueRanges"][0]['values'][$i];

            if($data["valueRanges"][2]['values'][1][1]=="TRUE"){
                $update_sheet = true;
            }

            if ($sett[0] != '') {
                $settings[$sett[0]][$sett[1]] = array_key_exists(2, $sett) ? $sett[2] : "TRUE";
            }
        }

        foreach ($data["valueRanges"][1]['values'] as $key => $value) {
            for ($i = 1; $i < count($value); $i++) {
                if ($key == 0) {
                    if ($value[$i] != "") {
                        if (key_exists($value[$i], $settings) && key_exists("show", $settings[$value[$i]])) {
                            if ($settings[$value[$i]]['show'] == "TRUE") {
                                $lang[$i] = array(
                                    "name" => $value[$i],
                                    "state" => true,
                                );
                            } else {
                                $lang[$i] = array(
                                    "name" => $value[$i],
                                    "state" => false,
                                );
                            }
                        } else {
                            $lang[$i] = array(
                                "name" => $value[$i],
                                "state" => true,
                            );
                        }
                    }
                } else {
                    if ($value[0] != "" && $lang[$i]["state"]) {
                        $language[$lang[$i]['name']][$value[0]] = $value[$i];
                    }
                }
            }
        }

        return response()->json(['success'=>true,'update_sheet'=>$update_sheet,'data'=>$language]);
        
    }


    /**
    * List Localizations
    * 
    * @return \Illuminate\Http\JsonResponse
    * @responseFile responses/translation/list.json
    * 
    * */
   public function listLocalizations()
   {

    
    $data = Languages::where('active',true)->get();

// $data = [
  //   ["lang" => "en", "name" => "English"],
  //   ["lang" => "ar", "name" => "عربي"],
  // ];


    $result = fractal($data, new LanguagesTransformer);

  return response()->json(['success'=>true,'message'=>'success','data'=>$result]);
  

   }


   /**
    * List testException
    * @return \Illuminate\Http\JsonResponse
    * 
    * */

   public function testException()
   {    

    if(request()->has('403')){

        $this->throwAuthorizationException();
    }

    if(request()->has('401')){

        return response()->json(['error' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);

    }

    if(request()->has('500')){

             $this->throwCustomException('custom-exception');
        
    }

    if(request()->has('422')){

        request()->validate([
        'email' => 'required|email|max:150',
        'mobile' => 'required',
        ]);
        
    }

   }
}
