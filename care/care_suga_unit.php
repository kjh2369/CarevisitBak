<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$year = Date('Y');
	$SR = $_GET['sr'];
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
			,	'SR':'<?=$SR;?>'
			,	'year':$('#lblYear').text()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));
				var html = '';

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						html += '<tr onmouseover="lfEvent(this,\'OVER\');" onmouseout="lfEvent(this,\'OUT\');">';

						if (col['mstCd']){
							html += '<td class="center" rowspan="'+col['mstCnt']+'" style="line-height:1.3em;">'+col['mstNm']+'</td>';
						}

						if (col['proCd']){
							html += '<td class="center" rowspan="'+col['proCnt']+'" style="line-height:1.3em;">'+col['proNm']+'</td>';
						}

						html += '<td class="left">'+col['svcNm']+'</td>';
						html += '<td class="">';
						html += '<input id="optUnit_'+i+'_1" name="optUnit_'+i+'" type="radio" class="radio" value="1" onclick="lfApply(\''+col['svcCd']+'\',\'1\');" '+(col['unit'] == '1' ? 'checked' : '')+'><label for="optUnit_'+i+'_1">명</label>';
						html += '<input id="optUnit_'+i+'_2" name="optUnit_'+i+'" type="radio" class="radio" value="2" onclick="lfApply(\''+col['svcCd']+'\',\'2\');" '+(col['unit'] == '2' ? 'checked' : '')+'><label for="optUnit_'+i+'_2">횟수</label>';
						html += '</td>';
						html += '<td class="left last"></td>';
						html += '</tr>';
					}
				}

				if (!html){
					html = '<tr><td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfEvent(obj,evt){
		var td = $('td',obj);
		var cnt = $(td).length;

		if (evt == 'OVER'){
			var clr = '#efefef';
		}else{
			var clr = '#ffffff';
		}

		for(var i=cnt-3; i<cnt; i++){
			$(td).eq(i).css('background-color',clr);
		}
	}

	function lfApply(cd,gbn){
		$.ajax({
			type :'POST'
		,	url  :'./care_apply.php'
		,	data :{
				'type':'<?=$type;?>'
			,	'year':$('#lblYear').text()
			,	'cd':cd
			,	'gbn':gbn
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">수가단위관리</div>
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
		<col width="90px">
		<col width="120px">
		<col width="170px">
		<col width="110px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">대분류</th>
			<th class="head">중분류</th>
			<th class="head">서비스</th>
			<th class="head">단위</th>
			<th class="head last">비고</th>
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