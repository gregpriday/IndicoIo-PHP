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
        $data = self::_callService('data', $text, 'political');
        if(array_key_exists('results', $data)){
            return $data['results'];
        }
        else {
            return $data;
        }
    }

	public static function sentiment($text)
	{
        $data = self::_callService('data', $text, 'sentiment');
        if(array_key_exists('results', $data)){
            return $data['results'];
        }
        else {
            return $data;
        }
	}

	public static  function posneg($text)
	{
		$data = self::sentiment($text);
        if(array_key_exists('results', $data)){
            return $data['results'];
        }
        else {
            return $data;
        }
	}

	public static function  language($text)
	{
		$data = self::_callService('data', $text, 'language');
        if(array_key_exists('results', $data)){
            return $data['results'];
        }
        else {
            return $data;
        }
	}

	public static  function  fer($image)
	{
		$data = self::_callService('data', $image, 'fer');
        if(array_key_exists('results', $data)){
            return $data['results'];
        }
        else {
            return $data;
        }
	}

	public static function facial_features($image)
	{
		$data = self::_callService('data', $image, 'facialfeatures');
        if(array_key_exists('results', $data)){
            return $data['results'];
        }
        else {
            return $data;
        }
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
