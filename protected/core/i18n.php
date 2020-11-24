<?

class i18n extends ActiveRecord {
	/**
	 * Plugin Settings
	 */
	final static function getPluginId(){ return __class__; }
	final static function getPluginRecord(){ return 'tbl_i18n'; }
	final static function getPluginSchema(){ return "CREATE TABLE IF NOT EXISTS `tbl_i18n` (
																				`id` int(11) NOT NULL AUTO_INCREMENT,
																				`language_id` char(2) NOT NULL,
																				`plugin_id` varchar(30) NOT NULL,
																				`plugin_item_id` int(11) NOT NULL,
																				`value` text NOT NULL,
																				PRIMARY KEY (`id`),
																				UNIQUE KEY `plugin` (`language_id`, `plugin_id`,`plugin_item_id`)
																			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"; }
	/**
	 * Properties
	 */
	public $id;
	public $language_id;
	public $plugin_id;
	public $plugin_item_id;
	public $value;
	
	/**
	 * Static methods
	 */
	public static function findByPlugin($langId, $pluginId, $itemId) {
		
		$return = static::findBy('id', 'DESC', array('language_id', 'plugin_id', 'plugin_item_id'), array($langId, $pluginId, $itemId));
		
		if(isset($return[0])) {
			return static::find($return[0]);
		}else{
			$i18n = new i18n;
			$i18n->language_id = $langId;
			$i18n->plugin_id = $pluginId;
			$i18n->plugin_item_id = $itemId;
			$i18n->value = "undefined";
			return $i18n;
		}
	}
	
	public static function findByLang($langId) {
		
		return static::findBy('id', 'DESC', 'language_id', $langId);
	}
	
	public static function e($langId, $pluginId, $itemId) {
		$i18n = static::findByPlugin($langId, $pluginId, $itemId);
		if(is_object($i18n) && $i18n->exists()) {
			return "<span class='poshytip' title='i18n:".$pluginId.":".$itemId."'>".$i18n->value."</span>";
		}else{
			return "<span class='poshytip' title='i18n:".$pluginId.":".$itemId."'>".$pluginId."-".$itemId."</span>";
		}
	}
	
	public static function t($langId, $pluginId, $itemId, $isAdmin = false) {
		if($isAdmin) {
			return static::e($langId, $pluginId, $itemId);
		}
		
		$i18n = static::findByPlugin($langId, $pluginId, $itemId);
		if(is_object($i18n) && $i18n->exists()) {
			return $i18n->value;
		}else{
			return $pluginId."-".$itemId;
		}
	}
}
?>
