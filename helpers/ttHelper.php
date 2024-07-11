<?php

/**
 * @package    tradingtechnologies
 * @copyright  Copyright (C) 2020 Maik Künnemann. All rights reserved.
 * @license    GNU General Public License http://www.gnu.org/copyleft/gpl.html
 * @author     Maik Künnemann
 */


defined('_JEXEC') or die;

use AcyMailing\Helpers\MailerHelper;



jimport('joomla.application.helper');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('JPATH_TT') or define('JPATH_TT', JPATH_ROOT . DS . 'components' . DS . 'com_tradingtechnologies');
defined('JPATH_TT_ADMINISTRATOR') or define('JPATH_TT_ADMINISTRATOR', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_tradingtechnologies');

if (!class_exists('ttConfig')) require JPATH_TT_ADMINISTRATOR . DS . 'helpers' . DS . 'config.php';
if (!class_exists('ttJsApi')) require(JPATH_TT_ADMINISTRATOR . DS . 'helpers' . DS . 'jsapi.php');
if (!class_exists('ttAPI')) require(JPATH_TT_ADMINISTRATOR . DS . 'helpers' . DS . 'ttAPI.php');

$config = new ttConfig();

class ttHelper
{

	public static function refreshAccount()
	{

		$response = json_decode(ttAPI::auth());

		if (!isset($response->data)) {
			ttdebug('can\'t get account data');
			return false;
		}

		$data = array();
		$data['name'] = 'user';
		$data['params'] = $response;

		$model = self::getModel('account');
		$stored = $model->save($data);
		if ($stored) {
			ttdebug('account data stored');
		} else {
			ttdebug('Error: account data not stored');
		}

		return true;
	}

	public static function getAccount()
	{

		$model = self::getModel('account');
		$account = $model->getAccount();

		if (empty($account)) {
			$account = self::refreshAccount();
		}

		return $account;
	}

	public static function getNewToken()
	{

		$response = json_decode(ttAPI::getToken());

		if (!isset($response->access_token)) {
			ttdebug('can\'t get token');
			return false;
		}

		self::storeToken($response);
	}

	public static function storeToken($token)
	{

		$data = array();
		$data['name'] = 'token';
		$data['params'] = $token;

		$model = self::getModel('credentials');
		$stored = $model->save($data);
		if ($stored) {
			ttdebug('token stored');
		} else {
			ttdebug('Error: token not stored');
		}

		$r_token = new StdClass;
		$r_token->token = $token->access_token;
		$r_token->exp = time() + $token->seconds_until_expiry;

		//return $token;
	}

	public static function getToken()
	{

		$model = self::getModel('credentials');
		$token = $model->getToken();

		if (empty($token->token)) {
			$token = self::getNewToken();
		}

		return $token;
	}

	public static function getPositions($nextPageKey = false, $account_id = false)
	{

		$response = json_decode(ttAPI::getPositions($nextPageKey, $account_id));

		//$response = json_decode('{"lastPage": "true", "positions": [{"avgBuy": 1540175.0, "avgSell": 1539750.0, "buyFillQty": 2.0, "buyWorkingQty": 0.0, "instrumentId": "3229355184209316523", "netPosition": 0.0, "openAvgPrice": 0.0, "pnl": -170.0000025331974, "pnlPrice": 1541300.0, "pnlPriceType": "MIDPOINT", "realizedPnl": -170.0000025331974, "sellFillQty": 2.0, "sellWorkingQty": 0.0, "sodNetPos": 0.0, "sodPriceType": "NONE", "accountId": 1256396}, {"avgBuy": 452000.0, "avgSell": 451825.0, "buyFillQty": 8.0, "buyWorkingQty": 0.0, "instrumentId": "17126231917098338340", "netPosition": 0.0, "openAvgPrice": 0.0, "pnl": -700.0, "pnlPrice": 452437.5, "pnlPriceType": "MIDPOINT", "realizedPnl": -700.0, "sellFillQty": 8.0, "sellWorkingQty": 0.0, "sodNetPos": 0.0, "sodPriceType": "NONE", "accountId": 1256396}], "status": "Ok"}');

		if (!isset($response->positions)) {
			ttdebug('can\'t get positions');
			return false;
		} else if (count($response->positions) < 1) {
				ttdebug('positions are empty');
			return false;
		}

		$positions = is_array($response->positions) ? $response->positions : array($response->positions);
		
		$model = self::getModel('positions');
		
		/*// REMOVE ME
		// ttdebug('Debugging...');
		$dummy = new stdClass();
		$dummy->avgBuy = 999;
		$dummy->avgSell = 999;
		$dummy->buyFillQty = 999;
		$dummy->buyWorkingQty = 999;
		$dummy->instrumentId = 5765452581318706671;
		$dummy->netPosition = 0;
		$dummy->openAvgPrice = 888;
		$dummy->pnl = 999;
		$dummy->pnlPrice = 999;
		$dummy->pnlPriceType = '999';
		$dummy->realizedPnl = 999;
		$dummy->sellFillQty = 999;
		$dummy->sellWorkingQty = 999;
		$dummy->sodNetPos = 999;
		$dummy->sodPriceType = '999';
		$dummy->accountId = 1248314;
		// $positions = [$dummy]; // uncomment when debugging
		// REMOVE ME*/

		foreach ($positions as $position) {

			$data = array();
			$data['avgBuy'] = $position->avgBuy;
			$data['avgSell'] = $position->avgSell;
			$data['buyFillQty'] = $position->buyFillQty;
			$data['buyWorkingQty'] = $position->buyWorkingQty;
			$data['instrumentId'] = $position->instrumentId;
			$data['netPosition'] = $position->netPosition;
			$data['openAvgPrice'] = $position->openAvgPrice;
			$data['pnl'] = $position->pnl;
			$data['pnlPrice'] = $position->pnlPrice;
			$data['pnlPriceType'] = $position->pnlPriceType;
			$data['realizedPnl'] = $position->realizedPnl;
			$data['sellFillQty'] = $position->sellFillQty;
			$data['sellWorkingQty'] = $position->sellWorkingQty;
			$data['sodNetPos'] = $position->sodNetPos;
			$data['sodPriceType'] = $position->sodPriceType;
			$data['accountId'] = $position->accountId;

			$stored = $model->save($data);

			if ($stored) {
				ttdebug('position ' . $position->accountId . '/' . $position->instrumentId . ' stored');
				//return true;
			} else {
				ttdebug('position ' . $position->accountId . '/' . $position->instrumentId . ' not stored');
				//return false;
			}
		}

		//$current_page = $response->page + 1;
		if (isset($response->nextPageKey) && !empty($response->nextPageKey)) {
			self::getPositions($response->nextPageKey);
		}
	}

	public static function getAccounts($nextPageKey = false)
	{

		$response = json_decode(ttAPI::getAccounts($nextPageKey));

		if (!isset($response->accounts)) {
			ttdebug('can\'t get accounts');
			return false;
		} else if (count($response->accounts) < 1) {
			ttdebug('accounts are empty');
			return false;
		}

		$accounts = is_array($response->accounts) ? $response->accounts : array($response->accounts);
		$model = self::getModel('accounts');

		$ttPositions = array();

		foreach ($accounts as $account) {

			$data = array();
			$data['id'] = $account->id;
			$data['name'] = $account->name;

			$stored = $model->save($data);
			if ($stored) {
				ttdebug('account ' . $account->id . '/' . $account->name . ' stored');
				//return true;
			} else {
				ttdebug('account ' . $account->id . '/' . $account->name . ' not stored');
				//return false;
			}
		}

		//$current_page = $response->page + 1;
		if (isset($response->nextPageKey) && !empty($response->nextPageKey)) {
			self::getAccounts($response->nextPageKey);
		}
	}

	public static function getInstruments($nextPageKey = false)
	{

		$positionModel = self::getModel('positions');
		$positionInstruments = $positionModel->getPositionInstruments();
		ttdebug('positionInstruments', $positionInstruments);


		foreach ($positionInstruments as $instrument) {

			if ($instrument->name && $instrument->productSymbol) continue;

			$response = json_decode(ttAPI::getInstrument($instrument->instrumentId));

			if (!isset($response->instrument)) {
				ttdebug('can\'t get instruments');
				return false;
			} else if (count($response->instrument) < 1) {
				ttdebug('instrument is empty');
				return false;
			}

			$instrument = is_array($response->instrument) ? $response->instrument[0] : array($response->instrument)[0];

			$response = json_decode(ttAPI::getProduct($instrument->productId));

			if (!isset($response->product)) {
				ttdebug('can\'t get product');
				return false;
			}

			$product = $response->product;
			//ttdebug('resp',$response);

			$model = self::getModel('instruments');

			$data = array();
			$data['id'] = $instrument->id;
			$data['name'] = $instrument->name;
			$data['productSymbol'] = $instrument->productSymbol;
			$data['productId'] = $instrument->productId;
			$data['productName'] = $product->name;

			$stored = $model->save($data);
			if ($stored) {
				ttdebug('instrument ' . $instrument->id . '/' . $instrument->name . ' stored');
				//return true;
			} else {
				ttdebug('instrument ' . $instrument->id . '/' . $instrument->name . ' not stored');
				//return false;
			}
		}
	}

	public static function getModel($name)
	{

		$path = JPATH_ADMINISTRATOR . '/components/com_tradingtechnologies/models/';
		JModelLegacy::addIncludePath($path);
		require_once $path . strtolower($name) . '.php';
		$model = JModelLegacy::getInstance($name, 'tradingtechnologiesModel');

		return $model;
	}

	public static function sendNotificationMail($type, $tradeData = [])
	{
		$db = JFactory::getDbo();
		$query = 'select * from #__users 
    				join #__tt_mail_list 
    				    on #__tt_mail_list.user_id = #__users.id 
         			where send_mail = 1';
		$db->setQuery($query);
		$db->execute();
		$users = $db->loadObjectList();
		$db->setQuery('select template_id, template2_id from #__tt_mail_template JOIN `#__acym_mail` ON `#__acym_mail`.`id` = `#__tt_mail_template`.`template_id`  limit 1');
		$db->execute();
		$template = $db->loadObjectList();

		$db->setQuery('select * from #__tt_instruments where id = ' .  $tradeData['instrumentId']);
		$db->execute();
		//$product = $db->loadResult();
		[$product] = $db->loadAssocList();


		//check mail templates exists
		if (count($template) === 0) {
			JFactory::getApplication()->enqueueMessage("Please set email templates first", "error");
			return;
		} else if ($template[0]->template_id == null || $template[0]->template2_id == null) {
			JFactory::getApplication()->enqueueMessage("Please set email templates for both opening and closing mails", "error");
			return;
		}


		$mailer = new MailerHelper();

		$mailer->report = true; // set it to true or false if you want Acy to display a confirmation message or not (message successfully sent to...)
		$mailer->trackEmail = true; // set it to true or false if you want Acy to track the message or not (it will be inserted in the statistics table)
		$mailer->autoAddUser = true; //// set it to true if you want Acy to automatically create the user if it does not exist in AcyMailing
		$mailer->addParam('debug', json_encode($tradeData)); // Acy will automatically replace the tag {var1} by the value specified in the second parameter... you can use this function several times to replace tags in your email
		$mailer->addParam('type', $type); // Acy will automatically replace the tag {var1} by the value specified in the second parameter... you can use this function several times to replace tags in your email
		$mailer->addParam('product', $product['productName']); // Acy will automatically replace the tag {var1} by the value specified in the second parameter... you can use this function several times to replace tags in your email
		$mailer->addParam('buy', $tradeData['avgBuy'] ?? null); // Acy will automatically replace the tag {var1} by the value specified in the second parameter... you can use this function several times to replace tags in your email
		$mailer->addParam('sell', $tradeData['avgSell'] ?? null); // Acy will automatically replace the tag {var1} by the value specified in the second parameter... you can use this function several times to replace tags in your email
		$mailer->addParam('net_position', $tradeData['netPosition'] ?? null); // Acy will automatically replace the tag {var1} by the value specified in the second parameter... you can use this function several times to replace tags in your email
		$mailer->addParam('pnl', $tradeData['pnl'] ?? null); // Acy will automatically replace the tag {var1} by the value specified in the second parameter... you can use this function several times to replace tags in your email
		$mailer->addParam('link', $product['instrument_link'] ?? null); // Acy will automatically replace the tag {var1} by the value specified in the second parameter... you can use this function several times to replace tags in your email
		//$mailer->addParam('trade_info', ($type === 'close') ? '' : ''); // Acy will automatically replace the tag {var1} by the value specified in the second parameter... you can use this function several times to replace tags in your email
		
		$mailer->addParam('position_open', ($tradeData['netPosition'] > 0) ? "Buy: " . $tradeData['avgBuy'] : "Sell: " . $tradeData['avgSell']);
		$mailer->addParam('position_close', ($tradeData['netPosition'] < 0) ? "Buy: " . $tradeData['avgBuy'] : "Sell: " . $tradeData['avgSell']);



		foreach ($users as $user) {
			if ($type === 'close') $mailer->sendOne($template[0]->template2_id, $user->email); // The first parameter is the ID of the email you want to send
			else $mailer->sendOne($template[0]->template_id, $user->email); // The first parameter is the ID of the email you want to send
		}


		JFactory::getApplication()->enqueueMessage("$type mails send");
	}

	public static function is_allowed($account_id)
	{
		$db = JFactory::getDbo();
		$db->setQuery("select send_mail from #__tt_accounts where id = '$account_id'");
		$db->execute();
		return $db->loadResult();
	}
}

class ttURI
{

	public static function getCleanUrl($JURIInstance = 0, $parts = array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'))
	{

		if (!class_exists('JFilterInput')) require(VMPATH_LIBS . DS . 'joomla' . DS . 'filter' . DS . 'input.php');
		$_filter = JFilterInput::getInstance(array('br', 'i', 'em', 'b', 'strong'), array(), 0, 0, 1);
		if ($JURIInstance === 0) $JURIInstance = JURI::getInstance();
		return $_filter->clean($JURIInstance->toString($parts));
	}
}

function ttdebug($debugdescr, $debugvalues = NULL)
{

	if (ttConfig::showDebug()) {

		$app = JFactory::getApplication();

		if ($app->isClient('admin')) JFactory::getDocument()->addStyleDeclaration('div#system-message-container { max-height: 300px; overflow: auto; }');

		if ($debugvalues !== NULL) {
			$args = func_get_args();
			if (count($args) > 1) {
				for ($i = 1; $i < count($args); $i++) {
					if (isset($args[$i])) {
						$methods = '';
						if (is_object($args[$i])) {
							$methods = print_r(get_class_methods($args[$i]), 1);
							if (!empty($methods) and is_array($methods) and count($methods) > 0) {
								$methods = '<br />' . $methods;
							}
						}

						$debugdescr .= ' Var' . $i . ': <pre>' . print_r($args[$i], 1) . $methods . '</pre>' . "\n";
					}
				}
			}
		}

		$app = JFactory::getApplication();
		$app->enqueueMessage('<span class="ttdebug" >ttdebug ' . $debugdescr . '</span>');
	}
}
