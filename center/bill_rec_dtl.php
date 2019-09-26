<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if ($_SESSION['userLevel'] == 'A'){
		$orgNo = $_POST['orgNo'];
	}else{
		$orgNo = $_SESSION['userCenterCode'];
	}
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	//사용기간
	$sql = 'SELECT	yymm, MIN(use_from) AS from_dt, MAX(use_to) AS to_dt, SUM(acct_amt) AS acct_amt
			FROM	cv_svc_acct_list
			WHERE	org_no	= \''.$orgNo.'\'
			AND		acct_ym = \''.$yymm.'\'';
	$row = $conn->get_array($sql);
	$ym		= $row['yymm'];
	$useFrom= $myF->dateStyle($row['from_dt'],'KOR');
	$useTo	= $myF->dateStyle($row['to_dt'],'KOR');
	$acctAmt= $row['acct_amt'];
	Unset($row);

	//전월까지 미납금액
	$sql = 'SELECT	(
					SELECT	IFNULL(SUM(acct_amt),0) AS acct_amt
					FROM	cv_svc_acct_list
					WHERE	org_no	= \''.$orgNo.'\'
					AND		yymm	< \''.$ym.'\')-(
					SELECT	IFNULL(SUM(link_amt),0) AS link_amt
					FROM	cv_cms_link
					WHERE	org_no	= \''.$orgNo.'\'
					AND		yymm	< \''.$ym.'\'
					AND		del_flag= \'N\'
					AND		CASE WHEN IFNULL(link_stat,\'1\') = \'1\' THEN 1 ELSE 0 END = 1)';
	$nonpay = $conn->get_data($sql);

	//당월입금금액
	$sql = 'SELECT	SUM(link_amt) AS link_amt
			FROM	cv_cms_link
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$ym.'\'
			AND		del_flag= \'N\'
			AND		CASE WHEN IFNULL(link_stat,\'1\') = \'1\' THEN 1 ELSE 0 END = 1';
	$nowMonthInAmt = $conn->get_data($sql);

	//당월미납금액
	$sql = 'SELECT	(
					SELECT	IFNULL(SUM(acct_amt),0) AS acct_amt
					FROM	cv_svc_acct_list
					WHERE	org_no	 = \''.$orgNo.'\'
					AND		yymm	<= \''.$ym.'\')-(
					SELECT	IFNULL(SUM(link_amt),0) AS link_amt
					FROM	cv_cms_link
					WHERE	org_no	 = \''.$orgNo.'\'
					AND		yymm	<= \''.$ym.'\'
					AND		del_flag = \'N\'
					AND		CASE WHEN IFNULL(link_stat,\'1\') = \'1\' THEN 1 ELSE 0 END = 1)';
	$nowMonthNonpay = $conn->get_data($sql);

	//CMS 정보
	$sql = 'SELECT	cms_no, cms_com
			FROM	cv_cms_list
			WHERE	org_no = \''.$orgNo.'\'';
	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$CMSCom = Array('1'=>'굿이오스', '2'=>'지케어', '3'=>'케어비지트');
	$CMSList = '';

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$CMSList .= ($CMSList ? '/' : '').$row['cms_no'];
	}

	$conn->row_free();

	//계좌정보
	$sql = 'SELECT	acct_gbn, trans_day, bank_nm, bank_no, bank_acct, bank_gbn, bill_yn
			FROM	cv_reg_info
			WHERE	org_no = \''.$orgNo.'\'';
	$row = $conn->get_array($sql);
	$acctGbn	= $row['acct_gbn'];
	$transDay	= IntVal($row['trans_day']);
	$billYn		= $row['bill_yn'];
	$bankNm		= $row['bank_nm'];
	$bankNo		= $row['bank_no'];
	$bankAcct	= $row['bank_acct'];
	$bankGbn	= $row['bank_gbn'];
	Unset($row);

	//청구구분
	if ($acctGbn == '1'){
		$acctGbn = 'CMS';
	}else if ($acctGbn == '2'){
		$acctGbn = '무통장';
	}

	//계좌구분
	if ($bankGbn == '1'){
		$bankGbn = '개인';
	}else if ($bankGbn == '2'){
		$bankGbn = '법인';
	}

	//출금예정일자
	$transDay = $myF->dateStyle($yymm.($transDay < 10 ? '0' : '').$transDay,'KOR');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#ID_DIV_LIST').unbind('mouseover').bind('mouseover',function(){
			$('body').css('overflow','hidden');
		}).unbind('mouseout').bind('mouseout',function(){
			$('body').css('overflow','');
		});

		$.ajax({
			type:'POST',
			url:'../center/bill_rec_dtl_search.php',
			data:{
				'orgNo':'<?=$orgNo;?>'
			,	'yymm':'<?=$ym;?>'
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(data){
				var unit= {'1':'고객', '2':'직원', '3':'문자'};
				var tot = {'TOTAL':0,'STND_AMT':0,'OVER_AMT':0,'STND_CNT':0,'OVER_CNT':0};
				var row = data.split('?');

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);
						var key = col['key'];
						var note= '';

						if (col['overCnt'] > 0) note += '추가 '+col['overCnt']+(col['unitCd'] == '3' ? '건' : '명')+'*'+__num2str(col['overCost']);
						if (col['disAmt'] > 0) note += (note ? '<br>' : '')+'할인금액 : '+__num2str(col['disAmt']);

						$('#ID_CELL_'+key+'_TOTAL').text(__num2str(col['acctAmt']));
						$('#ID_CELL_'+key+'_STND_AMT').text(__num2str(col['stndAmt']));
						$('#ID_CELL_'+key+'_OVER_AMT').text(__num2str(col['overAmt']));
						$('#ID_CELL_'+key+'_STND_CNT').text(__num2str(col['stndCnt']));
						$('#ID_CELL_'+key+'_OVER_CNT').text(__num2str(col['overCnt']));
						$('#ID_CELL_'+key+'_NOTE').text(note);

						tot['TOTAL']	+= __str2num(col['acctAmt']);
						tot['STND_AMT']	+= __str2num(col['stndAmt']);
						tot['OVER_AMT']	+= __str2num(col['overAmt']);
						tot['STND_CNT']	+= __str2num(col['stndCnt']);
						tot['OVER_CNT']	+= __str2num(col['overCnt']);
					}
				}

				$('#ID_CELL_TOTAL').text(__num2str(tot['TOTAL']));
				$('#ID_CELL_STND_AMT').text(__num2str(tot['STND_AMT']));
				$('#ID_CELL_OVER_AMT').text(__num2str(tot['OVER_AMT']));
				$('#ID_CELL_STND_CNT').text(__num2str(tot['STND_CNT']));
				$('#ID_CELL_OVER_CNT').text(__num2str(tot['OVER_CNT']));

				$('#tempLodingBar').remove();
			},
			error: function (request, status, error){
				$('#tempLodingBar').remove();

				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	});
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="center bold" style="border-bottom:1px solid #003399;">
				<div style="float:right; width:auto; margin-right:5px; margin-top:3px;"><a href="#" onclick="lfBillClose();"><img src="../image/btn_exit.png"></a></div>
				<div style="float:left; width:auto; margin-left:5px;"><?=$year;?>년 <?=$month;?>월 청구내역</div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="290px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="bottom last">
				<table class="my_table" style="width:270px; margin:10px; margin-bottom:0; border:1px solid #003399;">
					<colgroup>
						<col width="60px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">청구년월</th>
							<td class="last"><div class="left"><?=$year;?>년 <?=$month;?>월</div></td>
						</tr>
						<tr>
							<th class="center bottom">사용기간</th>
							<td class="bottom last"><div class="left"><?=$useFrom;?>~<?=$useTo;?></div></td>
						</tr>
					</tbody>
				</table>
			</td>
			<td class="bottom last">
				<table class="my_table" style="width:270px; margin:10px; margin-bottom:0; margin-left:0; border:1px solid #003399;">
					<colgroup>
						<col width="90px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">출금예정일자</th>
							<td class="last"><div class="left"><?=$transDay;?></div></td>
						</tr>
						<tr>
							<th class="center bottom">출금일자</th>
							<td class="bottom last"><div class="left"></div></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td class="bottom last">
				<table class="my_table" style="width:270px; margin:10px; margin-bottom:0; border:1px solid #003399;">
					<colgroup>
						<col width="60px">
						<col width="70px" span="3">
					</colgroup>
					<tbody>
						<tr>
							<th class="center" colspan="2">청구구분</th>
							<td class="last" colspan="2"><div class="left"><?=$acctGbn;?></div></td>
						</tr>
						<tr>
							<th class="center" rowspan="2">청구</th>
							<td class="center" style="background-color:#F6F6F6;">합계</td>
							<td class="center" style="background-color:#F6F6F6;">당월분</td>
							<td class="center" style="background-color:#F6F6F6;">미납분</td>
						</tr>
						<tr>
							<td class="center"><div class="right"><?=number_format($acctAmt+$nonpay);?></div></td>
							<td class="center"><div class="right"><?=number_format($acctAmt);?></div></td>
							<td class="center"><div class="right"><?=number_format($nonpay);?></div></td>
						</tr>
						<tr>
							<th class="center" rowspan="2">입금</th>
							<td class="center" style="background-color:#F6F6F6;">입금</td>
							<td class="center" style="background-color:#F6F6F6;">미납</td>
							<td class="center" style="background-color:#F6F6F6;">&nbsp;</td>
						</tr>
						<tr>
							<td class="center"><div class="right"><?=number_format($nowMonthInAmt);?></div></td>
							<td class="center"><div class="right"><?=number_format($nowMonthNonpay);?></div></td>
							<td class="center"><div class="right">&nbsp;</div></td>
						</tr>
						<tr>
							<th class="center" colspan="2">세금계산서</th>
							<td class="last" span="2"><div class="left"><?=$billYn;?></div></td>
						</tr>
					</tbody>
				</table>
			</td>
			<td class="bottom last">
				<table class="my_table" style="width:330px; margin:10px; margin-bottom:0; margin-left:0; border:1px solid #003399;">
					<colgroup>
						<col width="60px">
						<col width="70px">
						<col width="200px">
					</colgroup>
					<tbody>
						<tr>
							<th class="center" rowspan="6">출금<br>정보</th>
							<td class="center" style="background-color:#F6F6F6;"><div class="left">CMS 번호</div></td>
							<td class="last"><div class="left"><?=$CMSList;?></div></td>
						</tr>
						<tr>
							<td class="center" style="background-color:#F6F6F6;"><div class="left">은행명</div></td>
							<td class="last"><div class="left"><?=$bankNm;?></div></td>
						</tr>
						<tr>
							<td class="center" style="background-color:#F6F6F6;"><div class="left">계좌번호</div></td>
							<td class="last"><div class="left"><?=$bankNo;?></div></td>
						</tr>
						<tr>
							<td class="center" style="background-color:#F6F6F6;"><div class="left">예금주</div></td>
							<td class="last"><div class="left"><?=$bankAcct;?></div></td>
						</tr>
						<tr>
							<td class="center" style="background-color:#F6F6F6;"><div class="left">계좌구분</div></td>
							<td class="last"><div class="left"><?=$bankGbn;?></div></td>
						</tr>
						<tr>
							<td class="center" style="background-color:#F6F6F6;"><div class="left"></div></td>
							<td class="last"><div class="left"></div></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td class="bottom last" colspan="2">
				<table class="my_table" style="width:775px; margin:10px; border:1px solid #003399;">
					<colgroup>
						<col width="180px">
						<col width="70px">
						<col width="70px">
						<col width="70px">
						<col width="50px">
						<col width="50px">
						<col>
					</colgroup>
					<thead>
						<tr>
							<th class="head bold" rowspan="2">사용서비스</th>
							<th class="head bold" rowspan="2">합계</th>
							<th class="head bold" colspan="2">당월 청구 금액</th>
							<th class="head bold" colspan="2">사용수(건)</th>
							<th class="head bold" rowspan="2">비고</th>
						</tr>
						<tr>
							<th class="head">기본금</th>
							<th class="head">추가금</th>
							<th class="head">기본</th>
							<th class="head">추가</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="center bottom top" colspan="20">
								<div id="ID_DIV_LIST" style="width:100%; height:235px; overflow-x:hidden; overflow-y:scroll;">
									<table class="my_table" style="width:100%;">
										<colgroup>
											<col width="30px">
											<col width="40px">
											<col width="103px">
											<col width="70px">
											<col width="70px">
											<col width="70px">
											<col width="50px">
											<col width="50px">
											<col>
										</colgroup><?
										$sql = 'SELECT	COUNT(*)
												FROM	cv_svc_acct_list
												WHERE	org_no	= \''.$orgNo.'\'
												AND		acct_ym = \''.$yymm.'\'
												AND		svc_gbn	= \'9\'
												AND		svc_cd	= \'99\'';

										$dcCnt = $conn->get_data($sql);

										$sql = 'SELECT	1 AS gbn
												,		a.parent_cd AS cd
												,		b.svc_nm AS nm
												,		COUNT(a.parent_cd) AS cnt
												FROM	cv_svc_main AS a
												INNER	JOIN	cv_svc_main AS b
														ON		b.svc_cd = a.parent_cd
												WHERE	a.parent_cd IS NOT NULL
												GROUP	BY a.parent_cd
												UNION	ALL
												SELECT	2 AS gbn
												,		a.parent_cd AS cd
												,		b.svc_nm AS nm
												,		COUNT(a.parent_cd) AS cnt
												FROM	cv_svc_sub AS a
												INNER	JOIN	cv_svc_sub AS b
														ON		b.svc_cd = a.parent_cd
												WHERE	a.parent_cd IS NOT NULL
												GROUP	BY a.parent_cd';

										if ($dcCnt > 0){
											$sql .= '
												UNION	ALL
												SELECT	9, NULL, \'할인\', 1';
										}

										$conn->query($sql);
										$conn->fetch();

										$rowCnt = $conn->row_count();

										for($i=0; $i<$rowCnt; $i++){
											$row = $conn->select_row($i);

											$data[$row['gbn']]['list'][$row['cd']] = Array(
												'name'	=>$row['nm']
											,	'cnt'	=>$row['cnt']
											);
											$data[$row['gbn']]['cnt'] += $row['cnt'];
										}

										$conn->row_free();

										if (is_array($data)){
											foreach($data as $gbn => $gbnR){
												if ($gbn == 1){
													$str = 'C<br>a<br>r<br>e<br><br>서<br>비<br>스';
													$tbl = 'cv_svc_main';
												}else if ($gbn == 2){
													$str = '부<br>가';
													$tbl = 'cv_svc_sub';
												}else if ($gbn == 9){
													$str = '';
													$tbl = '';
												}else{
													break;
												}?>
												<tbody>
													<tr>
													<th class="head bold" rowspan="<?=$gbnR['cnt'];?>"><?=$str;?></th><?

													$IsFirst[0] = true;
													foreach($gbnR['list'] as $prtCd => $prtR){
														if ($IsFirst[0]){
															$IsFirst[0] = false;
														}else{?>
															<tr><?
														}

														if ($myF->_isKor($prtR['name'])){
															$len = $myF->len($prtR['name']);
															$str = '';
															$i = 0;

															while(true){
																$str .= $myF->mid($prtR['name'],$i,2);

																if ($i < $len) $str .= '<br>';

																$i += 2;

																if ($i > $len) break;
															}
														}else{
															$str = $prtR['name'];
														}?>
														<th class="head bold" rowspan="<?=$prtR['cnt'];?>"><?=$str;?></th><?

														if ($tbl){
															$sql = 'SELECT	svc_cd
																	,		svc_nm
																	,		unit_gbn
																	FROM	'.$tbl.'
																	WHERE	parent_cd = \''.$prtCd.'\'';

															$conn->query($sql);
															$conn->fetch();

															$rowCnt = $conn->row_count();
															$IsFirst[1] = true;

															for($i=0; $i<$rowCnt; $i++){
																$row = $conn->select_row($i);
																$key = $gbn.'_'.$row['svc_cd'];

																if ($IsFirst[1]){
																	$IsFirst[1] = false;
																}else{?>
																	<tr><?
																}?>

																<th class=""><?=$row['svc_nm'];?></th>
																<td><div id="ID_CELL_<?=$key;?>_TOTAL" class="right"></div></td>
																<td><div id="ID_CELL_<?=$key;?>_STND_AMT" class="right"></div></td>
																<td><div id="ID_CELL_<?=$key;?>_OVER_AMT" class="right"></div></td>
																<td><div id="ID_CELL_<?=$key;?>_STND_CNT" class="right"></div></td>
																<td><div id="ID_CELL_<?=$key;?>_OVER_CNT" class="right"></div></td>
																<td><div id="ID_CELL_<?=$key;?>_NOTE" class="left"></div></td>
																</tr><?
															}

															$conn->row_free();
														}else{
															if ($gbn == 9){?>
																<th class="">할인금액</th>
																<td><div id="ID_CELL_9_99_TOTAL" class="right"></div></td>
																<td><div id="ID_CELL_9_99_STND_AMT" class="right"></div></td>
																<td><div id="ID_CELL_9_99_OVER_AMT" class="right"></div></td>
																<td><div id="ID_CELL_9_99_STND_CNT" class="right"></div></td>
																<td><div id="ID_CELL_9_99_OVER_CNT" class="right"></div></td>
																<td><div id="ID_CELL_9_99_NOTE" class="left"></div></td>
																</tr><?
															}
														}
													}?>
												</tbody><?
											}
										}?>
									</table>
								</div>
							</td>
						</tr>
						<tr>
							<td class="sum"><div class="right">합계</div></td>
							<td class="sum"><div id="ID_CELL_TOTAL" class="right"></div></td>
							<td class="sum"><div id="ID_CELL_STND_AMT" class="right"></div></td>
							<td class="sum"><div id="ID_CELL_OVER_AMT" class="right"></div></td>
							<td class="sum"><div id="ID_CELL_STND_CNT" class="right"></div></td>
							<td class="sum"><div id="ID_CELL_OVER_CNT" class="right"></div></td>
							<td class="sum"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>