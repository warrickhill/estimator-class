<?php
include_once 'estimate.class.php';

// example array. we will be using the unix time stamp as the base for calculating Reading 1 and 2.
$array = array(
    0 =>
    array(
        'Date' => '1346630400',
        'Reading_1' => '10422',
        'Reading_2' => '9456',
    ),
    1 =>
    array(
        'Date' => '1350864000',
        'Reading_1' => '10651',
        'Reading_2' => '9551',
    ),
    72 =>
    array(
        'Date' => '1351382400',
        'Reading_1' => '10701',
        'Reading_2' => '9564',
    ),
    12 =>
    array(
        'Date' => '1366070400',
        'Reading_1' => '13205',
        'Reading_2' => '10336',
    ),
    4 =>
    array(
        'Date' => '1367366400',
        'Reading_1' => '13264',
        'Reading_2' => '10370',
    ),
    9 =>
    array(
        'Date' => '1369008000',
        'Reading_1' => '13365',
        'Reading_2' => '10418',
    ),
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
        <?php
        //loop through some random dates estmiating as we go
        $unix = time();
        for ($i = strtotime("-1 year", $unix); $unix > $i; $unix = strtotime("-1 month", $unix)) {
            $ele_est->estimate('Date', $unix, 'Reading_1');
            $ele_est->estimate('Date', $unix, 'Reading_2');
        }
        $data = $ele_est->sort_array($ele_est->estmated_output, 'Date');
        var_dump($data);
        // Now display the estimates on a chart
        ?>
        <div id="ele_chart_div" style="width: 100%;"></div>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Date', 'Reading 1', 'Reading 2']
<?php
foreach ($data as $value) {
    echo ",['" . date("d.m.y", $value['Date']) . "', " . $value['Reading_1'] . ", " . $value['Reading_2'] . "]\n";
}
?>
        ]);

        var options = {
            curveType: "function",
            title: 'The Chart'
        };

        var chart = new google.visualization.LineChart(document.getElementById('ele_chart_div'));
        chart.draw(data, options);
    }
        </script>
    </body>
</html>
