<?php

/**
 * Estimate is a php class designed to estimate meter readings, for water or eletric consumption mainly, but other applications could be found.
 *
 * Uses an inputed array to calculate the estimates.
 *
 * @author Warrick Hill - http://www.elephantpc.com
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
        $high = $this->get_highest($this->sorted_input, $key);

        $low = $this->get_lowest($this->sorted_input, $key);

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

    function average($key, $value, $point) {
        // first get closest high and low
        $high = $this->get_high($this->sorted_input, $key, $point);

        $low = $this->get_low($this->sorted_input, $key, $point);

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

    function insert_est_array($key, $point, $value, $estimate) {
        foreach ($this->estmated_output as $row => $out) {
            if ($out[$key] == $point) {
                if (!array_key_exists($value, $out)) {
                    $this->estmated_output[$row][$value] = $estimate;
                }
                return;
            }
        }
        $this->estmated_output[] = array(
            $key => $point,
            $value => $estimate
        );
    }

    function estimate($key, $point, $value) {
        $high = $this->get_high($this->sorted_input, $key, $point);
        $low = $this->get_low($this->sorted_input, $key, $point);

        if ($point > $high[$key]) {

            $average = $this->overall_average($key, $value);

            $estimate = (($point - $high[$key]) * $average) + $high[$value];
        } elseif ($point < $low[$key]) {

            $average = $this->overall_average($key, $value);

            $estimate = $low[$value] - ( ($low[$key] - $point) * $average);
        } else {

            $average = $this->average($key, $value, $point);

            $estimate = (($point - $low[$key]) * $average) + $low[$value];
        }

        $this->insert_est_array($key, $point, $value, $estimate);
        return $estimate;
    }

    function series_estimate($key, $point, $value, $series) {
        for ($i = 3; $i > 0; $i--) {
            $values[] = $this->estimate($key, $point, $value);
            $point = $point - $series;
        }
        $average = array_sum($values) / count($values);
        return $average;
    }

}

?>