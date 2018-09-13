<?php

namespace IndicoIo\Test;

use IndicoIo\IndicoIo as IndicoIo;
use IndicoIO\Configure;
use Utils\Image as Image;
use \Eventviva\ImageResize;

use PHPUnit\Framework\TestCase;

\PHPUnit\Framework\Error\Warning::$enabled = FALSE;
# notice, strict:
\PHPUnit\Framework\Error\Notice::$enabled = FALSE;

class IndicoIoTest extends TestCase
{
    private function skipIfMissingCredentials()
    {
        if (!IndicoIo::$config['api_key']) {
            $this->markTestSkipped('No auth credentials provided, skipping tests...');
        }
    }

    private function skipIfMissingEnvironmentVars()
    {
        if (!getenv("INDICO_API_KEY")) {
            $this->markTestSkipped('No auth credentials provided, skipping tests...');
        }
    }

    public function testEmotionWhenGivenTheRightParameters()
    {
        self::skipIfMissingCredentials();
        $keys_expected = array('anger', 'joy', 'fear', 'sadness', 'surprise');
        $data = IndicoIo::emotion('save the whales');
        $keys_result = array_keys($data);

        sort($keys_expected);
        sort($keys_result);

        $this->assertEquals($keys_expected, $keys_result);
    }


    /**
     * @expectedException Exception
     */
    public function testTextRaisesExceptionWhenGivenInteger()
    {
        self::skipIfMissingCredentials();
        $data_integer_request = IndicoIo::personality(2);
    }

    /**
     * @expectedException Exception
     */
    public function testTextRaisesExceptionWhenGivenBool()
    {
        self::skipIfMissingCredentials();
        $data_bool_request = IndicoIo::personality(true);
    }

    public function testSentimentWhenGivenTheRightParameters()
    {
        self::skipIfMissingCredentials();
        $data = IndicoIo::sentiment('worst day ever');

        $this->assertInternalType('float', $data);
    }

    public function testSentimentReturnValueBetweenOneAndZero()
    {
        self::skipIfMissingCredentials();
        $data = IndicoIo::sentiment('Excited to be alive!');
        $this->assertGreaterThan(0, $data);
        $this->assertGreaterThan($data, 1);
    }

    public function testSentimentHQReturnValueBetweenOneAndZero()
    {
        self::skipIfMissingCredentials();
        $data = IndicoIo::sentiment_hq('Excited to be alive!');
        $this->assertGreaterThan(0, $data);
        $this->assertGreaterThan($data, 1);
    }

    public function testLanguageWhenGivenTheRightPrameters()
    {
        self::skipIfMissingCredentials();
        $data = IndicoIo::language('Clearly an english sentence.!');
        $keys_result = array_keys($data);
        $this->assertEquals(count($keys_result), 33);
    }

    public function testTextTags()
    {
        self::skipIfMissingCredentials();
        $data = IndicoIo::text_tags('I want to move to New York City!');
        $keys_result = array_keys($data);
        $this->assertEquals(count($keys_result), 111);
    }

    public function testTopN()
    {
        $data = IndicoIo::text_tags('I want to move to New York City!', array("top_n"=>10));
        $keys_result = array_keys($data);
        $this->assertEquals(count($keys_result), 10);
    }

    public function testThreshold()
    {
        $data = IndicoIo::text_tags('I want to move to New York City!', array("threshold"=>0.05));
        $values = array_values($data);
        for ($i = 0; $i < count($values); $i++) {
            $this->assertGreaterThan(0.05, $values[$i]);
        }
    }

    public function testPersonality()
    {
        self::skipIfMissingCredentials();
        $data = IndicoIo::personality('I want to move to New York City!');
        $keys_result = array_keys($data);
        $this->assertEquals(count($keys_result), 4);
        $this->assertTrue(in_array('extraversion', $keys_result));
    }

    public function testPersonas()
    {
        self::skipIfMissingCredentials();
        $data = IndicoIo::personas('I want to move to New York City!');
        $keys_result = array_keys($data);
        $this->assertEquals(count($keys_result), 16);
        $this->assertTrue(in_array('commander', $keys_result));
    }

