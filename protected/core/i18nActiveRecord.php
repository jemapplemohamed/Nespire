<?

abstract class i18nActiveRecord extends ActiveRecord{
	public function delete() {
		$i18ns = i18n::findBy('id', 'DESC', array('plugin_id', 'plugin_item_id'), array($this->getPluginId()."-", $this->id), 0, 100000, false);
		
		foreach($i18ns as $i18n) {
			$i18n = @i18n::find($i18n);
			@$i18n->delete();
		}
		
		return parent::delete();
	}
	
	public function i18n($itemId, $langId, $isEdit = false) {
		return i18n::t($langId, $this->getPluginId() ."-".$itemId, $this->id, $isEdit);
	}
}
