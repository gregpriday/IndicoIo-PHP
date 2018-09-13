<?php

namespace IndicoIo\Utils;

class PDF
{
    public static function processPDFs($pdfs) {
        $array = array();
        foreach ($pdfs as $pdf) {
            array_push($array, self::processPDF($pdf));
        }

        return $array;
    }

    public static function processPDF($pdf) {
        if (gettype($pdf) == "array") {
            return self::processPDFs($pdf);
        }

        $filecontents = file_get_contents($pdf);
        $pdf = $filecontents ? base64_encode($filecontents) : $pdf;
        return $pdf;
    }

}