<?php 

namespace App\Services;

use App\Models\OAuthClientToken;

class OAuthClientTokenService
{   
    protected $oAuthClientToken;
   
    public function __construct()
    {
        $this->oAuthClientToken = new OAuthClientToken;    
    }

    public function tokenDetails(): ?object
    {
        return $this->oAuthClientToken->whereNotNull('access_token')->first();
    }

    public function hasTokenExpired($expiresIn, $updatedAt) 
    { 
        return now()->gte($updatedAt->addSeconds($expiresIn));
    }

    public function clearTokens() 
    {
        return $this->oAuthClientToken->whereNotNull('access_token')->delete();
    }

    public function insertNew(object $payload)
    {
        return $this->oAuthClientToken->create([
            'access_token' => $payload->access_token,
            'expires_in' => $payload->expires_in
        ]);
    }
}