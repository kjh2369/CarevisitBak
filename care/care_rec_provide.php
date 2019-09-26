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
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_rec_provide_q.php'
		,	data :{
				'type'	:'SEARCH_CLIENT'
			,	'SR'	:$('#sr').val()
			,	'year'	:$('#lblYYMM').attr('year')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('tr',$('#tbodyList')).unbind('mouseover').bind('mouseover',function(){
					$('.td',this).css('background-color','#EFEFEF');
				}).unbind('mouseout').bind('mouseout',function(){
					$('.td',this).css('background-color','#FFFFFF');
				});

				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfReg(obj){
		var width = 1024;
		var height = 768;
		var left = (screen.availWidth - width) / 2;
		var top = (screen.availHeight - height) / 2;

		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var url = './care_rec_provide_reg.php';
		var win = window.open('about:blank', 'REC_PROVIDE_REG', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'SR'	:$('#sr').val()
			,	'jumin'	:$(obj).attr('jumin')
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$(obj).attr('month')
			,	'objId'	:$(obj).attr('id')
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

		form.setAttribute('target', 'REC_PROVIDE_REG');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">서비스 제공기록지(<?=$title;?>)</div>
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
			<td class="last">&nbsp;</td><?
			//$myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM")); lfSearch();');?>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="40px">
		<col width="70px">
		<col width="50px">
		<col width="1px">
		<col width="48px" span="12">
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">대상자명</th>
			<th class="head">성별</th>
			<th class="head">생년월일</th>
			<th class="head">중점</th>
			<th class="head"></th>
			<th class="head">1월</th>
			<th class="head">2월</th>
			<th class="head">3월</th>
			<th class="head">4월</th>
			<th class="head">5월</th>
			<th class="head">6월</th>
			<th class="head">7월</th>
			<th class="head">8월</th>
			<th class="head">9월</th>
			<th class="head">10월</th>
			<th class="head">11월</th>
			<th class="head last">12월</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>