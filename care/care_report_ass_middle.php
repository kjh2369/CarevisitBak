<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$year = Date('Y');
	$month = Date('m');

	if ($_SESSION['userLevel'] == 'C'){
		$sql = 'SELECT	care_area
				FROM	b02center
				WHERE	b02_center = \''.$code.'\'
				ORDER	BY b02_kind
				LIMIT	1';

		$area = $conn->get_data($sql);

		if (!$area) $area = '99';
	}

	if ($area){
		$sql = 'SELECT	area_nm
				FROM	care_area
				WHERE	area_cd = \''.$area.'\'';

		$areaNm = $conn->get_data($sql);
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfLoad()',200);
	});

	function lfMoveYear(pos){
		var year = __str2num($('#lblYear').text());

		year += pos;

		$('#lblYear').text(year);

		lfLoad();
	}

	function lfLoad(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':'<?=$type;?>'
			,	'sr':'<?=$sr;?>'
			,	'area':$('#cboArea').val()
			,	'year':$('#lblYear').text()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				//if ('<?=$debug;?>' == '1') document.write(data);

				var row = data.split(String.fromCharCode(11));
				var first = true;
				var rowspan = 1;
				var no = 1;
				var sum = {};
				var html = '';

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						if (first){
							html += '<tr>';
							html += '<td class="center sum bold" style="background-color:#DDEEF3;" colspan="3"><div class="right">총&nbsp;&nbsp;&nbsp;계</div></td>';
							html += '<td class="center sum bold" style="background-color:#DDEEF3;"><div id="lblTotTarget" class="right">0</div></td>';
							html += '<td class="center sum bold" style="background-color:#DDEEF3;"><div id="lblTot1Quarter" class="right">0</div></td>';
							html += '<td class="center sum bold" style="background-color:#DDEEF3;"><div id="lblTot2Quarter" class="right">0</div></td>';
							html += '<td class="center sum bold" style="background-color:#DDEEF3;"><div id="lblTot3Quarter" class="right">0</div></td>';
							html += '<td class="center sum bold" style="background-color:#DDEEF3;"><div id="lblTot4Quarter" class="right">0</div></td>';
							html += '<td class="center sum bold" style="background-color:#DDEEF3;"><div id="lblTotAll" class="right">0</div></td>';
							html += '<td class="center sum bold" style="background-color:#DDEEF3;"><div id="lblTotRate" class="right">0</div></td>';
							html += '<td class="center sum last" style="background-color:#DDEEF3;"></td>';
							html += '</tr>';
							first = false;
						}

						if (col['mstCnt'] > 0){
							rowspan = __str2num(col['mstCnt'])+1;
							html += '<tr>';
							html += '<td class="center" rowspan="'+rowspan+'" style="line-height:1.3em;">'+col['mstNm']+'</td>';
							html += '<td class="center sum bold" style="background-color:#FDE9D9;" colspan="2"><div class="right">소&nbsp;&nbsp;&nbsp;계</div></td>';
							html += '<td class="center sum bold" style="background-color:#FDE9D9;"><div id="lblTotTarget_'+col['suga'].substring(0,1)+'" class="right">0</div></td>';
							html += '<td class="center sum bold" style="background-color:#FDE9D9;"><div id="lblTot1Quarter_'+col['suga'].substring(0,1)+'" class="right">0</div></td>';
							html += '<td class="center sum bold" style="background-color:#FDE9D9;"><div id="lblTot2Quarter_'+col['suga'].substring(0,1)+'" class="right">0</div></td>';
							html += '<td class="center sum bold" style="background-color:#FDE9D9;"><div id="lblTot3Quarter_'+col['suga'].substring(0,1)+'" class="right">0</div></td>';
							html += '<td class="center sum bold" style="background-color:#FDE9D9;"><div id="lblTot4Quarter_'+col['suga'].substring(0,1)+'" class="right">0</div></td>';
							html += '<td class="center sum bold" style="background-color:#FDE9D9;"><div id="lblTotAll_'+col['suga'].substring(0,1)+'" class="right">0</div></td>';
							html += '<td class="center sum bold" style="background-color:#FDE9D9;"><div id="lblTotRate_'+col['suga'].substring(0,1)+'" class="right">0</div></td>';
							html += '<td class="center sum last" style="background-color:#FDE9D9;"></td>';
							html += '</tr>';
						}

						var cnt = col['cnt'].split('/');
						var tot = 0;
						var rate = 0;

						for(var j=1; j<=4; j++){
							cnt[j] = (cnt[j] > 0 ? __num2str(cnt[j]) : '');
							tot += __str2num(cnt[j]);
						}

						tot = (tot > 0 ? __num2str(tot) : '');

						rate = __round(__str2num(tot) / (col['target'] > 0 ? __str2num(col['target']) : 1) * 100,1,false);
						rate = (rate > 0 ? rate : '');

						sum['Target_'+col['suga'].substring(0,1)] = __str2num(sum['Target_'+col['suga'].substring(0,1)]) + __str2num(col['target']);
						sum['1Quarter_'+col['suga'].substring(0,1)] = __str2num(sum['1Quarter_'+col['suga'].substring(0,1)]) + __str2num(cnt[1]);
						sum['2Quarter_'+col['suga'].substring(0,1)] = __str2num(sum['2Quarter_'+col['suga'].substring(0,1)]) + __str2num(cnt[2]);
						sum['3Quarter_'+col['suga'].substring(0,1)] = __str2num(sum['3Quarter_'+col['suga'].substring(0,1)]) + __str2num(cnt[3]);
						sum['4Quarter_'+col['suga'].substring(0,1)] = __str2num(sum['4Quarter_'+col['suga'].substring(0,1)]) + __str2num(cnt[4]);
						sum['All_'+col['suga'].substring(0,1)] = __str2num(sum['All_'+col['suga'].substring(0,1)]) + __str2num(tot);
						sum['Rate_'+col['suga'].substring(0,1)] = __round(__str2num(sum['All_'+col['suga'].substring(0,1)]) / (sum['Target_'+col['suga'].substring(0,1)] ? __str2num(sum['Target_'+col['suga'].substring(0,1)]) : 1) * 100,1,false);

						sum['Target'] = __str2num(sum['Target']) + __str2num(col['target']);
						sum['1Quarter'] = __str2num(sum['1Quarter']) + __str2num(cnt[1]);
						sum['2Quarter'] = __str2num(sum['2Quarter']) + __str2num(cnt[2]);
						sum['3Quarter'] = __str2num(sum['3Quarter']) + __str2num(cnt[3]);
						sum['4Quarter'] = __str2num(sum['4Quarter']) + __str2num(cnt[4]);
						sum['All'] = __str2num(sum['All']) + __str2num(tot);
						sum['Rate'] = __round(__str2num(sum['All']) / (sum['Target'] ? __str2num(sum['Target']) : 1) * 100,1,false);

						html += '<tr>';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="center"><div class="left">'+col['proNm']+'</div></td>';
						html += '<td class="center"><div class="right">'+col['target']+'</div></td>';
						html += '<td class="center"><div class="right">'+cnt[1]+'</div></td>';
						html += '<td class="center"><div class="right">'+cnt[2]+'</div></td>';
						html += '<td class="center"><div class="right">'+cnt[3]+'</div></td>';
						html += '<td class="center"><div class="right">'+cnt[4]+'</div></td>';
						html += '<td class="center"><div class="right bold">'+tot+'</div></td>';
						html += '<td class="center"><div class="right bold">'+rate+'</div></td>';
						html += '<td class="center last"></td>';
						html += '</tr>';

						no ++;
					}
				}

				if (!html){
					html = '<tr><td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('#tbodyList').html(html);
				//$('#lblTotal').text(__num2str(total)); //총계

				for(var i in sum){
					$('#lblTot'+i).text(sum[i] ? __num2str(sum[i]) : '');
				}

				var rate =

				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfExcel(){
		var url = './care_excel.php';
		var parm = new Array();
			parm = {
				'type':'<?=$type;?>_ASS_MIDDLE'
			,	'sr':'<?=$sr;?>'
			,	'area':$('#cboArea').val()
			,	'year':$('#lblYear').text()
			};
		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target', '_self');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">중분류보고서(<?=($sr == 'S' ? '재가지원' : '자원연계');?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td class="center last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYear"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="right last">
				<span class="btn_pack m"><span class="excel"></span><button type="button" onclick="lfExcel();">보고서출력</button></span>
			</td>
		</tr><?
		if (!Empty($area)){?>
			<tr>
				<th class="center">지역</th>
				<td class="left last" colspan="2"><?=$areaNm;?><input id="cboArea" type="hidden" value="<?=$area;?>"></td>
			</tr><?
		}else{?>
			<th class="center">지역</th>
			<td class="last" colspan="2">
				<select id="cboArea" style="width:auto;" onchange="lfLoad();">
					<option value="">전체</option><?
					$sql = 'SELECT	area_cd,area_nm
							FROM	care_area
							WHERE	show_flag = \'H\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['area_cd'];?>" <?=($row['area_cd'] == $area ? 'selected' : '');?>><?=$row['area_nm'];?></option><?
					}

					$conn->row_free();?>
				</select>
			</td><?
		}?>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="90px">
		<col width="50px">
		<col width="120px">
		<col width="70px" span="6">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">대분류</th>
			<th class="head">번호</th>
			<th class="head">중분류</th>
			<th class="head">목표</th>
			<th class="head">1/4분기</th>
			<th class="head">2/4분기</th>
			<th class="head">3/4분기</th>
			<th class="head">4/4분기</th>
			<th class="head">합계</th>
			<th class="head">달성률</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>