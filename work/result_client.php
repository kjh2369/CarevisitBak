<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$year	= Date('Y');
	$month	= IntVal(Date('m'));
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		var data = {};

		data = {
			'year'	:$('#yymm').attr('year')
		,	'month'	:$('#yymm').attr('month')
		};

		$.ajax({
			type :'POST'
		,	url  :'./result_client_search.php'
		,	data :data
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

	function lfExcel(){
		var parm = new Array();
			parm = {
				'year'	:$('#yymm').attr('year')
			,	'month'	:$('#yymm').attr('month')
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

		form.setAttribute('target', '_self');
		form.setAttribute('method', 'post');
		form.setAttribute('action', './result_client_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">수급자별 실적내역(실적기준)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col width="500px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="last">
				<div>
					<div style="float:left; width:auto; margin-left:5px; margin-top:3px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#yymm'),-1,'lfSearch()'); " onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="yymm" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; margin-top:3px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#yymm'),1,'lfSearch()');" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last" style="padding-top:1px;"><?echo $myF->_btn_month($month,'__moveMonth(',',$("#yymm"),"lfSearch()");');?></td>
			<td class="right last">
				<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel();">엑셀</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="100px">
		<col width="80px">
		<col width="100px">
		<col width="50px">
		<col width="70px" span="3">
		<col width="80px">
		<col width="50px">
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">수급자</th>
			<th class="head">요양보호사</th>
			<th class="head">제공서비스</th>
			<th class="head">횟수</th>
			<th class="head">총금액</th>
			<th class="head">공단청구액</th>
			<th class="head">본인부담금</th>
			<th class="head">근무시간</th>
			<th class="head">시급</th>
			<th class="head">급여</th>
			<th class="head last">처우개선비</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center last" colspan="12">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>