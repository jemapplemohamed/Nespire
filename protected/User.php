<?
class User extends ActiveRecord {
	/**
	 * Plugin Settings
	 */
	final static function getPluginId(){ return __class__; }
	final static function getPluginRecord(){ return 'tbl_nespocuser'; }
	final static function getPluginSchema(){ return "CREATE TABLE IF NOT EXISTS `tbl_nespocuser` (
																				`id` 		  int(11) NOT NULL AUTO_INCREMENT,
																				`username` char(40) UNIQUE NOT NULL,
																				`password` varchar(60) NOT NULL,
																				`usergroup_id` int(11) NULL,
																				`session_id` varchar(60) NULL,
																				`name`  text NOT NULL,
																				`image` longblob NULL,
																				`status` int(1) NULL,
																				PRIMARY KEY (`id`)
																			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"; }

	/**
	 * Properties
	 */
	public $username;
	public $password;
	public $usergroup_id;
	public $session_id;
	public $name;
	public $image;
	public $status;

	public function isRoot() {
		return $this->usergroup_id == 0;
	}

	public static function getSessionUser() {
		if(isset($_SESSION['uid'])) {
			return User::find($_SESSION['uid']);
		}

		return null;
	}

	public static function logout() {
		Log::d(User::getSessionUser()->id, Log::ACTION_LOGOUT, User::getSessionUser());
		
		$_SESSION['uid']      = null;
		$_SESSION['username'] = null;
		$_SESSION['password'] = null;
		return session_destroy();
	}

	public static function login($username, $password) {
		if(User::isLogged()) return false;

		$p = @User::findBy('id', 'DESC', array('username', 'password'), array($username, md5($password)));
		$p = @User::find($p[0]);
		if($p->exists() && $p->username == $username && $p->password == md5($password)) {
			$_SESSION['uid']      = $p->id;
			$_SESSION['username'] = $p->username;
			$_SESSION['password'] = $p->password;

			Log::d($p->id, Log::ACTION_LOGIN, $p);
			$p->session_id = session_id();
			@$p->save();
			return true;
		} else {
			Log::d($p->id, Log::ACTION_NOACCESS, $p);
			return false;
		}
	}

	public static function isLogged() {
		if(!isset($_SESSION['uid'])) return false;

		$p = User::find($_SESSION['uid']);
		if($p->exists() && $p->username == $_SESSION['username'] && $p->password == $_SESSION['password']) {
			return true;
		} else {
			return false;
		}
	}

	public static function findByUsername( $username ) {
		return User::findBy('id', 'DESC', 'username', $username);
	}

	/**
	 * Override jsonify
	 */
 public function jsonify( $expand = false ) {
	 $r = $this;

	 if($expand) {
		 // do something

	 } else {
		 // no expand - do stuff
		 if(!is_null($r->image)) {
			 $link = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . array_pop(array_slice(explode("?", $_SERVER['REQUEST_URI']), 0, 1));
			 $r->image = $link . "?act=image&id=" . $r->id;
		 }
	 }

	 return $r;
 }

	/** DO LOG STUFF */
	public function save() {
		$tid = $this->id;

		$r = parent::save();

		if($tid > 0) {
			Log::d(User::getSessionUser()->id, Log::ACTION_EDIT, $this);
		} else {
			Log::d(User::getSessionUser()->id, Log::ACTION_CREATE, $this);
		}

		return $r;
	}

	public function delete() {
		$r = parent::delete();

		if($r) {
			Log::d(User::getSessionUser()->id, Log::ACTION_DELETE, $this);
		}

		return $r;
	}
	/** /LOG STUFF */
}
?>
