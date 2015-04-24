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
if(!module_social::can_i('edit','Facebook','Social','social')){
    die('No access to Facebook accounts');
}

$social_facebook_id = isset($_REQUEST['social_facebook_id']) ? (int)$_REQUEST['social_facebook_id'] : 0;
$facebook = new ucm_facebook_account($social_facebook_id);
if($facebook->get('social_facebook_id')){

	// check for postback from our UCM facebook code handling script
	if(isset($_REQUEST['c'])) {
		$response = isset( $_REQUEST['c'] ) ? @json_decode( $_REQUEST['c'], true ) : false;
		//print_r($response);
		if ( ! $response || ! $response['code'] )
			die( 'Failed to get code from API, please press back and try again.' );
		$code = $response['code'];
		// https://graph.facebook.com/oauth/access_token?code=...&client_id=...&redirect_uri=...&machine_id= ...
		$url        = 'https://graph.facebook.com/oauth/access_token?code=' . urlencode( $code ) . '&redirect_uri=' . urlencode( 'http://ultimateclientmanager.com/api/facebook/logindone.php' ) . '&client_id=608055809278761';
		$machine_id = isset( $response['machine_id'] ) ? $response['machine_id'] : false;
		if ( $machine_id ) {
			$url .= '&machine_id=' . $machine_id;
		}
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$data   = curl_exec( $ch );
		$result = @json_decode( $data, true );
		if ( ! $result || ! isset( $result['access_token'] ) ) {
			die( 'Failed to get client access token from API, please press back and try again: '.$data );
		}
		$machine_id   = isset( $result['machine_id'] ) ? $result['machine_id'] : $machine_id;
		// todo - save machine id along with access_token, use in future requests.
		$facebook->update('facebook_token',$result['access_token']);
		$facebook->update('machine_id',$machine_id);
		// success!

		// now we load in a list of facebook pages to manage and redirect the user back to the 'edit' screen where they can continue managing the account.
		$facebook->graph_load_available_pages();
		redirect_browser(module_social_facebook::link_open($social_facebook_id));
	}
?>
<iframe src="http://ultimateclientmanager.com/api/facebook/login.php?return=<?php echo urlencode(module_social_facebook::link_open($social_facebook_id,false,false,'facebook_account_connect')); ?>&codes=<?php echo urlencode(htmlspecialchars(module_config::c('_installation_code')));?>" frameborder="0" style="width:100%; height:600px; background: transparent" ALLOWTRANSPARENCY="true"></iframe>
<?php } ?>