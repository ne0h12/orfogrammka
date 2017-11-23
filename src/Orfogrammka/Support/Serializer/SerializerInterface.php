<?php

/**
 * @author Klyachin Andrew <sfdiem5@gmail.com>
 */

namespace Orfogrammka\Support\Serializer;

interface SerializerInterface
{
    public function deserialize($data, $type);
}