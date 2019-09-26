<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$year	= Date('Y');
	$month	= Date('m');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfMoveYear(pos){
		var year = __str2num($('#lblYYMM').attr('year'));

		year += pos;

		$('#lblYYMM').attr('year',year).text(year);

		lfSearch();
	}

	function lfMoveMonth(month){
		var obj = $('div[id^="btnMonth_"]');

		$(obj).each(function(){
			if ($(obj).hasClass('my_month_y')){
				$(obj).removeClass('my_month_y').addClass('my_month_1');
				return false;
			}
		});

		obj = $('#btnMonth_'+month);

		$(obj).removeClass('my_month_1').addClass('my_month_y');
		$('#lblYYMM').attr('month',month);

		lfSearch();
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_report_new_search.php'
		,	data :{
				'SR'	:'<?=$sr;?>'
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LIST').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfExcel(type, prgGbn){
		if (!prgGbn) prgGbn = '1';

		var url = './care_report_excel.php';
		var parm = new Array();
			parm = {
				'type':'<?=$type;?>_'+type
			,	'SR':'<?=$sr;?>'
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			,	'prtGbn':prgGbn
			,	'quarter':$('#cboQuarter option:selected').val()
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
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}


	function lfFileUpload(){
		
		var h = 400;
		var w = 750;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = 'care_report_upload_pop.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open('', 'FILE_UPLOAD', option);
			win.opener = self;
			win.focus();
		
		var parm = new Array();
			parm = {
				'type':'REPORT'
			,	'sr':'<?=$sr;?>'
			};
		

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type','hidden');
			objs.setAttribute('name',key);
			objs.setAttribute('value',parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target','FILE_UPLOAD');
		form.setAttribute('method','post');
		form.setAttribute('action',url);

		document.body.appendChild(form);

		form.submit();
		
	}

</script>
<div class="title title_border">보고서(기관)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="center">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="left last"><? echo $myF->_btn_month($month,'lfMoveMonth(');?></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">보고서</th>
			<td class="last">
				<select id="cboQuarter" style="width:auto; margin-right:0;">
					<option value="1">1분기</option>
					<option value="2">2분기</option>
					<option value="3">3분기</option>
					<option value="4">4분기</option>
				</select>
				<span class="btn_pack m"><span class="excel"></span><button type="button" onclick="lfExcel('M');">분기(중분류)</button></span>
				<span class="btn_pack m"><span class="excel"></span><button type="button" onclick="lfExcel('M_S');">분기(세부사업 기본)</button></span>
				<span class="btn_pack m"><span class="excel"></span><button type="button" onclick="lfExcel('M_D','2');">분기(세부사업 상세)</button></span>
				<span class="btn_pack m"><span class="excel"></span><button type="button" onclick="lfExcel('D');">월보고서</button></span>
				<!--
				<span class="btn_pack m"><span class="excel"></span><button type="button" onclick="lfExcel('YEAR');">연보고서</button></span>
				-->
				<span class="btn_pack m"><button type="button" onclick="lfFileUpload();">보고서자료 등록</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="90px">
		<col width="120px">
		<col width="170px">
		<col width="170px">
		<col width="90px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">대분류</th>
			<th class="head">중분류</th>
			<th class="head">소분류</th>
			<th class="head">상세서비스</th>
			<th class="head">명/횟수</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="ID_LIST"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>