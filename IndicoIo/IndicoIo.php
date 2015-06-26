<?php

namespace IndicoIo;
use Exception;
use Utils\Multi as Multi;
use Configure\Configure as Configure;

require_once("Configure.php");
require_once("Utils.php");

/**
* Simple PHP wrapper for Indico
*/
class IndicoIo
{
	public static $config;
	public static $TEXT_APIS = array("sentiment", "text_tags", "language", "political");
	public static $IMAGE_APIS = array("fer", "image_features", "facial_features");

	protected static function api_url($cloud = false, $service, $batch = false, $api_key, $params = array()) {
		if ($cloud) {
			$root_url = "http://$cloud.indico.domains";
		}
		if (!$api_key) {
			throw new Exception("A valid API key must be provided.");
		}
		$root_url = self::$config['default_host'];
		$url = "$root_url/$service";
		if ($batch) {
			$url = $url . "/batch";
		}

		$url = $url . "?key=" . $api_key;

		foreach ($params as $key => $value) {
			$url = $url . "&" . $key . "=" . $value;
		}

		return $url;
	}

	public static function political($text, $api_key = false, $cloud = false)
	{
        return self::_callService($text, 'political', $cloud, $api_key);
    }

    public static function batch_political($text, $api_key = false, $cloud = false)
    {
    	return self::_callService($text, 'political', $cloud, $api_key, $batch = true);
    }

	public static function sentiment($text, $api_key = false, $cloud = false)
	{
        return self::_callService($text, 'sentiment', $cloud, $api_key);
	}

	public static function batch_sentiment($text, $api_key = false, $cloud = false)
	{
        return self::_callService($text, 'sentiment', $cloud, $api_key, $batch = true);
	}

	public static  function posneg($text, $api_key = false, $cloud = false)
	{
		return self::sentiment($text, $api_key, $cloud);
	}

	public static function batch_posneg($text, $api_key = false, $cloud = false)
	{
		return self::sentiment($text, $cloud, $api_key, $batch = true);
	}

	public static function language($text, $api_key = false, $cloud = false)
	{
		return self::_callService($text, 'language', $cloud, $api_key);
	}

	public static function batch_language($text, $api_key = false, $cloud = false)
	{
		return self::_callService($text, 'language', $cloud, $api_key, $batch = true);
	}

	public static function text_tags($text, $api_key = false, $cloud = false)
	{
		return self::_callService($text, 'texttags', $cloud, $api_key);
	}

	public static function batch_text_tags($text, $api_key = false, $cloud = false)
	{
		return self::_callService($text, 'texttags', $cloud, $api_key, $batch = true);
	}

	public static function fer($image, $api_key = false, $cloud = false)
	{
		return self::_callService($image, 'fer', $cloud, $api_key);
	}

	public static function batch_fer($images, $api_key = false, $cloud = false)
	{
		return self::_callService($images, 'fer', $cloud, $api_key, $batch = true);
	}

	public static function facial_features($image, $api_key = false, $cloud = false)
	{
		return self::_callService($image, 'facialfeatures', $api_key);
	}

	public static function batch_facial_features($images, $api_key = false, $cloud = false)
	{
		return self::_callService($images, 'facialfeatures', $cloud, $api_key, $batch = true);
	}

	public static function image_features($image, $api_key = false, $cloud = false)
	{
		return self::_callService($image, 'imagefeatures', $cloud, $api_key);
	}

	public static function batch_image_features($images, $api_key = false, $cloud = false)
	{
		return self::_callService($images, 'imagefeatures', $cloud, $api_key, $batch = true);
	}

	# Multi API Calls
	public static function predict_text($text, $apis = TEXT_APIS, $api_key = false, $cloud = false)
	{
		$converted_apis = Multi::filterApis($apis, self::$TEXT_APIS);
		$results = self::_callService($text, "apis", $cloud, $api_key, $batch=false, array("apis"=>$converted_apis));
		return Multi::convertResults($results, $apis);
	}

	public static function batch_predict_text($text, $apis = TEXT_APIS, $api_key = false, $cloud = false)
	{
		$converted_apis = Multi::filterApis($apis, self::$TEXT_APIS);
		$results = self::_callService($text, "apis", $cloud, $api_key, $batch = true, array("apis"=>$converted_apis));
		return Multi::convertResults($results, $apis);
	}

	public static function predict_image($image, $apis = IMAGE_APIS, $api_key = false, $cloud = false)
	{
		$converted_apis = Multi::filterApis($apis, self::$IMAGE_APIS);
		$results = self::_callService($image, "apis", $cloud, $api_key, $batch=false, array("apis"=>$converted_apis));
		return Multi::convertResults($results, $apis);
	}

	public static function batch_predict_image($images, $apis = IMAGE_APIS, $api_key = false, $cloud = false)
	{
		$converted_apis = Multi::filterApis($apis, self::$IMAGE_APIS);
		$results = self::_callService($images, "apis", $cloud, $api_key, $batch = true, array("apis"=>$converted_apis));
		return Multi::convertResults($results, $apis);
	}

	protected static function _callService($data, $service, $cloud = false, $api_key = false, $batch = false, $params = array())
	{
		# Load from configuration array if present
		if (!$api_key) {
			$api_key = self::$config['api_key'];
		}
		if (!$cloud) {
			$cloud = self::$config['cloud'];
		}

		$query_url = self::api_url($cloud, $service, $batch, $api_key, $params);
		$json_data = json_encode(array_merge(array('data' => $data), $params));

		$ch = curl_init($query_url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'Content-Type: application/json',
		    'Content-Length: ' . strlen($json_data),
		    'client-lib: php',
        	    'version-number: 0.1.0'
		));

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

IndicoIo::$config = Configure::loadConfiguration();
