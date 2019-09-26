<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$type	= $_POST['type'];
	$sr		= $_POST['sr'];
	$sugaCd	= $_POST['cd'];
	$year	= $_POST['year'];
	$area	= $_POST['area'];

	$mstCd	= SubStr($sugaCd,0,1);
	$proCd	= SubStr($sugaCd,1,2);
	$svcCd	= SubStr($sugaCd,3,2);

	if (StrLen($sugaCd) == 1){
		$showSuga = 'PRO';
	}else if (StrLen($sugaCd) == 3){
		$showSuga = 'SVC';
	}else{
		$showSuga = 'ALL';
	}

	$title = $year.'년 ';

	if ($_POST['month'] == 'A'){
		$month = 'A';
	}else{
		$month  = $myF->monthStr(IntVal($_POST['month']));
		$title .= IntVal($month).'월 ';
	}

	$title .= '기관별보고서';

	$sql = 'SELECT	DISTINCT
					nm1 AS mst_nm';

	if ($proCd){
		$sql .= ',	nm2 AS pro_nm';
	}

	if ($svcCd){
		$sql .= ',	nm3 AS svc_nm';
	}

	$sql .= '
			FROM	suga_care
			WHERE	cd1 = \''.$mstCd.'\'';

	if ($proCd){
		$sql .= '
			AND		cd2 = \''.$proCd.'\'';
	}

	if ($svcCd){
		$sql .= '
			AND		cd3 = \''.$svcCd.'\'';
	}

	$row = $conn->get_array($sql);

	$mstNm = Str_replace('<br>','',$row['mst_nm']);
	$proNm = Str_replace('<br>','',$row['pro_nm']);
	$svcNm = Str_replace('<br>','',$row['svc_nm']);

	Unset($row);
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfReSize();
		setTimeout('lfLoad()', 200);
	});

	function lfReSize(){
		var t = $('#divBody').offset().top;
		var h = $(document).height();
		var height = h - t - 3;

		$('#divBody').height(height);
	}

	function lfLoad(){
		var html = '';

		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':'<?=$type;?>_POP'
			,	'sr':'<?=$sr;?>'
			,	'sugaCd':'<?=$sugaCd;?>'
			,	'year':'<?=$year;?>'
			,	'month':'<?=$month;?>'
			,	'area':'<?=$area;?>'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:220px; left:270; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));
				var mon = '';
				var cnt = {}, pay = {}, tot = {}, no = 1;
				var first = true;
				var tmpRowPro = '', tmpRowSuga = '',tmpRowMonth = '', tmpRowAdd = false;
				var rowsCnt = {};

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						if (first && ('<?=$month;?>' == 'A' || '<?=$showSuga;?>' == 'SVC' || '<?=$showSuga;?>' == 'PRO')){
							first = false;

							var cols = 4;

							if ('<?=$month;?>' == 'A' && '<?=$showSuga;?>' == 'SVC'){
								cols ++;
							}else if ('<?=$showSuga;?>' == 'PRO'){
								cols ++;

								if ('<?=$month;?>' == 'A') cols ++;
							}

							html += '<tr>';
							html += '<td class="center bold" style="background-color:#DDEEF3;" colspan="'+cols+'"><div class="right">총&nbsp;&nbsp;계</div></td>';
							html += '<td class="center bold" style="background-color:#DDEEF3;"><div id="lblCnt" class="right">0</div></td>';
							html += '<td class="center bold" style="background-color:#DDEEF3;"><div id="lblPay" class="right">0</div></td>';
							html += '<td class="center bold" style="background-color:#DDEEF3;"></td>';
							html += '</tr>';
						}

						trmRowAdd = false;

						if ('<?=$month;?>' == 'A' || '<?=$showSuga;?>' == 'SVC' || '<?=$showSuga;?>' == 'PRO'){
							if ('<?=$showSuga;?>' == 'PRO'){
								var rows = __str2num(col['rowsP'])+__str2num(col['rowsS']);
							}else if ('<?=$month;?>' == 'A' && '<?=$showSuga;?>' == 'SVC'){
								var rows = __str2num(col['rowsS'])+__str2num(col['rowsM']);
							}else{
								var rows = __str2num(col['rows']);
							}

							var key = '', keyPro = '', keySuga = '', keyMonth = '';

							if (rows > 0){
								rows += 1;

								if ('<?=$showSuga;?>' == 'PRO'){
									keyPro = col['sugaCd'].substring(0,3);

									//
									if (tmpRowPro != keyPro){
										tmpRowPro  = keyPro;

										cols = 4;

										if ('<?=$month;?>' == 'A') cols ++;

										html += '<tr>';
										html += '<td class="center bold" id="rows_'+keyPro+'"><div class="left" style="line-height:1.3em;">'+col['proNm']+'</div></td>';
										html += '<td class="center bold" style="background-color:#FDE9D9;" colspan="'+cols+'"><div class="right">합&nbsp;&nbsp;계</div></td>';
										html += '<td class="center bold" style="background-color:#FDE9D9;"><div id="lblCnt_'+keyPro+'" class="right">0</div></td>';
										html += '<td class="center bold" style="background-color:#FDE9D9;"><div id="lblPay_'+keyPro+'" class="right">0</div></td>';
										html += '<td class="center bold" style="background-color:#FDE9D9;"></td>';
										html += '</tr>';

										rowsCnt[keyPro] = __str2num(rowsCnt[keyPro]) + 1;
									}

									keySuga = col['sugaCd'];
									key = keySuga;

									if (tmpRowSuga != key){
										tmpRowSuga  = key;
										trmRowAdd = true;
									}

									if (trmRowAdd){
										html += '<tr>';
										html += '<td class="center bold" id="rows_'+key+'"><div class="left" style="line-height:1.3em;">'+col['svcNm']+'</div></td>';
									}

								}else if ('<?=$month;?>' == 'A' && '<?=$showSuga;?>' == 'SVC'){
									keySuga = col['sugaCd'];

									if (tmpRowSuga != keySuga){
										tmpRowSuga	= keySuga;

										html += '<tr>';
										html += '<td class="center bold" id="rows_'+keySuga+'"><div class="left" style="line-height:1.3em;">'+col['svcNm']+'</div></td>';
										html += '<td class="center bold" style="background-color:#FDE9D9;" colspan="4"><div class="right">합&nbsp;&nbsp;계</div></td>';
										html += '<td class="center bold" style="background-color:#FDE9D9;"><div id="lblCnt_'+keySuga+'" class="right">0</div></td>';
										html += '<td class="center bold" style="background-color:#FDE9D9;"><div id="lblPay_'+keySuga+'" class="right">0</div></td>';
										html += '<td class="center bold" style="background-color:#FDE9D9;"></td>';
										html += '</tr>';

										rowsCnt[keySuga] = __str2num(rowsCnt[keySuga]) + 1;
									}

									keyMonth = col['month'];
									key = keySuga+'_'+keyMonth;

									if (tmpRowMonth != key){
										tmpRowMonth  = key;
										trmRowAdd = true;
									}

									if (trmRowAdd){
										html += '<tr>';
										html += '<td class="center bold" id="rows_'+key+'">'+col['month']+'월</td>';
									}
								}else if ('<?=$showSuga;?>' == 'SVC'){
									html += '<tr>';
									html += '<td class="center bold" rowspan="'+rows+'"><div class="left" style="line-height:1.3em;">'+col['svcNm']+'</div></td>';
									key = col['sugaCd'];
									trmRowAdd = true;
								}else if ('<?=$month;?>' == 'A'){
									html += '<tr>';
									html += '<td class="center bold" rowspan="'+rows+'">'+col['month']+'월</td>';
									key = col['month'];
									trmRowAdd = true;
								}

								if (trmRowAdd){
									cols = 3;

									if ('<?=$month;?>' == 'A' && '<?=$showSuga;?>' == 'PRO') cols ++;

									html += '<td class="center bold" style="background-color:#FFFF00;" colspan="'+cols+'"><div class="right">소&nbsp;&nbsp;계</div></td>';
									html += '<td class="center bold" style="background-color:#FFFF00;"><div id="lblCnt_'+key+'" class="right">0</div></td>';
									html += '<td class="center bold" style="background-color:#FFFF00;"><div id="lblPay_'+key+'" class="right">0</div></td>';
									html += '<td class="center bold" style="background-color:#FFFF00;"></td>';
									html += '</tr>';
								}

								if ('<?=$showSuga;?>' == 'PRO'){
									if ('<?=$month;?>' == 'A'){
										keyMonth = tmpRowPro+'_'+tmpRowSuga+'_'+col['month'];

										if (tmpRowMonth != keyMonth){
											tmpRowMonth  = keyMonth;

											html += '<tr>';
											html += '<td class="center bold" id="rows_'+keyMonth+'">'+col['month']+'월</td>';
											html += '<td class="center bold" style="background-color:#B0F0B3;" colspan="3"><div class="right">월&nbsp;&nbsp;계</div></td>';
											html += '<td class="center bold" style="background-color:#B0F0B3;"><div id="lblCnt_'+keyMonth+'" class="right">0</div></td>';
											html += '<td class="center bold" style="background-color:#B0F0B3;"><div id="lblPay_'+keyMonth+'" class="right">0</div></td>';
											html += '<td class="center bold" style="background-color:#B0F0B3;"></td>';
											html += '</tr>';

											rowsCnt[tmpRowPro]  = __str2num(rowsCnt[tmpRowPro]) + 1;
											rowsCnt[tmpRowSuga] = __str2num(rowsCnt[tmpRowSuga]) + 1;
											rowsCnt[tmpRowMonth] = __str2num(rowsCnt[tmpRowMonth]) + 1;
										}
									}
								}
							}
						}

						html += '<tr>';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="center">'+col['area']+'</td>';
						html += '<td class="center"><div class="left nowrap" style="width:170px;">'+col['nm']+'</div></td>';
						html += '<td class="center"><div class="right">'+__num2str(col['cnt'])+'</div></td>';
						html += '<td class="center"><div class="right">'+__num2str(col['pay'])+'</div></td>';
						html += '<td class="center"></td>';

						html += '</tr>';

						var key = '';

						if ('<?=$showSuga;?>' == 'PRO'){
							if ('<?=$month;?>' == 'A'){
								key = tmpRowMonth;
							}else{
								key = tmpRowSuga;
							}
						}else if ('<?=$month;?>' == 'A' && '<?=$showSuga;?>' == 'SVC'){
							key = tmpRowSuga+'_'+keyMonth;
						}else if ('<?=$month;?>' == 'A'){
							key = col['month'];
						}else if ('<?=$showSuga;?>' == 'SVC'){
							key = col['sugaCd'];
						}

						cnt[key] = __str2num(cnt[key]) + __str2num(col['cnt']);
						pay[key] = __str2num(pay[key]) + __str2num(col['pay']);
						tot['cnt'] = __str2num(tot['cnt']) + __str2num(col['cnt']);
						tot['pay'] = __str2num(tot['pay']) + __str2num(col['pay']);

						if ('<?=$showSuga;?>' == 'PRO'){
							cnt[tmpRowPro] = __str2num(cnt[tmpRowPro]) + __str2num(col['cnt']);
							pay[tmpRowPro] = __str2num(pay[tmpRowPro]) + __str2num(col['pay']);

							if ('<?=$month;?>' == 'A'){
								cnt[tmpRowSuga] = __str2num(cnt[tmpRowSuga]) + __str2num(col['cnt']);
								pay[tmpRowSuga] = __str2num(pay[tmpRowSuga]) + __str2num(col['pay']);
							}

							if (trmRowAdd){
								rowsCnt[tmpRowPro]  = __str2num(rowsCnt[tmpRowPro]) + 1;
								rowsCnt[tmpRowSuga] = __str2num(rowsCnt[tmpRowSuga]) + 1;
							}

							rowsCnt[tmpRowPro]  = __str2num(rowsCnt[tmpRowPro]) + 1;
							rowsCnt[tmpRowSuga] = __str2num(rowsCnt[tmpRowSuga]) + 1;

							if ('<?=$month;?>' == 'A'){
								rowsCnt[tmpRowMonth] = __str2num(rowsCnt[tmpRowMonth]) + 1;
							}

						}else if ('<?=$month;?>' == 'A' && '<?=$showSuga;?>' == 'SVC'){
							cnt[tmpRowSuga] = __str2num(cnt[tmpRowSuga]) + __str2num(col['cnt']);
							pay[tmpRowSuga] = __str2num(pay[tmpRowSuga]) + __str2num(col['pay']);

							if (trmRowAdd){
								rowsCnt[tmpRowSuga] = __str2num(rowsCnt[tmpRowSuga]) + 1;
								rowsCnt[tmpRowSuga+'_'+keyMonth] = __str2num(rowsCnt[tmpRowSuga+'_'+keyMonth]) + 1;
							}

							rowsCnt[tmpRowSuga] = __str2num(rowsCnt[tmpRowSuga]) + 1;
							rowsCnt[tmpRowSuga+'_'+keyMonth] = __str2num(rowsCnt[tmpRowSuga+'_'+keyMonth]) + 1;
						}

						no ++;
					}
				}

				$('#tbodyList').html(html);

				if (('<?=$month;?>' == 'A' && '<?=$showSuga;?>' == 'SVC') || ('<?=$showSuga;?>' == 'PRO')){
					for(var i in rowsCnt){
						$('#rows_'+i).attr('rowSpan',rowsCnt[i]);
					}
				}

				for(var i in cnt){
					var tmp;

					if (cnt[i] > 0){
						tmp = __num2str(cnt[i]);
					}else{
						tmp = '';
					}

					$('#lblCnt_'+i).text(tmp);
				}

				for(var i in pay){
					var tmp;

					if (pay[i] > 0){
						tmp = __num2str(pay[i]);
					}else{
						tmp = '';
					}

					$('#lblPay_'+i).text(tmp);
				}

				$('#lblCnt').text(__num2str(tot['cnt']));
				$('#lblPay').text(__num2str(tot['pay']));
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>

