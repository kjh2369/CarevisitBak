<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_GET['orgNo'];
	$year	= $_GET['year'];
	$month	= IntVal($_GET['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$ym		= $myF->dateAdd('month', -1, $yymm.'01', 'Ym');

	$sql = 'SELECT	m00_store_nm AS org_nm, m00_mname AS mg_nm
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'
			ORDER	BY m00_mkind
			LIMIT	1';

	$row = $conn->get_array($sql);

	$orgNm = $row['org_nm'];
	$mgNm = $row['mg_nm'];

	Unset($row);


	$sql = 'SELECT	SUM(CASE WHEN svc_gbn != \'9\' AND svc_cd != \'99\' THEN CASE WHEN tmp_amt > 0 THEN tmp_amt ELSE acct_amt END ELSE 0 END) AS acct_amt
			,		SUM(CASE WHEN svc_gbn = \'9\' AND svc_cd = \'99\' THEN acct_amt ELSE 0 END) AS dc_amt
			FROM	cv_svc_acct_list
			WHERE	org_no	= \''.$orgNo.'\'
			AND		acct_ym = \''.$yymm.'\'';

	$row = $conn->get_array($sql);

	$acAmt = $row['acct_amt'];
	$dcAmt = $row['dc_amt'];

	Unset($row);
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		$('#ID_BODY_LIST').height(__GetHeight($('#ID_BODY_LIST')));
		$('.CLS_DATA').unbind('change').bind('change', function(){
			var row = __GetTagObject($(this),'TR');
			var stndAmt = 0, overCnt = 0, overCost = 0, overAmt = 0, acctAmt = 0;

			stndAmt = __str2num($('#txtStndAmt',row).val());
			overCnt = __str2num($('#txtOverCnt',row).val());
			overCost = __str2num($('#txtOverCost',row).val());
			overAmt = overCnt * overCost;
			acctAmt = stndAmt + overAmt;

			$('div:first',$('td',row).eq(5)).css('color','RED').text(__num2str(overAmt));
			$('div:first',$('td',row).eq(6)).css('color','RED').text(__num2str(acctAmt));

			lfSetAmt();
		});

		lfSetAmt();
	});

	function lfSetAmt(){
		var totStndAmt = 0, totOverAmt = 0, totAcctAmt = 0, totTmpAmt = 0;

		$('input:text[id="txtStndAmt"]').each(function(){
			totStndAmt += __str2num($(this).val());
		});

		$('tr',$('tbody',$('#ID_BODY_LIST'))).each(function(){
			totOverAmt += __str2num($('div:first',$('td',this).eq(5)).text());
			totAcctAmt += __str2num($('div:first',$('td',this).eq(6)).text());
			totTmpAmt += __str2num($('div:first',$('td',this).eq(7)).text());
		});

		$('#ID_CELL_SUM_0').text(__num2str(totStndAmt));
		$('#ID_CELL_SUM_5').text(__num2str(totOverAmt));
		$('#ID_CELL_SUM_6').text(__num2str(totAcctAmt));
		$('#ID_CELL_SUM_7').text(__num2str(totTmpAmt));
	}

	function lfSave(){
		var data = {};

		data['orgNo'] = '<?=$orgNo;?>';
		data['year'] = '<?=$year;?>';
		data['month'] = '<?=$month;?>';
		data['data'] = '';

		$('tr',$('tbody',$('#ID_BODY_LIST'))).each(function(){
			data['data'] += (data['data'] ? '?' : '');
			data['data'] += 'svcGbn='+$(this).attr('svcGbn');
			data['data'] += '&svcCd='+$(this).attr('svcCd');
			data['data'] += '&proCd='+($(this).attr('proCd') ? $(this).attr('proCd') : '');
			data['data'] += '&acctGbn='+($(this).attr('acctGbn') ? $(this).attr('acctGbn') : '');
			data['data'] += '&unitCd='+($(this).attr('unitCd') ? $(this).attr('unitCd') : '');
			data['data'] += '&useFrom='+($(this).attr('useFrom') ? $(this).attr('useFrom') : '');
			data['data'] += '&useTo='+($(this).attr('useTo') ? $(this).attr('useTo') : '');
			data['data'] += '&stndAmt='+($('#txtStndAmt',this).val() ? $('#txtStndAmt',this).val() : '');
			data['data'] += '&overCnt='+($('#txtOverCnt',this).val() ? $('#txtOverCnt',this).val() : '');
			data['data'] += '&overCost='+($('#txtOverCost',this).val() ? $('#txtOverCost',this).val() : '');
		});

		$.ajax({
			type:'POST'
		,	url:'./center_FEE_EDIT_modify_save.php'
		,	data:data
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
				}else{
					alert(result);
				}
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
<div class="title title_border">요금조정</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="100px">
		<col width="50px">
		<col>
		<col width="70px">
		<col width="100px">
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관기호</th>
			<td class="left"><?=$orgNo;?></td>
			<th class="center">기관명</th>
			<td class="left"><?=$orgNm;?></td>
			<th class="center">대표자명</th>
			<td class="left last"><?=$mgNm;?></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="100px">
		<col width="70px">
		<col width="100px">
		<col width="70px">
		<col width="100px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">청구년월</th>
			<td class="left"><?=$year;?>년 <?=$month;?>월</td>
			<th class="center">청구액</th>
			<td class="left"><?=number_format($acAmt + $dcAmt);?></td>
			<th class="center">과금금액</th>
			<td class="left"><?=number_format($acAmt);?></td>
			<th class="center">할인금액</th>
			<td class="left last"><?=number_format($dcAmt);?></td>
		</tr>
	</tbody>
</table>
<div class="title title_border">
	<div style="float:right; width:auto; margin-top:10px; margin-right:5px;"><span class="btn_pack m"><button onclick="lfSave();">저장</button></span></div>
	<div style="float:left; width:auto;">요금상세내역</div>
</div><?
$colgrp = '
	<col width="110px">
	<col width="80px" span="8">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgrp;?></colgroup>
	<thead>
		<tr>
			<th class="head bold">서비스명</th>
			<th class="head bold">기본금</th>
			<th class="head bold">초과구분</th>
			<th class="head bold">제한수</th>
			<th class="head bold">초과수</th>
			<th class="head bold">초과단가</th>
			<th class="head bold">초과금액</th>
			<th class="head bold">청구금액</th>
			<th class="head bold">현재청구</th>
			<th class="head bold last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="center bold"><div class="right">합계</div></th>
			<td class="center sum"><div class="right" id="ID_CELL_SUM_0">0</div></td>
			<td class="center sum"><div class="right" id="ID_CELL_SUM_1"></div></td>
			<td class="center sum"><div class="right" id="ID_CELL_SUM_2"></div></td>
			<td class="center sum"><div class="right" id="ID_CELL_SUM_3"></div></td>
			<td class="center sum"><div class="right" id="ID_CELL_SUM_4"></div></td>
			<td class="center sum"><div class="right" id="ID_CELL_SUM_5">0</div></td>
			<td class="center sum"><div class="right" id="ID_CELL_SUM_6">0</div></td>
			<td class="center sum"><div class="right" id="ID_CELL_SUM_7">0</div></td>
			<td class="center sum"><div class="right" id="ID_CELL_SUM_8"></div></td>
		</tr>
	</tbody>
</table>
<div id="ID_BODY_LIST" style="overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgrp;?></colgroup>
		<tbody><?
			$strUnitGbn = Array('1'=>'고객', '2'=>'직원', '3'=>'SMS', '4'=>'고정'); //초과 단위 1:고객, 2:직원, 3:SMS, 4:고정

			$sql = 'SELECT	a.svc_gbn, a.svc_cd, a.svc_nm, c.stnd_amt, a.unit_gbn, b.limit_cnt, c.over_cnt, b.over_cost, c.tmp_amt
					,		a.pro_cd, b.acct_gbn, b.from_dt, b.to_dt
					FROM	(
							SELECT	CAST(\'1\' AS char) AS svc_gbn, svc_cd, svc_nm, unit_gbn, day_cal, pro_cd
							FROM	cv_svc_main
							WHERE	parent_cd IS NOT NULL
							UNION	ALL
							SELECT	CAST(\'2\' AS char), svc_cd, svc_nm, unit_gbn, day_cal, \'\'
							FROM	cv_svc_sub
							WHERE	parent_cd IS NOT NULL
							UNION	ALL
							SELECT	CAST(\'9\' AS char), CAST(\'99\' AS char), \'할인금\', \'4\', \'N\', \'\'
							) AS a
					INNER	JOIN	cv_svc_fee AS b
							ON		b.org_no	= \''.$orgNo.'\'
							AND		b.svc_gbn	= a.svc_gbn
							AND		b.svc_cd	= a.svc_cd
							AND		b.use_yn	= \'Y\'
							AND		b.del_flag	= \'N\'
							AND		LEFT(b.from_dt,6) <= \''.$ym.'\'
							AND		LEFT(b.to_dt,6)   >= \''.$ym.'\'
					LEFT	JOIN	cv_svc_acct_list AS c
							ON		c.svc_gbn	= a.svc_gbn
							AND		c.svc_cd	= a.svc_cd
							AND		c.org_no	= b.org_no
							AND		c.acct_ym	= \''.$yymm.'\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();
			$dcYn = false;

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if ($row['svc_gbn'] == '9' && $row['svc_cd'] == '99') $dcYn = true;?>
				<tr svcGbn="<?=$row['svc_gbn'];?>" svcCd="<?=$row['svc_cd'];?>" proCd="<?=$row['pro_cd'];?>" acctGbn="<?=$row['acct_gbn'];?>" unitCd="<?=$row['unit_gbn'];?>" useFrom="<?=$row['from_dt'];?>" useTo="<?=$row['to_dt'];?>">
					<th class="center"><div class="left"><?=$row['svc_nm'];?></div></th>
					<td class="center"><input id="txtStndAmt" type="text" value="<?=number_format($row['stnd_amt']);?>" class="number CLS_DATA" style="width:100%;"></td>
					<td class="center"><?=$strUnitGbn[$row['unit_gbn']];?></td>
					<td class="center"><div class="right"><?=$row['limit_cnt'];?></div></td>
					<td class="center"><input id="txtOverCnt" type="text" value="<?=number_format($row['over_cnt']);?>" class="number CLS_DATA" style="width:100%;"></td>
					<td class="center"><input id="txtOverCost" type="text" value="<?=number_format($row['over_cost']);?>" class="number CLS_DATA" style="width:100%;"></td>
					<td class="center"><div class="right"><?=number_format($row['over_cost'] * $row['over_cnt']);?></div></td>
					<td class="center"><div class="right"><?=number_format($row['stnd_amt'] + $row['over_cost'] * $row['over_cnt']);?></div></td>
					<td class="center"><div class="right"><?=number_format($row['tmp_amt']);?></div></td>
					<td class="center"></td>
				</tr><?
			}

			if (!$dcYn){?>
				<tr svcGbn="9" svcCd="99" acctGbn="2" unitCd="4">
					<th class="center"><div class="left">할인금</div></th>
					<td class="center"><input id="txtStndAmt" type="text" value="0" class="number CLS_DATA" style="width:100%;"></td>
					<td class="center">고정</td>
					<td class="center"><div class="right"></div></td>
					<td class="center"><div class="right">0</div></td>
					<td class="center"><div class="right">0</div></td>
					<td class="center"><div class="right">0</div></td>
					<td class="center"><div class="right">0</div></td>
					<td class="center"><div class="right">0</div></td>
					<td class="center"></td>
				</tr><?
			}

			$conn->row_free();?>
		</tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>