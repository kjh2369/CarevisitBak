<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$jumin	= $ed->de($_POST['jumin']);
	$appNo	= $_POST['appNo'];
	$key	= $_POST['key'];

	$sql = 'SELECT	CASE WHEN day >= 1 AND day <= 7 THEN 1
						 WHEN day >= 8 AND day <= 14 THEN 2
						 WHEN day >= 15 AND day <= 21 THEN 3
						 WHEN day >= 22 AND day <= 28 THEN 4 ElSE 5 END AS week
			,		day

			,		CASE WHEN notvisit_cd = \'1\' THEN \'사망\'
						 WHEN notvisit_cd = \'2\' THEN \'병원\'
						 WHEN notvisit_cd = \'3\' THEN \'해지\'
						 WHEN notvisit_cd = \'9\' THEN \'기타\' ELSE \'\' END AS notvisit_cd

			,		CASE WHEN send_gbn = \'02\' THEN \'자동전송\'
						 WHEN send_gbn = \'01\' THEN \'시작만전송\'
						 WHEN send_gbn = \'03\' THEN \'오류수정\'
						 WHEN send_gbn = \'04\' THEN \'직접입력\' ELSE \'미등록\' END AS send_gbn
			FROM	(
					SELECT	CAST(RIGHT(a.date,2) AS unsigned) AS day
					,		CASE WHEN IFNULL(notvisit_cd,\'\') = \'\' AND IFNULL(notvisit_reason,\'\') != \'\' THEN \'9\' ELSE notvisit_cd END AS notvisit_cd
					,		send_gbn
					FROM	(
							SELECT	date, notvisit_cd, notvisit_reason
							FROM	sw_log
							WHERE	org_no	= \''.$orgNo.'\'
							AND		jumin	= \''.$jumin.'\'
							AND		yymm	= \''.$year.$month.'\'
							) AS a
					LEFT	JOIN (
							SELECT	reg_dt AS date
							,		send_gbn
							FROM	lg2cv
							WHERE	org_no	= \''.$orgNo.'\'
							AND		yymm	= \''.$year.$month.'\'
							AND		app_no	= \''.$appNo.'\'
							) AS b
							ON		b.date = a.date
					) AS a';

	$sw = $conn->_fetch_array($sql,'week');

	//02:자동전송, 01:시작만전송, 03:오류수정, 04:직접입력, 99:기타
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody><?
		for($i=1; $i<=5; $i++){
			$file = findFile('./files/'.$orgNo.'/'.$year.$month, 'F_'.$key.'_'.$i);

			$str = '';

			if ($sw[$i]['day']) $str .= $sw[$i]['day'].'일';
			if ($sw[$i]['send_gbn']) $str .= '&nbsp;&nbsp;&nbsp;&nbsp;공단 : '.$sw[$i]['send_gbn'];
			if ($sw[$i]['notvisit_cd']) $str .= '&nbsp;&nbsp;&nbsp;&nbsp;사유 : '.$sw[$i]['notvisit_cd'];?>
			<tr>
				<th class="last"><?=$i;?>주차&nbsp;&nbsp;&nbsp;&nbsp;<?=$str;?></th>
			</tr>
			<tr>
				<td class="last">
					<div style="float:left; width:auto;"><input type="file" name="file_<?=$i;?>" style="width:300px; background-color:#FFFFFF;"></div><?
					if ($file){
						$tmpInfo = pathinfo($file);?>
						<div class="right" style="float:right; width:auto; margin-top:2px;">
							[<a href="<?=$file;?>" target="_blank">파일보기</a>]
							[<a href="#" onclick="lfAttchFileRemove('<?=$jumin;?>','<?=$appNo;?>','<?=$key;?>','<?=$i;?>','<?=$tmpInfo['extension'];?>'); return false;">파일삭제</a>]
						</div><?
					}?>
				</td>
			</tr><?
		}?>
		<tr>
			<td class="center bottom last" style="padding:10px;"><span class="btn_pack m"><button onclick="fileUpload();">저장</button></span></td>
		</tr>
	</tbody>
</table>
<?
	function findFile($path, $file){
		$dir = $path;
		$search = $file;

		if (is_dir($dir)){
			if ($dh = opendir($dir)){
				while (($file = readdir($dh)) != false){
					if ($file != "." && $file != ".."){
						if (filetype($dir ."/". $file) == "file"){
							$pattern = '/'.$search.'/';
							if (preg_match($pattern,$file)){
								$arr[] = $dir."/".$file;
							}
						}
					}
				}
			}

			closedir($dh);

			if (count($arr) != 0) sort($arr,SORT_REGULAR);
		}

		return $arr[0];
	}

	include_once('../inc/_db_close.php');
?>