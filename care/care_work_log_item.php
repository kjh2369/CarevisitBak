<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_work_log_item_search.php'
		,	data :{
				'SR'	:$('#sr').val()
			,	'sugaCd':$('#cboSugaCd').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfAdd(){
		var html = '';

		html += '<tr id="rowId_0" seq="0" save="N">'
			 +	'<td class="center">-</td>'
			 +	lfTdHtml()
			 +	'</tr>';

		if ($('tr',$('#tbodyList')).length > 0){
			$('tr:first',$('#tbodyList')).before(html);
		}else{
			$('#tbodyList').html(html);
		}

		$('input:text',$('tr:first',$('#tbodyList'))).each(function(){
			__init_object(this);
		});
	}

	function lfTdHtml(col){
		var html = '';
		//var today = GetToday();
		var dt = new Date();
		var today = dt.getFullYear()+'-'
				  + ((dt.getMonth()+1 < 10 ? '0' : '')+(dt.getMonth()+1))+'-'
				  + (dt.getDate() < 10 ? '0' : '')+dt.getDate();


		var name, fromDt, toDt, prtYn, orderNo, btn;

		if (col){
			name	= col['name'];
			fromDt	= col['fromDt'];
			toDt	= col['toDt'];
			prtYn	= col['prtYn'];
			orderNo = col['orderNo'];
			btn		= '<span class="btn_pack small"><button onclick="lfModifyCancel(this);" style="color:RED;">수정취소</button></span>';
		}else{
			name	= '';
			fromDt	= today;
			toDt	= '9999-12-31';
			prtYn	= 'Y';
			orderNo = '1';
			btn		= '<span class="btn_pack m"><span class="delete"></span><button onclick="lfRemove(this);">취소</button></span>';
		}

		html += '<td><input id="txtName" type="text" value="'+name+'" orgVal="'+name+'" style="width:100%;" onchange="lfChkVal(this);"></td>'
			 +	'<td><input id="txtFromDt" type="text" value="'+fromDt+'" orgVal="'+fromDt+'" class="date" onchange="lfChkVal(this);"></td>'
			 +	'<td><input id="txtToDt" type="text" value="'+toDt+'" orgVal="'+toDt+'" class="date" onchange="lfChkVal(this);"></td>'
			 +	'<td class="center"><input id="chkPrtYn" type="checkbox" class="checkbox" value="Y" orgVal="'+prtYn+'" onclick="lfChkVal(this);" '+(prtYn == 'Y' ? 'checked' : '')+'></td>'
			 +	'<td><input id="txtOrderNo" type="text" value="'+orderNo+'" orgVal="'+orderNo+'" class="number" style="width:100%;" onchange="lfChkVal(this);"></td>'
			 +	'<td class="left last">'+btn+'</td>';

		return html;
	}

	function lfModifyCancel(obj){
		var obj = __GetTagObject(obj, 'TR');
		if (!obj) return;

		var no, name, fromDt, toDt, prtYn, orderNo, btn;

		no		= $('td',obj).eq(0).text();
		name	= $('#txtName',obj).attr('orgVal');
		fromDt	= __getDate($('#txtFromDt',obj).attr('orgVal'),'.');
		toDt	= __getDate($('#txtToDt',obj).attr('orgVal'),'.');
		prtYn	= $('#chkPrtYn',obj).attr('orgVal');
		orderNo	= $('#txtOrderNo',obj).attr('orgVal');

		var html = '<td class="center">'+no+'</td>'
				 + '<td><div class="left">'+name+'</div></td>'
				 + '<td class="center">'+fromDt+'</td>'
				 + '<td class="center">'+toDt+'</td>'
				 + '<td class="center">'+prtYn+'</td>'
				 + '<td class="center">'+orderNo+'</td>'
				 + '<td class="last">'
				 + '<div class="left">'
				 + '<span class="btn_pack small"><button onclick="lfModify(this);" style="color:BLUE;">수정</button></span> '
				 + '<span class="btn_pack small"><button onclick="lfDelete(this);" style="color:RED;">삭제</button></span>'
				 + '</div>'
				 + '</td>';

		$(obj).html(html).css('background-color','#FFFFFF');
	}

	function lfChkVal(obj){
		var obj = __GetTagObject(obj, 'TR');
		if (!obj) return;
		$(obj).attr('save','N').css('background-color','#FAF4C0');
	}

	function lfRemove(obj){
		var obj = __GetTagObject(obj, 'TR');
		if (!obj) return;
		$(obj).remove();
	}

	function lfSave(){
		var data = '';

		$('tr[save="N"]',$('#tbodyList')).each(function(){
			if ($('#txtName',this).val()){
				data += (data ? '?' : '');
				data += 'seq='+$(this).attr('seq');
				data += '&name='+$('#txtName',this).val();
				data += '&fromDt='+$('#txtFromDt',this).val();
				data += '&toDt='+$('#txtToDt',this).val();
				data += '&prtYn='+($('#chkPrtYn',this).attr('checked') ? 'Y' : 'N');
				data += '&orderNo='+$('#txtOrderNo',this).val();
			}
		});

		if (!data){
			alert('저장할 내역이 없습니다.');
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./care_work_log_item_save.php'
		,	data :{
				'SR'	:$('#sr').val()
			,	'sugaCd':$('#cboSugaCd').val()
			,	'data'	:data
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (__resultMsg(result)){
					lfSearch();
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfModify(obj){
		var obj = __GetTagObject(obj, 'TR');
		if (!obj) return;

		var html = '';
		var data = {};

		data['no'] = $('td',obj).eq(0).text();
		data['name'] = $('td',obj).eq(1).text();
		data['fromDt'] = __getDate($('td',obj).eq(2).text());
		data['toDt'] = __getDate($('td',obj).eq(3).text());
		data['prtYn'] = $('td',obj).eq(4).text();
		data['orderNo'] = $('td',obj).eq(5).text();

		html += '<td class="center">'+data['no']+'</td>'
			 +	lfTdHtml(data);

		$(obj).html(html);
	}

	function lfDelete(obj){
		var obj = __GetTagObject(obj, 'TR');
		if (!obj) return;
		if (!confirm('삭제 후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_work_log_item_delete.php'
		,	data :{
				'SR'	:$('#sr').val()
			,	'sugaCd':$('#cboSugaCd').val()
			,	'seq'	:$(obj).attr('seq')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (__resultMsg(result)){
					lfSearch();
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">
	<div style="float:left; width:auto;">업무일지 항목관리(<?=$title;?>)</div>
	<div style="float:right; width:auto; margin-top:8px;">
		<span class="btn_pack m"><span class="add"></span><button onclick="lfAdd();">추가</button></span>
		<span class="btn_pack m"><span class="save"></span><button onclick="lfSave();">저장</button></span>
	</div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="120px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>업무일지 서비스 선택</th>
			<td class="last">
				<select id="cboSugaCd" style="width:auto;" onchange="lfSearch();"><?
					$sql = 'SELECT	code, name
							FROM	care_suga_comm';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['code'];?>"><?=$row['name'];?></option><?
					}

					$conn->row_free();?>
				</select>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="150px">
		<col width="80px" span="2">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">명칭</th>
			<th class="head">적용일자</th>
			<th class="head">종료일자</th>
			<th class="head">출력여부</th>
			<th class="head">정렬순서</th>
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