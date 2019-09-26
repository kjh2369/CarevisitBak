<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$area = $_GET['area'];
	$year = Date('Y');
	$month = Date('m');

	if ($area){
		$sql = 'SELECT	area_nm
				FROM	care_area
				WHERE	area_cd = \''.$area.'\'';

		$areaNm = $conn->get_data($sql);
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfLoad()',300);
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
			,	'year':$('#lblYear').text()
			,	'area':$('#cboArea').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));
				var first = true;
				var rowspan = 1;
				var total = {};
				var tot = {};
				var html = '';

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						if (first){
							html += '<tr>';
							html += '<td class="center sum bold" style="background-color:#DDEEF3;" colspan="3"><div class="right">총&nbsp;&nbsp;&nbsp;계</div></td>';
							html += '<td id="lblTot_tot" class="center sum" style="background-color:#DDEEF3;"></td>';

							for(var j=1; j<=12; j++){
								html += '<td id="lblTot_'+j+'" class="center sum '+(j == 12 ? 'last' : '')+'" style="background-color:#DDEEF3;"></td>';
							}
							html += '</tr>';
							first = false;
						}

						if (col['mstCnt'] > 0){
							rowspan = __str2num(col['mstCnt'])+1;
							html += '<tr>';
							html += '<td class="center" rowspan="'+rowspan+'" style="line-height:1.3em;">'+col['mstNm']+'</td>';
							html += '<td class="center sum bold" style="background-color:#FDE9D9;" colspan="2"><div class="right">합&nbsp;&nbsp;&nbsp;계</div></td>';
							html += '<td id="lblTot_'+col['suga'].substring(0,1)+'_tot" class="center sum" style="background-color:#FDE9D9;"></td>';

							for(var j=1; j<=12; j++){
								html += '<td id="lblTot_'+col['suga'].substring(0,1)+'_'+j+'" class="center sum '+(j == 12 ? 'last' : '')+'" style="background-color:#FDE9D9;"></td>';
							}
							html += '</tr>';
						}

						if (col['proCnt'] > 0){
							rowspan = __str2num(col['proCnt'])+1;
							html += '<tr>';
							html += '<td class="center" rowspan="'+rowspan+'" style="line-height:1.3em;">'+col['proNm']+'</td>';
							html += '<td class="center sum bold" style="background-color:#FFFF00;"><div class="right">소&nbsp;&nbsp;&nbsp;계</div></td>';
							html += '<td id="lblTot_'+col['suga'].substring(0,3)+'_tot" class="center sum" style="background-color:#FFFF00;"></td>';

							for(var j=1; j<=12; j++){
								html += '<td id="lblTot_'+col['suga'].substring(0,3)+'_'+j+'" class="center sum '+(j == 12 ? 'last' : '')+'" style="background-color:#FFFF00;"></td>';
							}
							html += '</tr>';
						}

						html += '<tr>';

						//합계
						//tot[col['suga'].substring(0,1)] = __str2num(tot[col['suga'].substring(0,1)]) + __str2num(cnt);

						html += '<td class="center"><div class="left" style="line-height:1.3em;">'+col['svcNm']+'</div></td>';
						html += '<td class="center" onclick="'+(col['tot'] ? 'lfLoadPop(\''+col['suga']+'\',\'A\');' : '')+'"><a href="#" onclick="return false;"><span class="bold">'+col['tot']+'</span></a></td>';

						//소계 합계
						tot[col['suga'].substring(0,3)+'_tot'] = __str2num(tot[col['suga'].substring(0,3)+'_tot']) + __str2num(col['tot']);

						//합계 합계
						tot[col['suga'].substring(0,1)+'_tot'] = __str2num(tot[col['suga'].substring(0,1)+'_tot']) + __str2num(col['tot']);

						//총계 합계
						total['tot'] = __str2num(total['tot']) + __str2num(col['tot']);

						for(var j=1; j<=12; j++){
							html += '<td class="center '+(j == 12 ? 'last' : '')+'" onclick="'+(col[j] ? 'lfLoadPop(\''+col['suga']+'\',\''+j+'\');' : '')+'"><a href="#" onclick="return false;">'+col[j]+'</a></td>';

							//소계
							tot[col['suga'].substring(0,3)+'_'+j] = __str2num(tot[col['suga'].substring(0,3)+'_'+j]) + __str2num(col[j]);

							//합계
							tot[col['suga'].substring(0,1)+'_'+j] = __str2num(tot[col['suga'].substring(0,1)+'_'+j]) + __str2num(col[j]);

							//총계
							total[j] = __str2num(total[j]) + __str2num(col[j]);
						}

						html += '<td class="center last"></td>';
						html += '</tr>';
					}
				}

				if (!html){
					html = '<tr><td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('#tbodyList').html(html);

				for(var i in total){
					var cnt;

					if (total[i] > 0){
						cnt = total[i];
					}else{
						cnt = '';
					}

					$('#lblTot_'+i).text(cnt);
				}

				for(var i in tot){
					var cnt;

					if (tot[i] > 0){
						cnt = '<a href="#" onclick="return false;"><span class="bold">'+tot[i]+'</span></a>';
					}else{
						cnt = '';
					}

					if (cnt){
						$('#lblTot_'+i).html(cnt).unbind('click').bind('click',function(){
							var tmp = $(this).attr('id').split('_');

							if (tmp[2] == 'tot') tmp[2] = 'A';

							lfLoadPop(tmp[1],tmp[2]);
						});
					}
				}

				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadPop(cd,month){
		var w = 800;
		var h = 600;
		var l = (screen.availWidth - w) / 2;
		var t = (screen.availHeight - h) / 2;

		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var url	= './care_report_center_pop.php';
		var win	= window.open('about:blank', 'CARE_REPORT_CENTER_POP', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'type':'<?=$type;?>'
			,	'sr':'<?=$sr;?>'
			,	'cd':cd
			,	'year':$('#lblYear').text()
			,	'month':month
			,	'area':$('#cboArea').val()
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

		form.setAttribute('target', 'CARE_REPORT_CENTER_POP');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
</script>

<div class="title title_border">서비스별 기관별보고서</div>

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
				<!--span class="btn_pack m"><span class="excel"></span><button type="button" onclick="lfExcel();">보고서출력</button></span-->
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
							FROM	care_area';

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
		<col width="70px">
		<col width="110px">
		<col width="120px">
		<col width="50px" span="13">
	</colgroup>
	<thead>
		<tr>
			<th class="head">대분류</th>
			<th class="head">중분류</th>
			<th class="head">서비스</th>
			<th class="head">합계</th><?
			for($i=1; $i<=12; $i++){?>
				<th class="head <?=$i == 12 ? 'last' : '';?>"><?=$i;?>월</th><?
			}?>
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