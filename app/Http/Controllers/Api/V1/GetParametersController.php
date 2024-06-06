<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobCategory;
use App\Models\DriverLicense;
use App\Models\Region;
use App\Models\CarColor;
use App\Models\Mark;
use App\Models\Models;
use App\Imports\MarksImport;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Validator;
use App\Models\SocialData;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use GuzzleHttp\Client;
use Intervention\Image\Facades\Image as RTY;



class GetParametersController extends Controller
{


    public function get_car_models(){

        $get_marks = Mark::doesntHave('model')->get();

   $i = 0;
        foreach ($get_marks as $mark){
//            $i++;
//
//                $requestCommand = "curl 'https://fleet.yandex.ru/api/v1/parks/cars/models/list' \
//  -H 'Accept: */*' \
//  -H 'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,hy;q=0.6,ka;q=0.5,ar;q=0.4' \
//  -H 'Connection: keep-alive' \
//  -H 'Content-Type: text/plain;charset=UTF-8' \
//  -H 'Cookie: yandexuid=2694046041669733615; yuidss=2694046041669733615; ymex=1697622506.oyu.6027001381694936885#2010296885.yrts.1694936885#2010296885.yrtsi.1694936885; is_gdpr=0; skid=3284803161695373148; is_gdpr_b=CJbqDxDf0QEoAg==; yashr=8943065081696402107; receive-cookie-deprecation=1; amcuid=2908825341709891784; _ym_d=1712422281; gdpr=0; _ym_uid=1713525204134374201; L=BAxaWX16Wk9vSwEHRGp8SHN3Tlx0QgNjMCsmXAAMCiAmJQgqPQ==.1713525441.15684.348828.226d0676de9d4c8c5326c70ee603fd76; yandex_login=aarmanaleqyan; yp=2012481791.pcs.0#1715014279.ygu.1#2028885441.udn.cDphYXJtYW5hbGVxeWFu#1714892084.szm.1:1600x900:1600x773; bh=Ej8iQ2hyb21pdW0iO3Y9IjEyNCIsIkdvb2dsZSBDaHJvbWUiO3Y9IjEyNCIsIk5vdC1BLkJyYW5kIjt2PSI5OSIaBSJ4ODYiIhAiMTI0LjAuNjM2Ny4yMDEiKgI/MDIJIk5leHVzIDUiOgkiV2luZG93cyJCCCIxMC4wLjAiSgQiNjQiUlwiQ2hyb21pdW0iO3Y9IjEyNC4wLjYzNjcuMjAxIiwiR29vZ2xlIENocm9tZSI7dj0iMTI0LjAuNjM2Ny4yMDEiLCJOb3QtQS5CcmFuZCI7dj0iOTkuMC4wLjAiIg==; i=kN7JFwxTXV2dwHdoLTeoO8Wx7ueyeonD/+Y3Tuecb5D7iHUzYBPuaIPHDQoEJTAQRLLM3KquCayD1TnYM9oTaRO/3Xw=; _ym_isad=2; Session_id=3:1715700975.5.0.1713525441909:9gJPuQ:50.1.2:1|1699797003.0.2.3:1713525441|3:10288139.325770.EeFEf3whN8v--LKgz07-LpHYIgs; sessar=1.1189.CiA58kkKPpg-3cAcFbXZF5sXIMLSqyKNskdLKxzBr6kTCA.ed-I4dE_xwvrL67onNEt-HTjRL8lyA5STFiuDOmwXsE; sessionid2=3:1715700975.5.0.1713525441909:9gJPuQ:50.1.2:1|1699797003.0.2.3:1713525441|3:10288139.325770.fakesign0000000000000000000; yabs-vdrf=BcS9b703tzS810; font_loaded=YSv1; park_id=2c815b35e735438ba814e036acdd55b6; ys=udn.cDphYXJtYW5hbGVxeWFu#c_chck.1195367365; _ym_visorc=b; _yasc=G7pg/L+6sAtG1QhOGRAoFyGxmCtDI3Z4FBN5uXY9Tz5ZouzmIIgDvrD7WeoRFiCQeayrBMt+vA==' \
//  -H 'Language: ru' \
//  -H 'Origin: https://fleet.yandex.ru' \
//  -H 'Referer: https://fleet.yandex.ru/vehicles/create?park_id=2c815b35e735438ba814e036acdd55b6' \
//  -H 'Sec-Fetch-Dest: empty' \
//  -H 'Sec-Fetch-Mode: cors' \
//  -H 'Sec-Fetch-Site: same-origin' \
//  -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36' \
//  -H 'X-Client-Version: fleet/9037' \
//  -H 'X-Park-Id: 2c815b35e735438ba814e036acdd55b6' \
//  -H 'sec-ch-ua: \"Chromium\";v=\"124\", \"Google Chrome\";v=\"124\", \"Not-A.Brand\";v=\"99\"' \
//  -H 'sec-ch-ua-mobile: ?0' \
//  -H 'sec-ch-ua-platform: \"Windows\"' \
//  --data-raw '{\"query\":{\"park\":{\"id\":\"2c815b35e735438ba814e036acdd55b6\"},\"brand\":{\"name\":\"$mark->name\"}}}'";
//                $response = exec($requestCommand);
//                dd($mark->name );
//                if ($i == 9){
//
//                    dd( json_decode($response ) );
//                }



// Выполните команду curl через exec

            $response = Http::withHeaders([
                'Accept' => '*/*',
                'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,hy;q=0.6,ka;q=0.5,ar;q=0.4',
                'Connection' => 'keep-alive',
                'Content-Type' => 'text/plain;charset=UTF-8',
                'Cookie' => 'yandexuid=2694046041669733615; yuidss=2694046041669733615; ymex=1697622506.oyu.6027001381694936885#2010296885.yrts.1694936885#2010296885.yrtsi.1694936885; is_gdpr=0; skid=3284803161695373148; is_gdpr_b=CJbqDxDf0QEoAg==; yashr=8943065081696402107; receive-cookie-deprecation=1; amcuid=2908825341709891784; _ym_d=1712422281; gdpr=0; _ym_uid=1713525204134374201; L=BAxaWX16Wk9vSwEHRGp8SHN3Tlx0QgNjMCsmXAAMCiAmJQgqPQ==.1713525441.15684.348828.226d0676de9d4c8c5326c70ee603fd76; yandex_login=aarmanaleqyan; yp=2012481791.pcs.0#1715014279.ygu.1#2028885441.udn.cDphYXJtYW5hbGVxeWFu#1714892084.szm.1:1600x900:1600x773; bh=Ej8iQ2hyb21pdW0iO3Y9IjEyNCIsIkdvb2dsZSBDaHJvbWUiO3Y9IjEyNCIsIk5vdC1BLkJyYW5kIjt2PSI5OSIaBSJ4ODYiIhAiMTI0LjAuNjM2Ny4yMDEiKgI/MDIJIk5leHVzIDUiOgkiV2luZG93cyJCCCIxMC4wLjAiSgQiNjQiUlwiQ2hyb21pdW0iO3Y9IjEyNC4wLjYzNjcuMjAxIiwiR29vZ2xlIENocm9tZSI7dj0iMTI0LjAuNjM2Ny4yMDEiLCJOb3QtQS5CcmFuZCI7dj0iOTkuMC4wLjAiIg==; i=kN7JFwxTXV2dwHdoLTeoO8Wx7ueyeonD/+Y3Tuecb5D7iHUzYBPuaIPHDQoEJTAQRLLM3KquCayD1TnYM9oTaRO/3Xw=; _ym_isad=2; Session_id=3:1715700975.5.0.1713525441909:9gJPuQ:50.1.2:1|1699797003.0.2.3:1713525441|3:10288139.325770.EeFEf3whN8v--LKgz07-LpHYIgs; sessar=1.1189.CiA58kkKPpg-3cAcFbXZF5sXIMLSqyKNskdLKxzBr6kTCA.ed-I4dE_xwvrL67onNEt-HTjRL8lyA5STFiuDOmwXsE; sessionid2=3:1715700975.5.0.1713525441909:9gJPuQ:50.1.2:1|1699797003.0.2.3:1713525441|3:10288139.325770.fakesign0000000000000000000; yabs-vdrf=BcS9b703tzS810; font_loaded=YSv1; park_id=2c815b35e735438ba814e036acdd55b6; ys=udn.cDphYXJtYW5hbGVxeWFu#c_chck.1195367365; _ym_visorc=b; _yasc=G7pg/L+6sAtG1QhOGRAoFyGxmCtDI3Z4FBN5uXY9Tz5ZouzmIIgDvrD7WeoRFiCQeayrBMt+vA==',
                'Language' => 'ru',
                'Origin' => 'https://fleet.yandex.ru',
                'Referer' => 'https://fleet.yandex.ru/vehicles/create?park_id=2c815b35e735438ba814e036acdd55b6',
                'Sec-Fetch-Dest' => 'empty',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Site' => 'same-origin',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                'X-Client-Version' => 'fleet/9037',
                'X-Park-Id' => '2c815b35e735438ba814e036acdd55b6',
                'sec-ch-ua' => '"Chromium";v="124", "Google Chrome";v="124", "Not-A.Brand";v="99"',
                'sec-ch-ua-mobile' => '?0',
                'sec-ch-ua-platform' => '"Windows"',
            ])->post('https://fleet.yandex.ru/api/v1/parks/cars/models/list', [
                'query' => [
                    'park' => [
                        'id' => '2c815b35e735438ba814e036acdd55b6',
                    ],
                    'brand' => [
                        'name' => $mark->name,
                    ],
                ],
            ]);

            $data = $response->json();




//            dd($data);

            $success = false;



                    foreach ($data['models'] as $models) {
                        Models::updateOrCreate(
                            ['mark_id' => $mark->id, 'name' => $models['name']],
                            ['mark_id' => $mark->id, 'name' => $models['name']]
                        );
                    }





            sleep(30);
        }




    }

