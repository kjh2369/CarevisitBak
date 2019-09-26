<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$sr		= $_POST['sr'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$findClient = $_POST['findClient'];

?>
	<tr>
		<td class="bottom last" style="padding-bottom:15px;" >
			<div style="margin-left:5px; margin-top:5px; width:auto; line-height:1em;"><label><input id="chk" name="chk" type="checkbox" class="checkbox" gbn="M" style="margin:0;" value="" onclick="$('input:checkbox[id^=\'chk\']').attr('checked',$(this).attr('checked'));"><span class="bold">전체</span></label></div><?
			$sql = 'SELECT	DISTINCT svc.jumin AS cd_key
					,		CASE WHEN IFNULL(b.jumin,\'\') != \'\' THEN b.jumin ELSE svc.jumin END AS jumin
					,		mst.m03_name AS name
					,		LEFT(mst.m03_name, 1) AS cho
					,		mst.m03_key AS cd
					FROM	client_his_svc AS svc
					INNER	JOIN	m03sugupja AS mst
							ON		m03_ccode = svc.org_no
							AND		m03_mkind = \'6\'
							AND		m03_jumin = svc.jumin
					INNER	JOIN	mst_jumin AS b
							ON		b.org_no= svc.org_no
							AND		b.gbn	= \'1\'
							AND		b.code	= svc.jumin
					WHERE	svc.org_no = \''.$code.'\'
					AND		svc.svc_cd = \''.$sr.'\'
					AND		DATE_FORMAT(svc.from_dt, \'%Y%m\') <= \''.$year.$month.'\'
					AND		DATE_FORMAT(svc.to_dt,   \'%Y%m\') >= \''.$year.$month.'\'';

			if($findClient) $sql .= 'AND mst.m03_name like \'%'.$findClient.'%\'';


			$sql .=	'ORDER	BY CASE WHEN cho >= \'가\' THEN 1 ELSE 2 END, name';

			//if ($debug) echo nl2br($sql);

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if ($cho != $myF->GetCho($row['cho'])){
					$cho  = $myF->GetCho($row['cho']);
					$choCd= $myF->GetChoCode($row['cho']);

					if ($choCd < 0) $choCd = 99999;?>
					<div style="width:auto; margin-left:20px; margin-top:3px; line-height:1em;"><label><input id="chk<?=$choCd;?>" name="chk" type="checkbox" class="checkbox" gbn="A" style="margin:0;" value=""><span class="bold"><?=($cho ? $cho : '그외');?></span></label></div><?
				}


				$birthDay	= $myF->issToBirthDay($row['jumin'],'.');
				$gender		= $myF->issToGender($row['jumin']);
				$key		= $ed->en($row['cd_key']);?>
				<div style="margin-left:35px; margin-top:3px; line-height:1em;">
					<label style="cursor:default;">
						<input id="chk<?=$choCd;?>_<?=$row['cd'];?>" name="chk" type="checkbox" class="checkbox on" gbn="P" style="margin:0;" value="<?=$key;?>">
						<span id="client_name" class="bold nowrap" style="width:70px; line-height:14px;"><?=$row['name'];?></span>
						<span class="nowrap" style="width:60px;"><?=$birthDay;?></span>
						<span class="nowrap" style="width:50px; color:#<?=($gender == '남' ? '0000FF' : 'FF0000');?>;"><?=$gender;?></span>
					</label>
				</div><?
			}

			$conn->row_free();

			Unset($arrCho);?>
		</td>
	</tr>
<?
	include_once('../inc/_db_close.php');
?>