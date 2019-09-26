<?
	if (!isset($__CURRENT_SVC_ID__)) include_once('../inc/_http_home.php');

	if ($lbTestMode){
		include('./client_svc_period_test.php');
	}else{
		include('./client_svc_period.php');
	}
?>