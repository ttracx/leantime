<?php

namespace Test\Unit;

use GuzzleHttp\HandlerStack;
use Safe4Work\Core\Http\Client\ApiClient;

class ApiClientTest extends \Unit\TestCase
{
    public function test_o_auth2(): void
    {
        $baseUri = 'http://test.com';
        $stack = HandlerStack::create();
        $requestDefaults = [];

        $client = ApiClient::oAuth2($baseUri, $stack, $requestDefaults);

        $this->assertEquals('http://test.com', $client->getConfig('base_uri'));
        $this->assertSame($stack, $client->getConfig('handler'));
        $this->assertEquals('oauth', $client->getConfig('auth'));
    }

    public function test_o_auth2_grants(): void
    {
        $baseUri = 'http://test.com';
        $creds = [
            'client_id' => 'testclient',
            'client_secret' => 'testsecret',
        ];

        $stack = ApiClient::oAuth2Grants($baseUri, $creds);

        $this->assertInstanceOf(HandlerStack::class, $stack);
    }

    public function test_o_auth1(): void
    {
        $baseUri = 'http://test.com';
        $creds = [
            'consumer_key' => 'testconsumer',
            'consumer_secret' => 'testsecret',
            'token' => 'testtoken',
            'token_secret' => 'testtokensecret',
        ];

        $client = ApiClient::oAuth1($baseUri, $creds);

        $this->assertEquals('http://test.com', $client->getConfig('base_uri'));
        $this->assertEquals('oauth', $client->getConfig('auth'));
    }

    public function test_basic_auth(): void
    {
        $baseUri = 'http://test.com';
        $creds = [
            'username' => 'testuser',
            'password' => 'testpass',
        ];

        $client = ApiClient::basicAuth($baseUri, $creds);

        $this->assertEquals('http://test.com', $client->getConfig('base_uri'));
        $this->assertEquals($creds, $client->getConfig('auth'));
    }

    public function test_digest(): void
    {
        $baseUri = 'http://test.com';
        $creds = [
            'username' => 'testuser',
            'password' => 'testpass',
            'digest' => 'testdigest',
        ];

        $client = ApiClient::digest($baseUri, $creds);

        $config = $client->getConfig();
        $this->assertEquals('http://test.com', $config[1]['base_uri']);
        $this->assertEquals($creds, $config[1]['auth']);
    }

    public function test_ntlm(): void
    {
        $baseUri = 'http://test.com';
        $creds = [
            'username' => 'testuser',
            'password' => 'testpass',
            'ntlm' => 'testntlm',
        ];

        $client = ApiClient::ntlm($baseUri, $creds);

        $this->assertEquals('http://test.com', $client->getConfig('base_uri'));
        $this->assertEquals($creds, $client->getConfig('auth'));
    }
}
