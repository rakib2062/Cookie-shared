<?php

namespace App\Services;

use App\Models\TrackerEvent;
use App\Models\TrackerMapping;
use App\Responses\CommonResponseEntity;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CookieService
{
    public function track(Request $request) {
        $commonResponseEntity = new CommonResponseEntity();
        try {
            $origin = $request->headers->get('origin');
            $allowed = config('tracker.allowed_origins', []);

            if (!$origin || !in_array($origin, $allowed)) {
                $commonResponseEntity->statusCode = 403;
                $commonResponseEntity->message = "Origin Not Allowed";
                return $commonResponseEntity;
            }

            $trackerId = $request->cookie('ssl-ck-rpd-tracker_id');
            $cookieDomain = config('tracker.cookie_domain');
            $cookieMinutes = config('tracker.cookie_minutes');

            if (!$trackerId) {
                $trackerId = Str::uuid()->toString();
            }

            // Upsert mapping
            $mapping = TrackerMapping::firstOrNew(['tracker_id' => $trackerId]);
            $now = Carbon::now();

            if (!$mapping->exists) {
                $mapping->first_ip = $request->ip();
                $mapping->first_user_agent = substr($request->userAgent() ?? '', 0, 1000);
                $mapping->first_seen = $now;
                $mapping->origin = $origin;
            }

            $mapping->last_ip = $request->ip();
            $mapping->last_user_agent = substr($request->userAgent() ?? '', 0, 1000);
            $mapping->last_seen = $now;
            $mapping->visit_count = $mapping->visit_count + 1;
            // Optional: store site_user_id if provided (should be hashed/tokenized server-side)
            if ($request->filled('site_user_id')) {
                $mapping->site_user_id = substr($request->input('site_user_id'), 0, 200);
            }
            $mapping->save();

            // add an event
            TrackerEvent::create([
                'tracker_mapping_id' => $mapping->id,
                'origin' => $origin,
                'path' => $request->getPathInfo(),
                'ip' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 1000),
                'meta' => [
                    'qs' => $request->query(),
                ]
            ]);

            // Queue cookie (SameSite=None required for cross-site usage)
            Cookie::queue(
                cookie(
                    name: 'ssl-ck-rpd-tracker_id',
                    value: $trackerId,
                    minutes: $cookieMinutes,
                    path: '/',
                    domain: $cookieDomain,
                    secure: true,
                    httpOnly: false,    // frontend may want to read it; set true if you don't need frontend access
                    raw: false,
                    sameSite: 'None'
                )
            );

            $commonResponseEntity->data = $trackerId;
        } catch (Exception $e) {
            Log::error('Something went wrong! : ' . $e->getMessage() . ' (' . __METHOD__ . ') [' . __FILE__ . ':' . __LINE__ . '] ');
            $commonResponseEntity->statusCode = 500;
            $commonResponseEntity->message = "Internal Server Error";
        }

        return $commonResponseEntity;
    }

    public function redirectSync(Request $request) {
        $commonResponseEntity = new CommonResponseEntity();
        try{
            $origin = $request->query('origin');
            $allowed = config('tracker.allowed_origins', []);

            if (!$origin || !in_array($origin, $allowed)) {
                $commonResponseEntity->statusCode = 400;
                $commonResponseEntity->message = "Invalid or missing origin";
                $commonResponseEntity->data = $origin;
                return $commonResponseEntity;
            }

            $trackerId = $request->cookie('ssl-ck-rpd-tracker_id') ?? Str::uuid()->toString();
            $cookieDomain = config('tracker.cookie_domain');
            $cookieMinutes = config('tracker.cookie_minutes');

            // Queue cookie
            Cookie::queue(
                cookie(
                    name: 'ssl-ck-rpd-tracker_id',
                    value: $trackerId,
                    minutes: $cookieMinutes,
                    path: '/',
                    domain: $cookieDomain,
                    secure: true,
                    httpOnly: false,
                    raw: false,
                    sameSite: 'None'
                )
            );

            // Update DB mapping similarly to the track() method
            $mapping = TrackerMapping::firstOrNew(['tracker_id' => $trackerId]);
            $now = Carbon::now();

            if (!$mapping->exists) {
                $mapping->first_ip = $request->ip();
                $mapping->first_user_agent = substr($request->userAgent() ?? '', 0, 1000);
                $mapping->first_seen = $now;
                $mapping->origin = $origin;
            }

            $mapping->last_ip = $request->ip();
            $mapping->last_user_agent = substr($request->userAgent() ?? '', 0, 1000);
            $mapping->last_seen = $now;
            $mapping->visit_count = $mapping->visit_count + 1;
            $mapping->save();

            // safe redirect back, origin is allowed and considered safe
            $redirectUrl = rtrim($origin, '/') . '?tracker_id=' . urlencode($trackerId);

            $commonResponseEntity->data = $redirectUrl;
            
        } catch (Exception $e) {
            Log::error('Something went wrong! : ' . $e->getMessage() . ' (' . __METHOD__ . ') [' . __FILE__ . ':' . __LINE__ . '] ');
            $commonResponseEntity->statusCode = 500;
            $commonResponseEntity->message = "Internal Server Error";
        }

        return $commonResponseEntity;
    }
}