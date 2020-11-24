<?
  $_BP = "../";
  require_once $_BP . "nespire.bootloader.php";

  header('Access-Control-Allow-Origin: *');

  if(!class_exists('User') || !User::isLogged() || User::getSessionUser()->usergroup_id != 0) {
    echo "-1000";
    exit(0);
  }

  header('Access-Control-Allow-Origin: *');

  $_acts = array("image", "changepass", "save", "delete");

  $_act = isset($_GET['act']) && in_array($_GET['act'], $_acts) ? $_GET['act'] : "default";
  $_id  = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0;
  $_start  = isset($_GET['start']) && is_numeric($_GET['start']) && $_GET['start'] >= 0 ? $_GET['start'] : 0;
  $_count  = isset($_GET['count']) && is_numeric($_GET['count']) && $_GET['count'] > 0 ? $_GET['count'] : 10;

  switch($_act) {
    case "image":
      $u = @User::find($_id);

      if($u->exists()) {
        header('Content-Type: image/jpeg');
        $a = explode(";base64,", $u->image);
        echo base64_decode($a[1]);
      }
    break;
    case "delete":
      $u = @User::find($_id);
      if($u->exists()) {
        if($u->delete()) {
          echo "1";
        } else {
          echo "-1";
        }
      } else {
        echo "0";
      }
    break;
    case "save":
      $d = @json_decode($_POST['data']);

      $u             = @User::find($d->id);
      $u->usergroup_id     = $d->usergroup_id > 0 ? $d->usergroup_id : 0;
      $u->name       = $d->name;
      $u->image       = $d->image;
      $u->status    = $d->status;
      $u->username     = $d->username;

      if($u->exists()) {
        $u->password      = isset($d->passwordx) && strlen($d->passwordx) > 2 ? md5($d->passwordx) : $u->password;
      } else {
        $u->password      = md5($d->password);
      }

      if(!empty($u->name) && !empty($u->username) && !empty($u->password) && is_numeric($u->usergroup_id) && $u->save()) {
        echo $u->id;
      } else {
        echo "0";
      }
    break;
    default:
      // findAll + find(id)
      if($_id) {
          $u = User::find($_id);

          if(!$u->exists()) { echo "-1"; exit(0); }

          echo json_encode($u->jsonify(true));
      } else {
        $ul = array();

        foreach(User::findBy('id', 'desc', null, null, $_start, $_count) as $i) {
          $u = User::find($i);

            if(!$u->exists()) continue;

            $ul[] = $u->jsonify(false);
        }

        $r = array('items' => $ul, 'total' => count(User::findAll()));
        echo json_encode($r);
      }
    break;
  }
?>
