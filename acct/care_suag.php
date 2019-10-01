<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	//include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$today	= Date('Y-m-d');
	$year	= Date('Y');

	$SR = $_GET['sr'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./search.php'
		,	data :{
				'mode':'<?=$type;?>'
			,	'SR':'<?=$SR;?>'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(13));
				var html = '';
				var cnt1 = 0, cnt2 = 0;
				var nm1 = '', nm2 = '';

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);
						var cd = col['cd1']+col['cd2']+col['cd3'];

						if (__str2num(col['cnt1']) > 0){
							cnt1 = __str2num(col['cnt1']);
							nm1 = col['nm1'];
						}

						if (__str2num(col['cnt2']) > 0){
							cnt2 = __str2num(col['cnt2']);
							nm2 = col['nm2'];
						}

						if (__str2num(col['cd3']) > 0){
							html += '<tr style="cursor:default;" onmouseover="lfEvent(\'M_OVER\',this);" onmouseout="lfEvent(\'M_OUT\',this);">';

							if (cnt1 > 0){
								html += '<th class="center" rowspan="'+cnt1+'">'+nm1+'</th>';
							}

							if (cnt2 > 0){
								html += '<td class="center" rowspan="'+cnt2+'">'+nm2+'</td>';
							}

							html += '<td class="center"><div class="left">'+col['nm3']+'</div></td>';
							//html += '<td class="center">'+col['from'].split('-').join('.')+'~'+col['to'].split('-').join('.')+'</td>';
							html += '<td class="center last">&nbsp;</td>';
							html += '</tr>';

							cnt1 = 0;
							cnt2 = 0;
						}
					}
				}

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfEvent(evt,obj){
		var cnt = $('td',obj).length-1;

		 if (evt == 'M_OVER'){
			$('td',obj).eq(cnt).css('background-color','#efefef');
			$('td',obj).eq(cnt-1).css('background-color','#efefef');
			$('td',obj).eq(cnt-2).css('background-color','#efefef');
			$('td',obj).eq(cnt-3).css('background-color','#efefef');
			$('td',obj).eq(cnt-4).css('background-color','#efefef');
		}else{
			$('td',obj).eq(cnt).css('background-color','#ffffff');
			$('td',obj).eq(cnt-1).css('background-color','#ffffff');
			$('td',obj).eq(cnt-2).css('background-color','#ffffff');
			$('td',obj).eq(cnt-3).css('background-color','#ffffff');
			$('td',obj).eq(cnt-4).css('background-color','#ffffff');
		}
	}

	function lfModify(){
		var objModal = new Object();
		var url = './care_suga_reg.php';
		var style = 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.SR = '<?=$SR;?>';
		objModal.win = window;

		window.showModalDialog(url, objModal, style);

		if (objModal.result == 1){
			lfSearch();
		}
	}
</script>
<div class="title my_border">
	<div style="float:left; width:auto;">서비스관리</div>
	<div style="float:right; width:auto; margin-top:8px;">
		<span class="btn_pack m"><button type="button" onclick="lfModify();">등록 및 수정</button></span>
	</div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="90px">
		<col width="120px">
		<col width="170px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">관</th>
			<th class="head">항</th>
			<th class="head">목</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center last" colspan="4">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last" style="background-color:white;"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>