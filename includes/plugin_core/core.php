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



class module_core extends module_base{

    public static function can_i($actions,$name=false,$category=false,$module=false){
        if(!$module)$module=__CLASS__;
        return parent::can_i($actions,$name,$category,$module);
    }
	public static function get_class() {
        return __CLASS__;
    }
	public function init(){
		$this->module_name = "core";
		$this->module_position = 0;

        $this->version = 2.129;
        //2.129 - 2014-08-24 - hours:minutes fix
        //2.128 - 2014-08-20 - tax_decimal_places and tax_trim_decimal
        //2.127 - 2014-07-31 - php 5.3 closure check
        //2.126 - 2014-07-22 - hours:minutes task formatting fix
        //2.125 - 2014-07-16 - translation fixes for success/error messages
        //2.124 - 2014-06-09 - hours:minutes task formatting
        //2.123 - 2014-04-10 - currency cache fix
        //2.122 - 2014-02-12 - number_trim_decimals fix
        //2.121 - 2014-02-06 - number_trim_decimals advanced settings
        //2.12 - 2013-05-08 - fix for static error on some php versions
        //2.11 - 2013-04-27 - fix for number rounding with international currency formats
        //2.1 - 2013-04-21 - initial release

	}

}

/* placeholder module to contain various functions used through out the system */
@include_once 'includes/functions.php'; // so we don't re-create old functions.


if(!function_exists('set_message')){
    function set_message($message){
        if(!isset($_SESSION['_message']))$_SESSION['_message']=array();
        $_SESSION['_message'][] = _l($message);
    }
}
if(!function_exists('set_error')){
    function set_error($message){
        if(!isset($_SESSION['_errors']))$_SESSION['_errors']=array();
        foreach($_SESSION['_errors'] as $existing_error){
            if($existing_error == _l($message)){
                return false;
            }
        }
        $_SESSION['_errors'][] = _l($message);
        return true;
    }
}
if(!function_exists('number_in')){
    function number_in($value){
        // convert a number in this format (eg: 1.234,56) to a system compatible format (eg: 1234.56)
        // only modify this number if it isn't already in db friendly format:
        $decimal_separator = module_config::c('currency_decimal_separator','.');
        $thounds_separator = module_config::c('currency_thousand_separator',',');
        $dec_positions = (int)module_config::c('currency_decimal_places',2);
        if( !is_numeric($value) || (float)$value != @number_format($value,$dec_positions,'.','')){
            //echo "Converting $value into ";
            $value = str_replace($thounds_separator,'',$value);
            if($decimal_separator!='.'){
                $value = str_replace($decimal_separator,'.',$value);
            }
            //echo "$value <br>";
        }
        return $value;
    }
}

if(!function_exists('number_out')){
    function number_out($value, $trim=false, $dec_positions = false){
        $decimal_separator = module_config::c('currency_decimal_separator','.');
        $thounds_separator = module_config::c('currency_thousand_separator',',');
        $dec_positions = $dec_positions === false ? module_config::c('currency_decimal_places',2) : $dec_positions;
        $num = number_format($value,$dec_positions,$decimal_separator,$thounds_separator);
        if($trim && module_config::c('number_trim_decimals',1)){
            $num = preg_replace('#('.preg_quote($decimal_separator,'#').'[1-9])0+#','$1',$num);
            $num = preg_replace('#'.preg_quote($decimal_separator,'#').'0+#','',$num);
        }
        return $num;
    }
}

if(!function_exists('decimal_time_in')){
	// we're saving the time from the user. convert it into a decimal for database storage if needed.
    function decimal_time_in($value){
	    // are times treated in base 60 or 100
	    if(module_config::c('task_time_as_hours_minutes',1) && strpos($value,':') !== false){
		    // if the time is 1:40 it means 1 hour and 40 minutes.
		    // if the time is 1:80 then we round it to 2 hours and 20 minutes
		    $bits = explode(':',$value);
		    $hours = (int)$bits[0];
		    $minutes = isset($bits[1]) ? preg_replace('#[^0-9]#','',$bits[1]) : 0;
		    if($minutes >= 60){
			    $hours++;
			    $minutes = $minutes-60;
		    }
		    $value = number_out($hours + ($minutes / 60));
	    }
        return $value;
    }
}

