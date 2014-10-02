<?php 

namespace IndicoIo\Test;

class IndicoIoTest extends \PHPUnit_Framework_TestCase
{

    public function testPoliticalWhenGivenTheRightParameters()
    {
        $keys_exptected = array('Libertarian', 'Liberal', 'Green', 'Conservative');
        $data = \IndicoIo\IndicoIo::political('Obama is the USA president !!');
        $keys_result = array_keys($data);
        
        sort($keys_exptected);
        sort($keys_result);

        $this->assertEquals($keys_exptected, $keys_result);
    }

    public function testPoliticalReturnFullErrorMsgWhenGivenIntegerORBool()
    {
        $data_integer_request = \IndicoIo\IndicoIo::political(2);
        $data_bool_request    = \IndicoIo\IndicoIo::political(true);
        
        $this->assertArrayHasKey('Error', $data_integer_request);
        $this->assertGreaterThan(0, strlen($data_integer_request['Error']));

        $this->assertArrayHasKey('Error', $data_bool_request);
        $this->assertGreaterThan(0, strlen($data_bool_request['Error']));
    }

}