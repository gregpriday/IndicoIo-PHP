<?php

namespace IndicoIo;

use Exception;
use IndicoIo\Utils\Multi as Multi;
use IndicoIo\Utils\Image as Image;
use IndicoIo\Utils\PDF as PDF;
use IndicoIo\Configure as Configure;

/**
* Simple PHP wrapper for Indico
*/
class IndicoIo
{
    public static $config;
    public static $TEXT_APIS = array("sentiment", "sentimenthq", "text_tags", "language", "political", "keywords", "twitter_engagement", "personality", "summarization");
    public static $IMAGE_APIS = array("fer", "image_features", "image_recognition", "facial_features", "content_filter");

    public static function boot()
    {
        // Load the configuration
        self::$config = Configure::loadConfiguration();
    }

    protected static function api_url($cloud = false, $service, $batch = false, $method = false, $api_key, $params = array()) {
        $root_url = self::$config['default_host'];
        if ($cloud) {
            $root_url = "https://$cloud.indico.domains";
        }
        if (!$api_key) {
            throw new Exception("A valid API key must be provided.");
        }

        $url = "$root_url/$service";
        if ($batch) {
            $url = $url . "/batch";
        }

        if ($method) {
            $url = $url . "/" . $method;
        }

        if (!empty($params)) {
            $url = $url . "?";
        }

        $url = $url . http_build_query($params);
        return $url;
    }


    public static function political($text, $params=array())
    {
        if (!array_key_exists('v', $params) && !array_key_exists('version', $params)) {
            $params['version'] = 2;
        }
        return static::_callService($text, 'political', 'predict', $params);
    }

    public static function emotion($text, $params=array())
    {
        return static::_callService($text, 'emotion', 'predict', $params);
    }

    public static function sentiment($text, $params=array())
    {
        return static::_callService($text, 'sentiment', 'predict', $params);
    }

    public static function sentiment_hq($text, $params=array())
    {
        return static::_callService($text, 'sentimenthq', 'predict', $params);
    }

    public static function language($text, $params=array())
    {
        return static::_callService($text, 'language', 'predict', $params);
    }


    public static function text_tags($text, $params=array())
    {
        return static::_callService($text, 'texttags', 'predict', $params);
    }


    public static function fer($image, $params=array())
    {
        $size = array_key_exists("detect", $params) && $params["detect"] ? false : 48;
        $image = Image::processImage($image, $size, false);
        return static::_callService($image, 'fer', 'predict', $params);
    }

    public static function keywords($text, $params=array())
    {
        if (!array_key_exists('v', $params) && !array_key_exists('version', $params)) {
            $params['version'] = 2;
        }

        if (array_key_exists("language", $params) && $params["language"] != "english") {
            $params["version"] = 1;
        }

        return static::_callService($text, 'keywords', 'predict', $params);
    }


    public static function twitter_engagement($text, $params=array())
    {
        return static::_callService($text, 'twitterengagement', 'predict', $params);
    }

    public static function people($text, $params=array())
    {
        if (!array_key_exists('v', $params) && !array_key_exists('version', $params)){
            $params['version'] = 2;
        }
        return static::_callService($text, 'people', 'predict', $params);
    }

    public static function places($text, $params=array())
    {
        if (!array_key_exists('v', $params) && !array_key_exists('version', $params)){
            $params['version'] = 2;
        }
        return static::_callService($text, 'places', 'predict', $params);
    }

    public static function organizations($text, $params=array())
    {
        if (!array_key_exists('v', $params) && !array_key_exists('version', $params)){
            $params['version'] = 2;
        }
        return static::_callService($text, 'organizations', 'predict', $params);
    }

    public static function relevance($text, $queries, $params=array())
    {
        $params['queries'] = $queries;
        $params['synonyms'] = false;
        return static::_callService($text, 'relevance', 'predict', $params);
    }

    public static function text_features($text, $params=array())
    {
        $params['synonyms'] = false;
        return static::_callService($text, 'textfeatures', 'predict', $params);
    }

    public static function intersections($input, $params=array())
    {
        $apis = self::get($params, "apis");
        if (is_array($input) && array_keys($input) !== range(0, count($input) - 1)) {
            $diff = array_diff(array_keys($input), $apis);
            if (!empty($diff)) {
                trigger_error(
                    "The `intersections` function expects the input to have the same keys as what is provided in `apis`",
                    E_USER_WARNING
                );
            }
        }
        $converted_apis = Multi::filterApis($apis, self::$TEXT_APIS);
        $params["apis"] = $converted_apis;
        return static::_callService($input, 'apis/intersections', false, $params);
    }


