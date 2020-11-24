<?
  $_BP = "../../";
  require_once $_BP . "nespire.bootloader.php";

  header('Access-Control-Allow-Origin: *');

  if(!class_exists('User')) {
    echo "-1000";
    exit(0);
  }

  $_acts = array("login", "logout");

  $_act = isset($_GET['act']) && in_array($_GET['act'], $_acts) ? $_GET['act'] : "default";
  $_id  = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0;

  switch($_act) {
    case "login":
      $username = @$_POST['username'];
      $password = @$_POST['password'];

      if(User::isLogged() || (!empty($username) && !empty($password) && User::login($username, $password))) {
        echo jsonify();
      } else {
        echo "0";
      }
    break;
    case "logout":
      if(User::logout()) {
        echo "1";
      } else {
        echo "0";
      }
    break;
    default:
      if(User::isLogged()) {
        echo jsonify();
      } else {
        "-1000";
      }
    break;
  }




  // Functions
  function jsonify() {
    $return = array();

    // User
    $return["user"] = User::getSessionUser();

    /* Special for a specific project *
    // Settings
    $return["settings"] = @json_decode(file_get_contents("../.Settings/Corporate.json"));

    // Axices
    if(class_exists('Axis')) {
      // Active Axis
      $return['axis'] = @Axis::getSessionAxis();

      // Other axices
      $axices = array();
      foreach(Axis::findBy('name', 'asc') as $i) {
        $c = Axis::find($i);

        if(!$c->exists()) continue;

        $axices[] = $c->jsonify();
      }

      $return['axices'] = $axices;
    } */
    return json_encode($return);
  }
?>
