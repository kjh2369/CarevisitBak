<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$year	= Date('Y');
	$month	= IntVal(Date('m'));
	$today	= Date('Y-m-d');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_svc_operate_stat_search.php'
		,	data :{
				'SR'	:$('#sr').val()
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
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

	function lfExcel(){
		var parm = new Array();
			parm = {
				'SR'	:$('#sr').val()
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
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
		form.setAttribute('action', './care_svc_operate_stat_excel.php');

		document.body.appendChild(form);

		form.submit();
	}

	function lfWorkLogExcel(){
		var parm = new Array();
			parm = {
				'SR'	:$('#sr').val()
			,	'date'	:$('#txtDate').val()
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
		form.setAttribute('action', './care_worklog_mg_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">서비스운영현황 일자별(<?=$title;?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td class="last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); lfSearch();" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); lfSearch();" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last">
				<div style="float:left; width:auto;"><?=$myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM")); lfSearch();');?></div>
				<div style="float:right; width:auto; margin-left:5px;">
					<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel();">엑셀</button></span>
				</div>

				<div style="float:right; width:auto;">
					<span class="btn_pack m"><span class="excel"></span><button onclick="lfWorkLogExcel();">운영일지</button></span>
				</div>
				<div style="float:right; width:auto; padding-top:1px;">
					<input id="txtDate" type="text" value="<?=$today;?>" class="date">
				</div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col width="200px">
		<col width="100px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">일자</th>
			<th class="head">서비스</th>
			<th class="head">대상자수</th>
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