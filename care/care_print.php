<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$year = Date('Y');
	$sr   = $_GET['sr'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfLoad();
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
				var mon = new Array();
				var html = '';
				var no = 1;

				for(var i=0; i<=12; i++){
					mon[i] = 0;
				}

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						html += '<tr style="cursor:default;" onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="left">'+col['name']+'</td>';
						html += '<td class="last">';

						for(var j=1; j<=12; j++){
							var cls = 'my_month_1';
							var clr = 'cccccc';
							var link = '';
							var yymm = $('#lblYear').text()+(j < 10 ? '0' : '')+j;

							if (col['m'+j] > 0){
								cls = 'my_month_y';
								link = 'lfShowCaln(\''+col['jumin']+'\',\'62\',\''+(j < 10 ? '0' : '')+j+'\')';
								mon[j] ++;
							}

							if (col['from'] <= yymm && col['to'] >= yymm){
								clr = '000000';
							}

							html+= '<div id="btnM'+j+'" class="my_month '+cls+'" style="float:left; margin-left:3px; margin-top:2px; color:#'+clr+'; cursor:default;" onclick="'+link+'; return false;">'+j+'월</div>';
						}

						html += '</td>';
						html += '</tr>';

						no ++;
					}
				}

				for(var i=1; i<=12; i++){
					var obj = $('#btnAllM'+i);

					if (mon[i] > 0){
						$(obj).removeClass('my_month_2').addClass('my_month_r').css('color','#000000');
					}else{
						$(obj).removeClass('my_month_r').addClass('my_month_2').css('color','#cccccc');
					}
				}

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfShowCaln(asJumin,asSvcCd,asMon,asShowGbn){
		var year = $('#lblYear').text();
		var paperDir = 'p';
		var printDT = '';
		var name = '';
		var chkSvc = '';

		var para = 'root=care'
				 + '&dir='+paperDir
				 + '&fileName=care_print'
				 + '&fileType=pdf'
				 + '&target=show.php'
				 + '&showForm=ILJUNG_CALN'
				 + '&code=<?=$code;?>'
				 + '&year='+year
				 + '&month='+asMon
				 + '&jumin='+asJumin
				 + '&showGbn='+asShowGbn
				 + '&mode=CARE_COUNSEL'
				 + '&name='+name
				 + '&chkSvc='+chkSvc
				 + '&printDT='+printDT
				 + '&useType=N'
				 + '&calnYn=Y'
				 + '&dtlYn=N'
				 + '&sr=<?=$sr;?>'
				 + '&param=';

		__printPDF(para);
	}
</script>
<div class="title title_border">사업계획</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="center last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYear"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="right last"></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head last">
				<div id="btnAllM1" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln('','','01'); return false;">1월</div>
				<div id="btnAllM2" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln('','','02'); return false;">2월</div>
				<div id="btnAllM3" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln('','','03'); return false;">3월</div>
				<div id="btnAllM4" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln('','','04'); return false;">4월</div>
				<div id="btnAllM5" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln('','','05'); return false;">5월</div>
				<div id="btnAllM6" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln('','','06'); return false;">6월</div>
				<div id="btnAllM7" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln('','','07'); return false;">7월</div>
				<div id="btnAllM8" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln('','','08'); return false;">8월</div>
				<div id="btnAllM9" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln('','','09'); return false;">9월</div>
				<div id="btnAllM10" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln('','','10'); return false;">10월</div>
				<div id="btnAllM11" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln('','','11'); return false;">11월</div>
				<div id="btnAllM12" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln('','','12'); return false;">12월</div>
			</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>