<?php
/**
 * @author Klyachin Andrew <sfdiem5@gmail.com>
 */

namespace Orfogrammka\Entity;

use Orfogrammka\Support\Data\DataWrapper;

class Response
{
    const SUCCESS = 'OK';
    const ERROR   = 'ERROR';

    /**
     * @var DataWrapper
     */
    private $data;

    /**
     * @param DataWrapper $data
     */
    public function __construct(DataWrapper $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->data->get('resp')->get('state')->toString();
    }

    /**
     * @return string
     */
    public function getAccountStatus(): string
    {
        return $this->data->get('resp')->get('kvas')
            ->get('status')->toString();
    }

    /**
     * @return string
     */
    public function getDocument(): string
    {
        return $this->data->get('resp')->get('_id')->toString();
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data->get('resp')->toArray();
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->data->get('status')->toString() == self::SUCCESS;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->data->get('message')
            ->get('context')
            ->get('message')
            ->toString();
    }
}