    public function testTwitterEngagement()
    {
        self::skipIfMissingCredentials();
        $examples = 'I want to move to New York City!';

        $data = IndicoIo::twitter_engagement($examples);
        $this->assertGreaterThan(0, $data);
        $this->assertGreaterThan($data, 1);
    }

    public function testRelevance()
    {
        self::skipIfMissingCredentials();
        $text = "president";
        $queries = "president";
        $result = IndicoIo::relevance($text, $queries);
        $this->assertGreaterThan(0.5, $result);
    }

    public function testBatchRelevance()
    {
        self::skipIfMissingCredentials();
        $text = ["president", "Barack Obama"];
        $queries = ["president", "prime minister"];
        $result = IndicoIo::relevance($text, $queries);
        $this->assertGreaterThan(0.5, $result[0][0]);
        $this->assertEquals(count($result), 2);
        $this->assertEquals(count($result[0]), 2);
    }

    public function testTextFeatures()
    {
        self::skipIfMissingCredentials();
        $text = "Queen of England";
        $result = IndicoIo::text_features($text);
        $this->assertEquals(count($result), 300);
    }

    public function testBatchTextFeatures()
    {
        self::skipIfMissingCredentials();
        $text = ["Queen of England", "Prime Minister of Canada"];
        $result = IndicoIo::text_features($text);
        $this->assertEquals(count($result), 2);
        $this->assertEquals(count($result[0]), 300);
        $this->assertEquals(count($result[1]), 300);
    }

    public function testFerWhenGivenTheRightParameters()
    {
        self::skipIfMissingCredentials();
        $keys_expected = array('Angry', 'Sad', 'Neutral', 'Surprise', 'Fear', 'Happy');
        $image = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');
        $data = IndicoIo::fer($image, array("version"=>2));
        $keys_result = array_keys($data);

        sort($keys_result);
        sort($keys_expected);

        $this->assertEquals($keys_expected, $keys_result);
    }

    public function testURLSupport()
    {
        self::skipIfMissingCredentials();
        $keys_expected = array('Angry', 'Sad', 'Neutral', 'Surprise', 'Fear', 'Happy');
        $image = "https://s3-us-west-2.amazonaws.com/indico-test-data/face.jpg";
        $data = IndicoIo::fer($image, array("version"=>2));
        $keys_result = array_keys($data);

        sort($keys_result);
        sort($keys_expected);

        $this->assertEquals($keys_expected, $keys_result);
    }

    public function testFacialLocalizationWhenGivenTheRightParameters()
    {
        self::skipIfMissingCredentials();
        $image = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');
        $data = IndicoIo::facial_localization($image);

        $this->assertInternalType('array', $data);
        $this->assertInternalType('array', $data[0]);
        $this->assertInternalType('array', $data[0]["top_left_corner"]);
    }

    public function testContentFilterWhenGivenTheRightParameters()
    {
        self::skipIfMissingCredentials();
        $image = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');
        $data = IndicoIo::content_filter($image);

        $this->assertGreaterThan(-0.0000001, $data);
        $this->assertLessThan(1.0000001, $data);
        $this->assertEquals(gettype($data), "double");

    }

    public function testFacialFeaturesWhenGivenTheRightParameters()
    {
        self::skipIfMissingCredentials();
        $image = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');
        $data = IndicoIo::facial_features($image);

        $this->assertEquals(count($data), 48);
    }

    public function testImageFeaturesWhenGivenTheRightParameters()
    {
        self::skipIfMissingCredentials();
        $image = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');
        $data = IndicoIo::image_features($image);

        $this->assertEquals(count($data), 4096);
    }

    public function testExplicitAuthArgument()
    {
        self::skipIfMissingEnvironmentVars();
        $examples = array('worst day ever', 'best day ever');
        $api_key = getenv("INDICO_API_KEY");
        $data = IndicoIo::sentiment($examples, array("api_key"=>$api_key));

        $this->assertEquals(count($data), count($examples));
        $this->assertInternalType('array', $data);
        $this->assertInternalType('float', $data[0]);
    }

    public function testBatchSentiment()
    {
        self::skipIfMissingCredentials();
        $examples = array('worst day ever', 'best day ever');
        $data = IndicoIo::sentiment_hq($examples);

        $this->assertEquals(count($data), count($examples));
        $this->assertInternalType('array', $data);
        $this->assertInternalType('float', $data[0]);
    }

