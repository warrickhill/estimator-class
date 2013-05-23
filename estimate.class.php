<?php

/**
 * Estimate is a php class designed to estimate meter readings, for water or eletric consumption mainly, but other applications could be found.
 *
 * Uses an inputed array to calculate the estimates.
 *
 * @author Warrick Hill - http://www.elephantpc.com
 */
class estimate {

    private $sorted_input;

    /**
     * An Array of all the estmiates performed so far.
     */
    var $estmated_output;

    function __construct($array, $key) {
        $this->sorted_input = $this->sort_array($array, $key);
    }

    /**
     * Sorts an Array by the $key field ascendingly
     */
    function sort_array($array, $key) {
        foreach ($array as $temp_list) {
            $sort_aux[] = ($temp_list[$key]);
        }
        array_multisort($sort_aux, SORT_ASC, $array);

        return $array;
    }

    /**
     * Returns the highest value of field $key.
     */
    function get_highest($array, $key) {
        $n[$key] = -99999999999;
        foreach ($array as $value) {
            if ($value[$key] >= $n[$key]) {
                $n = $value;
            }
        }
        return $n;
    }

    /**
     * Returns the lowest value of field $key.
     */
    function get_lowest($array, $key) {
        $n[$key] = 99999999999;
        foreach ($array as $value) {
            if ($value[$key] <= $n[$key]) {
                $n = $value;
            }
        }
        return $n;
    }

    /**
     * Returns the overall average of $value field per $key field.
     */
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

    /**
     * Returns the next highest value of field $key after $point.
     */
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

    /**
     * Returns the next lowest value of field $key before $point.
     */
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

    /**
     * Returns the average of $value field per $key at $point of $key.
     */
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

    /**
     * Populates the estimated_output array each time an estimate is made.
     */
    function insert_est_array($key, $point, $value, $estimate) {
        if (!empty($this->estmated_output)) {
            foreach ($this->estmated_output as $row => $out) {
                if ($out[$key] == $point) {
                    if (!array_key_exists($value, $out)) {
                        $this->estmated_output[$row][$value] = $estimate;
                    }
                    return;
                }
            }
        }
        $this->estmated_output[] = array(
            $key => $point,
            $value => $estimate
        );
    }

    /**
     * Returns the estmiate of the value of the $value field at $point of the $key field.
     */
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
        $point = $point + $series;
        for ($i = 3; $i > 0; $i--) {
            $values[] = $this->estimate($key, $point, $value);
            $point = $point - $series;
        }
        $average = array_sum($values) / count($values);
        return $average;
    }

    /**
     * Returns the differance or increase of $value field between $point of $key and ($point - $diff) of $key.
     */
    function differance_estimate($key, $point, $value, $diff) {
        $hightvalues = $this->estimate($key, $point, $value);
        $point = $point - $diff;
        $lowvalues = $this->estimate($key, $point, $value);
        $usage = $hightvalues - $lowvalues;
        return $usage;
    }

    /**
     * Returns the average over the last 3 series of differance_estimate.
     */
    function series_diff_estimate($key, $point, $value, $series, $diff) {
        for ($i = 3; $i > 0; $i--) {
            $values[] = $this->differance_estimate($key, $point, $value, $diff);
            $point = $point - $series;
        }
        $average = array_sum($values) / count($values);
        return $average;
    }

}

?>