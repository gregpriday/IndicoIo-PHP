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
		$query_url = self::$_options['default_host']."/$service";
		$json_data = json_encode(array($name_to_post => $data));

		$ch = curl_init($query_url);                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		    'Content-Type: application/json',                                                                                
		    'Content-Length: ' . strlen($json_data))                                                                       
		);    

		$result = curl_exec($ch); 
		curl_close($ch); 

		$parsed = json_decode($result, $assoc = true);
		if(array_key_exists('results', $parsed)){
            return $parsed['results'];
        }
        else {
            return $parsed;
        }
	}
}
