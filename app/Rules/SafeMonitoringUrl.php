<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeMonitoringUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a safe monitoring URL.');

            return;
        }

        $parts = parse_url($value);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            $fail('The :attribute must be a safe monitoring URL.');

            return;
        }

        $scheme = strtolower($parts['scheme']);

        if (! in_array($scheme, ['http', 'https'], true)) {
            $fail('The :attribute must use HTTP or HTTPS.');

            return;
        }

        $host = trim(strtolower($parts['host']), '[]');

        if ($this->isBlockedHostname($host) || $this->isBlockedIpAddress($host)) {
            $fail('The :attribute cannot point to a local or private network address.');
        }
    }

    private function isBlockedHostname(string $host): bool
    {
        return $host === 'localhost'
            || str_ends_with($host, '.localhost')
            || str_contains($host, '.local');
    }

    private function isBlockedIpAddress(string $host): bool
    {
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return $host === '::1';
        }

        if (! filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        $ip = ip2long($host);

        if ($ip === false) {
            return false;
        }

        $blockedRanges = [
            ['0.0.0.0', 8],
            ['10.0.0.0', 8],
            ['127.0.0.0', 8],
            ['169.254.0.0', 16],
            ['172.16.0.0', 12],
            ['192.168.0.0', 16],
        ];

        foreach ($blockedRanges as [$network, $prefix]) {
            if ($this->ipv4InCidr($ip, $network, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function ipv4InCidr(int $ip, string $network, int $prefix): bool
    {
        $networkIp = ip2long($network);

        if ($networkIp === false) {
            return false;
        }

        $mask = -1 << (32 - $prefix);

        return ($ip & $mask) === ($networkIp & $mask);
    }
}
