<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish\HttpRequest;

use CurlHandle;

final class CurlBasicAuthentication implements BasicAuthentication
{
    private CurlHandle $curlHandle;

    public function __construct(
        string $endPoint
    ) {
        $this->curlHandle = curl_init();
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_URL, $endPoint);
    }

    public function addBasicHttpAuthentication(
        string $username,
        string $password
    ): bool {
        return curl_setopt(
            $this->curlHandle,
            CURLOPT_USERPWD,
            $username . ':' . $password
        );
    }

    public function makeRequest(): string|bool
    {
        return curl_exec($this->curlHandle);
    }

    public function close(): void
    {
        curl_close($this->curlHandle);
        unset($this->curlHandle);
    }
}
