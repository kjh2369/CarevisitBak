<?
	include("../inc/_header.php");

	$code  = $_REQUEST["mCode"];
	$kind  = $_REQUEST["mKind"];
	$key   = $_REQUEST["mKey"];
	$year  = $_REQUEST["calYear"];
	$month = $_REQUEST["calMonth"];


	$kind_list = $conn->kind_list($code, true);
	$svc_id    = $kind_list[0]['id'];

	if ($lbTestMode){
		foreach($kind_list as $row){
			if ($row['code'] == $kind){
				$svc_id = $row['id'];
				break;
			}
		}
	}

	// 마감일자
	$close_yn = $conn->get_closing_act($code, $year.$month);

	if ($close_yn == 'Y'){
		$msg = '※ <font color="#ff0000">'.$year.'년 '.$month.'월</font> 실적등록마감이 완료되어 <font color="#ff0000">등록/수정/삭제</font>가 불가합니다.';
	}else{
		$msg = '';
	}

	// charset="euc-kr"
?>
<style>
body{
	margin-left:10px;
	margin-right:10px;
	overflow-x:hidden;
}
</style>

<script type='text/javascript' src='../js/change_info_guide.js'></script>
<script type="text/javascript" src="../js/iljung.reg.js"></script>
<script type="text/javascript" src="../js/iljung.add.js"></script>
<script type="text/javascript" src="../js/work.js"></script>
<script type="text/javascript" src="../longcare/longcare.js"></script>
<script type="text/javascript" src="./iljung.longcare.js"></script>
<script type="text/javascript" src="./iljung.longcare.result.js"></script>

<form name="f" method="post">

<input name='iljung_msg' type='hidden' value='<?=$msg;?>'>
<input name="pressCal" type="hidden" value="N">

<div id="loading"></div>

<div id="voucher_msg" style="display:none; margin-bottom:10px;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colgroup>
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="left bold" style="color:#ff0000;">
					생성된 바우처 내역이 없습니다. 바우처 생성내역을 먼저 등록하시거나 비급여로 등록하십시오.
				</th>
			</tr>
		</tbody>
	</table>
</div>

<div id="center_info"></div>

<div id="iljung_care"></div>

<div id="pattern" style="position:absolute; display:none; margin-top:2px; border:3px solid #194685; background:#cad8eb;"></div>

<div id="iljung_const"></div>

<div id="iljung_button"></div>

<div id="iljung_calendar"></div>

<div id="iljung_planlist" style="position:absolute; left:0; top:0; width:100%; height:auto; display:none;"></div>

<div id="iljung_family_info" style="position:absolute; z-index:10001; left:0; top:0; width:100%; height:auto; display:none;"></div>

<?
	include_once('iljung_message.php');
?>

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
					<option value="<?=$i;?>"<? if($i == $year){echo "selected";}?>><?=$i;?></option>
				<?
				}
			?>
			</select>
			<select name="copyMonth" style="width:55px;">
				<option value="01" <? if ($month == '01'){echo 'selected';} ?>>1월</option>
				<option value="02" <? if ($month == '02'){echo 'selected';} ?>>2월</option>
				<option value="03" <? if ($month == '03'){echo 'selected';} ?>>3월</option>
				<option value="04" <? if ($month == '04'){echo 'selected';} ?>>4월</option>
				<option value="05" <? if ($month == '05'){echo 'selected';} ?>>5월</option>
				<option value="06" <? if ($month == '06'){echo 'selected';} ?>>6월</option>
				<option value="07" <? if ($month == '07'){echo 'selected';} ?>>7월</option>
				<option value="08" <? if ($month == '08'){echo 'selected';} ?>>8월</option>
				<option value="09" <? if ($month == '09'){echo 'selected';} ?>>9월</option>
				<option value="10" <? if ($month == '10'){echo 'selected';} ?>>10월</option>
				<option value="11" <? if ($month == '11'){echo 'selected';} ?>>11월</option>
				<option value="12" <? if ($month == '12'){echo 'selected';} ?>>12월</option>
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
	<div id='divPopupLayer' style='position:absolute; left:0; top:0; display:none; z-index:11; width:200; padding:20px; border:2px solid #cccccc; background-color:#ffffff;'></div>
</div>
</form>
<?
	include("../inc/_footer.php");
?>
<script language="javascript">
	self.focus();

	window.onload = function(){
		_set_center_info('<?=$code;?>','<?=$key;?>','<?=$year;?>','<?=$month;?>','<?=$svc_id;?>','IN');
	}
</script>