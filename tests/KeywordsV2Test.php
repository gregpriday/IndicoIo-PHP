<?php

namespace IndicoIo\Test;
use \IndicoIo\IndicoIo as IndicoIo;
use Configure\Configure as Configure;


class KeywordsV2Test extends \PHPUnit_Framework_TestCase
{
    private function skipIfMissingCredentials()
    {
        if (!IndicoIo::$config['api_key']) {
            $this->markTestSkipped('No auth credentials provided, skipping batch tests...');
        }
    }

    public function testSingleKeywordsV2()
    {
        self::skipIfMissingCredentials();
        $text = "A working API is critical to the success of our company";
        $results = IndicoIo::keywords($text, array("version" => 2));
        $keywords = array_keys($results);
        foreach ($keywords as $keyword) {
            $this->assertTrue(strpos($text, $keyword));
        }
    }
    public function testBatchKeywordsV2()
    {
        self::skipIfMissingCredentials();
        $text = "A working API is critical to the success of our company";
        $results = IndicoIo::keywords(array($text, $text), array("version" => 2));
        $this->assertEquals(count($results), 2);
        $keywords = array_keys($results[0]);

        foreach ($keywords as $keyword) {
            $this->assertTrue(strpos($text, $keyword));
        }
    }

}
