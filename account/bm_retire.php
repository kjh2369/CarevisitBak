<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$power	= $_SESSION['userLevel'];
	$yymm	= Date('Y-m');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_retire_search.php'
		,	data :{
				'fromDt':$('#txtFromDt').val()
			,	'toDt'	:$('#txtToDt').val()
			}
		,	beforeSend:function(){
				$('#txtFromDt').attr('orgDt',$('#txtFromDt').val());
				$('#txtToDt').attr('orgDt',$('#txtToDt').val());
			}
		,	success:function(html){
				html = html.split('<!--CUT_LINE-->');
				$('#ID_LIST').html(html[0]);
				$('#ID_SUM').html(html[1]);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfExcel(){
		if (!$('#txtFromDt').attr('orgDt') || !$('#txtToDt').attr('orgDt')) return;

		var parm = new Array();
			parm = {
				'orgNo'	:'<?=$orgNo;?>'
			,	'fromDt':$('#txtFromDt').attr('orgDt').replace('-','')
			,	'toDt'	:$('#txtToDt').attr('orgDt').replace('-','')
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
		form.setAttribute('action', '../__management/retire_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">퇴직적립금현황</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기간</th>
			<td class="last">
				<div style="float:left; width:auto; padding-top:2px;">
					<input id="txtFromDt" type="text" value="<?=$yymm;?>" orgDt="" class="yymm"> ~
					<input id="txtToDt" type="text" value="<?=$yymm;?>" orgDt="" class="yymm">
				</div>
				<div style="float:left; width:auto; padding-top:2px;">
					<span class="btn_pack m"><button onclick="lfSearch()">조회</button></span>
				</div>
				<div class="right" style="float:right; width:auto; padding-top:2px;">
					<span class="btn_pack m"><button onclick="lfExcel()">퇴직적립금현황 출력</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="80px" span="3">
		<col width="100px">
		<col width="70px">
		<col width="70px">
		<col width="90px" span="2">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">생년월일</th>
			<th class="head">입사일자</th>
			<th class="head">퇴사일자</th>
			<th class="head">고용형태</th>
			<th class="head">근무일수</th>
			<th class="head">근무시간</th>
			<th class="head">급여</th>
			<th class="head">퇴직적립금</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="ID_LIST"></tbody>
	<tbody id="ID_SUM"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_db_close.php");
?>