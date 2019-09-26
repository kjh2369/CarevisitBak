<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$power	= $_SESSION['userLevel'];
	$year	= Date('Y');
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
		,	url  :'./bm_ie_search.php'
		,	data :{
				'year':$('#lblYYMM').text()
			}
		,	beforeSend:function(){
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

	function lfExcel(type, month){
		if (!month) return;

		var parm = new Array();
			parm = {
				'orgNo'	:'<?=$orgNo;?>'
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:month
			,	'path'	:'B'
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

		var url = '';

		if (type == 'M'){
			url = '../__management/stats_ie_acct_excel.php';
		}else if (type == 'T'){
			url = './be_id_excel.php';
		}else{
			url = '../__management/stats_ie_org_excel.php';
		}

		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">월 수지분석 현황 및 집계</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td class="last">
				<div class="left" style="float:left; width:auto; padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); lfSearch();" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); lfSearch();" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
				<div class="right" style="float:right; width:auto; padding-top:2px;">
					<span class="btn_pack m"><button onclick="lfExcel('A', '1')">상세 수지분석현황 출력</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="30px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col width="55px">
		<col width="45px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">월</th>
			<th class="head">목표금액</th>
			<th class="head">방문요양<br>매출액</th>
			<th class="head">기타/<br>가산</th>
			<th class="head">매출<br>합계</th>
			<th class="head">달성률</th>
			<th class="head">가구수<br>요양+<br>목욕</th>
			<th class="head">cost.1<br>요양사급여<br>(매입가)</th>
			<th class="head">차액<br>(매출액-<br>cost.1)</th>
			<th class="head">인건비<br>비율(%)</th>
			<th class="head">경비</th>
			<th class="head">관리자<br>인건비<br>공제전</th>
			<th class="head">공제전<br>이익율<br>(%)</th>
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