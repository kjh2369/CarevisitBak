<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$year = Date('Y');
	$month = Date('m');
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
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));
				var html = '';
				var tot = {};
				var first = true;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						if (first){
							first = false;

							html += '<tr>';
							html += '<td class="center bold" style="background-color:#DDEEF3;">총계</td>';
							html += '<td class="center bold" style="background-color:#DDEEF3;"><div id="lblTot" class="right">0</div></td>';

							for(var j=1; j<=12; j++){
								html += '<td class="center bold" style="background-color:#DDEEF3;"><div id="lblTot_'+j+'" class="right">0</div></td>';
							}

							html += '<td class="center last" style="background-color:#DDEEF3;"></td>';
							html += '</tr>';
						}

						html += '<tr>';
						html += '<td class="center bold">'+col['areaNm']+'</td>';
						html += '<td class="center bold" style="background-color:#FFFF00;"><div class="right">'+col['tot']+'</div></td>';

						for(var j=1; j<=12; j++){
							var link = '';

							if (col[j] > 0){
								link = '<a href="#" onclick="return false;"><div class="right" onclick="lfLoadPop(\''+col['areaCd']+'\',\''+j+'\');">'+col[j]+'</div></a>';
							}

							html += '<td class="center">'+link+'</td>';

							tot['tot'] = __str2num(tot['tot']) + __str2num(col[j]);
							tot[j] = __str2num(tot[j]) + __str2num(col[j]);
						}

						html += '<td class="center last"></td>';
						html += '</tr>';
					}
				}

				if (!html){
					 html = '<tr><td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('#tbodyList').html(html);

				for(var i in tot){
					var cnt;

					if (tot[i] > 0){
						cnt = tot[i];
					}else{
						cnt = '';
					}

					if (i == 'tot'){
						$('#lblTot').text(cnt);
					}else{
						$('#lblTot_'+i).text(cnt);
					}
				}

				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//상세
	function lfLoadPop(area,month){
		var w = 800;
		var h = 600;
		var l = (screen.availWidth - w) / 2;
		var t = (screen.availHeight - h) / 2;

		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var url	= './care_report_area_pop.php';
		var win	= window.open('about:blank', 'CARE_REPORT_AREA_POP', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'type':'<?=$type;?>'
			,	'sr':'<?=$sr;?>'
			,	'year':$('#lblYear').text()
			,	'month':month
			,	'area':area
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

		form.setAttribute('target', 'CARE_REPORT_AREA_POP');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfExcel(){
		var url = './care_excel.php';
		var parm = new Array();
			parm = {
				'type':'<?=$type;?>_AREA'
			,	'sr':'<?=$sr;?>'
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

<div class="title title_border">지역별보고서(<?=($sr == 'S' ? '재가지원' : '자원연계');?>)</div>

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
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="50px" span="13">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">지역</th>
			<th class="head">합계</th><?
			for($i=1; $i<=12; $i++){?>
				<th class="head"><?=$i;?>월</th><?
			}?>
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