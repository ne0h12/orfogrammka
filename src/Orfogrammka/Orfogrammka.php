<?php
/**
 * @author Klyachin Andrew <sfdiem5@gmail.com>
 */
namespace Orfogrammka;

class Orfogrammka
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @var array
     */
    private $config;

    /**
     * @param ApiClient $apiClient
     */
    public function __construct(ApiClient $apiClient, array $config)
    {
        $this->apiClient = $apiClient;
        $this->config    = $config;
    }

    /**
     * @param string $html
     * @param string $text
     *
     * @return array
     */
    public function checkText(string $html, string $text): array
    {
        $this->authenticate();

        $document = $this->apiClient->initCheckDoc($html, $text);

        return $this->doCheckText($document);
    }

    protected function authenticate(): void
    {
        $config = $this->config;
        $accountStatus = $this->apiClient->login($config['email'], $config['password']);

        switch ($accountStatus) {
            case Entity\AccountStatus::TRIAL:
            case Entity\AccountStatus::GOOD_GUY:
                break;
            case Entity\AccountStatus::BAD_GUY:
            default:
                throw new Exception\UnauthorizedResponse(
                    "Bad account status '{$accountStatus}'"
                );
                break;
        }
    }

    /**
     * @param string $document
     *
     * @return array
     * @throws Exception\FailedResponse
     */
    protected function doCheckText(string $document): array
    {
        $state = $this->apiClient->checkDocState($document);

        switch ($state) {
            case Entity\State::ESTIMATED_SUCCESS:
                $this->apiClient->startCheck($document);
                break;
            case Entity\State::WAITING_CHECK:
            case Entity\State::CHECKING:
                break;
            case Entity\State::CHECKED_SUCCESS:
                return $this->apiClient->getAnnotatedResult($document);
                break;
            default:
                throw new Exception\FailedResponse(
                    "Unknown document state '{$state}'", 400
                );
                break;
        }

        sleep($this->config['check_pause'] ?? 1.5);

        return $this->doCheckText($document);
    }
}