<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$orgNo = $_SESSION['userCenterCode'];
	$year = Date('Y');
	$month = Date('m');
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
		$('div[id^="btnMonth_"]').each(function(){
			if ($(this).hasClass('my_month_y')){
				$(this).removeClass('my_month_y').addClass('my_month_1');
				return true;
			}
		});

		$('#btnMonth_'+month).removeClass('my_month_1').addClass('my_month_y');
		$('#lblYYMM').attr('month',(month < 10 ? '0' : '')+month);
		lfSearch();
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_iljung_svc_group_search.php'
		,	data :{
				'SR':'<?=$sr;?>'
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			,	'category':$('#ID_CATEGORY').attr('category')
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

	function lfReg(grpCd,sugaCd,seq){
		var w = 1024;
		var h = 768;
		var l = (screen.availWidth - w) / 2;
		var t = (screen.availHeight - h) / 2;

		if (!grpCd) grpCd = '';
		if (!sugaCd) sugaCd = '';
		if (!seq) seq = '';

		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=yes';
		var url = './care_iljung_svc_group_reg.php';
		var win = window.open('', 'ILJUNG_SVC_GROUP_REG', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			,	'SR':'<?=$sr;?>'
			,	'grpCd':grpCd
			,	'sugaCd':sugaCd
			,	'seq':seq
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

		form.setAttribute('target', 'ILJUNG_SVC_GROUP_REG');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfDel(grpCd){
		if (!grpCd) return;
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_iljung_svc_group_delete.php'
		,	data :{
				'SR':'<?=$sr;?>'
			,	'grpCd':grpCd
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				$('#tempLodingBar').remove();

				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					lfSearch();
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfCategoryFind(){
		var objModal = new Object();
		var url = './care_svc_category_find.php';
		var style = 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win = window;
		objModal.SR	 = $('#sr').val();
		objModal.code= '';
		objModal.name= '';

		window.showModalDialog(url, objModal, style);

		if (objModal.code){
			$('#ID_CATEGORY').attr('category',objModal.code).text(objModal.name);
			lfSearch();
		}
	}
</script>
<div class="title title_border">
	<div style="float:left; width:auto;">묶음서비스 일정관리(<?=$title;?>)</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m"><span class="add"></span><button type="button" class="bold" onclick="lfReg();">등록</button></span>
	</div>
</div>
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
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last"><?=$myF->_btn_month($month,'lfMoveMonth(');?></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">카테고리 선택</th>
			<td id="ID_CATEGORY" class="left last" onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='#FFFFFF';" onclick="lfCategoryFind();" category=""></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="250px">
		<col width="70px">
		<col width="50px">
		<? if($sr=='R'){ ?><col width="50px"><? } ?>
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">묶음명</th>
			<th class="head">일자</th>
			<th class="head">시간</th>
			<? if($sr=='R'){ ?><th class="head">실적</th><? } ?>
			<th class="head">대상자수</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>