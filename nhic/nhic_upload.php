<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	$code = $_POST['code'];
	$date = $_POST['date'];
	$seq  = $_POST['seq'];
	$file = $_POST['file'];
	$gbn  = $_POST['gbn'];




	/*********************************************************

		파일업로드

	*********************************************************/
	if (empty($file)){
		$f = $_FILES['csv'];

		$f_name = $f['name']; //파일명
		$f_type = $f['type']; //파일형식
		$f_size = $f['size']; //파일크기

		if ($f['tmp_name'] != ''){
			$file_nm = $_SESSION['userCenterCode'].'.'.mktime();

			/*********************************************************
				같은 파일 존재여부를 확인 후 존재한다면
				어떻게 처리할지 판단 후 처리한다.
			*********************************************************/
			$sql = 'select nhic_dt
					,      nhic_seq
					,      nhic_file
					  from nhic_log_mst
					 where nhic_file_name = \''.$f_name.'\'
					   and nhic_file_type = \''.$f_type.'\'
					   and nhic_file_size = \''.$f_size.'\'
					 order by nhic_dt desc, nhic_seq desc
					 limit 1';

			$mst_if = $conn->get_array($sql);

			if (!empty($mst_if[0]) > 0){
				/*********************************************************
					템프파일을 만든다.
				*********************************************************/
				$file = '../file/csv/tmp_'.$file_nm;
				move_uploaded_file($f['tmp_name'], $file);
				echo '<form name=\'f\' method=\'post\'>
						<input name=\'code\'   type=\'hidden\' value=\''.$code.'\'>
						<input name=\'date\'   type=\'hidden\' value=\''.$mst_if[0].'\'>
						<input name=\'seq\'    type=\'hidden\' value=\''.$mst_if[1].'\'>
						<input name=\'f_old\'  type=\'hidden\' value=\''.$mst_if[2].'\'>
						<input name=\'file\'   type=\'hidden\' value=\''.$file.'\'>
						<input name=\'f_name\' type=\'hidden\' value=\''.$f_name.'\'>
						<input name=\'f_type\' type=\'hidden\' value=\''.$f_type.'\'>
						<input name=\'f_size\' type=\'hidden\' value=\''.$f_size.'\'>
					  </form>
					  <script>
						f.target=\'_self\';
						f.action=\'../nhic/nhic_quest.php\';
						f.submit();
					  </script>';
				exit;
			}

			$file = '../file/csv/'.$file_nm;

			if (is_file($file)){
				@unlink($file);
			}

			if (move_uploaded_file($f['tmp_name'], $file)){
				// 업로드 성공
				$upload = true;
			}else{
				// 업로드 실패
				$upload = false;
			}
		}else{
			// 업로드 실패
			$upload = false;
		}

		if (!$upload){
			echo '<script language="javascript">
					alert(\'파일업로드중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.\');
					history.back();
				  </script>';
			exit;
		}
	}else{
		if (!empty($gbn)){
			if (is_file($file)){
				$f_name = $_POST['f_name']; //파일명
				$f_type = $_POST['f_type']; //파일형식
				$f_size = $_POST['f_size']; //파일크기
				$f_old  = $_POST['f_old'];  //이전파일

				$old_file = $file;
				$file     = str_replace('tmp_', '', $file);

				copy($old_file, $file);
				@unlink($old_file);
				@unlink($f_old);
			}
		}
	}
?>

<script language='javascript'>
<!--

var f = null;
var is_load = false;

function show_loading(){
	f.target = 'fm_body';
	f.action = '../common/common_loading.php';
	f.submit();
}

function close_loading(){
	//var body = document.getElementById('fm_body');
	//	body.style.display = 'none';

	f.target = '_self';
	f.action = '../nhic/nhic_apply.php';
	f.submit();
}

function set_makedata(){
	f.target = 'fm_list';
	f.action = '../nhic/nhic_makedata.php';
	f.submit();
}

function set_listdata(){
	f.target = 'fm_list';
	f.action = '../nhic/nhic_apply.php';
	f.submit();
}

window.onload = function(){
	f = document.f;

	if (f.gbn.value == '1'){
		close_loading();
		set_listdata();
	}else{
		show_loading();
		set_makedata();
	}

	self.focus();
}

-->
</script>

<form name='f' method='post'>
<?
	if ($debug){?>
		<iframe name='fm_body' src='about:blank' style='width:100%; height:100px;' frameborder='0' scrolling='no'></iframe>
		<iframe name='fm_list' src='about:blank' style='width:100%; height:300px;' frameborder='0' scrolling='yes'></iframe><?
	}else{?>
		<iframe name='fm_body' src='about:blank' style='width:100%; height:100%;' frameborder='0' scrolling='no'></iframe>
		<iframe name='fm_list' src='about:blank' style='width:100%; height:100%;' frameborder='0' scrolling='yes'></iframe><?
	}
?>

<input name='code'   type='text' value='<?=$code;?>'>
<input name='file'   type='text' value='<?=$file;?>'>
<input name='f_name' type='text' value='<?=$f_name;?>'>
<input name='f_type' type='text' value='<?=$f_type;?>'>
<input name='f_size' type='text' value='<?=$f_size;?>'>
<input name='gbn'    type='text' value='<?=$gbn;?>'>
<input name='date'   type='text' value='<?=$date;?>'>
<input name='seq'    type='text' value='<?=$seq;?>'>
</form>

<?
	include_once('../inc/_footer.php');
?>