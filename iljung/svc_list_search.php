<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);
	$appNo	= $_POST['appNo'];

	$sql = 'SELECT	app_no
			,		name
			,		COUNT(reg_dt) AS cnt
			,		SUM(CASE WHEN svc_gbn = \'200\' THEN 1 ELSE 0 END) AS cnt_200
			,		SUM(CASE WHEN svc_gbn = \'500\' THEN 1 ELSE 0 END) AS cnt_500
			,		SUM(CASE WHEN svc_gbn = \'800\' THEN 1 ELSE 0 END) AS cnt_800
			FROM	lg2cv
			WHERE	org_no	= \''.$orgNo.'\'
			AND		reg_dt	BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'
			AND		use_yn	= \'Y\'';

	if ($appNo){
		$sql .= '
			AND		app_no = \''.$appNo.'\'';
	}

	$sql .= '
			GROUP	BY app_no
			ORDER	BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$clientCnt ++;
		$iljungCnt += $row['cnt'];?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><div class="left"><?=$row['name'];?></div></td>
			<td class="center"><div class="left"><?=$row['app_no'];?></div></td>
			<td class="center"><div class="right"><?=$row['cnt'] > 0 ? number_format($row['cnt']) : '';?></div></td>
			<td class="center last">
				<div class="left">
					<span class="btn_pack small"><button onclick="lfExcel('LIST','<?=$fromDt;?>','<?=$toDt;?>','<?=$row['app_no'];?>');">서비스내역</button></span><?
					if ($debug){?>
						<span class="btn_pack small"><button onclick="lfExcel('PROVIDE','<?=$fromDt;?>','<?=$toDt;?>','<?=$row['app_no'];?>','200');" <?=!$row['cnt_200'] ? 'disabled="true"' : '';?>>급여제공기록지(요양)</button></span>
						<span class="btn_pack small"><button onclick="lfExcel('PROVIDE','<?=$fromDt;?>','<?=$toDt;?>','<?=$row['app_no'];?>','500');" <?=!$row['cnt_500'] ? 'disabled="true"' : '';?>>급여제공기록지(목욕)</button></span>
						<span class="btn_pack small"><button onclick="lfExcel('PROVIDE','<?=$fromDt;?>','<?=$toDt;?>','<?=$row['app_no'];?>','800');" <?=!$row['cnt_800'] ? 'disabled="true"' : '';?>>급여제공기록지(간호)</button></span><?
					}?>
				</div>
			</td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	if ($rowCnt > 0){?>
		<!-- CUT_LINE -->
		<tr>
			<td class="center" colspan="3"><div class="right"><?=$clientCnt;?>명</div></td>
			<td class="center"><div class="right"><?=$iljungCnt;?></div></td>
				<td class="center last">
					<div class="left">
						<span class="btn_pack small"><button onclick="lfExcel('LIST','<?=$fromDt;?>','<?=$toDt;?>');">서비스내역</button></span>
						<span class="btn_pack small"><button onclick="lfExcel('LGC','<?=$fromDt;?>','<?=$toDt;?>');">공단자료확인</button></span>
					</div>
				</td>
		</tr><?
	}else{?>
		<tr>
			<td class="center last" colspan="5">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	include_once('../inc/_db_close.php');
?>