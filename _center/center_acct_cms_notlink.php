<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$CMS	= '00000000'.$_POST['CMS'];
	$CMS	= SubStr($CMS, StrLen($CMS) - 8, StrLen($CMS));
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$yymm	= $myF->dateAdd('day', -1, $yymm.'01', 'Ym');

	//청구금액
	$sql = 'SELECT	SUM(acct_amt)
			FROM	cv_svc_acct_list
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'';
	$acctAmt = $conn->get_data($sql);

	/*
	//CMS 연결금액
	$sql = 'SELECT	SUM(link_amt) AS link_amt
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'
			AND		cms_no	= \''.$CMS.'\'
			AND		del_flag= \'N\'';
	$linkAmt = $conn->get_data($sql);
	*/


	/*
	//입금등록금액
	$sql = 'SELECT	SUM(IFNULL(link_amt,0)) AS link_amt
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'
			AND		cms_no	= \'\'
			AND		cms_dt	= \'\'
			AND		cms_seq	= \'\'
			AND		del_flag= \'N\'';
	$bankAmt = $conn->get_data($sql);
	*/

	//연결금액
	$sql = 'SELECT	SUM(link_amt) AS link_amt
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$yymm.'\'
			AND		del_flag= \'N\'
			AND		CASE WHEN IFNULL(link_stat,\'\') = \'\' THEN \'1\' ELSE link_stat END = \'1\'';
	$linkAmt = $conn->get_data($sql);

	//미납금액
	$nonpay = $acctAmt - $linkAmt;

	$unitStr = Array('1'=>'<span style="color:BLUE;">연결</span>', '3'=>'일부연결', '9'=>'<span style="color:RED;">미연결</span>');
	$colgroup = '
		<col width="40px">
		<col width="50px">
		<col width="80px">
		<col width="70px">
		<col width="80px">
		<col width="70px">
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
			<th class="head">상태</th>
			<th class="head">연결금액</th>
			<th class="head">미연결금액</th>
			<th class="head">연결상태</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>

<div style="width:100%; height:10px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody id="ID_CMS_LIST"><?
			//CMS 미연결내역
			$sql = 'SELECT	a.cms_no, a.cms_dt, a.seq, a.in_amt, a.in_stat, a.link_stat, SUM(IFNULL(b.link_amt,0)) AS link_amt
					FROM	cv_cms_reg AS a
					LEFT	JOIN	cv_cms_link AS b
							ON		b.org_no	= a.org_no
							AND		b.cms_no	= a.cms_no
							AND		b.cms_dt	= a.cms_dt
							AND		b.cms_seq	= a.seq
							AND		b.del_flag	= \'N\'
					WHERE	a.org_no	 = \''.$orgNo.'\'
					AND		a.link_stat != \'1\'
					AND		a.del_flag	 = \'N\'
					GROUP	BY a.cms_no, a.cms_dt, a.seq
					ORDER	BY cms_dt';

			//echo '<tr><td colspan="10">'.nl2br($sql).'</td></tr>';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count($i);
			$no = 1;
			$inAmt = 0;
			$linkAmt = 0;

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				$nonLinkAmt = $row['in_amt'] - $row['link_amt'];

				if (!$row['link_stat']) $row['link_stat'] = '9';?>
				<tr no="<?=$row['cms_no'];?>" dt="<?=$row['cms_dt'];?>" yymm="<?=$row['yymm'];?>" seq="<?=$row['seq'];?>" amt="<?=$nonLinkAmt;?>">
					<td class="center"><?=$no;?></td>
					<td class="center">CMS</td>
					<td class="center"><?=$myF->dateStyle($row['cms_dt'],'.');?></td>
					<td class="center"><div class="right"><?=number_format($row['in_amt']);?></div></td>
					<td class="center"><?=$row['in_stat'];?></td>
					<td class="center"><div class="right"><?=number_format($row['link_amt']);?></div></td>
					<td class="center"><div class="right" style="color:BLUE;"><?=number_format($nonLinkAmt);?></div></td>
					<td class="center"><?=$unitStr[$row['link_stat']];?></td>
					<td class="center"><?
						if ($nonpay > 0){?>
							<div class="left">
								<span class="btn_pack small"><button onclick="lfAcctCMSLink(this);">연결</button></span>
							</div><?
						}?>
					</td>
				</tr><?

				$inAmt += $row['in_amt'];
				$linkAmt += $row['link_amt'];

				$no ++;
			}

			$conn->row_free();


			//입금미연결내역
			$sql = 'SELECT	a.yymm, a.seq, a.bank_dt, a.bank_nm, a.bank_acct, a.link_amt, a.link_stat, a.prepay_seq
					FROM	cv_cms_link AS a
					WHERE	a.org_no	= \''.$orgNo.'\'
					AND		a.link_stat = \'5\'
					AND		a.del_flag	= \'N\'
					AND		a.link_amt	> 0
					AND		IFNULL(a.cms_no,\'\') = \'\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);?>
				<tr yymm="<?=$row['yymm'];?>" seq="<?=$row['seq'];?>">
					<td class="center"><?=$no;?></td>
					<td class="center">입금</td>
					<td class="center"><?=$myF->dateStyle($row['bank_dt'],'.');?></td>
					<td class="center"><div class="right"><?=number_format($row['link_amt']);?></div></td>
					<td class="center"><?
						if ($row['prepay_seq']){
							echo '선입금';
						}else{
							echo '입금';
						}?>
					</td>
					<td class="center"><div class="right">0</div></td>
					<td class="center"><div class="right" style="color:BLUE;"><?=number_format($row['link_amt']);?></div></td>
					<td class="center"><span style="color:RED;">미연결</span></td>
					<td class="center"><?
						if ($nonpay > 0){?>
							<div class="left">
								<span class="btn_pack small"><button onclick="lfAcctBankLink(this);">연결</button></span>
							</div><?
						}?>
					</td>
				</tr><?
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
			<td class="center sum" style="border-top:1px solid #CCCCCC;"><div class="right"><?=number_format($inAmt);?></div></td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;">&nbsp;</td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;"><div class="right"><?=number_format($linkAmt);?></div></td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;"><div class="right"><?=number_format($inAmt - $linkAmt);?></div></td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;">&nbsp;</td>
			<td class="center sum last" style="border-top:1px solid #CCCCCC;">&nbsp;</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>