    public function testBatchSentimentHQ()
    {
        self::skipIfMissingCredentials();
        $examples = array('worst day ever', 'best day ever');
        $data = IndicoIo::sentiment($examples);

        $this->assertEquals(count($data), count($examples));
        $this->assertInternalType('array', $data);
        $this->assertInternalType('float', $data[0]);
    }

    public function testBatchLanguage()
    {
        self::skipIfMissingCredentials();
        $examples = array('Clearly an english sentence.', 'Hablas espanol?');
        $data = IndicoIo::language($examples);
        $this->assertEquals(count($data), count($examples));

        $datapoint = $data[0];
        $keys_result = array_keys($datapoint);
        $this->assertEquals(count($keys_result), 33);
    }

    public function testBatchTextTags()
    {
        self::skipIfMissingCredentials();
        $examples = array(
            'On Monday, the president will be ...',
            'We are in for a windy Thursday and a rainy Friday'
        );
        $data = IndicoIo::text_tags($examples);
        $this->assertEquals(count($data), count($examples));

        $datapoint = $data[0];
        $keys_result = array_keys($datapoint);
        $this->assertEquals(count($keys_result), 111);
    }

    public function testBatchPersonality()
    {
        self::skipIfMissingCredentials();
        $examples = array(
            'On Monday, the president will be ...',
            'We are in for a windy Thursday and a rainy Friday'
        );
        $data = IndicoIo::personality($examples);
        $this->assertEquals(count($data), count($examples));


        $keys_result = array_keys($data[0]);
        $this->assertEquals(count($keys_result), 4);
        $this->assertTrue(in_array('extraversion', $keys_result));
    }

    public function testBatchPersonas()
    {
        self::skipIfMissingCredentials();
        $examples = array(
            'On Monday, the president will be ...',
            'We are in for a windy Thursday and a rainy Friday'
        );
        $data = IndicoIo::personas($examples);
        $this->assertEquals(count($data), count($examples));


        $keys_result = array_keys($data[0]);
        $this->assertEquals(count($keys_result), 16);
        $this->assertTrue(in_array('commander', $keys_result));
    }


    public function testBatchTwitterEngagement()
    {
        self::skipIfMissingCredentials();
        $examples = array(
            'I want to move to New York City!',
            'Do you prefer Gandalf the Grey or Gandalf the White?'
        );

        $data = IndicoIo::twitter_engagement($examples);
        $this->assertEquals(count($data), count($examples));

        $this->assertGreaterThan(0, $data[0]);
        $this->assertGreaterThan($data[0], 1);
    }

    public function testBatchFer()
    {
        self::skipIfMissingCredentials();
        $keys_expected = array('Angry', 'Sad', 'Neutral', 'Surprise', 'Fear', 'Happy');
        $image = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');
        $examples = array($image, $image);

        $data = IndicoIo::fer($examples, array("version"=>2));
        $this->assertEquals(count($data), count($examples));

        $datapoint = $data[0];
        $keys_result = array_keys($datapoint);

        sort($keys_result);
        sort($keys_expected);

        $this->assertEquals($keys_expected, $keys_result);
    }

    public function testBatchContentFilter()
    {
        self::skipIfMissingCredentials();
        $image = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');
        $examples = array($image, $image);

        $data = IndicoIo::content_filter($examples);
        $this->assertEquals(count($data), count($examples));

        $datapoint = $data[0];

        $this->assertEquals(gettype($data), "array");
        $this->assertEquals(gettype($datapoint), "double");
        $this->assertGreaterThan(-0.0000001, $datapoint);
        $this->assertLessThan(1.0000001, $datapoint);
    }

    public function testBatchFacialFeatures()
    {
        self::skipIfMissingCredentials();
        $image = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');
        $examples = array($image, $image);
        $data = IndicoIo::facial_features($examples);
        $this->assertEquals(count($data), count($examples));

        $datapoint = $data[0];
        $this->assertEquals(count($datapoint), 48);
    }

    public function testBatchImageFeatures()
    {
        self::skipIfMissingCredentials();
        $image = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');
        $examples = array($image, $image);
        $data = IndicoIo::image_features($examples);
        $this->assertEquals(count($data), count($examples));

        $datapoint = $data[0];
        $this->assertEquals(count($datapoint), 4096);
    }

