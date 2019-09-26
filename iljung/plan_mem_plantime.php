<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$yymm	= $_POST['yymm'];

	$sql = 'SELECT	cf_mem_cd AS mem_cd, cf_mem_nm AS mem_nm
			FROM	client_family
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		cf_jumin = \''.$jumin.'\'
			ORDER	BY mem_nm';
	$memR = $conn->_fetch_array($sql);
	$memCnt = SizeOf($memR);

	if ($memCnt > 0){?>
		<table class="my_table" style="width:100%; border-top:1px solid #CCCCCC;">
			<colgroup>
				<col width="190px">
				<col>
			</colgroup>
			<tbody><?
				for($i=0; $i<$memCnt; $i++){?>
					<tr><?

					$sql = 'SELECT	ROUND(SUM(CASE WHEN t01_svc_subcode = \'200\' AND t01_toge_umu = \'Y\' THEN t01_sugup_soyotime ELSE 0 END) / 60, 1) AS family_time
							,		ROUND(SUM(CASE WHEN t01_svc_subcode = \'200\' AND t01_toge_umu = \'Y\' THEN 0 ELSE t01_sugup_soyotime END) / 60, 1) AS other_time
							FROM	t01iljung
							WHERE	t01_ccode LIKE \'dolvoin%\'
							AND		t01_mkind		 = \'0\'
							AND		t01_del_yn		 = \'N\'
							AND		IFNULL(t01_bipay_umu, \'N\') != \'Y\'
							AND		LEFT(t01_sugup_date,6) = \''.$yymm.'\'
							AND		INSTR(CONCAT(\'/\',t01_mem_cd1,\'/\',t01_mem_cd2),\'/'.$memR[$i]['mem_cd'].'\') > 0';

					$R = $conn->get_array($sql);

					if ($i == 0){?>
						<th class="center bold bottom" rowspan="<?=$memCnt;?>">가족케어 요양보호사 근무시간</th><?
					}?>
					<td class="left bottom last" style="border-top:<?=$i > 0 ? '1px solid #CCCCCC;' : 'none';?>">
						<div style="float:left; width:150px;">요양보호사명 : <?=$memR[$i]['mem_nm'];?></div>
						<div style="float:left; width:150px;">가족케어시간 : <?=$R['family_time'];?></div>
						<div style="float:left; width:150px;">타수급자케어시간 : <?=$R['other_time'];?></div><?
						if ($R['other_time'] > 150){?>
							<div class="bold" style="float:left; width:auto; color:RED;">※타수급자 근무시 160시간이상 근무 할 수 없습니다.</div>
							<script type="text/javascript">
								alert('가족요양보호사는 타수급자 근무시 160시간이상 근무할 수 없습니다.\n일정에 참고하여 주십시오.');
							</script><?
						}?>
					</td>
					</tr><?

					Unset($R);
				}?>
			</tbody>
		</table><?
	}
	Unset($memR);

	include_once('../inc/_db_open.php');
?>