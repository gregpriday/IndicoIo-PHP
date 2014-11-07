<?php 

namespace IndicoIo;

/**
* Simple PHP wrapper for Indico
*/
class IndicoIo
{
	protected static $_options = array(
		'default_host' => 'http://apiv1.indico.io'
	);

	public static function  political($text)
	{
        return self::_callService('data', $text, 'political');
    }

	public static function sentiment($text)
	{
        return self::_callService('data', $text, 'sentiment');
	}

	public static  function posneg($text)
	{
		return self::sentiment($text);
	}

	public static function language($text)
	{
		return self::_callService('data', $text, 'language');
	}

	public static function text_tags($text)
	{
		return self::_callService('data', $text, 'texttags');
	}

	public static function fer($image)
	{
		return self::_callService('data', $image, 'fer');
	}

	public static function facial_features($image)
	{
		return self::_callService('data', $image, 'facialfeatures');
	}

	protected static function _callService($name_to_post, $data, $service)
	{
		$context = stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-Type: application/json',
				'content' => json_encode(array($name_to_post => $data))
			)
		));

		$query_url = self::$_options['default_host']."/$service";
		$result = file_get_contents($query_url, false, $context);
		$parsed = self::_parseAnswer($result);
		if(array_key_exists('results', $parsed)){
            return $parsed['results'];
        }
        else {
            return $parsed;
        }
	}

	protected static function _parseAnswer($data, $returnArray=true)
	{
		return json_decode($data, $returnArray);
	}
}
