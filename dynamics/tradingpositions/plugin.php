<?php

use AcyMailing\Libraries\acymPlugin;

require_once __DIR__.DIRECTORY_SEPARATOR.'TradingpositionsInsertion.php';

class plgAcymTradingpositions extends acymPlugin
{
	use TradingpositionsInsertion;

	public function __construct()
	{
		parent::__construct();
		$this->pluginDescription->name = acym_translation('ACYM_TRADING_POSITIONS');
	}
}

?>