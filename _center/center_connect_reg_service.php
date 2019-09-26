<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	/*
	 *	계약등록
	 */
	$colgroup = '
		<col width="30px">
		<col width="80px" span="2">
		<col width="30px">
		<col width="80px" span="2">
		<col width="75px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="60px">
		<col>';
?>
<script type="text/javascript">
	var svcAmt = {};
	var date = new Date();
	var today = date.getFullYear()+(date.getMonth()+1 < 10 ? '0' : '')+(date.getMonth()+1)+(date.getDate() < 10 ? '0' : '')+date.getDate();

	svcAmt['1_01_stndAmt'] = '22,000';
	svcAmt['1_11_stndAmt'] = '11,000';
	svcAmt['1_11_overCost'] = '550';
	svcAmt['1_11_limitCnt'] = '30';
	svcAmt['1_14_stndAmt'] = '11,000';
	svcAmt['1_15_stndAmt'] = '11,000';
	svcAmt['1_21_stndAmt'] = '11,000';
	svcAmt['1_22_stndAmt'] = '11,000';
	svcAmt['1_23_stndAmt'] = '11,000';
	svcAmt['1_24_stndAmt'] = '11,000';
	svcAmt['1_41_stndAmt'] = '11,000';
	svcAmt['1_42_stndAmt'] = '11,000';

	svcAmt['2_11_stndAmt'] = '11,000';
	svcAmt['2_21_stndAmt'] = '5,500';
	svcAmt['2_21_overCost'] = '22';
	svcAmt['2_21_limitCnt'] = '300';
	svcAmt['2_P1_stndAmt'] = '0';
	svcAmt['2_31_stndAmt'] = '0';
	svcAmt['2_31_overCost'] = '110';

	if (today >= '20180401'){
		svcAmt['1_01_stndAmt'] = '24,200';
		svcAmt['1_11_stndAmt'] = '12,100';
		svcAmt['1_14_stndAmt'] = '12,100';
		svcAmt['1_15_stndAmt'] = '12,100';
		svcAmt['1_21_stndAmt'] = '12,100';
		svcAmt['1_22_stndAmt'] = '12,100';
		svcAmt['1_23_stndAmt'] = '12,100';
		svcAmt['1_24_stndAmt'] = '12,100';
		svcAmt['1_41_stndAmt'] = '12,100';
		svcAmt['1_42_stndAmt'] = '12,100';
		svcAmt['1_11_overCost'] = '6600';
	}


	$(document).ready(function(){
		$('#ID_DIV_LIST').height(__GetHeight($('#ID_DIV_LIST')));
		lfSearch();
	});

	function lfSearch(){
		var obj = $('tbody[id^="ID_LIST_"]');

		$('tr',obj).remove();
		$(obj).each(function(){
			var key		= $(this).attr('key');
			var svcGbn	= $(this).attr('svcGbn');
			var svcCd	= $(this).attr('svcCd');
			var unitGbn = $(this).attr('unitGbn');

			$.ajax({
				type:'POST'
			,	url:'./center_connect_reg_service_search.php'
			,	data:{
					'orgNo'	:'<?=$orgNo;?>'
				,	'svcGbn':svcGbn
				,	'svcCd'	:svcCd
				}
			,	beforeSend:function(){
				}
			,	success:function(data){
					if (!data) return;

					var row = data.split('?');

					for(var i=0; i<row.length; i++){
						lfAdd(svcGbn,svcCd,unitGbn,row[i]);
					}
				}
			,	error: function (request, status, error){
					alert('[ERROR No.02]'
						 +'\nCODE : ' + request.status
						 +'\nSTAT : ' + status
						 +'\nMESSAGE : ' + request.responseText);
				}
			});
		});
	}

	function lfSetAcctDt(obj){
		var row = __GetTagObject($(obj),'TR');
		$('#txtAcctDt',row).val($(obj).val());
	}

	function lfAdd(svcGbn,svcCd,unitGbn,val){
		var key		= svcGbn+'_'+svcCd;
		var obj		= $('#ID_LIST_'+key);
		var style	= '';
		var html	= '';
		var seq, useYn, fromDt, toDt, acctYn, acctFrom, acctTo, acctGbn, stndCost, overCost, limitCnt;

		if (val){
			v = __parseVal(val);

			seq		 = v['seq'];
			useYn	 = v['usrYn'];
			fromDt	 = __getDate(v['fromDt']);
			toDt	 = __getDate(v['toDt']);
			acctYn	 = v['acctYn'];
			acctFrom = __getDate(v['acctFrom']);
			acctTo	 = __getDate(v['acctTo']);
			acctGbn	 = v['acctGbn'];
			stndCost = __num2str(v['stndCost']);
			overCost = __num2str(v['overCost']);
			limitCnt = __num2str(v['limitCnt']);
		}else{
			var date = new Date();

			fromDt = date.getFullYear()+'-'+(date.getMonth() < 9 ? '0' : '')+(date.getMonth() + 1)+'-'+(date.getDate() < 10 ? '0' : '')+date.getDate();
			toDt = '9999-12-31';
			acctFrom = fromDt;
			acctTo = toDt;
		}

		if (!seq) seq = '0';
		if (!useYn) useYn = 'Y';
		if (!fromDt) fromDt = '';
		if (!toDt) toDt = '';
		//if (!acctYn) acctYn = 'Y';
		if (!acctFrom) acctFrom = '';
		if (!acctTo) acctTo = '';
		if (!acctGbn) acctGbn = '';
		if (!stndCost) stndCost = svcAmt[key+'_stndAmt'];
		if (!overCost) overCost = __str2num(svcAmt[key+'_overCost']);
		if (!limitCnt) limitCnt = __str2num(svcAmt[key+'_limitCnt']);

		if (!acctYn && svcGbn == '1'){
			if (svcCd >= '21' && svcCd <= '24'){
			}else{
				acctYn = 'Y';
			}
		}

		if (!acctYn && svcGbn == '2'){
			if (svcCd == '21' || svcCd <= '31'){
				acctYn = 'Y';
			}
		}


		if ($('tr:last',obj).length > 0){
			style = 'border-top:1px solid #CCCCCC;';
		}

		html += '<tr key="'+key+'" svcGbn="'+svcGbn+'" svcCd="'+svcCd+'" seq="'+seq+'">';
		html += '<td class="center bottom" style="'+style+'"><input id="chkUseYn" type="checkbox" value="Y" class="checkbox" '+(useYn == 'Y' ? 'checked' : '')+'></td>';
		html += '<td class="center bottom" style="'+style+'"><input id="txtFromDt" type="text" value="'+fromDt+'" class="date" onchange="lfSetAcctDt(this);"></td>';
		html += '<td class="center bottom" style="'+style+'"><input id="txtToDt" type="text" value="'+toDt+'" class="date"></td>';
		html += '<td class="center bottom" style="'+style+'"><input id="chkAcctYn" type="checkbox" value="Y" class="checkbox" '+(acctYn == 'Y' ? 'checked' : '')+'></td>';
		html += '<td class="center bottom" style="'+style+'"><input id="txtAcctFrom" type="text" value="'+acctFrom+'" class="date"></td>';
		html += '<td class="center bottom" style="'+style+'"><input id="txtAcctTo" type="text" value="'+acctTo+'" class="date"></td>';
		html += '<td class="center bottom" style="'+style+'">';
		html += '<select id="cboAcctGbn" style="width:auto;">'
			 +	'<option value="1" '+(acctGbn == '1' ? 'selected' : '')+'>정율제</option>'
			 +	'<option value="2" '+(acctGbn == '2' ? 'selected' : '')+'>정액제</option>'
			 +	'</select>';
		html += '</td>';
		html += '<td class="center bottom" style="'+style+'"><input id="txtStndCost" type="text" value="'+stndCost+'" class="number" style="width:100%;"></td>';
		html += '<td class="center bottom" style="'+style+'"><input id="txtOverCost" type="text" value="'+overCost+'" class="number" style="width:100%;"></td>';
		html += '<td class="center bottom" style="'+style+'"><input id="txtLimitCnt" type="text" value="'+limitCnt+'" class="number" style="width:100%;"></td>';
		html += '<td class="center bottom" style="'+style+'">';

		if (unitGbn == '1'){
			html += '<span>고객</span>';
		}else if (unitGbn == '2'){
			html += '<span>직원</span>';
		}else if (unitGbn == '3'){
			html += '<span>문자</span>';
		}else if (unitGbn == '4'){
			html += '<span>고정</span>';
		}

		html += '</td>';
		html += '<td class="center bottom last" style="'+style+'">';

		if (val){
			html += '<div class="left">'
				 +	'<span class="btn_pack small"><button onclick="lfSave(this,2);" style="color:BLUE;">수정</button></span>&nbsp;'
				 +	'<span class="btn_pack small"><button onclick="lfRemove(this);" style="color:RED;">삭제</button></span>'
				 +	'</div>';
		}else{
			html += '<div class="left">'
				 +	'<span class="btn_pack small"><button onclick="lfSave(this,1);">저장</button></span>&nbsp;'
				 +	'<span class="btn_pack small"><button onclick="lfCancel(this);">취소</button></span>'
				 +	'</div>';
		}

		html += '</td>';
		html += '</tr>';

		if ($('tr:last',obj).length > 0){
			$('tr:last',obj).after(html);
		}else{
			$(obj).html(html);
		}

		$('input:text',$('tr:last',obj)).each(function(){
			__init_object(this);
		});
	}

	function lfSave(o,gbn){
		var obj		= __GetTagObject($(o),'TR');
		var svcGbn	= $(obj).attr('svcGbn');
		var svcCd	= $(obj).attr('svcCd');
		var seq		= $(obj).attr('seq');

		if (!$('#txtFromDt',obj).val() || !$('#txtToDt',obj).val()){
			alert((!$('#txtFromDt',obj).val() ? '시작일자' : '종료일자')+'를 입력하여 주십시오.');
			$('#'+(!$('#txtFromDt',obj).val() ? 'txtFromDt' : 'txtToDt'),obj).focus();
			return;
		}

		if ($('#chkAcctYn',obj).attr('checked')){
			if (!$('#txtAcctFrom',obj).val()){
				alert('과금시작일을 입력하여 주십시오.');
				$('#txtAcctFrom',obj).focus();
				return;
			}

			if (!$('#txtAcctTo',obj).val()){
				alert('과금종료일을 입력하여 주십시오.');
				$('#txtAcctTo',obj).focus();
				return;
			}
		}

		$.ajax({
			type:'POST'
		,	url:'./center_connect_reg_service_save.php'
		,	data:{
				'orgNo'		:'<?=$orgNo;?>'
			,	'svcGbn'	:svcGbn
			,	'svcCd'		:svcCd
			,	'seq'		:seq
			,	'useYn'		:$('#chkUseYn',obj).attr('checked') ? 'Y' : 'N'
			,	'fromDt'	:$('#txtFromDt',obj).val().split('-').join('')
			,	'toDt'		:$('#txtToDt',obj).val().split('-').join('')
			,	'acctYn'	:$('#chkAcctYn',obj).attr('checked') ? 'Y' : 'N'
			,	'acctFrom'	:$('#txtAcctFrom',obj).val().split('-').join('')
			,	'acctTo'	:$('#txtAcctTo',obj).val().split('-').join('')
			,	'acctGbn'	:$('#cboAcctGbn',obj).val()
			,	'stndCost'	:$('#txtStndCost',obj).val().split(',').join('')
			,	'overCost'	:$('#txtOverCost',obj).val().split(',').join('')
			,	'limitCnt'	:$('#txtLimitCnt',obj).val().split(',').join('')
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					if (gbn == 1){
						lfSearch();
					}
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

	function lfCancel(obj){
		var obj = __GetTagObject($(obj),'TR');
		var key = $(obj).attr('key');
		$(obj).remove();
		$('td',$('tr:first',$('#ID_LIST_'+key))).css('border-top','none');
	}

	function lfRemove(obj){
		if (!confirm('삭제 후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		var o		= __GetTagObject($(obj),'TR');
		var svcGbn	= $(o).attr('svcGbn');
		var svcCd	= $(o).attr('svcCd');
		var seq		= $(o).attr('seq');

		$.ajax({
			type:'POST'
		,	url:'./center_connect_reg_service_remove.php'
		,	data:{
				'orgNo'		:'<?=$orgNo;?>'
			,	'svcGbn'	:svcGbn
			,	'svcCd'		:svcCd
			,	'seq'		:seq
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					lfCancel(obj);
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
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="177px">
		<col width="30px">
		<col width="80px" span="2">
		<col width="30px">
		<col width="80px" span="2">
		<col width="75px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head bold" rowspan="2">서비스 종류</th>
			<th class="head bold" colspan="3">서비스기간</th>
			<th class="head bold" colspan="8">과금기준</th>
			<th class="head bold" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">사용</th>
			<th class="head">시작</th>
			<th class="head">종료</th>
			<th class="head">과금</th>
			<th class="head">과금시작</th>
			<th class="head">과금종료</th>
			<th class="head">과금기준</th>
			<th class="head">기본금</th>
			<th class="head">초과단가</th>
			<th class="head">제한수</th>
			<th class="head">초과구분</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top" colspan="20">
				<div id="ID_DIV_LIST" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="20px">
							<col width="40px">
							<col width="90px">
							<col width="20px">
							<col>
						</colgroup><?
						$sql = 'SELECT	0 AS gbn, \'00\' AS cd, \'기본금\' AS nm, 1 AS cnt
								UNION	ALL
								SELECT	1 AS gbn
								,		a.parent_cd AS cd
								,		b.svc_nm AS nm
								,		COUNT(a.parent_cd) AS cnt
								FROM	cv_svc_main AS a
								INNER	JOIN	cv_svc_main AS b
										ON		b.svc_cd = a.parent_cd
								WHERE	a.parent_cd IS NOT NULL
								AND		a.parent_cd != \'00\'
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
								GROUP	BY a.parent_cd
								UNION	ALL
								SELECT	9 AS gbn, \'99\' AS cd, \'할인금\' AS nm, 1 AS cnt';

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
								if ($gbn == 0){
									$str = '기본금';
									$tbl = '';
								}else if ($gbn == 1){
									$str = 'C<br>a<br>r<br>e<br><br>서<br>비<br>스';
									$tbl = 'cv_svc_main';
								}else if ($gbn == 2){
									$str = '부<br>가';
									$tbl = 'cv_svc_sub';
								}else if ($gbn == 9){
									$str = '할인금';
									$tbl = '';
								}else{
									break;
								}

								if ($gbn == 0){?>
									<tbody>
										<tr>
											<th class="center bold last" colspan="3"><?=$str;?></th>
											<th class="top head" style="padding-top:2px;"><span class="btn_pack m" onclick="lfAdd('1','01','');"><span class="add"></span></span></th>
											<td class="center" onmouseover="this.style.backgroundColor='#EFEFEF';" onmouseout="this.style.backgroundColor='#FFFFFF';">
												<table class="my_table" style="width:100%;">
													<colgroup><?=$colgroup;?></colgroup>
													<tbody id="ID_LIST_1_01" key="1_01" svcGbn="1" svcCd="01" unitGbn=""></tbody>
												</table>
											</td>
										</tr>
									</tbody><?
								}else if ($gbn == 9){?>
									<tbody>
										<tr>
											<th class="center bold last" colspan="3"><?=$str;?></th>
											<th class="top head" style="padding-top:2px;"><span class="btn_pack m" onclick="lfAdd('9','99','');"><span class="add"></span></span></th>
											<td class="center" onmouseover="this.style.backgroundColor='#EFEFEF';" onmouseout="this.style.backgroundColor='#FFFFFF';">
												<table class="my_table" style="width:100%;">
													<colgroup><?=$colgroup;?></colgroup>
													<tbody id="ID_LIST_9_99" key="9_99" svcGbn="9" svcCd="99" unitGbn=""></tbody>
												</table>
											</td>
										</tr>
									</tbody><?
								}else{?>
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

												<th class="top last" style="padding-top:3px;"><?=$row['svc_nm'];?></th>
												<th class="top head" style="padding-top:2px;"><span class="btn_pack m" onclick="lfAdd('<?=$gbn;?>','<?=$row['svc_cd'];?>','<?=$row['unit_gbn'];?>');"><span class="add"></span></span></th>
												<td class="center" onmouseover="this.style.backgroundColor='#EFEFEF';" onmouseout="this.style.backgroundColor='#FFFFFF';">
													<table class="my_table" style="width:100%;">
														<colgroup><?=$colgroup;?></colgroup>
														<tbody id="ID_LIST_<?=$key;?>" key="<?=$key;?>" svcGbn="<?=$gbn;?>" svcCd="<?=$row['svc_cd'];?>" unitGbn="<?=$row['unit_gbn'];?>"></tbody>
													</table>
												</td>
												</tr><?
											}

											$conn->row_free();
										}?>
									</tbody><?
								}
							}
						}?>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>