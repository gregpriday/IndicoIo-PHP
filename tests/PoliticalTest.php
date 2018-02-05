<?php

namespace IndicoIo\Test;
use \IndicoIo\IndicoIo as IndicoIo;
use Configure\Configure as Configure;


class PoliticalsV2Test extends \PHPUnit_Framework_TestCase
{
    private function skipIfMissingCredentials()
    {
        if (!IndicoIo::$config['api_key']) {
            $this->markTestSkipped('No auth credentials provided, skipping batch tests...');
        }
    }

    public function testPoliticalV2WhenGivenTheRightParameters()
    {
        self::skipIfMissingCredentials();
        $keys_expected = array('Libertarian', 'Liberal', 'Green', 'Conservative');
        $data = IndicoIo::political('save the whales', array("version" => 2));
        $keys_result = array_keys($data);

        sort($keys_expected);
        sort($keys_result);

        $this->assertEquals($keys_expected, $keys_result);
    }

    public function testBatchPoliticalV2()
    {
        self::skipIfMissingCredentials();
        $keys_expected = array('Libertarian', 'Liberal', 'Green', 'Conservative');
        $examples = array('save the whales', 'cut taxes');
        $data = IndicoIo::political($examples, array("version" => 2));

        $this->assertEquals(count($data), count($examples));

        $datapoint = $data[0];
        $keys_result = array_keys($datapoint);

        sort($keys_expected);
        sort($keys_result);

        $this->assertEquals($keys_expected, $keys_result);
    }
}
