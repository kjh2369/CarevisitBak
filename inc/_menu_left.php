<?
	if (isSet($_SESSION["userCode"])){
		$sql = "select count(*)
				  from m00center
				 inner join g01ins
					on g01_code = m00_ins_code
				   and g01_use  = 'Y'
				 where m00_mcode = '".$_SESSION["userCode"]."'";
		if ($conn->get_data($sql) > 0){
			$insFlag = 'Y';
		}else{
			$insFlag = 'N';
		}
	}

	if ($_SERVER['REMOTE_ADDR'] == '115.90.90.146'){
		$showMensFlag[0] = true;
	}else{
		$showMensFlag[0] = false;
	}

	$temp_uri = explode('/', $_SERVER["REQUEST_URI"]);

	if ($temp_uri[sizeOf($temp_uri)-1] == 'main.php'){
		//$left_menu = '';
		//$_COOKIE['__left_menu__'] = '';
		$_SESSION['menuName'] = '';
	}else{
		//$left_menu = $_COOKIE['__left_menu__'];
		//$left_menu = $_GET['menu'];
		if ($_GET['menu']) $_SESSION['menuName'] = $_GET['menu'];
	}

	$left_menu = $_SESSION['menuName'];

	if ($left_menu != ''){
		//try{}catch(Exception $e){}
		@include_once('../inc/menu_left_'.$left_menu.'.php');
	}
?>