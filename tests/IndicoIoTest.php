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

}