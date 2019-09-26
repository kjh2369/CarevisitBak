<?
	include("../inc/_header.php");
	include("../inc/_body_header.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code  = $_SESSION["userCenterCode"];
	$kind  = $_SESSION["userCenterKind"][0];
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function member_detail(year, month, jumin, member){
	var f = document.f;

	f.year.value   = year;
	f.month.value  = month;
	f.jumin.value  = jumin;
	f.member.value = member;

	f.action = '../work/work_month.php';
	f.submit();
}

// 근무현황 실시간 조회
function getWorkRealList2(mCode, mKind, mStat, mSuName, mSvc){
	var URL = 'work_real_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:mCode,
				mKind:mKind,
				mStat:mStat,
				mSuName:mSuName,
				mSvc:mSvc
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}


function real_excel(){

	var f = document.f;

	f.action = "work_real_excel.php";
	f.submit();
}
-->
</script>

<div class="title" style="width:auto; float:left;">당일일정(기관)</div>
<div class="my_right" style="padding-top:10px;">
	새로고침<input name="progressTime" type="text" value="30" style="width:30px; text-align:center;" onFocus="document.getElementById('reloadTime').focus();" readOnly>초
	간격<input name="reloadTime" type="text" value="30" style="width:30px; text-align:center;" onFocus="this.select();">
	<span class="btn_pack small"><button type="button" onFocus="this.blur();" onClick="timerRestart();">적용</button></span>
</div>

<form name="f" method="post">

<table class="my_table my_border">
	<colgroup>
		<col width="40px">
		<col width="100px">
		<col width="40px">
		<col width="100px">
		<col width="40px">
		<col width="200px">
		<col >
	</colgroup>
	<tbody>
		<tr>
			<th class="center">고객명</th>
			<td>
				<input name="su_name" type="text" value="<?=$su_name;?>" style="width:100px;" onFocus="this.select();">
			</td>
			<th class="center">서비스</th>
			<td>
			<?
				$kind_list = $conn->kind_list($code, $gHostSvc['voucher']);

				echo '<select name=\'svc_kind\' style=\'width:auto;\'>';
				echo '<option value=\'all\'>전체</option>';

				foreach($kind_list as $i => $k){
					echo '<option value=\''.$k['code'].'\' '.($code == $k['code'] ? 'selected' : '').'>'.$k['name'].'</option>';
				}

				echo '</select>';
			?>
			</td>
			<th class="head">상태</th>
			<td class="left last">
				<select name="status" style="width:auto; margin:0;">
				<option value="all">전체</option>
				<?
					$sql = "select m81_code"
						 . ",      m81_name"
						 . "  from m81gubun"
						 . " where m81_gbn = 'STA'";
					$conn->query($sql);
					$conn->fetch();
					$row_count = $conn->row_count();

					for($i=0; $i<$row_count; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['m81_code'];?>" <? if($row['m81_code'] == $status){echo 'selected';} ?>><?=$row['m81_name'];?></option><?
					}

					$conn->row_free();
				?>
				</select>
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onFocus="this.blur();" onClick="getWorkRealList2('<?=$code;?>', '<?=$kind;?>', document.f.status.value, document.f.su_name.value, document.f.svc_kind.value); return false;">조회</button></span>
				<span class="btn_pack m icon"><span class="excel"></span><button name="btnExcel" type="button" onFocus="this.blur();" onClick="real_excel();">엑셀</button></span>
			</td>
			<td class="right last"><?
				$weekDay = date('w', mkTime());

				switch($weekDay){
					case 0: $weekDay = '일요일'; break;
					case 1: $weekDay = '월요일'; break;
					case 2: $weekDay = '화요일'; break;
					case 3: $weekDay = '수요일'; break;
					case 4: $weekDay = '목요일'; break;
					case 5: $weekDay = '금요일'; break;
					case 6: $weekDay = '토요일'; break;
				}
				$dateTimeString = '[일자 및 시간 : '.date('Y.m.d', mkTime()).' '.$weekDay.' '.date('H:i', mkTime()).']';
				echo $dateTimeString;?>
			</td>
		</tr>
	</tbody>
</table>

<div id="myBody" style="width:100%;"></div>

</form>
<?
	include("../inc/_body_footer.php");
	include("../inc/_footer.php");
?>
<script language="javascript">
	getWorkRealList('<?=$code;?>','<?=$kind;?>','all');
</script>
<script language="javascript">
<!--

var it_timer    = null;
var ii_sec      = 30;
var it_secounds = 30;

function timerInt(){
	it_timer = setInterval("timer()", 1000);
}

function timerClear(){
	clearInterval(it_timer);
}

function timer(){
	ii_sec--;

	if (ii_sec < 1){
		ii_sec = it_secounds;
		getWorkRealList('<?=$code;?>', '<?=$kind;?>', document.f.status.value);
	}

	document.getElementById('progressTime').value = ii_sec;
}

function timerRestart(){
	it_secounds = document.getElementById('reloadTime').value;
	ii_sec = it_secounds;
	timerClear();
	timerInt();
}
timerInt();
//-->
</script>