<?php 

namespace IndicoIo;

/**
* Simple PHP wrapper for Indico
*/
class IndicoIo
{
	protected static $_options = array(
		'default_host' => 'http://api.indico.io'
	);

	public static function  political($text)
	{
		return self::_callService($text, 'political');
	}

	public static function  sentimental($text)
	{
		return self::_callService($text, 'sentimental');
	}

	protected static function _callService($text, $service)
	{
		$context = stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-Type: application/json',
				'content' => json_encode(array('text' => $text))
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

	/*public static  function  posneg($value='')
	{
		# code...
	}

	public static function  language($value='')
	{
		# code...
	}

	public static  function  fer($value='')
	{
		# code...
	}

	public static function facial_features($value='')
	{
		# code...
	}*/

}