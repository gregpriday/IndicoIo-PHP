<?php 

namespace IndicoIo\Test;

class IndicoIoTest extends \PHPUnit_Framework_TestCase
{

    public function testPoliticalWhenGivenTheRightParameters()
    {
        $keys_expected = array('Libertarian', 'Liberal', 'Green', 'Conservative');
        $data = \IndicoIo\IndicoIo::political('save the whales');
        $keys_result = array_keys($data);
        
        sort($keys_expected);
        sort($keys_result);

        $this->assertEquals($keys_expected, $keys_result);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Input must be text.
     */
    public function testPoliticalRaisesExceptionWhenGivenInteger()
    {
        $data_integer_request = \IndicoIo\IndicoIo::political(2);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Input must be text.
     */
    public function testPoliticalRaisesExceptionWhenGivenBool()
    {
        $data_bool_request = \IndicoIo\IndicoIo::political(true);
    }

    public function testSentimentWhenGivenTheRightParameters()
    {
        $data = \IndicoIo\IndicoIo::sentiment('whales suck');

        $this->assertInternalType('float', $data);
    }


    public function testSentimentReturnValueBetweenOneAndZero()
    {
        $data = \IndicoIo\IndicoIo::sentiment('Obama is the USA president !!');
        //$this->assertArrayHasKey('Sentiment', $data);
        // The returned must be between 0 and 1.
        $this->assertGreaterThan(0, $data);
        $this->assertGreaterThan($data, 1);
    }

    public function testLanguageWhenGivenTheRightPrameters()
    {
        $expected_languages = array(
            'English',
            'Spanish',
            'Tagalog',
            'Esperanto',
            'French',
            'Chinese',
            'French',
            'Bulgarian',
            'Latin',
            'Slovak',
            'Hebrew',
            'Russian',
            'German',
            'Japanese',
            'Korean',
            'Portuguese',
            'Italian',
            'Polish',
            'Turkish',
            'Dutch',
            'Arabic',
            'Persian (Farsi)',
            'Czech',
            'Swedish',
            'Indonesian',
            'Vietnamese',
            'Romanian',
            'Greek',
            'Danish',
            'Hungarian',
            'Thai',
            'Finnish',
            'Norwegian',
            'Lithuanian'
        );

        $data = \IndicoIo\IndicoIo::language('bonsoir les jeunes !');
        $keys_result = array_keys($data);
        $this->assertEquals(count($keys_result), 33);
    }

    public function testTextTags()
    {
        $data = \IndicoIo\IndicoIo::text_tags('On Monday, president Barack Obama will be ...');
        $keys_result = array_keys($data);
        $this->assertEquals(count($keys_result), 111);
    }
    
    public function testFerWhenGivenTheRightParameters()
    {
        $humour_expected = array('Angry', 'Sad', 'Neutral', 'Surprise', 'Fear', 'Happy');
        $file_content =  file_get_contents(dirname(__FILE__) .DIRECTORY_SEPARATOR.'/data_test.json');
        $image = json_decode($file_content, true);
        $data = \IndicoIo\IndicoIo::fer($image);
        $keys_result = array_keys($data);

        sort($keys_result);
        sort($humour_expected);

        $this->assertEquals($humour_expected, $keys_result);
    }

    public function testFacialFeaturesWhenGivenTheRightParameters()
    {
        $file_content =  file_get_contents(dirname(__FILE__) .DIRECTORY_SEPARATOR.'/data_test.json');
        $image = json_decode($file_content, true);
        $data = \IndicoIo\IndicoIo::facial_features($image);

        $this->assertEquals(count($data), 48);
    }

    public function testImageFeaturesWhenGivenTheRightParameters()
    {
        $file_content =  file_get_contents(dirname(__FILE__) .DIRECTORY_SEPARATOR.'/data_test.json');
        $image = json_decode($file_content, true);
        $data = \IndicoIo\IndicoIo::image_features($image);

        $this->assertEquals(count($data), 2048);
    }
}
