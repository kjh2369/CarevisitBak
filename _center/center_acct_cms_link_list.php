<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$CMS	= $_POST['CMS'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$yymm	= $myF->dateAdd('day', -1, $yymm.'01', 'Ym');

	if (StrLen($CMS) < 8){
		$CMS = '00000000'.$CMS;
		$CMS = SubStr($CMS, StrLen($CMS) - 8, StrLen($CMS));
	}

	$colgroup = '
		<col width="40px">
		<col width="50px">
		<col width="80px">
		<col width="70px">
		<col width="100px">
		<col width="70px">
		<col width="70px">
		<col>';
?>
<table id="ID_CMS_LIST_CAPTION" class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">구분</th>
			<th class="head">일자</th>
			<th class="head">금액</th>
			<th class="head">은행명</th>
			<th class="head">입금자</th>
			<th class="head">상태</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>

<div style="width:100%; height:10px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody id="ID_CMS_LIST"><?
			$sql = 'SELECT	seq, cms_no, cms_dt, cms_seq, link_amt
					,		bank_nm, bank_acct, prepay_seq, in_stat
					,		IFNULL(link_stat,\'1\') AS link_stat
					,		CASE WHEN IFNULL(cms_no,\'\') != \'\' THEN cms_dt ELSE bank_dt END AS date
					,		CASE WHEN IFNULL(cms_no,\'\') != \'\' THEN \'CMS\' ELSE \'입금\' END AS gbn
					FROM	cv_cms_link
					WHERE	org_no	 = \''.$orgNo.'\'
					AND		yymm	 = \''.$yymm.'\'
					AND		del_flag = \'N\'
					AND		IFNULL(link_stat,\'\') != \'5\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count($i);
			$no = 1;

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if ($row['link_stat'] == '1'){
					$row['link_stat'] = '연결';
				}else if ($row['link_stat'] == '5'){
					if ($row['prepay_seq']){
						$row['link_stat'] = '선입금';
					}else{
						$row['link_stat'] = '입금';
					}
				//}else if ($row['link_stat'] == '9'){
				//	$row['link_stat'] = '결손';
				}

				if ($row['in_stat'] == '9'){
					$row['link_stat'] = '결손/'.$row['link_stat'];
				}?>
				<tr seq="<?=$row['seq'];?>" CMSNo="<?=$row['cms_no'];?>" CMSDate="<?=$row['cms_dt'];?>" CMSSeq="<?=$row['cms_seq'];?>" prepaySeq="<?=$row['prepay_seq'];?>">
					<td class="center"><?=$no;?></td>
					<td class="center"><?=$row['gbn'];?></td>
					<td class="center"><?=$myF->dateStyle($row['date'],'.');?></td>
					<td class="center"><div class="right"><?=number_format($row['link_amt']);?></div></td>
					<td class="center"><?=$row['bank_nm'];?></td>
					<td class="center"><?=$row['bank_acct'];?></td>
					<td class="center"><?=$row['link_stat'];?></td>
					<td class="center">
						<div class="left"><?
							if ($row['gbn'] == 'CMS'){?>
								<span class="btn_pack small"><button onclick="lfAcctCMSUnlink(this);">해제</button></span><?
							}else{
								if ($row['prepay_seq']){
									$s = '해제';
								}else{
									$sql = 'SELECT	COUNT(*)
											FROM	cv_cms_link
											WHERE	org_no		= \''.$orgNo.'\'
											AND		prepay_seq	= \''.$yymm.'_'.$row['seq'].'\'
											AND		del_flag	= \'N\'';
									$prepayCnt = $conn->get_data($sql);

									if ($prepayCnt > 0){
										$s = '해제';
									}else{
										$s = '삭제';
									}
								}?>
								<span class="btn_pack small"><button onclick="lfAcctCMSUnlink(this);"><?=$s;?></button></span><?
							}?>
						</div>
					</td>
				</tr><?

				$linkAmt += $row['link_amt'];

				$no ++;
			}

			$conn->row_free();?>
		</tbody>
	</table>
</div>

<table id="ID_CMS_LIST_SUM" class="my_table" style="width:100%; margin-top:-1px;">
	<colgroup><?=$colgroup;?></colgroup>
	<tbody>
		<tr>
			<td class="center sum" style="border-top:1px solid #CCCCCC;">&nbsp;</td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;">&nbsp;</td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;">합계</td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;"><div class="right"><?=number_format($linkAmt);?></div></td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;">&nbsp;</td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;">&nbsp;</td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;">&nbsp;</td>
			<td class="center sum last" style="border-top:1px solid #CCCCCC;">&nbsp;</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>