<?
	/** Class List */
	$_class_list = array('Log', 'User',
											 'Axis',
											 	'AccountHead', 'Account', 'Entry', 'Transaction',
											 'StockStorage', 'StockItem',
										 	 'Client', 'CashbackClient', 'Quote', 'Invoice', 'InvoicePayment',
										 	 'Bill');
	/* /Class List */


	// no editing beyond this point
	if( !isset($_BP) ) { $_BP = "./"; }
	include $_BP ."nespire.config.php";
	Config::$BASE_PATH = $_BP;
	include $_BP ."protected/core/ActiveRecord.php";
	include $_BP ."protected/core/i18n.php";
	include $_BP ."protected/core/i18nActiveRecord.php";
	include $_BP ."protected/NespoCASH.php";

	foreach($_class_list as $c) {
		(include $_BP ."protected/".$c.".php") or die("Error: Unable to locate module `".$c."`.");
		@$c::install();
	}

	if(isset($_GET['lang']) && in_array($_GET['lang'], Config::$AVAILAB_LANG)) {
		$_SESSION['_LANG'] = $_GET['lang'];
	}

	if(User::isInstalled()) {
		$user = new User;
		$user->username = "root";
		$user->password = md5("opendoor");
		$user->usergroup_id = 0;
		$user->name     = "SYSADMIN";
		$user->image = null;
		$user->status = null;
		$user->session_id = null;

		@$user->save();
	}
?>
