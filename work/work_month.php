<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_body_header.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code	= $_SESSION["userCenterCode"];
	$mode   = $_POST['mode'] != '' ? $_POST['mode'] : 1;
	$jumin  = $_POST['jumin'];
	$member = $_POST['member'];
	$year	= $_POST['year']  != '' ? $_POST['year']  : date('Y', mktime());
	$month	= $_POST['month'] != '' ? $_POST['month'] : date('m', mktime());

	$init_year = $myF->year();

?>
<script type="text/javascript" src="../js/work.js"></script>
<script language="javascript">
<!--

// 근무현황 실시간 조회
function show_detail(mode, jumin, member, yoy_name, su_name, svc_kind, year, month){

	var URL = 'work_mon_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mode:mode,
				jumin:jumin,
				member:member,
				yoy_name:yoy_name,
				su_name:su_name,
				svc_kind:svc_kind,
				year:year,
				month:month
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

//요양사 상세조회
function show_detail2(mode, jumin, member){

	var f = document.f;

	f.mode.value   = mode;
	f.jumin.value  = jumin;
	f.member.value = member;
	f.su_name.value = '';
	f.yoy_name.value = '';
	f.svc_kind.value = 'all';

	f.submit();
}

//당일일정(기관) 이전으로
function return_back(){
	var f = document.f;

	f.su_name.value = '';

	f.action = '../work/work_real.php';
	f.submit();
}

//당일일정(요양보호사) 목록보기
function return_back2(){
	var f = document.f;

	f.mode.value = '1';
	f.su_name.value = '';

	f.action = "../work/work_month.php";
	f.submit();
}

//요양사상세 엑셀 출력
function real_excel(){

	var f = document.f;

	f.mode.value   = '2';

	f.action = "../work/work_month_excel.php";
	f.submit();
}

//근무현황 실시간 엑셀출력
function real_excel2(){

	var f = document.f;

	f.mode.value   = '1';

	f.action = "../work/work_month_excel.php";
	f.submit();
}

-->
</script>
<div class="title title_border"><? if($mode == 1){?>당일일정<?=$today;?>(요양보호사)<?}else{?>월별진행일정(요양보호사)<?} ?></div>
<form name="f" method="post">
<?
	if ($mode == 1){ ?>
		<table class="my_table" style='width:100%;'>
			<colgroup>
				<col width="40px">
				<col width="90px">
				<col width="40px">
				<col width="100px">
				<col width="40px">
				<col>
			</colgroup>
			<tbody>
			<th class="center">요양사</th>
			<td>
				<input name="yoy_name" type="text" value="<?=$yoy_name;?>" style="width:90px;" onFocus="this.select();">
			</td>
			<th class="center">고객명</th>
			<td>
				<input name="su_name" type="text" value="<?=$su_name;?>" style="width:90px;" onFocus="this.select();">
			</td>
			<th class="center">서비스</th>
			<td>
			<?
				$kind_list = $conn->kind_list($code, $gHostSvc['voucher']);	//$gHostSvc['voucher']

				echo '<select name=\'svc_kind\' style=\'width:auto;\'>';
				echo '<option value=\'all\'>전체</option>';

				foreach($kind_list as $i => $k){
					echo '<option value=\''.$k['code'].'\' '.($code == $k['code'] ? 'selected' : '').'>'.$k['name'].'</option>';
				}

				echo '</select>';
			?>
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" style="margin:0;" onClick="show_detail('<?=$mode?>','',document.getElementById('member').value, document.getElementById('yoy_name').value, document.getElementById('su_name').value, document.getElementById('svc_kind').value);return false;">조회</button></span>
				<span class="btn_pack m icon"><span class="excel"></span><button name="btnExcel" type="button" onFocus="this.blur();" onClick="real_excel2();">엑셀</button></span>
			</td>
			</tbody>
		</table><?
	}else{?>
		<table class="my_table" style='width:100%;'>
			<colgroup>
				<col width="35px">
				<col width="150px">
				<col width="40px">
				<col width="200px">
				<col width="88px">
				<col span="2">
			</colgroup>
			<tbody>
				<tr>
					<th>년월</th>
					<td class="left">
						<select name="year" style="width:auto; margin:0;"><?
						for($i=$init_year[0]; $i<=$init_year[1]; $i++){
						?>
							<option value="<?=$i;?>" <? if($i == $year){echo 'selected';} ?>><?=$i;?>년</option>
						<?
						}?>
						</select>
						<select name="month" style="width:auto; margin:0;">
							<option value="01"<? if($month == "01"){echo "selected";}?>>1월</option>
							<option value="02"<? if($month == "02"){echo "selected";}?>>2월</option>
							<option value="03"<? if($month == "03"){echo "selected";}?>>3월</option>
							<option value="04"<? if($month == "04"){echo "selected";}?>>4월</option>
							<option value="05"<? if($month == "05"){echo "selected";}?>>5월</option>
							<option value="06"<? if($month == "06"){echo "selected";}?>>6월</option>
							<option value="07"<? if($month == "07"){echo "selected";}?>>7월</option>
							<option value="08"<? if($month == "08"){echo "selected";}?>>8월</option>
							<option value="09"<? if($month == "09"){echo "selected";}?>>9월</option>
							<option value="10"<? if($month == "10"){echo "selected";}?>>10월</option>
							<option value="11"<? if($month == "11"){echo "selected";}?>>11월</option>
							<option value="12"<? if($month == "12"){echo "selected";}?>>12월</option>
						</select>
					</td>
					<th class="center">고객명</th>
					<td>
						<input name="su_name" type="text" value="<?=$su_name;?>" style="width:90px;" onFocus="this.select();">
						<span class="btn_pack m icon"><span class="refresh"></span><button type="button" style="margin:0;" onClick="show_detail('<?=$mode?>',document.getElementById('jumin').value,document.getElementById('member').value,'', document.getElementById('su_name').value,'',document.getElementById('year').value,document.getElementById('month').value);">조회</button></span>
					</td>

					<th class="left">요양보호사</th>
					<td class="left last"><?=$member;?></td>
					<td class="right last">
					<?
						switch($back_mode){
						case 'day':?>
							<span class="btn_pack m icon"><span class="excel"></span><button name="btnExcel" type="button" onFocus="this.blur();" onClick="real_excel();">엑셀</button></span>
							<span class="btn_pack m icon"><span class="before"></span><button type="button" style="margin:0;" onClick="return_back();">이전</button></span><?
							break;
						default:?>
							<span class="btn_pack m icon"><span class="excel"></span><button name="btnExcel" type="button" onFocus="this.blur();" onClick="real_excel();">엑셀</button></span>
							<span class="btn_pack m icon"><span class="list"></span><button type="button" style="margin:0;" onClick="return_back2();">리스트</button></span><?
						}
					?>
					</td>
				</tr>
			</tbody>
		</table><?
	}
?>

<div id="myBody" style="width:100%;"></div>

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
<script language="javascript">
	show_detail('<?=$mode?>','<?=$jumin;?>','<?=$member;?>','','','');
</script>