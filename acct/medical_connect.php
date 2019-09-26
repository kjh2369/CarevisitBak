<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	$type = 99;

?>
<script type="text/javascript">
	$(document).ready(function(){
		
		$('input:text').each(function(){
			__init_object(this);
		});

		setTimeout('lfSearch()',200);
	});

	
	function lfSearch(){
		
		$.ajax({
			type :'POST'
		,	url  :'./medical_connect_search.php'
		,	data :{
				'orgNo' : $('#txtOrgNo').val(),
				'orgNm' : $('#txtOrgNm').val(),
				'orgMdNm' : $('#txtMdOrgNm').val(),
				'fromDt':$('#fromDt').val(),
				'toDt':$('#toDt').val()
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

	
	function lfReg(org_no,m_org_no,from){
		
		var url = './medical_connect_reg.php';

		var width = 1000;
		var height = 600;
		var top  = (window.screen.height - height) / 2;
		var left = (window.screen.width  - width)  / 2;

		var option = 'left='+left+'px, top='+top+'px, width='+width+'px,height='+height+'px,scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'MEDICAL_CONNECT_REG', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'orgNo':org_no
			,	'mdOrgNo':m_org_no
			,	'fromDt':from
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

		form.setAttribute('target', 'MEDICAL_CONNECT_REG');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
	
	function lfDel(org_no,m_org_no,from){
		
		if (!confirm('삭제된 데이타는 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./medical_connect_delete.php'
		,	data :{
				'orgNo':org_no
			,	'mdOrgNo':m_org_no
			,	'fromDt':from
			}
		,	beforeSend:function(){
				//$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					lfSearch();
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
	
</script>

<div class="title title_border" style="width:100%;">
	<div style="float:left; width:auto;">의료기관신청 연결</div>
	<div style="float:right; width:auto; margin-top:9px;"><span class="btn_pack m"><span class="add"></span><button onclick="lfReg('','','');">등록</button></span></div>
</div>
<?
	$colgroup = '<col width="40px">
				 <col width="120px">
				 <col width="150px">
				 <col width="150px">
				 <col width="200px">
				 <col>';
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="100px">
		<col width="70px">
		<col width="150px">
		<col width="70px">
		<col width="150px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관기호</th>
			<td>
				<input id="txtOrgNo" type="text" value="" style="width:100%;">
			</td>
			<th class="center">기관명</th>
			<td>
				<input id="txtOrgNm" type="text" value="" style="width:100%;">
			</td>
			<th class="center">의료기관명</th>
			<td>
				<input id="txtMdOrgNm" type="text" value="" style="width:100%;">
			</td>
			<td class="left last" rowspan="2">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head" >기관코드</th>
			<th class="head" >기관명</th>
			<th class="head" >의료기관명</th>
			<th class="head" >적용기간</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<tr>
			<td class="center bottom last" colspan="7"><? include_once('../inc/_page_script.php');?></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>