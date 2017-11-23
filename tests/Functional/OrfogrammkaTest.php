<?php

namespace Orfogrammka\Tests\Functional;

use GuzzleHttp\Psr7\Response;
use Http\Client\HttpClient;
use Http\Mock\Client as HttpMock;
use Orfogrammka\Tests\Fixtures\Fixture;
use Orfogrammka\ApiClient;
use Orfogrammka\Orfogrammka;

class OrfogrammkaTest extends \PHPUnit_Framework_TestCase
{
    private $config = [
        'email'       => 'account@orfogrammka.ru',
        'password'    => 'secret',
        'check_pause' => 0.1
    ];

    public function testSpellingText()
    {
        $http = new HttpMock();
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('auth_success')));
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('paste_success')));
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('state_estimated_success')));
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('start_check')));
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('state_waiting_check')));
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('state_checking')));
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('state_checked_success')));
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('annotated_success')));

        $apiClient   = new ApiClient($http);
        $orfogrammka = new Orfogrammka($apiClient, $this->config);

        $text = 'Hello world!';
        $html = '<p>Hello world!</p>';

        $result = $orfogrammka->checkText($html, $text);

        $this->assertEquals([
            "allowAnnotationChars" => 1515,
            "annotations"          => [
                "annotations" => [],
                "kinds"       => [],
                "kindsOrder"  => [],
                "otherErrors" => [],
                "water"       => [
                    "content" => 0
                ]
            ],
            "html" => "<p>Hello world!</p>"
        ], $result);
    }

    public function testApiClientDiscoverDefaultHttpClient()
    {
        $client = new ApiClient();

        $reflection = new \ReflectionObject($client);
        $httpClient = $reflection->getProperty('http');
        $httpClient->setAccessible(true);

        $this->assertInstanceOf(HttpClient::class, $httpClient->getValue($client));
    }

    /**
     * @expectedException \Orfogrammka\Exception\FailedResponse
     * @expectedExceptionMessage Неверно указан адрес электронной почты или пароль. Пожалуйста, введите их правильно.
     */
    public function testFailedAuthorization()
    {
        $http = new HttpMock();
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('auth_failure')));

        $apiClient   = new ApiClient($http);
        $orfogrammka = new Orfogrammka($apiClient, $this->config);

        $orfogrammka->checkText('html', 'text');
    }

    /**
     * @expectedException \Orfogrammka\Exception\UnauthorizedResponse
     * @expectedExceptionMessage Authorization is failed. Check your email and password.
     */
    public function testFailedAuthentication()
    {
        $http = new HttpMock();
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('auth_success')));
        $http->addResponse(new Response(401));

        $apiClient   = new ApiClient($http);
        $orfogrammka = new Orfogrammka($apiClient, $this->config);

        $orfogrammka->checkText('html', 'text');
    }

    /**
     * @expectedException \Orfogrammka\Exception\FailedResponse
     * @expectedExceptionMessage Unknown document state 'UNKNOWN'
     */
    public function testWhenGotUnknownState()
    {
        $http = new HttpMock();
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('auth_success')));
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('paste_success')));
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('state_unknown')));

        $apiClient   = new ApiClient($http);
        $orfogrammka = new Orfogrammka($apiClient, $this->config);

        $orfogrammka->checkText('html', 'text');
    }

    /**
     * @expectedException \Orfogrammka\Exception\FailedResponse
     */
    public function testWhenFailedRequestToOrfogrammka()
    {
        $http = new HttpMock();
        $http->addResponse(new Response(402));

        $apiClient   = new ApiClient($http);
        $orfogrammka = new Orfogrammka($apiClient, $this->config);

        $orfogrammka->checkText('html', 'text');
    }

    /**
     * @expectedException \Orfogrammka\Exception\UnauthorizedResponse
     * @expectedExceptionMessage Bad account status 'BAD_GUY'
     */
    public function testWhenAccountStatusIsBadGuy()
    {
        $http = new HttpMock();
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('auth_bad_guy')));
        $apiClient   = new ApiClient($http);
        $orfogrammka = new Orfogrammka($apiClient, $this->config);

        $orfogrammka->checkText('html', 'text');
    }

    /**
     * @expectedException \Orfogrammka\Support\Serializer\Exception\Exception
     * @expectedExceptionMessage Could not decode JSON
     */
    public function testWhenCouldNotParseResponseBody()
    {
        $http = new HttpMock();
        $http->addResponse(new Response(200, [], Fixture::orfogrammka('bad_response', 'html')));
        $apiClient   = new ApiClient($http);
        $orfogrammka = new Orfogrammka($apiClient, $this->config);

        $orfogrammka->checkText('html', 'text');
    }
}