    public function ggmb(){
        return response()->json([
           'status' => true,
           'message' => 'Telegram xxx  Сукин сын не дал денги и кинул за это приложения telegram https://t.me/Arman1997  Отвечает за свои слова'
        ],200);
    }
    public function check_license(Request $request){

        $apiKey = 'К85638582288957';

        $imageUrl = 'http://driverconnect.digiluys.com/uploads/1714459313-663092b10f759.jpgСПС';

        $response = Http::get('https://api.ocr.space/parse/ImageUrl', [
            'apikey' => $apiKey,
            'url' => $imageUrl,
            'language' => 'eng',
            'scale' => 'true',
            'isTable' => 'true',
            'OCREngine' => 2,
            'isOverlayRequired' => 'true',
        ]);

        $parsedData = $response->json();


        if (isset($parsedData['ParsedResults'])) {
            $extractedText = $parsedData['ParsedResults'][0]['ParsedText'];
            if (strpos($extractedText, 'ВОДИТЕЛЬСКОЕ УДОСТОВЕРЕНИЕ') !== false) {
                echo "Фраза 'ВОДИТЕЛЬСКОЕ УДОСТОВЕРЕНИЕ' найдена в тексте.";
            } else {
                echo "Фраза 'ВОДИТЕЛЬСКОЕ УДОСТОВЕРЕНИЕ' не найдена в тексте.";
            }
        } else {
            // Handle API error
            dd($parsedData['ErrorMessage']);
        }

    }


