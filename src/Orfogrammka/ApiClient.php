<?php
/**
 * @author Klyachin Andrew <sfdiem5@gmail.com>
 */

namespace Orfogrammka;

use GuzzleHttp\Psr7\Request;
use Http\Client\Common\Plugin\CookiePlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Message\CookieJar;
use Orfogrammka\Support\Serializer\JsonSerializer;
use Orfogrammka\Support\Serializer\SerializerInterface;
use Orfogrammka\Support\Http as HttpSupport;

class ApiClient
{
    /**
     * @var string
     */
    private $rootUrl = 'https://orfogrammka.ru';

    /**
     * @var HttpClient
     */
    private $http;

    public function __construct(HttpClient $http = null, string $rootUrl = null,
                                SerializerInterface $serializer = null)
    {
        $this->http       = $http ?: $this->createDefaultHttpClient();
        $this->rootUrl    = $rootUrl ?: $this->rootUrl;
        $this->serializer = $serializer ?: new JsonSerializer();
    }

    public function login(string $email, string $password): string
    {
        $url  = $this->rootUrl . '/кабинет/ajax/auth.jsp';
        $body = [
            'action'   => 'LOGIN',
            'email'    => $email,
            'password' => $password
        ];

        return $this->sendRequest($url, $body)->getAccountStatus();
    }

    public function initCheckDoc(string $html, string $text): string
    {
        $url  = $this->rootUrl . '/кабинет/ajax/documents.jsp';
        $body = [
            'action'  => 'PASTE',
            'title'   => '',
            'html'    => $html,
            'text'    => $text,
            'profile' => 'COMMON'
        ];

        return $this->sendRequest($url, $body)->getDocument();
    }

    public function checkDocState(string $document): string
    {
        $url  = $this->rootUrl . '/кабинет/ajax/documents.jsp';
        $body = [
            'action'   => 'CHECK_DOC_STATE',
            'document' => $document
        ];

        return $this->sendRequest($url, $body)->getState();
    }

    public function startCheck(string $document): void
    {
        $url  = $this->rootUrl . '/кабинет/ajax/documents.jsp';
        $body = [
            'action'   => 'START_CHECK',
            'document' => $document,
            'profile'  => 'COMMON'
        ];

        $this->sendRequest($url, $body);
    }

    public function getAnnotatedResult(string $document): array
    {
        $url  = $this->rootUrl . '/кабинет/ajax/documents.jsp';
        $body = [
            'action'   => 'ANNOTATED',
            'document' => $document
        ];

        return $this->sendRequest($url, $body)->getData();
    }

    /**
     * @param string $url
     * @param array  $body
     *
     * @return Entity\Response
     */
    private function sendRequest(string $url, array $body): Entity\Response
    {
        $request  = $this->createRequest($url, $body);
        $response = $this->http->sendRequest($request);

        if (HttpSupport\Response::isSuccessful($response)) {
            /**@var Entity\Response $parsedBody */
            $parsedBody = $this->serializer->deserialize(
                $response->getBody()->getContents(), Entity\Response::class
            );

            if ($parsedBody->isSuccess()) {
                return $parsedBody;
            } else {
                throw new Exception\FailedResponse(
                    $parsedBody->getError(), 400
                );
            }

        } elseif (HttpSupport\Response::isUnauthorized($response)) {
            throw new Exception\UnauthorizedResponse();
        } else {
            throw new Exception\FailedResponse(
                $response->getBody()->getContents(),
                $response->getStatusCode()
            );
        }
    }

    /**
     * @param string $url
     * @param array  $body
     *
     * @return Request
     */
    private function createRequest(string $url, array $body): Request
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
        ];

        return new Request('POST', $url, $headers, http_build_query($body));
    }

    /**
     * @return HttpClient
     */
    private function createDefaultHttpClient(): HttpClient
    {
        $client = HttpClientDiscovery::find();
        $cookie = new CookiePlugin(new CookieJar());

        return new PluginClient($client, [$cookie]);
    }
}
