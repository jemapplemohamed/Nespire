<?

if(isset($_GET['sid']) && $_GET['sid'] != null && $_GET['sid'] != 'null') {
	session_id($_GET['sid']);
} else {
	session_regenerate_id();
}

@session_start();

$_SESSION['_LANG'] = isset($_SESSION['_LANG']) ? $_SESSION['_LANG'] : "en";

// TimeZone
date_default_timezone_set("Africa/Khartoum");

class Config {

	public static $DEBUG_MODE  = true;
	public static $BASE_PATH   = "./";

	/** Device Type */
	public static function deviceType() {
		return strpos($_SERVER['HTTP_USER_AGENT'], "Mobile") > 0 ? "Mobile" : "Desktop";
	}
	/* /Device Type */

	/** Language Options */

	public static $AVAILAB_LANG = array('ar', 'en');
	public static $DEFAULT_LANG = "en";

	public static function lv($lang = "") {
		if($lang == "") { $lang = $_SESSION['_LANG']; }

		switch(Config::ld($lang)) {
			case "rtl":
				return "right";
			default:
				return "left";
			break;
		}
	}

	public static function lvi($lang = "") {
		if($lang == "") { $lang = $_SESSION['_LANG']; }

		switch(Config::ld($lang)) {
			case "ltr":
				return "right";
			default:
				return "left";
			break;
		}
	}

	public static function ld($lang = "") {
		if($lang == "") { $lang = $_SESSION['_LANG']; }

		switch($lang) {
			case "ar":
				return "rtl";
			break;
			default:
				return "ltr";
			break;
		}
	}

	public static function ln($lang = "") {
		if($lang == "") { $lang = $_SESSION['_LANG']; }

		switch($lang) {
			case "ar":
				return "العربية";
			break;
			default:
				return "English";
			break;
		}
	}

	public static function lc() {
		if($lang == "") $lang = $_SESSION['_LANG'];

		return $_SESSION['_LANG'];
	}

	public static function lci($lang = "") {
		if($lang == "") $lang = $_SESSION['_LANG'];

		switch($lang) {
			case "en":
				return "ar";
			default:
				return "en";
		}
	}

	public static function isEdit() {
		return @User::isLogged();
	}

	public static function isDebug() {
		return true;
	}

	/**
	 * MySql DB
	 */
	public static $MYSQL_HOST = "localhost";
	public static $MYSQL_USER = "USER";
	public static $MYSQL_PASS = "PASS";
	public static $MYSQL_NAME = "DB";

	public static $MYSQL_HANDLE = null;
}

Config::$MYSQL_HANDLE = mysqli_connect(Config::$MYSQL_HOST, Config::$MYSQL_USER, Config::$MYSQL_PASS, Config::$MYSQL_NAME);

if(!Config::$MYSQL_HANDLE){
	echo mysqli_connect_error();
	exit(0);
}

if(Config::isDebug()) {
	error_reporting(E_ALL);
	ini_set('error_reporting', 1);
	ini_set('display_errors', 1);
	ini_set('display_html_errors', 1);
}
?>
