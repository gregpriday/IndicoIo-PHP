<?php

namespace Utils;



Class Multi
{
    public static $MAP_APIS = array(
        "sentiment" => "sentiment",
        "text_tags" => "texttags",
        "language" => "language",
        "political" => "political",
        "fer" => "fer",
        "image_features" => "imagefeatures",
        "facial_features" => "facialfeatures"
    );

    public static function filterApis($apis, $accepted) {
        $converted_apis = array();

        foreach ($apis as $api) {
            if (!in_array($api, $accepted)) {
                throw new Exception(
                    $api
                    + " is not an acceptable api name. Please use "
                    + implode(",", $accepted)
                );
            }

            $converted_apis[] = self::$MAP_APIS[$api];
        }

        return implode(",", $converted_apis);
    }

    public static function convertResults($results, $apis) {
        $converted_results = array();
        foreach ($apis as $api) {
            $response = $results[self::$MAP_APIS[$api]];
            if (array_key_exists("results", $response)) {
                $converted_results[$api] = $response["results"];
            } else {
                throw new Exception($api . " encountered an error: " . $response['error']);
            }
        }

        return $converted_results;
    }
}
