<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class ZoomAccesToken
{
    public static function getAccessToken()
    {
        $baseUri = 'https://zoom.us/oauth/token';

        $clientId = config('zoom.ZOOM_CLIENT_ID');
        $clientSecret = config('zoom.ZOOM_CLIENT_SECRET_ID');
        $accountId = config('zoom.ACCOUNT_ID');

        $headers = [
            'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
        ];

        $body = [
            'grant_type' => 'account_credentials',
            'account_id' => $accountId,
        ];

        try {
            $client = new Client(['base_uri' => $baseUri]);
            $response = $client->post('', [
                'headers' => $headers,
                'form_params' => $body,
            ]);

            $responseData = json_decode($response->getBody(), true);

            $access_token = $responseData['access_token'];
            return $access_token;

            // return $responseData['access_token'];
        } catch (\Exception $e) {
            \Log::error('Error getting Zoom access token:', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
