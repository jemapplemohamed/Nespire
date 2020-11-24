<?
class Log extends ActiveRecord {
	const ACTION_CREATE 	= 0;
	const ACTION_EDIT 		= 1;
	const ACTION_DELETE 	= 2;
	const ACTION_LOGIN 		= 3;
	const ACTION_LOGOUT 	= 4;
	const ACTION_NOACCESS 	= 5;
	/**
	 * Plugin Settings
	 */
	final static function getPluginId(){ return __class__; }
	final static function getPluginRecord(){ return 'tbl_nespolog'; }
	final static function getPluginSchema(){ return "CREATE TABLE IF NOT EXISTS `tbl_nespolog` (
																				`id` 		  int(11) NOT NULL AUTO_INCREMENT,
																				`user_id` int(11) NOT NULL,
																				`timestamp` datetime NOT NULL,
																				`action` int(2) NOT NULL,
																				`class` char(100) NOT NULL,
																				`object` int(11) NOT NULL,
																				`ip` char(15) NOT NULL,
																				PRIMARY KEY (`id`)
																			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"; }
	
	/**
	 * Properties
	 */
	public $user_id;
	public $timestamp;
	public $action;
	public $class;
	public $object;
	public $ip;

	public static function d($user_id = null, $action, $object) {
		$user_id = is_null($user_id) ? User::getSessionUser()->id : $user_id;
		if(!User::find($user_id)->exists() || !$object->exists() || !in_array($action, array(Log::ACTION_CREATE,
																							 Log::ACTION_EDIT,
																							 Log::ACTION_DELETE,
																							 Log::ACTION_LOGIN,
																							 Log::ACTION_LOGOUT,
																							 Log::ACTION_NOACCESS))) return false; // not a valid object

		$log = new Log;
		$log->user_id = $user_id;
		$log->timestamp = date('Y-m-d H:i:s');
		$log->action = $action;
		$log->class = get_class($object);
		$log->object = $object->id;
		$log->ip = $_SERVER['REMOTE_ADDR'];

		return $log->save();
	}

	public function getUser() {
		return User::find($this->user_id);
	}

	public function jsonify( $isParent = false) {
		$r['id'] = $this->id;
		$r['user'] = array('id' => $this->user_id, 'username' => $this->getUser()->username);
		$r['timestamp'] = $this->timestamp;
		$r['action'] = @$this->action;
		$r['class'] = @$this->class;
		$r['object'] = @$this->object;
		$r['ip'] = @$this->ip;
		
		if($isParent) {
			//$['reservations'] = @Reservation::find($this->reservation)->jsonify();
		}

		return $r;
	}
}
?>
