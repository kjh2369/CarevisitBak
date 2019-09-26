<?
	include_once('../inc/_header.php');
	include_once("../inc/_login.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$year	= Date('Y');
	$month	= IntVal(Date('m'));
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfResize();

		/*
			$('#ID_HEAD').scroll(function(){
				$('#ID_BODY').scrollLeft($(this).scrollLeft());
			});
		 */

		$('#ID_BODY').scroll(function(){
			$('#ID_HEAD').scrollLeft($(this).scrollLeft());
		});
	});

	$(window).bind('resize', function(e){
		window.resizeEvt;
		$(window).resize(function(){
			clearTimeout(window.resizeEvt);
			window.resizeEvt = setTimeout(function(){
				lfResize();
			}, 250);
		});
	});

	function lfResize(){
		var obj = __GetTagObject($('#ID_LIST'),'DIV');
		$(obj).height(__GetHeight($(obj)));
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./svc_rec_search2.php'
		,	data:{
				'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			}
		,	beforeSend:function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LIST').html(html);
				$('#tempLodingBar').remove();
			}
		,	complete: function(){
			}
		,	error:function(request, status, error){
				$('#tempLodingBar').remove();

				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);

				return false;
			}
		}).responseXML;
	}

	function lfSetLog(obj, appNo, regDt, svcCd, from, to){
		if (svcCd == '500'){
			if (from.indexOf('/') >= 0) from = from.substr(0,from.indexOf('/'));
			if (to.indexOf('/') >= 0) to = to.substr(0,to.indexOf('/'));
		}

		$.ajax({
			type:'POST'
		,	url:'./svc_rec_set.php'
		,	data:{
				'appNo'	:appNo
			,	'regDt'	:regDt
			,	'svcCd'	:svcCd
			,	'from'	:from
			,	'to'	:to
			}
		,	beforeSend:function (){
			}
		,	success:function(result){
				var clr = '';

				if (result == 'Y'){
					clr = '#fdfab3';
				}

				obj = __GetTagObject($(obj),'TD');
				$(obj).css('background-color',clr);
			}
		,	complete: function(){
			}
		,	error:function(request, status, error){
				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);

				return false;
			}
		}).responseXML;
	}

	function lfExcel(){
		var parm = new Array();
			parm = {
				'year'	:$('#lblYYMM').attr('year')
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

		form.setAttribute('method', 'post');
		form.setAttribute('action', './svc_rec_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">요양보호사 방문기록지 작성확인</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col width="550px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="last">
				<div>
					<div style="float:left; width:auto; margin-left:5px; margin-top:3px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1,'lfSearch()');" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; margin-top:3px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1,'lfSearch()');" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last" style="padding-top:1px;"><?echo $myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM"),"lfSearch()")');?></td>
			<td class="right last">
				<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel();">Excel</button></span>
			</td>
		</tr>
	</tbody>
</table>
<div id="ID_HEAD" style="width:100%; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:1900px;">
		<colgroup>
			<col width="70px" span="2">
			<col width="50px">
			<col width="50px" span="31">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">직원</th>
				<th class="head">대상자</th>
				<th class="head">구분</th><?
				for($i=1; $i<=31; $i++){?>
					<th class="head"><?=$i;?></th><?
				}?>
				<th class="head">비고</th>
			</tr>
		</thead>
	</table>
</div>
<div id="ID_BODY" style="width:100%; height:100px; overflow-x:scroll; overflow-y:scroll;">
	<table class="my_table" style="width:1900px;">
		<colgroup>
			<col width="70px" span="2">
			<col width="50px">
			<col width="50px" span="31">
			<col>
		</colgroup>
		<tbody id="ID_LIST"></tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>