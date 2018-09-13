<?php

namespace IndicoIo\Utils;

use Eventviva\ImageResize;

Class Image
{
    public static function processImages($array_string, $size, $min_axis) {
        $array = array();
        foreach ($array_string as $string) {
            array_push($array, self::processImage($string, $size, $min_axis));
        }

        return $array;
    }

    public static function isValidURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function processImage($string, $size, $min_axis) {
        if (gettype($string) == "array") {
            return self::processImages($string ,$size, $min_axis);
        }

        if (file_exists($string)) {
            return self::resizeImage(new ImageResize($string), $size, $min_axis);
        } else if (self::isValidURL($string)) {
            return $string;
        } else {
            try {
                $image = ImageResize::createFromString(base64_decode($string));
                return self::resizeImage(
                    $image,
                    $size,
                    $min_axis
                );
            } catch (\Exception $e) {
                $msg = $e->getMessage();
                if (strpos($msg, "Could not read file") !== FALSE) {
                    throw new ImageException();
                } else {
                    throw $e;
                }
            }
        }
    }

    public static function resizeImage($image, $size, $min_axis) {
        // Check Aspect Ratio
        $ratio = ($image->getSourceWidth())/($image->getSourceHeight());
        if ($ratio >= 10 || $ratio <= .1) {
            echo "For best performance, we recommend images of apsect ratio less than 1:10.";
        }

        if ($min_axis) {
            $image -> resizeToBestFit($size, $size);
            return base64_encode($image);
        }
        if ($size) {
            $image -> resize($size, $size);
        }
        $image -> getImageAsString(IMAGETYPE_PNG, 4);
        return base64_encode($image);
    }
}