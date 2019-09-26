<?
	include("../inc/_header.php");

	//$tempNow = explode('-', date("Y-m-d", strtotime(date("Y-m", mktime())."-01")));
	$mYear = $_REQUEST['calYear'];
	$mMonth = $_REQUEST['calMonth'];
?>
<style>
body{
	margin-top:10px;
	margin-left:10px;
	overflow-x:hidden;
}
</style>
<script type="text/javascript" src="../js/work.js"	></script>
<form name="f" method="post" action="su_iljung_ok.php">
<input name="mMode" type="hidden" value="MODIFY">
<div id="center_info"></div>
<!--div id="yoy_const" style="padding-top:10px;"></div-->
<div id="su_const" style="padding-top:10px;"></div>
<div style="width:900px; padding-top:10px; padding-bottom:5px; text-align:left;">
	색상정보 : <font color="#1b8830">완료</font> | <font color="#0000ff">수행중</font> | <font color="#000000">준비중</font> | <font color="#ff0000">미수행</font>
</div>
<div id="calendar" style="width:900px;"></div>
<div id="bodyLayer" style="z-index:0; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); background-color:black; position:absolute; top:0px; left:0px; height:0px; width:0px;"></div>
<div id="tableLayer" style="z-index:1; left:0; top:0; position:absolute; color:#000000;">
	<table id="iljungCopyTable" style="width:290px; height:80px; background-color:#ffffff; display:none;">
	<tr>
	<td class="noborder" style="width:10px;"></td>
	<td class="title" style="width:280px;">복사할 일정의 년월을 선택하여 주십시오.</td>
	</tr>
	<tr>
	<td class="noborder" colspan="2">
		<table>
		<tr>
		<td style="border:0px;">
			<select name="copyYear" style="width:65px;">
			<?
				$years = $conn->get_min_max_year('t01iljung', 't01_sugup_date');
				$years[1] = date("Y", mkTime());
				for($i=$years[0]; $i<=$years[1]; $i++){
				?>
					<option value="<?=$i;?>"<? if($i == $mYear){echo "selected";}?>><?=$i;?></option>
				<?
				}
			?>
			</select>
			<select name="copyMonth" style="width:55px;">
				<option value="01" <? if ($mMonth == '01'){echo 'selected';} ?>>1월</option>
				<option value="02" <? if ($mMonth == '02'){echo 'selected';} ?>>2월</option>
				<option value="03" <? if ($mMonth == '03'){echo 'selected';} ?>>3월</option>
				<option value="04" <? if ($mMonth == '04'){echo 'selected';} ?>>4월</option>
				<option value="05" <? if ($mMonth == '05'){echo 'selected';} ?>>5월</option>
				<option value="06" <? if ($mMonth == '06'){echo 'selected';} ?>>6월</option>
				<option value="07" <? if ($mMonth == '07'){echo 'selected';} ?>>7월</option>
				<option value="08" <? if ($mMonth == '08'){echo 'selected';} ?>>8월</option>
				<option value="09" <? if ($mMonth == '09'){echo 'selected';} ?>>9월</option>
				<option value="10" <? if ($mMonth == '10'){echo 'selected';} ?>>10월</option>
				<option value="11" <? if ($mMonth == '11'){echo 'selected';} ?>>11월</option>
				<option value="12" <? if ($mMonth == '12'){echo 'selected';} ?>>12월</option>
			</select>
		</td>
		<td style="border:0px; padding-left:5px;">
			<a href="#" onClick="_iljungCopyExec();"><img src="../image/btn_copy.png"></a>
			<a href="#" onClick="_iljungCopyCancel();"><img src="../image/btn_cancel.png"></a>
		</td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
</div>
</form>
<?
	include("../inc/_footer.php");
?>
<script language='javascript'>
function _setYoyConst(){
	//yoy_const.innerHTML = getHttpRequest('su_yoy_const.php?mType=search&mCode=<?=$_REQUEST["mCode"];?>&mKind=<?=$_REQUEST["mKind"];?>&mKey=<?=$_REQUEST["mKey"];?>');
}
function _setSuConst(){
	su_const.innerHTML = getHttpRequest('su_const_iljung.php?mType=search&mCode=<?=$_REQUEST["mCode"];?>&mKind=<?=$_REQUEST["mKind"];?>&mKey=<?=$_REQUEST["mKey"];?>&ym=<?=$_REQUEST["calYear"]?><?=$_REQUEST["calMonth"]?>');
}
function _setCalendar(pCalYear, pCalMonth){
	var calYear = null;
	var calMonth = null;
	var mCode = null;
	var mKind = null;
	var mKey = null;
	var mJuminNo = null;

	try{
		calYear = pCalYear ;
		calMonth = pCalMonth;

		if (calYear == undefined || calMonth == undefined){
			calYear = '';
			calMonth = '';
		}

		if (calYear == '' || calMonth == ''){
			calYear = document.f.calYear.value;
			calMonth = document.f.calMonth.value;
		}
	}catch(e){
		var now = new Date();

		calYear = now.getFullYear();
		calMonth = now.getMonth()+1;
	}

	mCode    = document.f.mCode.value;
	mKind    = document.f.mKind.value;
	mKey     = document.f.mKey.value;
	mJuminNo = document.f.mJuminNo.value;

	calendar.innerHTML = getHttpRequest('su_calendar.php?gubun=search&calYear='+calYear+'&calMonth='+calMonth+'&mCode='+mCode+'&mKind='+mKind+'&mKey='+mKey+'&mJuminNo='+mJuminNo);

	//_addYoySudangList();
}
_setCenterInfo('<?=$_REQUEST["mCode"];?>', '<?=$_REQUEST["mKind"];?>', '<?=$_REQUEST["mKey"];?>','<?=$_REQUEST["calYear"]?>','<?=$_REQUEST["calMonth"]?>');
//_setYoyConst();
_setSuConst();
_setCalendar('<?=$_REQUEST["calYear"]?>','<?=$_REQUEST["calMonth"]?>');
//_addYoySudangList();
</script>
<script language="javascript">
	self.focus();
</script>