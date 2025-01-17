<?php

namespace App\Components;

use GuzzleHttp\Client;

class ImportDataClient
{
    public $client;

    /**
     * @param $client
     */
    public function __construct()
    {
        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => config('api.base_url'),
            // You can set any number of default request options.
            'timeout' => 3.0,
        ]);
    }

}
