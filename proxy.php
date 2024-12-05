<?php

session_start();

class FreeProxy
{
    protected $proxies = [];

    public function __construct()
    {
        $this->fetchProxies();
    }

    public function fetchProxies()
    {
        $response = @file_get_contents('https://free-proxy-list.net');

        if (!$response) {
            die("Error: Unable to fetch proxies.");
        }

        preg_match_all(
            "#<tr><td>(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})</td><td>(\d+)</td><td>([^<]+)</td><td class='hm'>([^<]+)</td>#",
            $response,
            $results
        );

        if (empty($results[1])) {
            die("Error: No proxies found or structure changed.");
        }

        foreach ($results[1] as $index => $ip) {
            $this->proxies[] = [
                'ip'    => $ip,
                'port'  => (int) $results[2][$index],
                'https' => (strpos($results[4][$index], 'yes') !== false),
            ];
        }
    }

    public function getRandomProxy()
    {
        if (empty($this->proxies)) {
            return null;
        }

        return $this->proxies[array_rand($this->proxies)];
    }
}

// Validate and sanitize the requested URL
if (empty($_GET['url'])) {
    die("No URL provided.");
}
$url = filter_var($_GET['url'], FILTER_SANITIZE_URL);
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    die("Invalid URL.");
}

// Check if a proxy is already stored in the session
if (!isset($_SESSION['proxy'])) {
    // Initialize proxy class and get a random proxy
    $fp = new FreeProxy();
    $proxy = $fp->getRandomProxy();

    if (!$proxy) {
        die("No proxies available.");
    }

    // Store the selected proxy in the session
    $_SESSION['proxy'] = $proxy;
} else {
    // Use the proxy from the session
    $proxy = $_SESSION['proxy'];
}

// Set up cURL with the selected proxy
$proxyIp = $proxy['ip'];
$proxyPort = $proxy['port'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_PROXY, "$proxyIp:$proxyPort");
curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy['https'] ? CURLPROXY_HTTPS : CURLPROXY_HTTP);

$response = curl_exec($ch);

if (!$response) {
    die("Error: " . curl_error($ch));
}
curl_close($ch);

// Output the proxied response
echo $response;
