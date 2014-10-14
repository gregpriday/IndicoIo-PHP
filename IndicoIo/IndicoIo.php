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
		return self::_callService('data', $text, 'political')['results'];
	}

	public static function sentiment($text)
	{
		return self::_callService('data', $text, 'sentiment')['results'];
	}

	public static  function posneg($text)
	{
		return self::sentiment($text)['results'];
	}

	public static function  language($text)
	{
		return self::_callService('data', $text, 'language')['results'];
	}

	public static  function  fer($image)
	{
		return self::_callService('data', $image, 'fer')['results'];
	}

	public static function facial_features($image)
	{
		return self::_callService('data', $image, 'facialfeatures')['results'];
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
		return self::_parseAnswer($result);
	}

	protected static function _parseAnswer($data, $returnArray=true)
	{
		return json_decode($data, $returnArray);
	}
}
