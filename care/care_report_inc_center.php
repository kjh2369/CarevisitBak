<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$year = Date('Y');
	$month = Date('m');
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfLoad()',200);
	});

	function lfMoveYear(pos){
		var year = __str2num($('#lblYYMM').attr('year'));

		year += pos;

		$('#lblYYMM').attr('year',year).text(year);

		lfLoad();
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

		lfLoad();
	}

	function lfLoad(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':'<?=$type;?>'
			,	'sr':'<?=$sr;?>'
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				$('#tbodyList').html(data);	
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfExcel(){
		var parm = new Array();
			parm = {
				'type':'<?=$type;?>'
			,	'SR'	:$('#sr').val()
			,	'sr':'<?=$sr;?>'
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
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
		form.setAttribute('action', './care_report_inc_center_excel.php');

		document.body.appendChild(form);

		form.submit();
	}

</script>
<div class="title title_border">소속기관별</div>
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
			<td>
				<div style="float:right; width:auto;">
					<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel();">엑셀</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="160px">
		<col width="130px">
		<col width="130px">
		<col width="150px">
		<col width="150px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">소속기관</th>
			<th class="head">대분류</th>
			<th class="head">중분류</th>
			<th class="head">소분류</th>
			<th class="head">서비스</th>
			<th class="head">횟수</th>
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