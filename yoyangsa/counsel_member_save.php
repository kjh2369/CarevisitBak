<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	echo $myF->header_script();
	
	$mode  = $_POST['mode'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	
	//print_r($_POST); exit;


	$conn->begin();
	
	if($mode == 'process'){
		include_once('../counsel/mem_stress_save.php');
	}else {
		
		include_once('../counsel/client_counsel_'.$mode.'_save.php');
	}
	
	$conn->commit();

	include_once('../inc/_db_close.php');
	
	if($is_pop == 'Y'){
		echo '<form name="f" method="post">';
		echo '<input name="type" type="hidden" value="'.$mode.'">';
		if($mode == 'process'){
			echo '<input name="code" type="hidden" value="'.$_POST['process_code'].'">';
		}else {
			echo '<input name="code" type="hidden" value="'.$_POST['code'].'">';
		}
		echo '<input name="ssn" type="hidden" value="'.$_POST[$mode.'_ssn'].'">';
		echo '<input name="seq" type="hidden" value="'.$_POST[$mode.'_seq'].'">';
		echo '<input name="yymm" type="hidden" value="'.substr(str_replace('-','',$_POST[$mode.'_dt']), 0, 6).'">';
		echo '</form>';
		echo '<script>';
		echo 'alert(\''.$myF->message('ok','N').'\');';
		echo 'f.action = "../yoyangsa/process_counseling_reg.php";'; 
		echo 'f.submit();';
		echo '</script>';
	}else {
		echo '<script>';
		echo 'alert(\''.$myF->message('ok','N').'\');';
		echo 'location.replace(\'./counsel_member.php?year='.$year.'&month='.$month.'\');';
		echo '</script>';
	}
?>