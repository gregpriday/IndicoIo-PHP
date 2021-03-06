<?php

namespace IndicoIo\Test;
use \IndicoIo\IndicoIo as IndicoIo;
use PHPUnit\Framework\TestCase;


class ImageRecognitionTest extends TestCase
{
    protected function setUp()
    {
        self::skipIfMissingCredentials();
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    private function skipIfMissingCredentials()
    {
        if (!IndicoIo::$config['api_key']) {
            $this->markTestSkipped('No auth credentials provided, skipping batch tests...');
        }
    }

    public function testSingleImageRecognition()
    {
        $image = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');

        $data = IndicoIo::image_recognition($image, array("top_n" => 3));
        $keys_result = array_keys($data);

        $this->assertEquals(count($keys_result), 3);
    }
    public function testBatchImageRecognition()
    {
        $image = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');

        $data = IndicoIo::image_recognition(array($image, $image), array("top_n" => 3));
        $this->assertEquals(count($data), 2);
        $this->assertEquals(count($data[0]), 3);
    }

}
