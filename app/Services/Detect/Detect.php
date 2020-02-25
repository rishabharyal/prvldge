<?php

namespace App\Services\Detect;

use App\Services\Detect\Contracts\DetectInterface;

class Detect implements DetectInterface {

    private $location;

    public function __construct(Location $location)
    {
        $this->location = $location;
    }

    /**
     * @return array
     */
    public function platform(): array
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform = 'Unknown OS Platform';
        $os_array = [
            '/windows phone 8/i' => 'Windows Phone 8',
            '/windows phone os 7/i' => 'Windows Phone 7',
            '/windows nt 10/i' => 'Windows 10',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iOS',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile',
        ];

        $device = '';
        foreach ($os_array as $regex => $value) {

            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
                $device = !preg_match('/(Windows|mac|linux|ubuntu)/i', $os_platform) ? 'Mobile' : (preg_match('/phone/i', $os_platform) ? 'Mobile' : 'Desktop');
            }
        }
        $device = !$device ? 'Desktop' : $device;
        return ['os' => $os_platform, 'device' => $device];
    }

    /**
     * @return mixed|string
     */
    public function browser() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $found = false;
        $browser_array = [
            '/mobile/i' => 'Handheld Browser',
            '/msie/i' => 'Internet Explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/opera/i' => 'Opera',
            '/netscape/i' => 'Netscape',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror'
        ];

        foreach ($browser_array as $regex => $value) {
            if ($found) {
                break;
            }

            if (preg_match($regex, $user_agent, $result)) {
                $browser = $value;
            }
        }
        return $browser;
    }

    public function ip() {
        return $this->location->ip();
    }

    public function country() {
        return $this->location->getGeoData()['geoplugin_countryCode'] ?? null;
    }
}
