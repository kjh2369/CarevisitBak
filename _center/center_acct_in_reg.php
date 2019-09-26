<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$CMS	= $_POST['CMS'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);

	if ($year){
		$yymm = $year.($month < 10 ? '0' : '').$month;
		$yymm = $myF->dateAdd('day', -1, $yymm.'01', 'Ym');
	}else{
		$year = '0000';
		$month= '00';
		$yymm = '000000';
	}

	if (StrLen($CMS) < 8){
		$CMS = '00000000'.$CMS;
		$CMS = SubStr($CMS, StrLen($CMS) - 8, StrLen($CMS));
	}

	$colgroup = '
		<col width="40px">
		<col width="80px">
		<col width="100px">
		<col width="70px">
		<col width="80px">
		<col width="62px">
		<col>';
?>
<script type="text/javascript">
	//입금등록
	function lfAcctInReg(obj){
		var obj = __GetTagObject($(obj),'TR');

		if ($('#ID_ACCT_IN').attr('id')){
			var date	= $('#txtDate',$('#ID_ACCT_IN')).val();
			var bankNm	= $('#txtBankNm',$('#ID_ACCT_IN')).val();
			var acctNm	= $('#txtAcctNm',$('#ID_ACCT_IN')).val();
			var inAmt	= $('#txtAmt',$('#ID_ACCT_IN')).val();
			var stat	= $('#cboStat',$('#ID_ACCT_IN')).val();

			if (!date){
				alert('입금일자를 입력하여 주십시오.');
				$('#txtDate',$('#ID_ACCT_IN')).focus();
				return;
			}

			if (__str2num(inAmt) == 0){
				alert('입금금액을 입력하여 주십시오.');
				$('#txtAmt',$('#ID_ACCT_IN')).focus();
				return;
			}
		}else{
			var date	= $('#txtDate').val();
			var bankNm	= $('#txtBankNm').val();
			var acctNm	= $('#txtAcctNm').val();
			var inAmt	= $('#txtAmt').val();
			var stat	= $('#cboStat').val();

			if (!date){
				alert('입금일자를 입력하여 주십시오.');
				$('#txtDate').focus();
				return;
			}

			if (__str2num(inAmt) == 0){
				alert('입금금액을 입력하여 주십시오.');
				$('#txtAmt').focus();
				return;
			}
		}

		//var obj	= $('div[id^="ID_CENTER_"][selYn="Y"]');
		var orgNo	= '<?=$orgNo;?>'; //$('#ID_CELL_NO',obj).text();
		var CMS		= ''; //$('#ID_CELL_CMS',obj).text();

		$.ajax({
			type:'POST'
		,	url:'./center_acct_in_set.php'
		,	data:{
				'orgNo'	:orgNo
			,	'CMS'	:CMS
			,	'date'	:date
			,	'bankNm':bankNm
			,	'acctNm':acctNm
			,	'inAmt'	:inAmt
			,	'stat'	:stat
			,	'year'	:'<?=SubStr($yymm, 0, 4);?>'//$('#yymm').attr('year')
			,	'month'	:'<?=IntVal(SubStr($yymm, 4));?>'//$('#yymm').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					try{
						//청구 및 입금정보
						lfAcctInfo(obj);

						//입금등록내역
						lfAcctIn();
					}catch(e){
						lfLoadBody();
					}
				}else{
					alert(result);
				}

				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfDpsDelete(cmsNo, cmsDt, cmsSeq){
		if (!cmsNo || !cmsDt || !cmsSeq) return;

		$.ajax({
			type:'POST'
		,	url:'./center_acct_deposit_delete.php'
		,	data:{
				'orgNo'	:'<?=$orgNo;?>'
			,	'year'	:'<?=SubStr($yymm, 0, 4);?>'
			,	'month'	:'<?=IntVal(SubStr($yymm, 4));?>'
			,	'cmsNo'	:cmsNo
			,	'cmsDt'	:cmsDt
			,	'cmsSeq':cmsSeq
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					try{
						//청구 및 입금정보
						lfAcctInfo(obj);

						//입금등록내역
						lfAcctIn();
					}catch(e){
						alert('정상적으로 처리되었습니다.');
						lfLoadBody();
					}
				}else{
					alert(result);
				}

				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
</script>

<table id="ID_CMS_LIST_CAPTION" class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">일자</th>
			<th class="head">은행명</th>
			<th class="head">입금자</th>
			<th class="head">금액</th>
			<th class="head">상태</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center">-</td>
			<td class="center"><input id="txtDate" type="text" value="" class="date"></td>
			<td class="center"><input id="txtBankNm" type="text" value="" style="width:100%;"></td>
			<td class="center"><input id="txtAcctNm" type="text" value="" style="width:100%;"></td>
			<td class="center"><input id="txtAmt" type="text" value="0" class="number" style="width:100%;"></td>
			<td class="center">
				<select id="cboStat" style="width:auto;">
					<option value="5">입금</option>
					<!--option value="9">결손</option-->
				</select>
			</td>
			<td class="center last">
				<div class="left">
					<span class="btn_pack small"><button onclick="lfAcctInReg(this);">등록</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<div style="width:100%; height:10px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody id="ID_CMS_LIST"><?
			/*
			$sql = 'SELECT	seq, bank_dt, bank_nm, bank_acct, link_amt, link_stat, in_stat, prepay_seq, org_amt
					FROM	cv_cms_link
					WHERE	org_no	= \''.$orgNo.'\'
					AND		yymm	= \''.$yymm.'\'
					AND		del_flag= \'N\'
					AND		cms_no	= \'\'
					AND		IFNULL(link_stat,\'\') != \'\'';
			*/

			$sql = 'SELECT	cms_no, cms_dt, seq, bank_nm, bank_acct, in_amt
					FROM	cv_cms_reg
					WHERE	org_no	= \''.$orgNo.'\'
					AND		cms_no	LIKE \'BANK%\'
					AND		del_flag= \'N\'
					ORDER	BY cms_dt DESC';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();
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
				}

				if ($row['prepay_seq']){
					$cls = $row['prepay_seq'];
				}else{
					$cls = $yymm.'_'.$row['seq'];
				}?>
				<tr class="CLS_ROW_<?=$cls;?>">
					<td class="center"><?=$no;?></td>
					<td class="center"><?=$myF->dateStyle($row['cms_dt'],'.'); //$myF->dateStyle($row['bank_dt'],'.');?></td>
					<td class="center"><?=$row['bank_nm'];?></td>
					<td class="center"><?=$row['bank_acct'];?></td>
					<td class="center"><div class="right"><?=number_format($row['in_amt']); //number_format($yymm == '000000' ? $row['org_amt'] : $row['link_amt']);?></div></td>
					<td class="center">입금<?//=$row['link_stat'];?></td>
					<td class="last"><?
						//if ($yymm == '000000'){
						//	if ($row['org_amt'] == $row['link_amt']){?>
								<span class="btn_pack small" style="margin-left:5px;"><button onclick="lfDpsDelete('<?=$row['cms_no'];?>','<?=$row['cms_dt'];?>','<?=$row['seq'];?>');" style="color:RED;">삭제</button></span><?
						//	}
						//}?>
					</td>
				</tr><?

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
			<td class="center sum" style="border-top:1px solid #CCCCCC;">&nbsp;</td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;">합계</td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;"><div class="right"><?=number_format($linkAmt);?></div></td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;">&nbsp;</td>
			<td class="center sum last" style="border-top:1px solid #CCCCCC;">&nbsp;</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>