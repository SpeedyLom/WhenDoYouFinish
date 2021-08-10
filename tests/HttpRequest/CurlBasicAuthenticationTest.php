<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish\Tests\HttpRequest;

use PHPUnit\Framework\TestCase;
use SpeedyLom\WhenDoYouFinish\HttpRequest\CurlBasicAuthentication;

class CurlBasicAuthenticationTest extends TestCase
{
    private CurlBasicAuthentication $curl;

    protected function setUp(): void
    {
        $this->curl = new CurlBasicAuthentication('https://github.com/');
    }

    public function testAddBasicHttpAuthenticationSuccessful(): void
    {
        $this->assertTrue(
            $this->curl->addBasicHttpAuthentication(
                'username',
                'password'
            )
        );
    }

    public function testMakeRequestWithoutAuthentication(): void
    {
        $this->assertIsString($this->curl->makeRequest());
    }

    public function testCloseCausesErrorExceptionOnReuse(): void
    {
        $this->curl->close();
        $this->expectException(\Error::class);
        $this->curl->makeRequest();
    }
}
