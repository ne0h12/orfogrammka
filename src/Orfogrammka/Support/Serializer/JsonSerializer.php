<?php

/**
 * @author Klyachin Andrew <sfdiem5@gmail.com>
 */

namespace Orfogrammka\Support\Serializer;

use Orfogrammka\Support\Data\DataWrapper;

class JsonSerializer implements SerializerInterface
{
    public function deserialize($data, $type)
    {
        $data = $this->decode($data);

        return new $type(new DataWrapper($data));
    }

    protected function decode($data)
    {
        $decoded = json_decode($data, true);
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $decoded;
            default:
                throw new Exception\Exception('Could not decode JSON.');
        }
    }
}