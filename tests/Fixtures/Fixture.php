<?php
/**
 * @author Klyachin Andrey <andrey.klyachin@kiwitaxi.com>
 */

namespace Orfogrammka\Tests\Fixtures;

class Fixture
{
    public static function orfogrammka($name, $format = 'json')
    {
        return file_get_contents(
            __DIR__ . "/Orfogrammka/{$name}.{$format}"
        );
    }
}