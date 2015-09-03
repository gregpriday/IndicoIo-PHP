<?php

namespace IndicoIo\Test;
use \IndicoIo\IndicoIo as IndicoIo;
use Configure\Configure as Configure;


class IndicoIoVersionTest extends \PHPUnit_Framework_TestCase
{
    private function skipIfMissingCredentials()
    {
        if (!IndicoIo::$config['api_key']) {
            $this->markTestSkipped('No auth credentials provided, skipping batch tests...');
        }
    }

    public function testSpecifiedVersion()
    {
        self::skipIfMissingCredentials();
        $data = IndicoIo::sentiment('Excited to be alive!', array("version" => 1));
        $this->assertGreaterThan(0, $data);
        $this->assertGreaterThan($data, 1);
    }
    public function testImageFeaturesV2()
    {
        self::skipIfMissingCredentials();
        $image = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');
        $data = IndicoIo::image_features($image, array("version" => 2));
        $this->assertEquals(count($data), 4096);
    }

}
