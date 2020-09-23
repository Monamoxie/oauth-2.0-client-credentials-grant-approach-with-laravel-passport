<?php

namespace App\Http\Controllers;

use App\Services\OAuthClientTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index(Request $request, OAuthClientTokenService $oAuthClientTokenService)
    {
        $posts = []; 

        // check if there's a token for this client to access the remote
        $oAuthClientTokenDetails = $oAuthClientTokenService->tokenDetails();
         
        if($oAuthClientTokenDetails === null 
            || (isset($oAuthClientTokenDetails->expires_in) && $oAuthClientTokenService->hasTokenExpired($oAuthClientTokenDetails->expires_in, $oAuthClientTokenDetails->updated_at))
        ) {
            return redirect('/dashboard/token/request');
        }
        
        $resourceResponse = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '. $oAuthClientTokenDetails->access_token
        ])->get(env('RESOURCE_APP_URL') . 'api/user/resource/posts');
         
        if($resourceResponse->status() === 200) {
            $posts = $resourceResponse->json();
        }
        
        return view('dashboard', compact('posts'));
    }

    public function requestToken(Request $request, OAuthClientTokenService $oAuthClientTokenService)
    {
         
        $response = Http::post(env('RESOURCE_APP_URL') . 'oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
            'scope' => '*', 
        ]);

        $resourceResponse = json_decode($response->getBody());

        // Delete all existing tokens if any
        $oAuthClientTokenService->clearTokens();

        // Insert new record
        $oAuthClientTokenService->insertNew($resourceResponse);

        return redirect('/dashboard');
       
    }
}
