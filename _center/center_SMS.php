<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$colgroup = '
		<col width="35px">
		<col width="170px">
		<col width="100px">
		<col width="70px">
		<col width="70px">
		<col width="60px">
		<col width="50px" span="5">
		<col>';
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#cboCompany option:eq(0)').text('전체').attr('selected','selected');
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_search.php'
		,	data:{
				'company':$('#cboCompany').val()
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			,	'orgNo'	:$('#txtOrgNo').val()
			,	'orgNm'	:$('#txtOrgNm').val()
			,	'orgMg'	:$('#txtOrgMg').val()
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

	//엑셀 출력
	function lfExcel(){
		var parm = new Array();
			parm = {
				'company':$('#cboCompany').val()
			,	'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
			,	'orgNo'	:$('#txtOrgNo').val()
			,	'orgNm'	:$('#txtOrgNm').val()
			,	'orgMg'	:$('#txtOrgMg').val()
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

		form.setAttribute('method', 'post');
		form.setAttribute('action', './center_<?=$menuId?>_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>

<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관명</th>
			<th class="head">기관기호</th>
			<th class="head">CMS</th>
			<th class="head">CMS회사</th>
			<th class="head">대표자</th>
			<th class="head">사용</br>건수</th>
			<th class="head">기본</br>금액</th>
			<th class="head">추가</br>건수</th>
			<th class="head">추가</br>금액</th>
			<th class="head">합계</br>금액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>

<div id="ID_BODY_LIST" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody></tbody>
	</table>
</div>