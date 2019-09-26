<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$colgroup = '
		<col width="40px">
		<col width="150px">
		<col width="100px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col>';
?>
<script type="text/javascript">
	var winPos = {};

	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(column){
		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_search.php'
		,	data:{
				'column':$('#cboColumn').val() //$('#ID_BODY_LIST').attr('column')
			,	'orderBy':$('input:radio[name="optOrderBy"]:checked').val()
			,	'rsGbn':$('input:radio[name="optRsGbn"]:checked').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('tbody',$('#ID_BODY_LIST')).html(html);
				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				$('#tempLodingBar').remove();
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfSelOrg(orgNo){
		var width = 900;
		var height = 700;
		var left = window.screenLeft + $('#left_box').width();
		var top = window.screenTop + $('#divTitle').offset().top;
		var option = 'left='+left+'px, top='+top+'px, width='+width+'px,height='+height+'px,scrollbars=no,status=no,resizable=no';
		var url = './center_connect_info.php';
		var win = window.open('about:blank', 'CONNECT_INFO', option);
			win.opener = self;
			win.focus();

		winPos['X'] = left;
		winPos['Y'] = top;

		var parm = new Array();
			parm = {
				'orgNo':orgNo
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

		form.setAttribute('target', 'CONNECT_INFO');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function GetScreenInfo(){
		return winPos;
	}
</script>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="270px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">정렬</th>
			<td class="">
				<label><input name="optOrderBy" type="radio" class="radio" value="DESC" checked>내림차순</label>
				<label><input name="optOrderBy" type="radio" class="radio" value="ASC">오름차순</label>
				<select id="cboColumn" style="width:auto;">
					<option value="org_nm">기관명</option>
					<option value="org_no">기관기호</option>
					<option value="start_dt" selected>연결일자</option>
				</select>
			</td>
			<td class="left last" rowspan="2">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
			</td>
		</tr>
		<tr>
			<th class="center">상태</th>
			<td>
				<label><input name="optRsGbn" type="radio" class="radio" value="ALL" checked>전체</label>
				<label><input name="optRsGbn" type="radio" class="radio" value="">신규연결</label>
				<label><input name="optRsGbn" type="radio" class="radio" value="04">캐어비지트</label>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관명</th>
			<th class="head">기관기호</th>
			<th class="head">대표자</th>
			<th class="head">연결일자</th>
			<th class="head">담당자</th>
			<th class="head">상태</th>
			<th class="head">등록자</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>

<div id="ID_BODY_LIST" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;" column="org_nm">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody></tbody>
	</table>
</div>