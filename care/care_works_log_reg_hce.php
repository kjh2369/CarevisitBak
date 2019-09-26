<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['svcCd'];
	$jumin	= $ed->de($_POST['jumin']);

	//대상자 정보
	$sql = 'SELECT	m03_name
			,		m03_key
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \'6\'
			AND		m03_jumin = \''.$jumin.'\'';

	$row = $conn->get_array($sql);

	$IPIN = $row['m03_key'];

	Unset($row);?>
	<div class="title title_border">사례관리 과정상담연계</div>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="90px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th>사례관리 차수</th>
				<td class="last">
					<select id="cboHCESeq" style="width:auto;" onchange="lfHCEProcList('<?=$IPIN;?>',$(this).val());"><?
					//사례관리 정보
					$sql = 'SELECT	rcpt_seq
							,		hce_seq
							,		rcpt_dt
							FROM	hce_receipt
							WHERE	org_no	= \''.$orgNo.'\'
							AND		org_type= \''.$SR.'\'
							AND		IPIN	= \''.$IPIN.'\'
							AND		del_flag= \'N\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();
					$hce = '';

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['rcpt_seq'];?>" selected><?=$myF->dateStyle($row['rcpt_dt'],'.');?> / <?=$row['hce_seq'];?>차</option><?
					}

					$conn->row_free();?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="50px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="">상담자</th>
				<td class="left last" onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='#FFFFFF';"><div id="ID_PROC_MEM" jumin="" onclick="lfMemFind();">선택하여주십시오.</div></td>
			</tr>
			<tr>
				<th class="last" colspan="2">
					<div style="float:left; width:auto;">과정상담이력</div>
					<div style="float:right; width:auto; padding-right:5px;"><span class="btn_pack small"><button onclick="lfHCEProcReg('<?=$IPIN;?>',$('#cboHCESeq').val(),'',$('#cboGbn').val());">추가</button></span></div>
					<div style="float:right; width:auto;">
						<select id="cboGbn" style="width:auto;"><?
						$sql = 'SELECT	code, name
								FROM	hce_gbn
								WHERE	type	= \'CT\'
								AND		use_yn	= \'Y\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count($i);

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<option value="<?=$row['code'];?>"><?=$row['name'];?></option><?
						}

						$conn->row_free();?>
						</select>
					</div>
				</th>
			</tr>
		</tbody>
	</table>
	<div id="ID_PROC_COUNSEL" style="width:100%; height:99px; overflow-x:hidden; overflow-y:scroll; padding:3px;"></div><?
	include_once('../inc/_db_close.php');
?>