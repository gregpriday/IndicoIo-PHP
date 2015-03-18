<?php 

namespace IndicoIo;
use Exception;

use Configure\Configure as Configure;

require_once("Configure.php");

/**
* Simple PHP wrapper for Indico
*/
class IndicoIo
{
	public static $_options;

	protected static function api_url($cloud = false, $service, $batch = false) {
		if ($cloud) {
			$root_url = "http://$cloud.indico.domains";
		}
		$root_url = self::$_options['default_host'];
		$url = "$root_url/$service";
		if ($batch) {
			$url = $url . "/batch";
		}
		return $url;
	}

	public static function political($text, $cloud = false, $auth = false)
	{
        return self::_callService($text, 'political', $cloud, $auth);
    }

    public static function batch_political($text, $cloud = false, $auth = false) 
    {
    	return self::_callService($text, 'political', $cloud, $auth, $batch = true);
    }

	public static function sentiment($text, $cloud = false, $auth = false)
	{
        return self::_callService($text, 'sentiment', $cloud, $auth);
	}

	public static function batch_sentiment($text, $cloud = false, $auth = false)
	{
        return self::_callService($text, 'sentiment', $cloud, $auth, $batch = true);
	}

	public static  function posneg($text, $cloud = false, $auth = false)
	{
		return self::sentiment($text, $cloud, $auth);
	}

	public static function batch_posneg($text, $cloud = false, $auth = false)
	{
		return self::sentiment($text, $cloud, $auth, $batch = true);
	}

	public static function language($text, $cloud = false, $auth = false)
	{
		return self::_callService($text, 'language', $cloud, $auth);
	}

	public static function batch_language($text, $cloud = false, $auth = false)
	{
		return self::_callService($text, 'language', $cloud, $auth, $batch = true);
	}

	public static function text_tags($text, $cloud = false, $auth = false)
	{
		return self::_callService($text, 'texttags', $cloud, $auth);
	}

	public static function batch_text_tags($text, $cloud = false, $auth = false)
	{
		return self::_callService($text, 'texttags', $cloud, $auth, $batch = true);
	}

	public static function fer($image, $cloud = false, $auth = false)
	{
		return self::_callService($image, 'fer', $cloud, $auth);
	}

	public static function batch_fer($images, $cloud = false, $auth = false)
	{
		return self::_callService($images, 'fer', $cloud, $auth, $batch = true);
	}

	public static function facial_features($image, $cloud = false, $auth = false)
	{
		return self::_callService($image, 'facialfeatures', $auth);
	}

	public static function batch_facial_features($images, $cloud = false, $auth = false)
	{
		return self::_callService($images, 'facialfeatures', $cloud, $auth, $batch = true);
	}

	public static function image_features($image, $cloud = false, $auth = false)
	{
		return self::_callService($image, 'imagefeatures', $cloud, $auth);
	}

	public static function batch_image_features($images, $cloud = false, $auth = false)
	{
		return self::_callService($images, 'imagefeatures', $cloud, $auth, $batch = true);
	}

	protected static function _callService($data, $service, $cloud = false, $auth = false, $batch = false)
	{
		$query_url = self::api_url($cloud, $service, $batch);
		$json_data = json_encode(array('data' => $data));

		$ch = curl_init($query_url);                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		    'Content-Type: application/json',                                                                                
		    'Content-Length: ' . strlen($json_data))                                                                       
		);    

		# Load from configuration array if present
		if (!$auth) {
			$auth = self::$_options['auth'];
		} 
		if (!$cloud) {
			$cloud = self::$_options['cloud'];
		}

		# Use HTTP Basic Auth if batch method or private cloud is used
		if ($batch || $cloud) {
			if (count($auth) != 2) {
				throw new Exception("Username and password must be provided");
			}
			curl_setopt($ch, CURLOPT_USERPWD, $auth[0] . ":" . $auth[1]);
		}

		$result = curl_exec($ch); 
		curl_close($ch); 

		$parsed = json_decode($result, $assoc = true);
		if (array_key_exists('results', $parsed)) {
            return $parsed['results'];
        } else if (array_key_exists('error', $parsed)) {
            throw new Exception($parsed['error']);
        } else {
        	throw new Exception($parsed);
        }
	}
}

IndicoIo::$_options = Configure::loadConfiguration();