    public function testBatchFacialLocalization()
    {
        self::skipIfMissingCredentials();
        $image = array(file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json'));
        $data = IndicoIo::facial_localization($image);

        $this->assertInternalType('array', $data);
        $this->assertInternalType('array', $data[0]);
        $this->assertInternalType('array', $data[0][0]["top_left_corner"]);
    }

    public function testPredictText()
    {
        self::skipIfMissingCredentials();
        $data = IndicoIo::analyze_text('Excited to be alive!', array("apis"=>array("sentiment", "political")));
        $this->assertGreaterThan(0, $data["sentiment"]);
        $this->assertGreaterThan($data["sentiment"], 1);
    }

    public function testBatchPredictText()
    {
        self::skipIfMissingCredentials();
        $data = IndicoIo::analyze_text(array("Excited to be alive!", "sadness"), array("apis"=>array("sentiment", "political")));
        $this->assertGreaterThan(0, $data["sentiment"][0]);
        $this->assertGreaterThan($data["sentiment"][0], 1);
        $this->assertGreaterThan(.5, $data["sentiment"][1]);
    }


    public function testPredictImage()
    {
        self::skipIfMissingCredentials();
        $image = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');
        $data = IndicoIo::analyze_image($image, array("apis"=>array("image_features")));

        $this->assertEquals(count($data["image_features"]), 4096);
    }

    public function testBatchPredictImage()
    {
        self::skipIfMissingCredentials();
        $image = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');
        $examples = array($image, $image);
        $data = IndicoIo::analyze_image($examples, array("apis"=> array("image_features")));

        $this->assertEquals(count($data["image_features"]), count($examples));
        $datapoint = $data["image_features"][0];
        $this->assertEquals(count($datapoint), 4096);
    }

    public function testConfigureFromEnvironmentVariables()
    {
        # store previous settings to reset later
        $prev_api_key = getenv("INDICO_API_KEY");
        $api_key = "env-api-key";
        putenv("INDICO_API_KEY=$api_key");

        $config = Configure::loadConfiguration();
        $this->assertEquals($config['api_key'], $api_key);

        # reset to previous configuration
        putenv("INDICO_API_KEY=$prev_api_key");
    }

    public function testImageMinResizeFunctionality() {
        $imageb64 = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'/data_test.json');
        $pre_image = ImageResize::createFromString(base64_decode($imageb64));
        $pre_width = $pre_image->getSourceWidth();
        $pre_height = $pre_image->getSourceHeight();

        $image = Image::processImage($imageb64, 128, true);
        $image = ImageResize::createFromString(base64_decode($image));
        $width = $image->getSourceWidth();
        $height = $image->getSourceHeight();

        $this->assertEquals($pre_width/$pre_height, $width/$height);
        if ($pre_width > $pre_height) {
            $this->assertEquals($width, 128);
        } else {
            $this->assertEquals($height, 128);
        }
    }

    public function testConfigureFromConfigFile()
    {
        $filename = tempnam("./", 'tmp');
        $handle = fopen($filename, "w");
        $api_key = "file-api-key";
        fwrite($handle, "[auth]\napi_key=$api_key");
        fclose($handle);

        $config = IndicoIo::$config;
        $config = Configure::loadConfigFile($filename, $config);
        $this->assertEquals($config['api_key'], $api_key);

        # cleanup
        unlink($filename);
    }

    public function testEnvironmentVariablesTakePrecedence()
    {
        # store previous settings to reset later
        $prev_api_key = getenv("INDICO_API_KEY");

        $env_api_key = "env-api-key";
        putenv("INDICO_API_KEY=$env_api_key");

        $filename = tempnam("./", 'tmp');
        $handle = fopen($filename, "w");
        $api_key = "file-api-key";
        fwrite($handle, "[auth]\napi_key=$api_key");
        fclose($handle);

        $config = IndicoIo::$config;
        $config = Configure::loadConfigFile($filename, $config);
        $config = Configure::loadEnvironmentVars($config);
        $this->assertEquals($config['api_key'], $env_api_key);

        # reset to previous configuration
        putenv("INDICO_API_KEY=$prev_api_key");
        unlink($filename);
    }
}
