<?php

namespace IndicoIo\Test;

use IndicoIo\IndicoIo as IndicoIo;
use PHPUnit\Framework\TestCase;

class PDFExtractionTest extends TestCase
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

    public function testPDFExtractionFromUrl()
    {
        $pdf_url = "https://papers.nips.cc/paper/4824-imagenet-classification-with-deep-convolutional-neural-networks.pdf";
        $results = IndicoIo::pdf_extraction($pdf_url);
        $expected_keys = array('metadata', 'text');

        foreach ($expected_keys as $expected_key) {
            $this->assertTrue(array_key_exists($expected_key, $results));
        }

    }

    public function testLocalPDFFile() {
        $pdf_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . '/test.pdf';
        $results = IndicoIo::pdf_extraction($pdf_path);
        $expected_keys = array('metadata', 'text');

        foreach ($expected_keys as $expected_key) {
            $this->assertTrue(array_key_exists($expected_key, $results));
        }
    }
}