if(!function_exists('decimal_time_out')){
    function decimal_time_out($value){
        if(module_config::c('task_time_as_hours_minutes',1)){
	        $bits = explode('.',$value);
		    $hours = (int)$bits[0];
		    $minutes = isset($bits[1]) ? preg_replace('#[^0-9]#','',$bits[1]) : 0;
	        $minutes = round((".".$minutes)*60);
	        if($minutes <= 0)$minutes = '00';
	        else if($minutes < 10)$minutes = '0'.$minutes;
	        else if($minutes >= 60)$minutes = '00';
	        else $minutes = str_pad($minutes,2,'0',STR_PAD_RIGHT);
	        $value = $hours . ':' . $minutes;
        }
        return $value;
    }
}

if(!function_exists('dollar')){
    function dollar($number,$show_currency=true,$currency_id=false){
        return currency(number_out($number, false),$show_currency,$currency_id);
    }
}
if(!function_exists('currency')){
    function currency($data,$show_currency=true,$currency_id=false){
	    static $currency_cache = array();
        // find the default currency.
        if(!defined('_DEFAULT_CURRENCY_ID')){
            $default_currency_id = module_config::c('default_currency_id',1);
            foreach(get_multiple('currency','','currency_id') as $currency){
                if($currency['currency_id']==$default_currency_id){
                    define('_DEFAULT_CURRENCY_ID',$default_currency_id);
                    define('_DEFAULT_CURRENCY_SYMBOL',$currency['symbol']);
                    define('_DEFAULT_CURRENCY_LOCATION',$currency['location']);
                    define('_DEFAULT_CURRENCY_CODE',$currency['code']);
                }
            }
        }
        $currency_symbol = defined('_DEFAULT_CURRENCY_SYMBOL') ? _DEFAULT_CURRENCY_SYMBOL : '$';
        $currency_location = defined('_DEFAULT_CURRENCY_LOCATION') ? _DEFAULT_CURRENCY_LOCATION : 1;
        $currency_code = defined('_DEFAULT_CURRENCY_CODE') ? _DEFAULT_CURRENCY_CODE : 'USD';
        $show_name = false;

        if($currency_id && defined('_DEFAULT_CURRENCY_ID') && $currency_id != _DEFAULT_CURRENCY_ID){
            if($show_currency){
                $show_name = true;
            }
            $currency = isset($currency_cache[$currency_id]) ? $currency_cache[$currency_id] : get_single('currency','currency_id',$currency_id);
	        $currency_cache[$currency_id] = $currency;
            if($currency){
                $currency_symbol = $currency['symbol'];
                $currency_location = $currency['location'];
                $currency_code = $currency['code'];
            }
            /*
            foreach(get_multiple('currency','','currency_id') as $currency){
                if($currency['currency_id']==$currency_id){
                    $currency_symbol = $currency['symbol'];
                    $currency_location = $currency['location'];
                    $currency_code = $currency['code'];
                }
            }*/
        }
        /*$currency_location = module_config::c('currency_location','before');
        $currency_code = module_config::c('currency','$');
        $currency_name = module_config::c('currency_name','USD');*/

        switch(strtolower($currency_symbol)){
            case "yen":
                $currency_symbol = '&yen;';
                break;
            case "eur":
                $currency_symbol = '&euro;';
                break;
            case "gbp":
                $currency_symbol = '&pound;';
                break;
            default:
                break;
        }

        if(!$show_currency){
            $currency_symbol = '';
        }
        if(module_config::c('currency_show_code_always',0)){
            $data .= ' '.$currency_code;
        }else if($show_name && module_config::c('currency_show_non_default',1)){
            $data .= ' '.$currency_code;
        }

        switch($currency_location){
            case 'after':
            case 0:
                return $data.$currency_symbol;
                break;
            case 1:
            default:
                return $currency_symbol.$data;
        }
    }
}
if(!function_exists('is_closure')){
    function is_closure($t) {
        return is_object($t) && ($t instanceof Closure);
    }
}