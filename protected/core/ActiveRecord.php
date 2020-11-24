<?
abstract class ActiveRecord {
	/**
	 * Plugin Settings
	 */
	abstract static function getPluginId();
	abstract static function getPluginRecord();
	abstract static function getPluginSchema();

	/**
	 * Properties
	 */
	public $id;

	/**
	 * public methods
	 */
	public function jsonify() {
		return $this->toArray();
	}

	public function exists() {

		return mysqli_num_rows(
							mysqli_query(Config::$MYSQL_HANDLE, "SELECT * FROM `".$this->getPluginRecord()."` WHERE `id` = '".$this->id."';")
					 ) > 0;
	}

	public function save() {

		if($this->exists()){
			$update = true;
		}else{
			$update = false;
		}

		if(!$update) {
			$query = "INSERT INTO `".$this->getPluginRecord()."` ";
		}

		$vars = get_object_vars($this);

		$keys = array_keys($vars);
		$vals = array_values($vars);

		if(!$update){
			$keys_str = "(";
			$vals_str = "(";
		}

		if($update){
			$return = true;
		}

		for($i=0; $i<count($keys);$i++){
			if($update) {
				if($keys[$i] == "id") continue;

				$tval = "null";

				if(!is_null($vals[$i])) {
					$tval = "'".mysqli_real_escape_string(Config::$MYSQL_HANDLE, stripslashes($vals[$i]))."'";
				}

				$return &= mysqli_real_query(Config::$MYSQL_HANDLE, "UPDATE `".$this->getPluginRecord()."` SET `".$keys[$i]."` = ".$tval." WHERE `id` = '".$this->id."';");
			}else{
				if($keys[$i] == "id") continue;

				$keys_str .= "`".$keys[$i]."`";

				if(is_null($vals[$i])) {
					$vals_str .= "null";
				} else {
					$vals_str .= "'".mysqli_real_escape_string(Config::$MYSQL_HANDLE, $vals[$i])."'";
				}

				$keys_str .= ",";
				$vals_str .= ",";
			}
		}

			$keys_str = rtrim($keys_str, ",");
			$vals_str = rtrim($vals_str, ",");

		if($update){
			return $return;
		}

		if(!$update){
			$keys_str .= ")";
			$vals_str .= ")";

			$query .= $keys_str . " VALUES ". $vals_str .";";
			$return = mysqli_real_query(Config::$MYSQL_HANDLE, $query);
			if($return) {
				$this->id = array_pop(static::findBy('id', 'DESC', null, null, 0, 1));
			}

			return $return;
		}
	}

	public function delete() {

		return mysqli_real_query(Config::$MYSQL_HANDLE, "DELETE FROM `".$this->getPluginRecord()."` WHERE `id` = '".$this->id."';");
	}

	public function fromDB() {
		if(!$this->exists()) {
			return false;
		}

		$query = mysqli_fetch_assoc(mysqli_query(Config::$MYSQL_HANDLE, "SELECT * FROM `".$this->getPluginRecord()."` WHERE `id` = '".$this->id."';"));

		foreach(array_keys(get_object_vars($this)) as $key) {
			$this->$key = $query[$key];
		}

		return true;
	}

	public function toArray() {
		return get_object_vars($this);
	}

	/**
	 * Static Methods
	 */