    public static function facial_features($image, $params=array())
    {
        $image = Image::processImage($image, 64, false);
        return static::_callService($image, 'facialfeatures', 'predict', $params);
    }

    public static function image_features($image, $params=array())
    {
        $image = Image::processImage($image, 512, true);
        if (!array_key_exists('v', $params) || !array_key_exists('version', $params)){
            $params['version'] = 3;
        }
        return static::_callService($image, 'imagefeatures', 'predict', $params);
    }

    public static function image_recognition($image, $params=array())
    {
        $image = Image::processImage($image, 144, true);
        return static::_callService($image, 'imagerecognition', 'predict', $params);
    }

    public static function content_filter($image, $params=array())
    {
        $image = Image::processImage($image, 128, true);
        return static::_callService($image, 'contentfiltering', 'predict', $params);
    }

    public static function facial_localization($image, $params=array())
    {
        $image = Image::processImage($image, false, false);
        return static::_callService($image, 'faciallocalization', 'predict', $params);
    }

    public static function personality($text, $params=array())
    {
        return static::_callService($text, 'personality', 'predict', $params);
    }

    public static function personas($text, $params=array())
    {
        $params['persona'] = True;
        return static::_callService($text, 'personality', 'predict', $params);
    }

    public static function summarization($text, $params=array())
    {
        return static::_callService($text, 'summarization', 'predict', $params);
    }

    public static function pdf_extraction($pdf, $params=array())
    {
        return static::_callService(PDF::processPDF($pdf), 'pdfextraction', 'predict', $params);
    }

    # Multi API Calls
    public static function analyze_text($text, $params=array())
    {
        $apis = self::get($params, "apis");
        $converted_apis = Multi::filterApis($apis, self::$TEXT_APIS);
        $params["apis"] = $converted_apis;
        $results = static::_callService($text, "apis/multiapi", false, $params);
        return Multi::convertResults($results, $apis);
    }

    public static function analyze_image($image, $params=array())
    {
        $apis = self::get($params, "apis");
        $converted_apis = Multi::filterApis($apis, self::$IMAGE_APIS);
        $params["apis"] = $converted_apis;
        $results = static::_callService($image, "apis/multiapi", false, $params);
        return Multi::convertResults($results, $apis);
    }

    public static function _callService($data, $service, $method, $params = array())
    {
        # Load from configuration array if present
        $api_key = self::get($params, 'api_key');
        $cloud = self::get($params, "cloud");
        $batch = gettype($data) == "array";

        # Override $batch for custom API addData method
        if ($method == 'add_data' && !self::get($params, "batch")) {
            $batch = False;
        }

        $apis = self::get($params, "apis");
        $version = self::get($params, "version");
        unset($params["apis"]);
        unset($params["version"]);
        unset($params["batch"]);

        # Set up Url Paramters
        $url_params = array();

        # apis is already an imploded string here. might want to move that logic here.
        if ($apis) {
            $url_params["apis"] = $apis;
        }

        if ($version) {
            $url_params["version"] = $version;
        }

        # Set up Request
        $query_url = self::api_url($cloud, $service, $batch, $method, $api_key, $url_params);

        if ($data != NULL) {
            $params = array_merge(array('data' => $data), $params);
        }
        $json_data = json_encode($params, JSON_NUMERIC_CHECK);

        # handle edge case of PHP json encoding function
        if ($json_data == '[]') {$json_data = '{}';}

        $ch = curl_init($query_url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_data),
            'client-lib: php',
            'version-number: 0.2.0',
            'X-ApiKey: ' . $api_key
        ));

        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = mb_substr($response, 0, $header_size);
        $result = mb_substr($response, $header_size);

        $headers = explode("\n", $headers);
        foreach($headers as $header) {
            if (stripos($header, 'x-warning:') !== false) {
                list ($key, $value) = explode(':', $header, 2);
                trigger_error($value, E_USER_WARNING);
            }
        }

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

    static function get(&$array, $key) {
        if (array_key_exists($key, $array)) {
            $value = $array[$key];
        } elseif (array_key_exists($key, self::$config)) {
            $value = self::$config[$key];
        } else {
            $value = False;
        }

        unset($array[$key]);
        return $value;
    }

    public static function collections($params = array()) {
        return static::_callService(NULL, 'custom', 'collections', $params);
    }
}

// Load the configuration
IndicoIo::boot();
