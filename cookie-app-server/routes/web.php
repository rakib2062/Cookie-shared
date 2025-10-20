<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ✅ Allowed domains (your trusted websites)
$allowedOrigins = [
    'https://siteA.com',
    'https://siteB.com',
];

Route::get('/track', function (Request $request) use ($allowedOrigins) {
    $origin = $request->headers->get('origin');
    $trackerId = $request->cookie('tracker_id');

    // ✅ Validate request origin
    if ($origin && !in_array($origin, $allowedOrigins)) {
        return Response::json(['error' => 'Origin not allowed'], 403);
    }

    // ✅ Generate tracker ID if not set
    if (!$trackerId) {
        $trackerId = Str::uuid()->toString();
    }

    // ✅ Build response with CORS
    $response = Response::json(['tracker_id' => $trackerId])
        ->withCookie(cookie(
            name: 'tracker_id',
            value: $trackerId,
            minutes: 60 * 24 * 365,
            path: '/',
            domain: '.mycompany.com',
            secure: true,
            httpOnly: false,
            raw: false,
            sameSite: 'None'
        ));

    // ✅ Add proper CORS headers
    if ($origin && in_array($origin, $allowedOrigins)) {
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
    }

    return $response;
});


// 🚀 Redirect-based fallback (for Safari / strict browsers)
Route::get('/redirect-sync', function (Request $request) use ($allowedOrigins) {
    $origin = $request->query('origin');

    // ✅ Validate origin
    if (!$origin || !in_array($origin, $allowedOrigins)) {
        return Response::make('Invalid or missing origin', 400);
    }

    // ✅ Get or create tracker_id
    $trackerId = $request->cookie('tracker_id') ?? Str::uuid()->toString();

    // ✅ Set tracker cookie
    Cookie::queue(
        Cookie::make(
            name: 'tracker_id',
            value: $trackerId,
            minutes: 60 * 24 * 365,
            path: '/',
            domain: '.mycompany.com',
            secure: true,
            httpOnly: false,
            raw: false,
            sameSite: 'None'
        )
    );

    // ✅ Safe redirect back (prevents open redirect attacks)
    $redirectUrl = rtrim($origin, '/') . '?tracker_id=' . urlencode($trackerId);
    return redirect()->away($redirectUrl);
});