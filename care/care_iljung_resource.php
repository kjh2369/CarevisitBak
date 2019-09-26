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
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfLoadSuga()',200);
	});

	function lfMoveYear(pos){
		var year = __str2num($('#lblYYMM').attr('year'));

		year += pos;

		$('#lblYYMM').attr('year',year).text(year);

		lfLoadSuga();
	}

	function lfLoadSuga(){
		$.ajax({
			type :'POST'
		,	url  :'./care_iljung_resource_load_suga.php'
		,	data :{
				'sr':'<?=$sr;?>'
			,	'year':$('#lblYYMM').attr('year')
			,	'str':$('#txtCategory').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	complete:function(){
				lfLoadIljung();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadIljung(){
		$.ajax({
			type :'POST'
		,	url  :'./care_iljung_resource_load_iljung.php'
		,	data :{
				'sr'	:'<?=$sr;?>'
			,	'year'	:$('#lblYYMM').attr('year')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);

						lfSetIljung(col['cd'],col['mon']);
					}
				}

				$('#tempLodingBar').remove();
			}
		,	complete:function(){

			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSetIljung(code,month){
		var obj = $('#ILJUNG_'+code+'_'+month);

		$(obj).removeClass('my_month_2').addClass('my_month_y');
		$('a',obj).css('color','#000000');
	}

	function lfRegIljung(obj){
		var w = 1000;
		var h = 600;
		var l = (screen.availWidth - w) / 2;
		var t = (screen.availHeight - h) / 2;


		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=yes';
		var url = './care_iljung_resource_reg.php';
		var win = window.open('', 'RESOURCE_REG', option);
			win.opener = self;
			win.focus();

		var tmp = $(obj).attr('id').split('_');

		var parm = new Array();
			parm = {
				'code'	:tmp[1]
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month' :tmp[2]
			,	'sr'	:'<?=$sr;?>'
			,	'from'	:$(obj).attr('from')
			,	'to'	:$(obj).attr('to')
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

		form.setAttribute('target', 'RESOURCE_REG');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">서비스별 일정관리(<?=$title;?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col width="40px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td class="">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<th class="center">검색</th>
			<td><input id="txtCategory" type="text" value=""></td>
			<td class="left last"><span class="btn_pack m"><button onclick="lfLoadSuga();">조회</button></span></td>
		</tr>
	</tbody>
</table>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="100px">
				<col width="100px">
				<col width="100px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">대분류</th>
					<th class="head">중분류</th>
					<th class="head">소분류</th>
					<th class="head last">상세서비스</th>
				</tr>
			</thead>
			<tbody id="tbodyList"></tbody>
			<tfoot>
				<tr>
					<td class="bottom last"></td>
				</tr>
			</tfoot>
		</table>
	<!--
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="40px">
				<col width="200px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">No</th>
					<th class="head">서비스</th>
					<th class="head last">비고</th>
				</tr>
			</thead>
			<tbody id="tbodyList"></tbody>
		</table>
	-->
<?
	include_once('../inc/_db_close.php');
?>