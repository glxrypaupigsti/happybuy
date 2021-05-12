<?php
    
    date_default_timezone_set("Asia/Shanghai");
    $time = time();
    
    $month_name = array('U', '1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月');
    $date_array = array();
    
    $select_date = strtotime($_COOKIE['deliver_date']);
    $month = intval(date('m', $select_date));
    $day = intval(date('j', $select_date));
    $select_date_str = $month_name[$month] . $day . '日';
    
    $start_deliver_time = 14;
    $end_deliver_time = 18;
    $buffer_time = 60;//minutes
    $col2 = 1;
    
    if ($select_date > strtotime(date('Y-m-d', $time))) {
        $start_hour = $start_deliver_time;
    } else {
        $this_hour = idate('H');
        $this_min = idate('i');
        if (($this_min+$buffer_time) >= 60) $this_hour++;
        $start_hour = ($this_hour < $start_deliver_time)? $start_deliver_time: $this_hour;
    }
    $day = array(
                 'id' => '1',
                 'name' => $select_date_str,
                 );
    for ($j = $start_hour; $j < $end_deliver_time; $j++,$col2++) {
        $day['child'][] = array(
                                'id' => strval($col2),
                                'name' => $j.':00-'. ($j+1).':00'
                                );
    }
    
    
    // deliver time for a day: 14:00 -- 18:00, 1 hour as interval
    /*
    $start_deliver_time = 14;
    $end_deliver_time = 18;
    $buffer_time = 60;//minutes
    
    $this_year = idate('Y');
    $this_month = idate('m');
    $this_days = idate('t');
    $today = idate('d');
    $this_hour = idate('H');
    $this_min = idate('i');
    if (($this_min+$buffer_time)>=60) $this_hour++;
    $col0 = $col1 = $col2 = 1;
    
    $this_month_data = array(
                             'id' => strval($col0),
                             'name' => $month_name[$this_month-1],
                             );
    // today
    if (($this_hour < $end_deliver_time)) {
        $day = array(
                     'id' => strval($col1),
                     'name' => strval($today).'日',
                     );
        $col1++;
        $start_hour = ($this_hour < $start_deliver_time)?$start_deliver_time:$this_hour;
        for ($j=$start_hour; $j<$end_deliver_time; $j++, $col2++) {
            $day['child'][] = array(
                                    'id' => strval($col2),
                                    'name' => $j.':00-'. ($j+1).':00'
                                    );
        }
        $this_month_data['child'][] = $day;
    }
    // remain days of this month
    for ($i=$today+1; $i <= $this_days; $i++, $col1++) {
        $day = array(
                     'id' => strval($col1),
                     'name' => strval($i).'日',
                     );
        for ($j=$start_deliver_time; $j<$end_deliver_time; $j++,$col2++) {
            $day['child'][] = array(
                                    'id' => strval($col2),
                                    'name' => $j.':00-'. ($j+1).':00'
                                    );
        }
        $this_month_data['child'][] = $day;
        $this_month_data['selec'] = $select_date;
    }
    $date_array[] = $this_month_data;
    
    // next month
    $col0++; $col1 = $col2 = 1;
    $next_year = $this_year;
    $next_month = $this_month+1;
    if ($next_month > 12) {
        $next_year = $this_year + 1;
        $next_month = 1;
    }
    $next_days = cal_days_in_month(CAL_GREGORIAN, $next_month, $next_year);
    
    $next_month_data = array(
                             'id' => strval($col0),
                             'name' => $month_name[$next_month-1],
                             );
    for ($i=1; $i <= $next_days; $i++, $col1++) {
        $day = array(
                     'id' => strval($col1),
                     'name' => strval($i).'日',
                     );
        for ($j=$start_deliver_time; $j<$end_deliver_time; $j++, $col2++) {
            $day['child'][] = array(
                                    'id' => strval($col2),
                                    'name' => $j.':00-'. ($j+1).':00'
                                    );
        }
        $next_month_data['child'][] = $day;
        
    }
     */
    $date_array[] = $day;
     
    echo htmlspecialchars(json_encode(array('data' => $date_array)), ENT_NOQUOTES);

?>