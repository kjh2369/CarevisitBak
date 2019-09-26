<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	/*
		$year	= Date('Y');
		$month	= IntVal(Date('m'));
	*/
	$fromDt	= Date('Y-m-01');
	$toDt	= $myF->dateAdd('day',-1,$myF->dateAdd('month', 1, $fromDt, 'Y-m-d'),'Y-m-d');
?>
<script type="text/javascript">
	var findFromDt = '', findToDt = '';

	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		var data = {};

		/*
			data = {
				'SR'	:$('#sr').val()
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			,	'useYn'	:($('#chkUseYn').attr('checked') ? 'Y' : 'N')
			};
		*/
		data = {
			'SR'	:$('#sr').val()
		,	'fromDt':$('#txtFrom').val()
		,	'toDt'	:$('#txtTo').val()
		,	'useYn'	:($('#chkUseYn').attr('checked') ? 'Y' : 'N')
		};

		findFromDt	= data['fromDt'];
		findToDt	= data['toDt'];

		$.ajax({
			type :'POST'
		,	url  :'./care_svc_use_stat_search.php'
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

	function lfNameTable(mstCd,proCd,svcCd,subCd){
		/*
		var objModal = new Object();
		var url = './care_svc_use_stat_nametable.php';
		var style = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';
		
		objModal.SR		= $('#sr').val();
		objModal.code	= mstCd+proCd+svcCd+subCd;
		
		objModal.year	= $('#lblYYMM').attr('year');
		objModal.month	= $('#lblYYMM').attr('month');
		 
		objModal.fromDt	= findFromDt;
		objModal.toDt	= findToDt;
		
		
		window.showModalDialog(url, objModal, style);
		*/

		var width = 500;
		var height = 500;
		var left = (window.screen.width  - width)  / 2;
		var top  = (window.screen.height - height) / 2;

		var URL = './care_svc_use_stat_nametable.php?SR='+$('#sr').val()+'&code='+mstCd+proCd+svcCd+subCd+'&fromDt='+findFromDt+'&toDt='+findToDt;
		var popup = window.open(URL,'NAMETABLE','width='+width+',height='+height+',left='+left+',top='+top+',scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no');

	}

	function lfExcel(){
		var parm = new Array();
			parm = {
				'SR'	:$('#sr').val()
			,	'fromDt':$('#txtFrom').val()
			,	'toDt'	:$('#txtTo').val()
			,	'useYn'	:($('#chkUseYn').attr('checked') ? 'Y' : 'N')
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
		form.setAttribute('action', './care_svc_use_stat_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">서비스이용현황(<?=$title;?>)</div>
<!--
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
				<td class="last"><?=$myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM")); lfSearch();');?></td>
			</tr>
			<tr>
				<th class="center">선택</th>
				<td class="last" colspan="2">
					<label><input id="chkUseYn" type="checkbox" class="checkbox" value="Y" onclick="lfSearch();" checked>이용자수 있는 서비스만 출력</label>
				</td>
			</tr>
		</tbody>
	</table>
-->
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기간</th>
			<td class="last">
				<div style="float:left; width:auto;">
					<input id="txtFrom" type="text" class="date" value="<?=$fromDt;?>"> ~
					<input id="txtTo" type="text" class="date" value="<?=$toDt;?>">
				</div>
				<div style="float:left; width:auto;">
					<span class="btn_pack m"><span class="refresh"></span><button onclick="lfSearch();">조회</button></span>
				</div>
				<div style="float:right; width:auto;">
					<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel();">엑셀</button></span>
				</div>
			</td>
		</tr>
		<tr>
			<th class="center">선택</th>
			<td class="last" colspan="2">
				<label><input id="chkUseYn" type="checkbox" class="checkbox" value="Y" onclick="lfSearch();" checked>이용자수 있는 서비스만 출력</label>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="150px">
		<col width="150px">
		<col width="150px">
		<col width="200px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">대분류</th>
			<th class="head">중분류</th>
			<th class="head">소분류</th>
			<th class="head">서비스명</th>
			<th class="head">이용자수</th>
			<th class="head">서비스횟수</th>
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