<?php 
    /**
     * VER: 1.0.0 (Updated: 02/21/2020)
     *
     * Prerequisites: PHP7 (tested 02/21/2020)
     * Maintainers: Brandon Elliott
     *
     *	For the latest documentation and best practices: please visit https://api.optguard.com/doc
     */
    define('OPTGUARD_ACCESS_KEY', get_option(esc_attr('optguard_field_access_key')));
    define('OPTGUARD_SECRET_KEY', get_option(esc_attr('optguard_field_secret_key')));
    define('API_REVISION', 'v1'); // Which API revision?
	
    /************************************************************************
    |	Usage: Enter your access and secret keys, from your optGuard        |
    |          account dashboard, into the fields above.                    |
    |                                                                       |
    |          Include this file within your PHP website template OR        |
    |          copy all of this code within the PHP tags and put it         |
    |          into your website template file, directly.*                  |
    |                                                                       |
    |   *For WordPress, this means putting it into a functions.php          |
    |   *If you have a static HTML site, host this script where it can      |
    |    be accessed by your website and use the following Javascript,      |
    |    in the <HEAD> of all pages, to trigger the PHP script.             |
    |                                                                       |
    |   <script defer src="https://[domain.tld]/fraudwatch.php"></script>   |
    |                                                                       |
    ************************************************************************/	

    /******************************************** 
    |	!! DO NOT CHANGE BELOW THIS LINE !!     |
    |	...unless you know what you are doing   |
    ********************************************/	
	
    // optGuard defines.
    define('OPTGUARD',                          'optguard');                            // WARNING!! Changing this could break prior installs!
    define('OPTGUARD_OPTIONS',                  'optguard_options');                    // WARNING!! Changing this could break prior installs!
    define('OPTGUARD_SECTION_CREDENTIALS',      'optguard_section_credentials');        // WARNING!! Changing this could break prior installs!
    define('OPTGUARD_CUSTOM_DATA',              'optguard_custom_data');                // WARNING!! Changing this could break prior installs!
    define('OPTGUARD_LABEL_FOR',                'label_for');                           // WARNING!! Changing this could break prior installs!
	define('OPTGUARD_VERSION',					'1.0.0');
	define('OPTGUARD_WP_VERSION',				'4.9');
	define('OPTGUARD_PHP_VERSION',				'5.6');
    define('VER',								'1.0.0');                               // allows us to identify known bugs and version control; DONT touch!
    define('PROTOCOL', 'https://');	// Probably shouldn't touch! ;)
    error_reporting(E_ALL ^ E_NOTICE); // Avoid notice outputs for default PHP config
    
    $referrer = getReferrer($_SERVER, $HTTP_X_FORWARDED_FOR);

    $aArgs['ip'] = $referrer['ip'];
    $aArgs['access_key'] = ACCESS_KEY;
    $aArgs['secret_key'] = SECRET_KEY;
    $aArgs['ver'] = VER;

    $sResp = _fileGetContent( PROTOCOL.'api.optguard.com/'.API_REVISION.'/check?', $aArgs );
    // $sResp = _fileGetContent( 'http://localhost:3000/'.API_REVISION.'/check?', $aArgs ); // For optGuard local DEV testing ;)

    // print_r($referrer); print_r($sResp);

	/**
    *   Gets file content by URL. 
    *   Attempts in priority of WP method, then cURL method, then legacy PHP method.
	*/
	function _fileGetContent($sUrl, $aArgs = array(), $sResult=null) {
        if (function_exists('wp_remote_get')) {
            $sResult = wp_remote_get( $sUrl, $aArgs );
        }
        else if (function_exists('curl_init')) {
            $rConnect = curl_init();
			curl_setopt($rConnect, CURLOPT_URL, $sUrl . http_build_query($aArgs, '', '&'));
			curl_setopt($rConnect, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($rConnect, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($rConnect, CURLOPT_HEADER, 0); // must be 0 or else headers will break SimpleXML parsing
			// Without the two options below, some servers will report cURL error 51: 
			//		SSL: no alternative certificate subject name matches target host name 'api.optguard.com'
			// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); 
			// curl_setopt($process, CURLOPT_SSL_VERIFYPEER, FALSE);

			$sResult = curl_exec($rConnect);
			curl_close($rConnect);
		}
		else
			$sResult = @file_get_contents($sUrl . http_build_query($aArgs, '', '&'));

		return $sResult;
    }

    /*******************************************************
        Get visitor referrer
    *******************************************************/
    function getReferrer($server, $HTTP_X_FORWARDED_FOR){
        if ( !empty($server['HTTP_X_REAL_IP']) ) {                                      // Using optGuard's recommended server configuration
            $vip = $server['HTTP_X_REAL_IP'];
        } else{ $vip = $server['REMOTE_ADDR']; }										// Visitor's remote IP
        
        if ( !empty($server['HTTP_REFERER']) ) {$referrer = $server['HTTP_REFERER'];}	// if they were referred from another page/site OR
        elseif ( !empty($HTTP_X_FORWARDED_FOR) ) {$referrer = $HTTP_X_FORWARDED_FOR;}	// if they are behind a proxy OR
        elseif ( !empty($server['HTTP_HOST']) ) {$referrer = $server['HTTP_HOST'];} 	// if they are on OUR site (or directly accessing this script)
        else { $referrer=$vip; }

        return array('ip'=>$vip, 'referrer'=>$referrer);
	}
