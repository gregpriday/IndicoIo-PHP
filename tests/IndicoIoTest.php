<?php 

namespace IndicoIo\Test;
use \IndicoIo\IndicoIo as IndicoIo;
use Configure\Configure as Configure;

class IndicoIoTest extends \PHPUnit_Framework_TestCase
{
    private function skipIfMissingCredentials() 
    {
        if (!IndicoIo::$_options['auth']) {
            $this->markTestSkipped('No auth credentials provided, skipping batch tests...');
        }
    }

    private function skipIfMissingEnvironmentVars()
    {
        if (!getenv("INDICO_USERNAME") || ! getenv("INDICO_PASSWORD")) {
            $this->markTestSkipped('No auth credentials provided, skipping batch tests...');
        }
    }

    public function testPoliticalWhenGivenTheRightParameters()
    {
        $keys_expected = array('Libertarian', 'Liberal', 'Green', 'Conservative');
        $data = IndicoIo::political('save the whales');
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
        $data_integer_request = IndicoIo::political(2);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Input must be text.
     */
    public function testPoliticalRaisesExceptionWhenGivenBool()
    {
        $data_bool_request = IndicoIo::political(true);
    }

    public function testSentimentWhenGivenTheRightParameters()
    {
        $data = IndicoIo::sentiment('worst day ever');

        $this->assertInternalType('float', $data);
    }

    public function testSentimentReturnValueBetweenOneAndZero()
    {
        $data = IndicoIo::sentiment('Excited to be alive!');
        $this->assertGreaterThan(0, $data);
        $this->assertGreaterThan($data, 1);
    }

    public function testLanguageWhenGivenTheRightPrameters()
    {
        $data = IndicoIo::language('Clearly an english sentence.!');
        $keys_result = array_keys($data);
        $this->assertEquals(count($keys_result), 33);
    }

    public function testTextTags()
    {
        $data = IndicoIo::text_tags('On Monday, the president will be ...');
        $keys_result = array_keys($data);
        $this->assertEquals(count($keys_result), 111);
    }

    public function testFerWhenGivenTheRightParameters()
    {
        $humour_expected = array('Angry', 'Sad', 'Neutral', 'Surprise', 'Fear', 'Happy');
        $file_content =  file_get_contents(dirname(__FILE__) .DIRECTORY_SEPARATOR.'/data_test.json');
        $image = json_decode($file_content, true);
        $data = IndicoIo::fer($image);
        $keys_result = array_keys($data);

        sort($keys_result);
        sort($humour_expected);

        $this->assertEquals($humour_expected, $keys_result);
    }

    public function testFacialFeaturesWhenGivenTheRightParameters()
    {
        $file_content =  file_get_contents(dirname(__FILE__) .DIRECTORY_SEPARATOR.'/data_test.json');
        $image = json_decode($file_content, true);
        $data = IndicoIo::facial_features($image);

        $this->assertEquals(count($data), 48);
    }

    public function testImageFeaturesWhenGivenTheRightParameters()
    {
        $file_content =  file_get_contents(dirname(__FILE__) .DIRECTORY_SEPARATOR.'/data_test.json');
        $image = json_decode($file_content, true);
        $data = IndicoIo::image_features($image);

        $this->assertEquals(count($data), 2048);
    }

    public function testExplicitAuthArgument() {
        self::skipIfMissingEnvironmentVars(); 
        $examples = array('worst day ever', 'best day ever');
        $auth = array(getenv("INDICO_USERNAME"), getenv("INDICO_PASSWORD"));
        $data = IndicoIo::batch_sentiment($examples, $auth);

        $this->assertEquals(count($data), count($examples));
        $this->assertInternalType('array', $data);
        $this->assertInternalType('float', $data[0]);
    }

    public function testBatchPolitical() {
        self::skipIfMissingCredentials();       
        $keys_expected = array('Libertarian', 'Liberal', 'Green', 'Conservative');
        $examples = array('save the whales', 'cut taxes');
        $data = IndicoIo::batch_political($examples);

        $this->assertEquals(count($data), count($examples));

        $datapoint = $data[0];
        $keys_result = array_keys($datapoint);
        
        sort($keys_expected);
        sort($keys_result);

        $this->assertEquals($keys_expected, $keys_result);
    }

    public function testBatchSentiment()
    {
        self::skipIfMissingCredentials();       
        $examples = array('worst day ever', 'best day ever');
        $data = IndicoIo::batch_sentiment($examples);

        $this->assertEquals(count($data), count($examples));
        $this->assertInternalType('array', $data);
        $this->assertInternalType('float', $data[0]);
    }

    public function testBatchLanguage()
    {
        self::skipIfMissingCredentials();
        $examples = array('Clearly an english sentence.', 'Hablas espanol?');
        $data = IndicoIo::batch_language($examples);
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
        $data = IndicoIo::batch_text_tags($examples);
        $this->assertEquals(count($data), count($examples));

        $datapoint = $data[0];
        $keys_result = array_keys($datapoint);
        $this->assertEquals(count($keys_result), 111);
    }

    public function testBatchFer()
    {
        self::skipIfMissingCredentials();
        $humour_expected = array('Angry', 'Sad', 'Neutral', 'Surprise', 'Fear', 'Happy');
        $file_content =  file_get_contents(dirname(__FILE__) .DIRECTORY_SEPARATOR.'/data_test.json');
        $image = json_decode($file_content, true);
        $examples = array($image, $image);

        $data = IndicoIo::batch_fer($examples);
        $this->assertEquals(count($data), count($examples));

        $datapoint = $data[0];
        $keys_result = array_keys($datapoint);

        sort($keys_result);
        sort($humour_expected);

        $this->assertEquals($humour_expected, $keys_result);
    }

    public function testBatchFacialFeatures()
    {
        self::skipIfMissingCredentials();
        $file_content =  file_get_contents(dirname(__FILE__) .DIRECTORY_SEPARATOR.'/data_test.json');
        $image = json_decode($file_content, true);
        $examples = array($image, $image);
        $data = IndicoIo::batch_facial_features($examples);
        $this->assertEquals(count($data), count($examples));

        $datapoint = $data[0];
        $this->assertEquals(count($datapoint), 48);
    }

    public function testBatchImageFeatures()
    {
        self::skipIfMissingCredentials();
        $file_content =  file_get_contents(dirname(__FILE__) .DIRECTORY_SEPARATOR.'/data_test.json');
        $image = json_decode($file_content, true);
        $examples = array($image, $image);
        $data = IndicoIo::batch_image_features($examples);
        $this->assertEquals(count($data), count($examples));

        $datapoint = $data[0];
        $this->assertEquals(count($datapoint), 2048);
    }

    public function testConfigureFromEnvironmentVariables() 
    {
        # store previous settings to reset later
        $prev_username = getenv("INDICO_USERNAME");
        $prev_password = getenv("INDICO_PASSWORD");

        $username = "env-username";
        $password = "env-password";
        putenv("INDICO_USERNAME=$username");
        putenv("INDICO_PASSWORD=$password");

        $config = Configure::loadConfiguration();
        $this->assertEquals($config['auth'][0], $username);
        $this->assertEquals($config['auth'][1], $password);

        # reset to previous configuration
        putenv("INDICO_USERNAME=$prev_username");
        putenv("INDICO_PASSWORD=$prev_password");
    }

    public function testConfigureFromConfigFile()
    {
        $filename = tempnam("./", 'tmp');
        $handle = fopen($filename, "w");
        $username = "file-username";
        $password = "file-password";
        fwrite($handle, "[auth]\nusername=$username\npassword=$password");
        fclose($handle);

        $config = IndicoIo::$_options;
        $config = Configure::loadConfigFile($filename, $config);
        $this->assertEquals($config['auth'][0], $username);
        $this->assertEquals($config['auth'][1], $password);

        # cleanup
        unlink($filename);
    }

    public function testEnvironmentVariablesTakePrecedence()
    {
        # store previous settings to reset later
        $prev_username = getenv("INDICO_USERNAME");
        $prev_password = getenv("INDICO_PASSWORD");

        $env_username = "env-username";
        $env_password = "env-password";
        putenv("INDICO_USERNAME=$env_username");
        putenv("INDICO_PASSWORD=$env_password");

        $filename = tempnam("./", 'tmp');
        $handle = fopen($filename, "w");
        $username = "file-username";
        $password = "file-password";
        fwrite($handle, "[auth]\nusername=$username\npassword=$password");
        fclose($handle);

        $config = IndicoIo::$_options;
        $config = Configure::loadConfigFile($filename, $config);
        $config = Configure::loadEnvironmentVars($config);
        $this->assertEquals($config['auth'][0], $env_username);
        $this->assertEquals($config['auth'][1], $env_password);

        # reset to previous configuration
        putenv("INDICO_USERNAME=$prev_username");
        putenv("INDICO_PASSWORD=$prev_password");
        unlink($filename);
    }
}
