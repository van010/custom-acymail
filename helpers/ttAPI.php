<?php
/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik Künnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik Künnemann
 */

defined('_JEXEC') or die;

//jimport( 'joomla.application.helper' );

class ttAPI {

	public $_allow = array();
	public $_content_type = "application/json";
	public $_request = array();

	// Log in User into system
	public static function getToken() {

		$tt_app_secret = ttConfig::get('tt_app_secret');

		$data = 'grant_type=user_app&app_key='. $tt_app_secret;

		$environment = ttConfig::get('tt_environment');
		$app_company = ttConfig::get('tt_company');

		$header = array('requestId: token-'. $app_company .'--4037847b-de40-46c8-b55e-66186d657614');

		$result = self::callAPI('POST', 'https://apigateway.trade.tt/ttid/'. $environment .'/token', $data, false, $header);

		return $result;
	}

	// Get positions
	public static function getPositions($nextPageKey = false, $accountId = false) {

		$environment = ttConfig::get('tt_environment');
		$app_company = ttConfig::get('tt_company');

		$params = array('requestId=positions-'. $app_company .'--4037847b-de40-46c8-b55e-66186d657614');
		if ($nextPageKey) $params[] = 'nextPageKey='.$nextPageKey;
		if ($accountId) $params[] = 'accountId='.$accountId;
        
		$result = self::callAPI('GET', 'https://apigateway.trade.tt/ttmonitor/'. $environment .'/position?'. implode('&', $params) );

		return $result;
	}
	
	// Get accounts
	public static function getAccounts($nextPageKey = false, $accountId = false) {

		$environment = ttConfig::get('tt_environment');
		$app_company = ttConfig::get('tt_company');

		$params = array('requestId=accounts-'. $app_company .'--4037847b-de40-46c8-b55e-66186d657614');
		if ($nextPageKey) $params[] = 'nextPageKey='.$nextPageKey;

		$result = self::callAPI('GET', 'https://apigateway.trade.tt/ttaccount/'. $environment .'/accounts?'. implode('&', $params) );

		return $result;
	}
	
	// Get account
	public static function getAccount($accountId) {

		$environment = ttConfig::get('tt_environment');
		$app_company = ttConfig::get('tt_company');

		$params = array('requestId=account-'. $app_company .'--4037847b-de40-46c8-b55e-66186d657614');
		if ($nextPageKey) $params[] = 'nextPageKey='.$nextPageKey;

		$result = self::callAPI('GET', 'https://apigateway.trade.tt/ttaccount/'. $environment .'/account/'.$accountId.'?'. implode('&', $params) );

		return $result;
	}
	
	// Get instrument
	public static function getInstrument($instrumentId) {

		$environment = ttConfig::get('tt_environment');
		$app_company = ttConfig::get('tt_company');

		$params = array('requestId=instrument-'. $app_company .'--4037847b-de40-46c8-b55e-66186d657614');

		$result = self::callAPI('GET', 'https://apigateway.trade.tt/ttpds/'. $environment .'/instrument/'.$instrumentId.'?'. implode('&', $params) );

		return $result;
	}
	
	// Get product
	public static function getProduct($productId) {

		$environment = ttConfig::get('tt_environment');
		$app_company = ttConfig::get('tt_company');

		$params = array('requestId=product-'. $app_company .'--4037847b-de40-46c8-b55e-66186d657614');

		$result = self::callAPI('GET', 'https://apigateway.trade.tt/ttpds/'. $environment .'/product/'.$productId.'?'. implode('&', $params) );

		return $result;
	}



	// Method: POST, PUT, GET etc
	// Data: array("param" => "value") ==> index.php?param=value
	public static function callAPI($method, $url, $data = false, $auth = true, $header = array()) {

		$app_key = ttConfig::get('tt_app_key');
		
		$header = array_merge(array('x-api-key: '. $app_key), $header);

		if ($auth) {
			$header[] = 'Authorization: Bearer '. ttHelper::getToken()->token;
		}


		
		$curl = curl_init();

		switch ($method) {
			case "POST":
				curl_setopt($curl, CURLOPT_POST, 1);

				if ($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				break;
			case "PATCH":
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');

				if ($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				break;
			case "DELETE":
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');

				if ($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				break;
			case "PUT":
				curl_setopt($curl, CURLOPT_PUT, 1);
				break;
			default:
				if ($data)
					$url = sprintf("%s?%s", $url, http_build_query($data));
		}

		// Optional Authentication:
		//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($curl, CURLOPT_USERPWD, "username:password");

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);

		$result = curl_exec($curl);
		//ttdebug('API Request', curl_getinfo($curl));
		//ttdebug('API Response', $result);

		curl_close($curl);

		return $result;
	}
}