<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 6525 ae190c43422d2b2a7c6934e00e8b8686
  * Envato: 82b7b3b1-2e56-457f-aac2-ff4d410e1e54
  * Package Date: 2014-10-02 15:05:08 
  * IP Address: 203.125.187.204
  */

header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename="cal.ics"');

$alerts = array();
$foo = 'calendar';
$results = handle_hook("home_alerts",$foo,true);
if (is_array($results)) {
    foreach ($results as $res) {
        if (is_array($res)) {
            foreach ($res as $r) {
                $alerts[] = $r;
            }
        }
    }
}

echo 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Ultimate Client Manager/Calendar Plugin v1.0//EN
CALSCALE:GREGORIAN
X-WR-CALNAME:'._l('Alerts').'
X-WR-TIMEZONE:UTC
';


//$local_timezone_string = date('e');
//$local_timezone = new DateTimeZone($local_timezone_string);
//$local_time = new DateTime("now", $local_timezone);
$timezone_hours = module_config::c('timezone_hours',0);


if (count($alerts)) {
    $x = 0;
    foreach ($alerts as $alert) {

        $time = strtotime($timezone_hours.' hours',strtotime($alert['date']));
        echo 'BEGIN:VEVENT
UID:'.md5($alert['name'].$alert['link']).'@ultimateclientmanager.com
';
        // work out the UTC time for this event, based on the timezome we have set in the configuration options

        /*DTSTART;VALUE=DATE:'.date('Ymd',$time).'
DTEND;VALUE=DATE:'.date('Ymd',strtotime('+1 day',$time)).'*/

        echo 'DTSTAMP:'.date('Ymd').'T090000Z
DTSTART:'.date('Ymd',$time).'T090000Z
DTEND:'.date('Ymd',strtotime('+1 day',$time)).'T010000Z
SUMMARY: '.$alert['item'].(isset($alert['name']) ? ' - '.$alert['name'] : '').'
DESCRIPTION:'._l('Alert Item: %s',$alert['name']).'<br><a href="'.$alert['link'].'">'._l('Link').'</a>
END:VEVENT
';
    }
}


echo 'END:VCALENDAR';

