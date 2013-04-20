<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of estimate
 *
 * @author diddle
 */
class estimate {

    var $sorted_input;
    var $estmated_output;

    function __construct($array, $key) {
        $this->sorted_input = $this->sort_array($array, $key);
        $this->estmated_output = $this->sort_array($array, $key);
    }

    function sort_array($array, $key) {
        foreach ($array as $temp_list) {
            $sort_aux[] = ($temp_list[$key]);
        }
        array_multisort($sort_aux, SORT_ASC, $array);

        return $array;
    }

    function get_highest($array, $key) {
        $n[$key] = -99999999999;
        foreach ($array as $value) {
            if ($value[$key] >= $n[$key]) {
                $n = $value;
            }
        }
        return $n;
    }

    function get_lowest($array, $key) {
        $n[$key] = 99999999999;
        foreach ($array as $value) {
            if ($value[$key] <= $n[$key]) {
                $n = $value;
            }
        }
        return $n;
    }

    function overall_average($key, $value) {
        // first get high and low
        $high_si = $this->get_highest($this->sorted_input, $key);
        $high_eo = $this->get_highest($this->estmated_output, $key);
        if ($high_eo >= $high_si) {
            $high = $high_si;
        } else {
            $high = $high_eo;
        }

        $low_si = $this->get_lowest($this->sorted_input, $key);
        $low_eo = $this->get_lowest($this->estmated_output, $key);
        if ($low_eo >= $low_si) {
            $low = $low_si;
        } else {
            $low = $low_eo;
        }

        // get differance in key

        $key_diff = $high[$key] - $low[$key];

        // get differance in value
        $value_diff = $high[$value] - $low[$value];

        // work out average
        // check if zero first
        if ($key_diff == 0) {
            $key_diff = 1;
        }

        $average = $value_diff / $key_diff;
        return $average;
    }

    function get_high($array, $key, $point) {
        $n = $this->get_highest($array, $key);
        foreach ($array as $value) {
            if ($value[$key] >= $point) {
                if ($value[$key] <= $n[$key]) {
                    $n = $value;
                }
            }
        }
        return $n;
    }

    function get_low($array, $key, $point) {
        $n = $this->get_lowest($array, $key);
        foreach ($array as $value) {
            if ($value[$key] <= $point) {
                if ($value[$key] >= $n[$key]) {
                    $n = $value;
                }
            }
        }
        return $n;
    }

    function estimate($key, $point, $value) {
        // first get closest high and low
        $high_si = $this->get_high($this->sorted_input, $key, $point);
        $high_eo = $this->get_high($this->estmated_output, $key, $point);
        if ($high_eo >= $high_si) {
            $high = $high_si;
        } else {
            $high = $high_eo;
        }

        $low_si = $this->get_low($this->sorted_input, $key, $point);
        $low_eo = $this->get_low($this->estmated_output, $key, $point);
        if ($low_eo >= $low_si) {
            $low = $low_si;
        } else {
            $low = $low_eo;
        }

        if ($point > $high[$key]) {

            $average = $this->overall_average($key, $value);

            // work out estimate

            $estimate = (($point - $high[$key]) * $average) + $high[$value];
        } elseif ($point < $low[$key]) {

            $average = $this->overall_average($key, $value);

            // work out estimate

            $estimate = $low[$value] - ( ($low[$key] - $point) * $average);
        } else {

            // get differance in key

            $key_diff = $high[$key] - $low[$key];

            // get differance in value
            $value_diff = $high[$value] - $low[$value];

            // work out average
            // check if zero first
            if ($key_diff == 0) {
                $key_diff = 1;
            }

            $average = $value_diff / $key_diff;

            // work out estimate

            $estimate = (($point - $low[$key]) * $average) + $low[$value];
        }

        return round($estimate, 1);
    }

}

?>
