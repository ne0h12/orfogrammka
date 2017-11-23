<?php

/**
 * @author Klyachin Andrew <sfdiem5@gmail.com>
 */

namespace Orfogrammka\Tests\Unit;

use Orfogrammka\Support\Data\DataWrapper;

class DataWrapperTest extends \PHPUnit_Framework_TestCase
{
    public function testTypicallyUsage()
    {
        $data = new DataWrapper([
            'data' => [
                'sentiment' => 'Happy New Year!',
                'year'      => 2018,
                'cost'      => 100.00,
                'is_cool'   => true
            ]
        ]);

        $this->assertArrayHasKey('data', $data->toArray());
        $this->assertTrue($data->has('data'));
        $this->assertEquals('Happy New Year!', $data->get('data')->get('sentiment')->toString());
        $this->assertEquals(2018, $data->get('data')->get('year')->toInteger());
        $this->assertEquals(100.00, $data->get('data')->get('cost')->toFloat());
        $this->assertTrue($data->get('data')->get('is_cool')->toBoolean());
    }

    public function testInjectDefaultValue()
    {
        $data = new DataWrapper([
            'money' => '100$'
        ]);

        $this->assertEquals(200, $data->get('dollars', 200)->toInteger());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The internal data is not array. Key: bitcoin
     */
    public function testWhenInternalDataIsNotArray()
    {
        $data = new DataWrapper([
            'money' => '100$'
        ]);

        $data->get('money')->get('bitcoin');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWhenInputDataIsNotScalar()
    {
        $data = new DataWrapper(new \stdClass());
    }
}
