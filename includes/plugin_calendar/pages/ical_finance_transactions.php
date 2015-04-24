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

$search = array(); // todo - pass any ical options through to search
$recent_transactions = module_finance::get_finances($search);

// are we showing income or expense?


echo 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Ultimate Client Manager/Calendar Plugin v1.0//EN
CALSCALE:GREGORIAN
X-WR-CALNAME:'.(isset($options['credit'])&&$options['credit'] ? _l('Credit').' ':'').(isset($options['debit'])&&$options['debit'] ? _l('Debit').' ':'').'Financial Transactions
X-WR-TIMEZONE:UTC
';

//$local_timezone_string = date('e');
//$local_timezone = new DateTimeZone($local_timezone_string);
//$local_time = new DateTime("now", $local_timezone);
$timezone_hours = module_config::c('timezone_hours',0);

foreach($recent_transactions as $recent_transaction){

    if($recent_transaction['credit']<=0 && $recent_transaction['debit']<=0){
        continue; // skip empty ones.
    }
    if(!isset($options['credit'])){
        // we dont want to show credit items.
        if($recent_transaction['credit'] > 0){
            continue;
        }
    }
    if(!isset($options['debit'])){
        // we dont want to show credit items.
        if($recent_transaction['debit'] > 0){
            continue; 
        }
    }

    $time = strtotime($timezone_hours.' hours',strtotime($recent_transaction['transaction_date']));
    echo 'BEGIN:VEVENT
UID:'.md5(mt_rand(1,100)).'@ultimateclientmanager.com
';

    if(strlen($recent_transaction['name'])){
        $recent_transaction['name'] = '('.$recent_transaction['name'].")";
    }
    // work out the UTC time for this event, based on the timezome we have set in the configuration options

    echo 'DTSTAMP:'.date('Ymd').'T090000Z
DTSTART;VALUE=DATE:'.date('Ymd',$time).'
DTEND;VALUE=DATE:'.date('Ymd',strtotime('+1 day',$time)).'
SUMMARY:'.($recent_transaction['credit']>0 ? '+'.dollar($recent_transaction['credit']) : '').($recent_transaction['debit']>0 ? '-'.dollar($recent_transaction['debit']) : '')." ".$recent_transaction['name'].'
DESCRIPTION:'.preg_replace('#[\r\n]+#','<br>',$recent_transaction['description']).' <br><a href="'.$recent_transaction['url'].'">'._('Open Link').'</a>
END:VEVENT
';
}
echo 'END:VCALENDAR';

