<?php

namespace App\traits;

trait PeselTrait
{
    /**
     * @param $pesel
     * @param $format
     * @return string
     */
    private function getFormattedDateFromPesel($pesel, $format = "Y-m-d")
    {
        $year = substr($pesel, 0, 2);
        $month = substr($pesel, 2, 2);
        $day = substr($pesel, 4, 2);

        $century = substr($pesel, 2, 1);
        $century = (intval($century) + 2) % 10;
        $century = round($century / 2, 0, PHP_ROUND_HALF_DOWN) + 18;

        $fullYear = $century . $year;
        $transformedMonth = str_pad(intval($month) % 20, 2, '0', STR_PAD_LEFT);

        return date(
            $format,
            strtotime($fullYear . "-" . $transformedMonth . "-" . $day)
        );
    }
}