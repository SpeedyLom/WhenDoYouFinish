<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish\HttpRequest;

final class CurlBasicAuthentication implements BasicAuthentication
{
    private \CurlHandle $curlHandle;
    private array $enabledOptions = [];

    public function __construct(string $endPoint)
    {
        $this->curlHandle = curl_init();
        $this->setOption('CURLOPT_RETURNTRANSFER', true);
        $this->setOption('CURLOPT_URL', $endPoint);
    }

    private function setOption(string $option, mixed $value): bool
    {
        $this->enabledOptions[$option] = $value;

        return curl_setopt($this->curlHandle, constant($option), $value);
    }

    public function getOptionValue(string $option): mixed
    {
        return $this->enabledOptions[$option] ?? null;
    }

    public function addBasicHttpAuthentication(string $username, string $password): bool
    {
        return $this->setOption('CURLOPT_USERPWD', $username . ':' . $password);
    }

    public function makeRequest(): string | bool
    {
        return curl_exec($this->curlHandle);
    }

    public function close(): void
    {
        curl_close($this->curlHandle);
        unset($this->curlHandle);
    }
}
