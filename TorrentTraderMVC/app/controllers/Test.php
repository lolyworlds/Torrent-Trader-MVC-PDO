<?php
class Test extends Controller
{
    public function __construct()
    {
        //$this->loggedIn();
        //$this->ipBanned();
    }

    // Function that calculates the Hours Minutes Seconds of a Timestamp
    public static function index()
    {
        // 928202012
        // 2018-05-04 15:22:50

        echo 'string<br>';
        
        echo 'Function that calculates the Hours Minutes Seconds of a Timestamp -string<br>';
        echo TimeDate::mkprettytime('928202012').'<br>';

        echo 'Time to Time Conversion Function With Time Zone -date <br>';
        echo TimeDate::gmtime().'<br>';

        echo 'function Week Day Hour Minute Second According to a Timestamp -string<br>';
        echo TimeDate::get_elapsed_time('928202012').'<br>';

        echo 'get date - date<br>';
        echo TimeDate::get_date_time('928202012').'<br>';

        echo 'Function that calculates the Hours Minutes Seconds of a Timestamp -now & string<br>';
        echo TimeDate::utc_to_tz().'<br>';
        //TimeDate::utc_to_tz('928202012').'<br>';


        echo 'date<br>';

        echo 'Function That Returns The UNIX Timestamp Of A Date -date<br>';
        echo TimeDate::sql_timestamp_to_unix_timestamp('2018-05-04 15:22:50').'<br>';

        echo 'Function That Returns A Timestamp According To The Members Time Zone -date<br>';
        echo TimeDate::utc_to_tz_time().'<br>';
        echo TimeDate::utc_to_tz_time('2018-05-04 15:22:50').'<br><br>';

        echo '////////////////////////////////////////////////////<br>';
        echo 'lets try objects <br><br>';
        echo 'date =   / time =  <br><br>';

        echo 'timestamp to date <br><br>';

        echo 'date to stamp <br><br>';
        
        echo 'timestamp  with timezone<br><br>';

        echo 'date   with timezone<br><br>';

        echo 'timestamp to date with timezone<br><br>';

        echo 'date to stamp  with timezone<br><br>';


        echo 'add - subtract 2000-01-01<br>';
$date = new DateTime('2000-01-01');
$date->add(new DateInterval('P1M'));
echo $date->format('Y-m-d H:i:s') . "\n";
$date = new DateTime('2000-01-01');
$date->add(new DateInterval('S1M'));
echo $date->format('Y-m-d H:i:s') . "\n";
    }

}