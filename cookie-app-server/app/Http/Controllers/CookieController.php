<?php

namespace App\Http\Controllers;

use App\Services\CookieService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class CookieController extends Controller
{
    protected CookieService $cookieService;

    public function __construct(CookieService $cookieService)
    {
        $this->cookieService = $cookieService;
        // $this->middleware('throttle:'.config('tracker.rate_limit_per_min').',1')->only(['track']);
    }

    // AJAX / Fetch endpoint
    public function track(Request $request)
    {
        try{
            $trackerIdResponse = $this->cookieService->track($request);

            if($trackerIdResponse->statusCode !== 200) {
                return Response::json(['error' => $trackerIdResponse->message], $trackerIdResponse->statusCode);
            }

            Log::info('Cookie track service response : ' . $trackerIdResponse->message. ':' . json_encode($trackerIdResponse) . ' (' . __METHOD__ . ') [' . __FILE__ . ':' . __LINE__ . '] ');
            $response = Response::json(['tracker_id' => $trackerIdResponse->data]);
        } catch (Exception $e) {
            Log::error('Something went wrong! : ' . $e->getMessage() . ' (' . __METHOD__ . ') [' . __FILE__ . ':' . __LINE__ . '] ');
            $response = Response::json(['error' => 'Something went wrong'], 500);
        }

        return $response;
    }

    // Redirect-based fallback for strict browsers (origin validated)
    public function redirectSync(Request $request)
    {
        try{
            $redirectUrlResponse = $this->cookieService->redirectSync($request);
            Log::info('Cookie redirectSync service response : ' . $redirectUrlResponse->message. ':' . json_encode($redirectUrlResponse) . ' (' . __METHOD__ . ') [' . __FILE__ . ':' . __LINE__ . '] ');
        } catch (Exception $e) {
            Log::error('Something went wrong! : ' . $e->getMessage() . ' (' . __METHOD__ . ') [' . __FILE__ . ':' . __LINE__ . '] ');
        }

        return redirect()->away($redirectUrlResponse->data);
    }
}