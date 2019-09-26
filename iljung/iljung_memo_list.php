<?
	include_once('../inc/_header.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= Date('Y');
	$month	= IntVal(Date('m'));
	$toDt	= Date('Y-m-d');
	$fromDt	= $myF->dateAdd('day', -15, $toDt, 'Y-m-d');
	
	if($_SESSION['MENU_TOP']){
		if($_SESSION['MENU_TOP'] == 'K'){ //재가지원
			$svcCd = 'S';
		}else if($_SESSION['MENU_TOP'] == 'L'){	//자원연계
			$svcCd = 'R';
		}else { //전체
			$svcCd = 'all';
		}
	}else {
		//재가지원 또는 자원연계
		$svcCd = $_SESSION['userTypeSR'];
	}

?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.clsObj').each(function(){
			__init_object(this);
		});
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type	:'POST'
		,	url		:'./iljung_memo_search.php'
		,	data	:{
				'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			,	'name'	:$('#txtName').val()
			,	'from'	:$('#txtFrom').val()
			,	'to'	:$('#txtTo').val()
			,	'svcCd' :$('#findSvcCd').val()
			}
		,	beforeSend	:function(){
			}
		,	success	:function(html){
				$('#tbodyList').html(html);
			}
		,	error:function(request, status, error){
				alert(error);
			}
		});
	}

	function lfModify(jumin,yymm,seq,svcCd){
		var width = 800;
		var height = 300;
		var left = (screen.availWidth - width) / 2;
		var top = (screen.availHeight - height) / 2;
		var target = 'ILJUNG_MEMO';
		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var url = './iljung_memo_reg.php';
			gPlanWin = window.open('about:blank', target, option);
			gPlanWin.opener = self;
			gPlanWin.focus();

		var parm = new Array();
			parm = {
				'jumin'	:jumin
			,	'year'	:yymm.substr(0,4)
			,	'month'	:yymm.substr(4,2)
			,	'seq'	:seq
			,	'svcCd'	:svcCd
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

		form.setAttribute('target', target);
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfDelete(jumin,yymm,seq,no){
		if (!confirm('삭제 후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type	:'POST'
		,	url		:'./iljung_memo_delete.php'
		,	data	:{
				'jumin'	:jumin
			,	'year'	:yymm.substr(0,4)
			,	'month'	:yymm.substr(4,2)
			,	'seq'	:seq
			}
		,	beforeSend	:function(){
			}
		,	success	:function(result){
				if (__resultMsg(result)){
					$('tr[id="rowId_'+no+'"]').remove();
				}
			}
		,	error:function(request, status, error){
				alert(error);
			}
		});
	}

	function lfExcel(){
		var parm = new Array();
			parm = {
				'name'	:$('#txtName').val()
			,	'from'	:$('#txtFrom').val()
			,	'to'	:$('#txtTo').val()
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
		form.setAttribute('action', './iljung_memo_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">메모관리</div>
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
			<td class="last"><?
				for($i=1; $i<=12; $i++){
					if ($i == $month){
						$cls = 'my_month_y';
					}else{
						$cls = 'my_month_1';
					}?>
					<div id="btnMonth_<?=$i;?>" class="my_month <?=$cls;?>" style="float:left; margin-right:3px;"><a href="#" onclick="__moveMonth('<?=$i;?>',$('#lblYYMM')); lfSearch();"><span style="color:#000000;"><?=$i;?>월</span></a></div><?
				}?>
			</td>
		</tr>
	</tbody>
</table>
-->
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="85px">
		<col width="70px">
		<col width="180px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">수급자명</th>
			<td class=""><input id="txtName" type="text" value="" class="clsObj" style="width:100%;"></td>
			<th class="center">작성일자</th>
			<td class=""><input id="txtFrom" type="text" value="<?=$fromDt;?>" class="date clsObj"> ~ <input id="txtTo" type="text" value="<?=$toDt;?>" class="date clsObj"></td>
			<th class="center">서비스</th>
			<td class="left">
			<?
			if($svcCd == 'R' || $svcCd == 'S'){
				echo $svcCd == 'S' ?  '<span>재가지원</span>' : '<span>자원연계</span>';
				echo '<input id=\'findSvcCd\' type=\'hidden\' value=\''.$svcCd.'\'>';
			}else {
				$kind_list = $conn->kind_list($orgNo, $gHostSvc['voucher']);

				echo '<select id=\'findSvcCd\' name=\'findSvcCd\' style=\'width:auto;\'>';
				echo '<option value=\'all\'>전체</option>';

				foreach($kind_list as $i => $k){
					echo '<option value=\''.$k['code'].'\' '.($svcCd == $k['code'] ? 'selected' : '').'>'.$k['name'].'</option>';
				}

				echo '</select>';
			}
			?>
			</td>
			<td class="left last">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
				<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel();">Excel</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="70px">
		<col width="500px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">작성일</th>
			<th class="head">수급자</th>
			<th class="head">작성내용</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>