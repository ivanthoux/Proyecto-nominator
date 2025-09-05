<?php 

function calc_total_list ($list,$field){
	$total  = 0;
	foreach ($list as $item) {
		$total = $total + $item[$field];
	}
	return $total;
}

function get_days_by_range($first, $last, $step = '+1 day', $output_format = 'd/m/Y' ) {

    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);

    while( $current <= $last ) {

        $dates[] = date($output_format, $current);
        $current = strtotime($step, $current);
    }

    return $dates;
}

function create_array_value_from_days_range ($array_days,$array_value){
	$response = array();

	foreach ($array_days as $day) {
		$sum = 0;
		foreach ($array_value as $value) {
			if ($day == $value['date']){
				$sum = intval($value['value']);
			}
		}
		if ($sum == 0){
			$response[] = 0;
		}else{
			$response[] = $sum;
		}
	}

    return $response;
}