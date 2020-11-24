<?
  $_BP = "../";
  require_once $_BP . "nespire.bootloader.php";

  header('Access-Control-Allow-Origin: *');

  if(!class_exists('User') || !User::isLogged()) {
    echo "-1000";
    exit(0);
  }
  
  $_acts = array();

  $_act = isset($_GET['act']) && in_array($_GET['act'], $_acts) ? $_GET['act'] : "default";
  $_id  = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0;
  $_soid  = isset($_GET['soid']) && is_numeric($_GET['soid']) ? $_GET['soid'] : 0;
  $_start  = isset($_GET['start']) && is_numeric($_GET['start']) && $_GET['start'] >= 0 ? $_GET['start'] : 0;
  $_count  = isset($_GET['count']) && is_numeric($_GET['count']) && $_GET['count'] > 0 ? $_GET['count'] : 10;
  $_dstart = isset($_GET['dstart']) && !empty($_GET['dstart']) ? $_GET['dstart'] : date('2014-09-01');
  $_dend   = isset($_GET['dend']) && !empty($_GET['dend']) ? $_GET['dend'] : date('Y-m-d');
  $_period = 7;
  
  switch($_act) {
    default:
      // findAll + find(id)
      if($_id) {
          $l = Log::find($_id);

          if(!$l->exists()) { echo "-1"; exit(0); }

          echo json_encode($l->jsonify(true));
      } else {
        $ll = array();

        $list = isset($_GET['uid']) && $_GET['uid'] > 0 ? Log::findBy('id', 'desc', 'user_id', $_GET['uid'], $start, $count) : Log::findBy('id', 'desc', null, null, $_start, $_count);
        $total = isset($_GET['uid']) && $_GET['uid'] > 0 ? count(Log::findBy('id', 'desc', 'user_id', $_GET['uid'])) : count(Log::findBy('id', 'desc', null, null));

        foreach($list as $i) {
          $l = Log::find($i);

            if(!$l->exists()) continue;

            $ll[] = $l->jsonify(true);
        }

        $r = array('items' => $ll, 'total' => $total);
        echo json_encode($r);
      }
    break;
  }
?>