<form id="f" name="f" method="post">
<div id="lsTitle" class="title title_border"><?=$title;?></div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">대분류</th>
			<td class="left"><?=$mstNm;?></td>
		</tr><?
		if ($proNm){?>
			<tr>
				<th class="center">중분류</th>
				<td class="left"><?=$proNm;?></td>
			</tr><?
		}
		if ($svcNm){?>
			<tr>
				<th class="center">소분류</th>
				<td class="left"><?=$svcNm;?></td>
			</tr><?
		}?>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup><?
		if ($showSuga == 'PRO'){?>
			<col width="100px" span="2"><?
		}else if ($showSuga == 'SVC'){?>
			<col width="100px"><?
		}
		if ($month == 'A'){?>
			<col width="30px"><?
		}?>
		<col width="40px">
		<col width="50px">
		<col width="170px">
		<col width="70px" span="2">
		<col>
	</colgroup>
	<thead>
		<tr><?
			if ($showSuga == 'PRO'){?>
				<th class="head">중분류</th>
				<th class="head">소분류</th><?
			}else if ($showSuga == 'SVC'){?>
				<th class="head">소분류</th><?
			}
			if ($month == 'A'){?>
				<th class="head">월</th><?
			}?>
			<th class="head">No</th>
			<th class="head">지역</th>
			<th class="head">기관명</th>
			<th class="head">횟수</th>
			<th class="head">금액</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top" colspan="10">
				<div id="divBody" style="height:100px; overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup><?
							if ($showSuga == 'PRO'){?>
								<col width="100px" span="2"><?
							}else if ($showSuga == 'SVC'){?>
								<col width="100px"><?
							}
							if ($month == 'A'){?>
								<col width="30px"><?
							}?>
							<col width="40px">
							<col width="50px">
							<col width="170px">
							<col width="70px" span="2">
							<col>
						</colgroup>
						<tbody id="tbodyList"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
</form>
<div id='divTemp' style='position:absolute; left:0; top:0; display:none; z-index:11;'></div>
<?
	include_once('../inc/_footer.php');
?>