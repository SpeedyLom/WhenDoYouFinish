<?php

declare(strict_types=1);

namespace SpeedyLom\WhenDoYouFinish\Tests\HttpRequest;

use SpeedyLom\WhenDoYouFinish\HttpRequest\CurlBasicAuthentication;
use PHPUnit\Framework\TestCase;

class CurlBasicAuthenticationTest extends TestCase
{
    private CurlBasicAuthentication $curl;
    
    protected function setUp(): void
    {
        $this->curl = new CurlBasicAuthentication('https://github.com/');
    }
    
    public function testAddBasicHttpAuthentication(): void
    {
        $this->assertTrue($this->curl->addBasicHttpAuthentication('username', 'password'));
    }

    public function testAuthenticationSuccessful()
    {
        $this->curl->addBasicHttpAuthentication('username', 'password');
        $this->assertSame('username:password', $this->curl->getOptionValue('CURLOPT_USERPWD'));
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
