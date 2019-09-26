<?
	if (!IsSet($_SESSION['USER_CODE'])){
		exit;
	}

	include_once('../inc/_menu_top.php');

	$colgroup = '<col width="50px"><col width="250px"><col width="60px"><col width="90px"><col width="60px"><col width="90px"><col width="60px"><col width="90px"><col width="60px"><col width="90px"><col>';
?>
<table style="width:100%; min-width:1024px;" cellpadding="0" cellspacing="0">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr style="height:35px;">
			<th style="background-color:#efefef; border-right:1px solid #cccccc;" rowspan="2">No</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc;" rowspan="2">기관명</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc;" colspan="2">급여</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc;" colspan="2">보험</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc;" colspan="2">기타</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc;" colspan="2">합계</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc;"  rowspan="2">비고</th>
		</tr>
		<tr style="height:35px;">
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-top:1px solid #cccccc;">건수</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-top:1px solid #cccccc;">금액</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-top:1px solid #cccccc;">건수</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-top:1px solid #cccccc;">금액</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-top:1px solid #cccccc;">건수</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-top:1px solid #cccccc;">금액</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-top:1px solid #cccccc;">건수</th>
			<th style="background-color:#efefef; border-right:1px solid #cccccc; border-top:1px solid #cccccc;">금액</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="border-top:1px solid #cccccc; border-right:1px solid #cccccc;" colspan="11">
				<div id="listCenter" style="cursor:default; overflow-x:hidden; overflow-y:auto; width:100%; height:100px;" onclick=""></div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_menu_foot.php');
?>
<script type="text/javascript">
var modal = null;
var timer1 = null;
var timer2 = null;

$(document).ready(function(){
	lfResize();
	lfSearchCenter();

	timer1 = setInterval('lfSearchCenter()', 10000);
	timer2 = setInterval('lfSearchSMS()', 10000);
});

function lfResize(){
	var h = $(this).height() - $('#top').height() - 40 - 71;

	$('#listCenter').height(h);
	$('#listRequest').height(h);
}

function test(){
	alert('test');
}

function lfSearchCenter(){
	if (modal != null) return;

	$.ajax({
		type: 'POST'
	,	url : '../trans/center_list.php'
	,	data: {
		}
	,	beforeSend: function (){
			$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
		}
	,	success: function (data){
			var list = data.split(String.fromCharCode(1));
			var html = '<table style="width:100%;" cellpadding="0" cellspacing="0">'
					 + '<colgroup><?=$colgroup;?></colgroup>'
					 + '<tbody>';

			if (data){
				for(var i=0; i<list.length; i++){
					if (list[i]){
						var val = list[i].split(String.fromCharCode(2));

						html += '<tr style="height:25px;" onmouseover="this.style.backgroundColor=\'#efeff9\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">'
							 +  '<td style="border-bottom:1px solid #cccccc; text-align:center;">'+(i+1)+'</td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-left:1px solid #cccccc; text-align:left;"><div style="padding-left:5px;">'+val[1]+'</div></td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-left:1px solid #cccccc; text-align:right;"><div style="padding-right:5px;">'+__num2str(val[2])+'</div></td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-left:1px solid #cccccc; text-align:right;"><div style="padding-right:5px;">'+__num2str(val[3])+'</div></td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-left:1px solid #cccccc; text-align:right;"><div style="padding-right:5px;">'+__num2str(val[4])+'</div></td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-left:1px solid #cccccc; text-align:right;"><div style="padding-right:5px;">'+__num2str(val[5])+'</div></td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-left:1px solid #cccccc; text-align:right;"><div style="padding-right:5px;">'+__num2str(val[6])+'</div></td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-left:1px solid #cccccc; text-align:right;"><div style="padding-right:5px;">'+__num2str(val[7])+'</div></td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-left:1px solid #cccccc; text-align:right;"><div style="padding-right:5px; font-weight:bold;">'+__num2str(val[8])+'</div></td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-left:1px solid #cccccc; text-align:right;"><div style="padding-right:5px; font-weight:bold;">'+__num2str(val[9])+'</div></td>'
							 +  '<td style="border-bottom:1px solid #cccccc; border-left:1px solid #cccccc; text-align:left;"><div style="">'
							 +  '<span class="btn_pacm m"><button type="button" style="width:50px; padding-top:3px;" onclick="lfDetail(\''+val[0]+'\');">상세</button></span>'
							 +  '</div></td>'
							 +  '</tr>';
					}
				}
			}else{
				html += '<tr style="height:25px;">'
					 +  '<td style="text-align:center; border-bottom:1px solid #cccccc;">::검색된 데이타가 없습니다.::</td>'
					 +  '</tr>';
			}

			html += '</tbody>'
				 +  '</table>';

			$('#listCenter').html(html);
			$('#tempLodingBar').remove();
		}
	,	error: function (){
		}
	}).responseXML;
}

function lfSearchSMS(){
	$.ajax({
		type: 'POST'
	,	url : '../trans/sms_list.php'
	,	data: {
		}
	,	beforeSend: function (){
		}
	,	success: function (result){
			//alert(result);
		}
	,	error: function (){
		}
	}).responseXML;
}

function lfDetail(orgNo){
	if (modal != null) return;

	var objModal = new Object();
	var url      = '../trans/detail.php';
	var style    = 'dialogWidth:900px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

	objModal.orgNo = orgNo;
	objModal.parent = window;

	try{
		modal.close();
	}catch(e){
	}

	//window.showModalDialog(url, objModal, style);
	modal = window.showModelessDialog(url, objModal, style);
}
</script>