    protected $httpClient;
    protected $iamApiUrl = 'https://iam.api.cloud.yandex.net/iam/v1/tokens';



    public function get_photo_data($base64Image,$extension,$model){
        $this->httpClient =  new Client();
        $iamApiUrl = 'https://iam.api.cloud.yandex.net/iam/v1/tokens';
        $requestData = [
            'yandexPassportOauthToken' => 'y0_AgAAAAB05PUEAATuwQAAAAEDD-2LAADbdJczTr1JlZ1BH4JmwlgQlYXNcA'
        ];

        $headers = [
            'Content-Type' => 'application/json',
        ];
        $response = $this->httpClient->post($iamApiUrl, [
            'headers' => $headers,
            'json' => $requestData,
        ]);
        $responseData = json_decode($response->getBody()->getContents(), true);
//        if (!isset($request->photo)){
//            return response()->json([
//               'status' => 'false',
//               'message' => 'photo_required'
//            ]);
//        }
//        $photo = $request->photo;
//        if ($photo) {
//            $base64Image =  base64_encode($photo->getContent());
//        }




        $requestData = [
            'mime_type' => $extension,
            'languageCodes' => ['*'],
            'model' => $model,
            'content' => $base64Image
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $responseData['iamToken'],
            'x-folder-id' => env('YANDEX_CLOUD_FOLDER_ID')
        ];

        $ocrApiUrl = 'https://ocr.api.cloud.yandex.net/ocr/v1/recognizeText';

        $response = Http::withHeaders($headers)
            ->post($ocrApiUrl, $requestData)
            ->json();



        return  $response['result']['textAnnotation']['entities']??null;




        //        $this->httpClient =  new Client();
//        $ocrApiUrl = 'https://ocr.api.cloud.yandex.net/ocr/v1/recognizeText';
//        $response = $this->httpClient->post($ocrApiUrl, [
//            'headers' => $headers,
//            'json' => $requestData,
//        ]);
//
//        return json_encode($response->getBody()->getContents(), true)  ;
    }
    /**
     * @OA\Get(
     *     path="/api/get_whatsapp_and_telegram",
     *     summary="Get WhatsApp and Telegram contact information",
     *     tags={"Social Data"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function get_whatsapp_and_telegram(){
            $get = SocialData::first();
            return response()->json([
               'status' => true,
               'data' => $get
            ]);
    }
    /**
     * @OA\Get(
     *     path="/api/job_category",
     *     summary="Get list of job categories",
     *     tags={"Job Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function get_job_category(Request  $request){
        $get = JobCategory::orderby('order', 'asc')->get();


        return response()->json([
           'status' => true,
            'data' => $get
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/get_regions",
     *     summary="Get list of regions",
     *     tags={"Regions"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function get_regions(){
        $get = Region::orderby('order', 'asc')->get();

        return response()->json([
           'status' =>  true,
           'data' => $get
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/drive_license_country",
     *     summary="Get list of driver license countries",
     *     tags={"Driver License Country"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function get_drive_license_country(){
        $get  = DriverLicense::orderby('order', 'asc')->get();

        return response()->json([
           'status' => true,
           'data' => $get
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/car_color",
     *     summary="Get list of car colors",
     *     tags={"Car"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function car_color(){
        $get = CarColor::get();

        return response()->json([
           'status' => true,
            'data' => $get
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/get_car_marks",
     *     summary="Get list of car marks",
     *     tags={"Car"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search string",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function get_car_marks(Request  $request){
        $get = Mark::query();
        $string = $request->search;
        $cacheKey = 'marks_' . $string;
        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
        } else {
            $keyword = $string;
            $name_parts = explode(" ", $keyword);
            $query = clone $get;
            $query->where('name', $string);
            foreach ($name_parts as $part) {
                $query->orWhere(function ($q) use ($part) {
                    $q->where('name', 'like', "%{$part}%");
                });
            }
            $data = $query->get();
            Cache::put($cacheKey, $data, now()->addDay()); // Кеширование на один день с учетом значения поискового запроса
        }
        return response()->json([
           'status' => true,
           'data' => $data,

        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/get_car_model",
     *     summary="Get list of car models by mark ID",
     *     tags={"Car"},
     *     @OA\Parameter(
     *         name="mark_id",
     *         in="query",
     *         description="ID of the car mark",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search string",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 description="Status of the operation"
     *             ),
     *             @OA\Property(
     *                 property="validation_error",
     *                 type="boolean",
     *                 description="Indicates whether the error is due to validation"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="object",
     *                 description="Validation error messages"
     *             )
     *         )
     *     )
     * )
     */
    public function get_car_model(Request  $request){

        $rules=array(
            'mark_id' => 'required|exists:marks,id'
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json([
                'status' => false,
                'validation_error' => true,
                'message' =>$validator->errors()
            ],400);
        }

        $get = Models::query();
        $string = $request->search;
        $cacheKey = 'models_' . $request->mark_id . '_' . $string;

        $get->where('mark_id', $request->mark_id);

        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
        } else {

            $keyword = $string;
            $name_parts = explode(" ", $keyword);
            $query = clone $get;
            $query->where('name', $string);
            foreach ($name_parts as $part) {
                $query->orWhere(function ($q) use ($part) {
                    $q->where('name', 'like', "%{$part}%");
                });
            }
            $query->where('mark_id', $request->mark_id);
            $data = $query->get();
            Cache::put($cacheKey, $data, now()->addDay()); // Кеширование на один день с учетом значения поискового запроса
        }
        return response()->json([
            'status' => true,
            'data' => $data,

        ]);
    }






    public function import()
    {
        Excel::import(new MarksImport, request()->file('file'));

        dd(1);
        return redirect()->back()->with('success', 'Excel file imported successfully.');
    }
}
