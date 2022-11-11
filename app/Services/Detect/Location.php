<?php

namespace App\Services\Detect;

use GuzzleHttp\Client;

class Location {

    private $ip;
    private $request;

    private $geoData;

    /**
     * Geolocation constructor.
     * @param Client $request
     */
    public function __construct(Client $request)
    {
        $this->request = $request;
        $this->ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_FORWARDED'] ?? $_SERVER['HTTP_FORWARDED_FOR'] ?? $_SERVER['HTTP_FORWARDED'] ?? $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $this->requestForGeoData();
    }

    private $url = 'http://www.geoplugin.net/json.gp?ip=';

    /**
     *
     */
    public function getGeoData() {
        return $this->geoData;
    }

    private function requestForGeoData(): void
    {
        $request = $this->request->get(
            $this->url . $this->ip
        );

        $this->geoData =  json_decode($request->getBody()->getContents(), true);
    }

    public function ip() {
        return $this->ip;
    }

}
