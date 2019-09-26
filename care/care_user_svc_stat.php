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
		,	url  :'./care_user_svc_stat_search.php'
		,	data :{
				'SR'	:$('#sr').val()
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('tr',$('#tbodySvcHis')).remove();
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();

				var obj = $('tr',$('#tbodyList'));
				var cnt = $(obj).length;

				$('#lblMsg').text('서비스 이용자 수 : '+cnt+'명');
				$(obj).attr('selYn','N').css('cursor','pointer').unbind('mouseover').bind('mouseover',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','#D9E5FF');
				}).unbind('mouseout').bind('mouseout',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','#FFFFFF');
				}).unbind('click').bind('click',function(){
					$(obj).attr('selYn','N').css('background-color','#FFFFFF');
					$(this).attr('selYn','Y').css('background-color','#FAF4C0');

					lfLoadSvcHis($(this).attr('code'));
				});

				$('tr:first',$('#tbodyList')).click();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadSvcHis(code){
		if (!code) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_user_svc_stat_his.php'
		,	data :{
				'SR'	:$('#sr').val()
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			,	'code'	:code
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodySvcHis').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfNameTable(mstCd,proCd,svcCd,subCd){
		var objModal = new Object();
		var url = './care_svc_use_stat_nametable.php';
		var style = 'dialogWidth:500px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

		objModal.SR		= $('#sr').val();
		objModal.code	= mstCd+proCd+svcCd+subCd;
		objModal.year	= $('#lblYYMM').attr('year');
		objModal.month	= $('#lblYYMM').attr('month');

		window.showModalDialog(url, objModal, style);
	}
</script>
<div class="title title_border">이용자별 서비스현황(<?=$title;?>)</div>
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
			<td class="center last" colspan="3">
				<div id="lblMsg" class="left"></div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;"><?
	$colgroup = '
		<col width="40px">
		<col width="70px">
		<col width="70px">
		<col width="40px">
		<col width="50px">
		<col width="40px">
		<col width="150px">
		<col width="150px">
		<col width="50px">
		<col width="50px">
		<col>';?>
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">생년월일</th>
			<th class="head">성별</th>
			<th class="head">상태</th>
			<th class="head">중점</th>
			<th class="head">주소</th>
			<th class="head">연락처</th>
			<th class="head">서비스</th>
			<th class="head">이용수</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top last" colspan="11">
				<div style="width:100%; height:207px; overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody id="tbodyList"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<div class="title title_border">서비스 이용내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="200px">
		<col width="200px">
		<col width="70px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">서비스</th>
			<th class="head">자원</th>
			<th class="head">담당자</th>
			<th class="head">서비스 횟수</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodySvcHis"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>