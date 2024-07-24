<?php

namespace Modules\Primavera\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Support\Facades\Cache;

class PrimaveraAuthService
{

    /**
     * The URI of the Primavera API
     *
     * @var string
     */
    private $uriPrimavera;

    /**
     * The constructor of the class
     */
    public function __construct()
    {
        $this->uriPrimavera = env('PRIMAVERA_URI');
    }

    /**
     * It sends a POST request to the API endpoint with the required parameters and returns the response as
     * a JSON object
     *
     * @return The access token and the refresh token.
     */
    public function getAuth()
    {
        if (Cache::get('primavera_token')) {
            return Cache::get('primavera_token');
        }

        $headers = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'form_params' => [
                'username' => env('PRIMAVERA_USERNAME'),
                'password' => env('PRIMAVERA_PASSWORD'),
                'company' => env('PRIMAVERA_COMPANY'),
                'instance' => env('PRIMAVERA_INSTANCE'),
                'grant_type' => env('PRIMAVERA_GRANT_TYPE'),
                'line' => env('PRIMAVERA_LINE')
            ]
        ];

        $body = $this->requestPrimaveraApi('POST', '/WebApi/token', $headers, ['verify' => false]);

        if (!empty($body->access_token)) {
            Cache::put('primavera_token', $body->access_token, 60);
            return Cache::get('primavera_token');
        }
    }

    function requestPrimaveraApi($method, $url, $data = [], $options = ['verify' => false, 'timeout' => 60])
    {
        $client = new Client($options);
        $retryCount = 0;

        while ($retryCount < 3) {
            try {
                if (!str_contains($url, 'token')) {
                    $data = array_merge(
                        ['headers' => [
                            'Authorization' => 'Bearer ' . $this->getAuth()
                            ]
                        ],
                        ['json' => $data] ,
                        ['timeout' => 180]
                    );
                }

                $response = $client->request(
                    $method,
                    $this->uriPrimavera . $url,
                    $data
                );

                if ($response->getStatusCode() === 200) {
                    return json_decode($response->getBody()->getContents());
                }
            } catch ( \GuzzleHttp\Exception\ConnectException $e){
                Cache::put('primavera_token', null);
                if ($e->getCode() === 0) {
                    throw $e;
                }
            } catch (\Exception $e) {
                Cache::put('primavera_token', null);
                if ($e->getCode() === 401) {
                    $this->requestPrimaveraApi($method, $url, $data);
                    $retryCount++;
                } else {
                    throw $e;
                }
             }
        }

        throw new Exception("Authentication failed after 3 retries");
    }
}
