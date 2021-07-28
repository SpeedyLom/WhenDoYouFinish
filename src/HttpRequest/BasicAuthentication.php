<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish\HttpRequest;

interface BasicAuthentication
{
    public function __construct(string $endPoint);

    public function addBasicHttpAuthentication(
        string $username,
        string $password
    ): bool;

    public function makeRequest(): string | bool;

    public function close(): void;
}
