<?php
include_once 'estimate.class.php';

// example array. we will be using the unix time stamp as the base for calculating Reading 1 and 2.
$array = Array
(
    [0] => Array
        (
            [Date] => 1346630400
            [Reading_1] => 10422
            [Reading_2] => 9456
        )

    [1] => Array
        (
            [Date] => 1350864000
            [Reading_1] => 10651
            [Reading_2] => 9551
        )

    [2] => Array
        (
            [Date] => 1351382400
            [Reading_1] => 10701
            [Reading_2] => 9564
        )

    [3] => Array
        (
            [Date] => 1366070400
            [Reading_1] => 13205
            [Reading_2] => 10336
        )

);

// create new object. the array and the key which we will use to calculate the estimates must be passed
$ele_est = new estimate($array, 'Date');
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <div id="ele_chart_div" style="width: 100%;"></div>

        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Date', 'Reading 1', 'Reading 2', 'Total']
<?php
$unix = 1354838400;
$t = time();
$lowest['ele'][1] = $ele_est->estimate('Date', $unix, 'Reading_1');
$lowest['ele'][2] = $ele_est->estimate('Date', $unix, 'Reading_2');
while ($unix < $t) {
    echo ",['" . date("d.m.y", $unix) . "', " . ($ele_est->estimate('Date', $unix, 'Reading_1') - $lowest['ele'][1]) . ", " . ($ele_est->estimate('Date', $unix, 'Reading_2') - $lowest['ele'][2]) . ", " . (($ele_est->estimate('Date', $unix, 'Reading_1') - $lowest['ele'][1]) + ($ele_est->estimate('Date', $unix, 'Reading_2') - $lowest['ele'][2])) . "]\n";
    $unix = $unix + (60 * 60 * 24 * 7 * 4.3);
}
echo ",['" . date("d.m.y", $t) . "', " . ($ele_est->estimate('Date', $t, 'Reading_1') - $lowest['ele'][1]) . ", " . ($ele_est->estimate('Date', $t, 'Reading_2') - $lowest['ele'][2]) . ", " . (($ele_est->estimate('Date', $t, 'Reading_1') - $lowest['ele'][1]) + ($ele_est->estimate('Date', $t, 'Reading_2') - $lowest['ele'][2])) . "]\n";
?>
            ]);

            var options = {
                title: 'Water Usage'
            };

            var chart = new google.visualization.LineChart(document.getElementById('ele_chart_div'));
            chart.draw(data, options);
        }
        </script>
    </body>
</html>