	public static function findBy($sortKey = 'id', $sortFlag = 'DESC', $filterKey = null, $filterValue = null, $limitStart = 0, $limitCount = 999999999, $filterExact = true, $returnKey = "id"){
		$query_str = "SELECT `".$returnKey."` FROM `".static::getPluginRecord()."`";

		if($filterKey != null && $filterValue != null) {
			if($filterExact) {
				$filterType = "=";
				$filterPrepend  = "";
				$filterAppend   = "";
			}else{
				$filterType = "LIKE";
				$filterPrepend = "%";
				$filterAppend  = "%";
			}


			if(is_array($filterKey) && is_array($filterValue) && count($filterKey) == count($filterValue)) {
				$query_str .= " WHERE ";
				for($i=0;$i<count($filterKey);$i++) {
					if(is_array($filterValue[$i]) && count($filterValue[$i]) == 2) {
						$fv = $filterValue[$i];
						$query_str .= " ( `".$filterKey[$i]."` BETWEEN '".$fv[0]."' AND '".$fv[1]."' ) ";
						if($i == count($filterKey) - 1) continue;
						$query_str .= " AND ";
					}else if(!is_array($filterValue[$i])) {
						$query_str .= " `".$filterKey[$i]."` ".$filterType." '".$filterPrepend."".$filterValue[$i]."".$filterAppend."' ";
						if($i == count($filterKey) - 1) continue;
						$query_str .= " AND ";
					}
				}

			}elseif(!is_array($filterKey) && !is_array($filterValue)){
				$query_str .= " WHERE `".$filterKey."` ".$filterType." '".$filterPrepend."".$filterValue."".$filterAppend."'";
			}elseif(!is_array($filterKey) && is_array($filterValue) && count($filterValue) == 2){
				$query_str .= " WHERE `".$filterKey."` BETWEEN '".$filterValue[0]."' AND '".$filterValue[1]."'";
			}
		}

		$query_str .= "  ORDER BY `".$sortKey."` ".$sortFlag." LIMIT ".$limitStart.", ".$limitCount.";";


		$query = mysqli_query(Config::$MYSQL_HANDLE, $query_str);

		if(!$query)
		 	return null;

		if(mysqli_num_rows($query) == 0) {
			return null;
		}

		$return = array();

		if($query) {
			while($row = mysqli_fetch_assoc($query)){
				array_push($return, $row[$returnKey]);
			}
		}

		return $return;
	}

	public static function deleteBy($filterKey = null, $filterValue = null, $filterExact = true){
		$query_str = "DELETE FROM `".static::getPluginRecord()."`";

		if($filterKey != null && $filterValue != null) {
			if($filterExact) {
				$filterType = "=";
				$filterPrepend  = "";
				$filterAppend   = "";
			}else{
				$filterType = "LIKE";
				$filterPrepend = "%";
				$filterAppend  = "%";
			}


			if(is_array($filterKey) && is_array($filterValue) && count($filterKey) == count($filterValue)) {
				$query_str .= " WHERE ";
				for($i=0;$i<count($filterKey);$i++) {
					if(is_array($filterValue[$i]) && count($filterValue[$i]) == 2) {
						$fv = $filterValue[$i];
						$query_str .= " ( `".$filterKey[$i]."` BETWEEN '".$fv[0]."' AND '".$fv[1]."' ) ";
						if($i == count($filterKey) - 1) continue;
						$query_str .= " AND ";
					}else if(!is_array($filterValue[$i])) {
						$query_str .= " `".$filterKey[$i]."` ".$filterType." '".$filterPrepend."".$filterValue[$i]."".$filterAppend."' ";
						if($i == count($filterKey) - 1) continue;
						$query_str .= " AND ";
					}
				}

			}elseif(!is_array($filterKey) && !is_array($filterValue)){
				$query_str .= " WHERE `".$filterKey."` ".$filterType." '".$filterPrepend."".$filterValue."".$filterAppend."'";
			}elseif(!is_array($filterKey) && is_array($filterValue) && count($filterValue) == 2){
				$query_str .= " WHERE `".$filterKey."` BETWEEN '".$filterValue[0]."' AND '".$filterValue[1]."'";
			}
		}

		$query_str .= ";";

		return mysqli_real_query(Config::$MYSQL_HANDLE, $query_str);
	}

	public static function findAll() {

		return static::findBy();
	}

	public static function find($id) {
		$class = static::getPluginId();
		$Object = new $class;
		$Object->id = $id;
		$Object->fromDB();

		return $Object;
	}

	public static function clear() {

		foreach(static::findAll() as $item){
			static::find($item)->delete();
		}
	}

	public static function count() {

		return count(static::findAll());
	}

	public static function isInstalled() {

		return mysqli_num_rows(mysqli_query(Config::$MYSQL_HANDLE, "SHOW TABLES LIKE '".static::getPluginRecord()."';")) > 0;
	}

	public static function install() {

		return mysqli_real_query(Config::$MYSQL_HANDLE, static::getPluginSchema());
	}

	public static function drop() {

		return mysqli_real_query(Config::$MYSQL_HANDLE, "DROP TABLE `".static::getPluginRecord()."`;");
	}
}
?>
