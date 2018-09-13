<?php

namespace IndicoIo\Utils;

use Exception;

Class Multi
{
    public static function filterApis($apis, $accepted) {
        foreach ($apis as $api) {
            if (!in_array($api, $accepted)) {
                throw new Exception(
                    $api
                    + " is not an acceptable api name. Please use "
                    + implode(",", $accepted)
                );
            }
        }
        return implode(",", $apis);
    }
    public static function convertResults($results, $apis) {
        $converted_results = array();
        foreach ($apis as $api) {
            $response = $results[$api];
            if (array_key_exists("results", $response)) {
                $converted_results[$api] = $response["results"];
            } else {
                throw new Exception($api . " encountered an error: " . $response['error']);
            }
        }
        return $converted_results;
    }
}