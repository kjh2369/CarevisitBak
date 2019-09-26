<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	$fromDt = Date('Y-m-').'01';
	$toDt = Date('Y-m-').$myF->lastDay(Date('y'),Date('m'));
?>
<script type="text/javascript">
	$(document).ready(function(){
			$('input:text').each(function(){
				__init_object(this);
			});

			$('#ID_BODY').unbind('mouseover').bind('mouseover',function(){
				$('body').css('overflow','hidden');
			}).unbind('mouseout').bind('mouseout',function(){
				$('body').css('overflow','');
			});

			lfResize();
		});

		$(window).bind('resize', function(e){
			window.resizeEvt;
			$(window).resize(function(){
				clearTimeout(window.resizeEvt);
				window.resizeEvt = setTimeout(function(){
					lfResize();
				}, 250);
			});
		}).scroll(function(){
			lfResize();
		});

		function lfResize(){
			$('div[id^="ID_LIST"]',$('#ID_BODY')).each(function(){
				try{
					var top = $(this).offset().top;
					var height = document.body.offsetHeight;
					var menu = 0; //$('#left_box').offset().top + $('#left_box').height();
					var bottom = $('#copyright').height();
					var foot = $('#ID_FOOT').height();

					if ($('#left_box').length > 0) menu = $('#left_box').offset().top + $('#left_box').height();
					if (!foot) foot = 0;

					if (menu + bottom > height){
						var h = height - top - foot;

						if ($('body').scrollTop() > 0) h = menu - top - foot;
					}else{
						var h = height - top - bottom - foot;
					}

					$(this).height(h - 2);
				}catch(e){
				}
			});
		}

	function lfTgtLoad(){
		$.ajax({
			type :'POST'
		,	url  :'./care_tgt_conf_fun.php'
		,	data :{
				'SR':$('#sr').val()
			,	'fromDt':$('#txtFromDt').val()
			,	'toDt':$('#txtToDt').val()
			,	'type':'1'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('tbody',$('#ID_LIST_TGT')).html(html);
				$('tr',$('tbody',$('#ID_LIST_TGT'))).css('cursor','default').attr('selYn','N').unbind('mouseover').bind('mouseover',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','#EAEAEA');
				}).unbind('mouseout').bind('mouseout',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','');
				}).unbind('click').bind('click',function(){
					$('tr',$('tbody',$('#ID_LIST_TGT'))).attr('selYn','N').css('background-color','');
					$(this).attr('selYn','Y').css('background-color','#FAF4C0');

					lfSvcLoad($(this).attr('jumin'));
				});

				if ($('tbody',$('#ID_LIST_TGT')).height() > $('#ID_LIST_TGT').height()){
					$('td',$('tr:last',$('tbody',$('#ID_LIST_TGT')))).css('border-bottom','none');
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSvcLoad(jumin){
		$.ajax({
			type :'POST'
		,	url  :'./care_tgt_conf_fun.php'
		,	data :{
				'SR':$('#sr').val()
			,	'fromDt':$('#txtFromDt').val()
			,	'toDt':$('#txtToDt').val()
			,	'jumin':jumin
			,	'type':'2'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('tbody',$('#ID_LIST_SVC')).html(html);

				if ($('tbody',$('#ID_LIST_TGT')).height() > $('#ID_LIST_TGT').height()){
					$('td',$('tr:last',$('tbody',$('#ID_LIST_TGT')))).css('border-bottom','none');
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfExcel(){
		var parm = new Array();
			parm = {
				'SR':$('#sr').val()
			,	'fromDt':$('#txtFromDt').val()
			,	'toDt':$('#txtToDt').val()
			,	'jumin':$('tr[selYn="Y"]',$('tbody',$('#ID_LIST_TGT'))).attr('jumin')
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
		form.setAttribute('action', './care_tgt_conf_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>

<div class="title title_border">대상자별 실적(<?=$title;?>)</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="177px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">조회기간</th>
			<td>
				<input id="txtFromDt" type="text" value="<?=$fromDt;?>" class="date"> ~
				<input id="txtToDt" type="text" value="<?=$toDt;?>" class="date">
			</td>
			<td class="left last">
				<div style="float:left; width:auto;">
					<span class="btn_pack m"><button onclick="lfTgtLoad();">조회</button></span>
				</div>
				<div style="float:right; width:auto;">
					<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel();">엑셀</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<div id="ID_BODY">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="355px">
		<col width="1px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="top">
				<table class="my_table" style="width:100%;"><?
					$colgrp = '
						<col width="40px">
						<col width="80px">
						<col width="70px">
						<col>';?>
					<colgroup><?=$colgrp;?></colgroup>
					<thead>
						<tr>
							<th class="head">No</th>
							<th class="head">성명</th>
							<th class="head">생년월일</th>
							<th class="head last">이용기간</th>
						</tr>
					</thead>
				</table>
				<div id="ID_LIST_TGT" style="overflow-x:hidden; overflow-y:scroll; height:100px;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgrp;?></colgroup>
						<tbody></tbody>
					</table>
				</div>
			</td>
			<td></td>
			<td class="top last">
				<table class="my_table" style="width:100%;"><?
					$colgrp = '
						<col width="250px">
						<col width="55px" span="3">
						<col>';?>
					<colgroup><?=$colgrp;?></colgroup>
					<thead>
						<tr>
							<th class="head">서비스</th>
							<th class="head">년</th>
							<th class="head">월</th>
							<th class="head">일</th>
							<th class="head last">횟수</th>
						</tr>
					</thead>
				</table>
				<div id="ID_LIST_SVC" style="overflow-x:hidden; overflow-y:scroll; height:100px;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgrp;?></colgroup>
						<tbody></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
</div>
<?
	include_once('../inc/_db_close.php');
?>