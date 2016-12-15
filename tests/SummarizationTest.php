<?php

namespace IndicoIo\Test;
use \IndicoIo\IndicoIo as IndicoIo;
use Configure\Configure as Configure;


class SummarizationTest extends \PHPUnit_Framework_TestCase
{
    private function skipIfMissingCredentials()
    {
        if (!IndicoIo::$config['api_key']) {
            $this->markTestSkipped('No auth credentials provided, skipping batch tests...');
        }
    }

    public function testSummarization()
    {
        $num_sentences = 3;
        self::skipIfMissingCredentials();
        $text = "A sentence. A second sentence. One more for good measure. This should be sufficient.";
        $results = IndicoIo::summarization($text, array('top_n' => $num_sentences));
        $this->assertEquals(count($results), $num_sentences);
    }
}
