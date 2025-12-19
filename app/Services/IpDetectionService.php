<?php

// app/Services/IpDetectionService.php
// Create this new service: php artisan make:service IpDetectionService

namespace App\Services;

use Illuminate\Support\Facades\Http;

class IpDetectionService
{
    /**
     * Get the real public IP address (not localhost)
     */
    public function getRealPublicIp()
    {
        // Try multiple services to get public IP
        $services = [
            'https://api.ipify.org',
            'https://icanhazip.com',
            'https://ipinfo.io/ip',
            'https://checkip.amazonaws.com',
        ];

        foreach ($services as $service) {
            try {
                $response = Http::timeout(5)->get($service);
                if ($response->successful()) {
                    $ip = trim($response->body());
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip;
                    }
                }
            } catch (\Exception $e) {
                continue; // Try next service
            }
        }

        return null;
    }

    /**
     * Get all possible IP addresses
     */
    public function getAllIpAddresses()
    {
        return [
            'request_ip' => request()->ip(),
            'server_remote_addr' => $_SERVER['REMOTE_ADDR'] ?? null,
            'http_client_ip' => $_SERVER['HTTP_CLIENT_IP'] ?? null,
            'http_x_forwarded_for' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
            'http_x_real_ip' => $_SERVER['HTTP_X_REAL_IP'] ?? null,
            'public_ip' => $this->getRealPublicIp(),
            'is_localhost' => $this->isLocalhost(request()->ip()),
        ];
    }

    /**
     * Check if IP is localhost/private
     */
    public function isLocalhost($ip)
    {
        $privateRanges = [
            '127.0.0.0/8',    // Localhost
            '10.0.0.0/8',     // Private A
            '172.16.0.0/12',  // Private B
            '192.168.0.0/16', // Private C
            '169.254.0.0/16', // Link-local
        ];

        foreach ($privateRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the best IP for attendance checking
     */
    public function getBestIpForAttendance()
    {
        $allIps = $this->getAllIpAddresses();

        // Always use request IP (the actual client IP through proxy)
        return $allIps['request_ip'];
    }

    private function ipInRange($ip, $range)
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        [$subnet, $bits] = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;

        return ($ip & $mask) === $subnet;
    }
}
