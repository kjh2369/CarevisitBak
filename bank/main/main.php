<?
	include_once('../inc/_header.php');

	if (IsSet($_SESSION['USER_CODE'])){
		$type = $_GET['type'];

		switch($type){
			case 'trans_request': //이체요청 리스트
				$menu = "0";
				include_once('../trans/request.php');
				break;

			case 'trans_result': //이케결과
				$menu = '1';
				include_once('../trans/result.php');
				break;

			case 'config': //설정
				$menu = '2';
				include_once('../config/config.php');
				break;

			default: //로그인 페이지
				$menu = 'login';
				include_once('./login.php');
		}
	}else{

		//로그인 페이지
		$menu = 'login';
		include_once('./login.php');
	}

	include_once('../inc/_footer.php');
?>
<div id='divTemp' style='position:absolute; left:0; top:0; display:none; z-index:11;'></div>
<script type="text/javascript">

</script>