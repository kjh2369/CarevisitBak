<?
	include_once("../inc/_header.php");

	$code  = $_GET['mCode'];
	$kind  = $_GET['mKind'];
	$key   = $_GET['mKey'];
	$day   = $_GET['mDay'];
	$index = $_GET['mIndex'];
	$date  = $_GET['mDate'];
	$week  = $_GET['mWeek'];
	$mode  = $_GET['mMode'];

	$kind_list = $conn->kind_list($code, true);

	foreach($kind_list as $num => $k_list){
		if ($k_list['code'] == $kind){
			$svc_id = $k_list['id'];
			break;
		}
	}

	$year  = substr($date,0,4);
	$month = substr($date,4,2);
?>
<style>
body{
	margin-top:10px;
	margin-left:10px;
}
</style>
<script type="text/javascript" src="../js/iljung.reg.js"></script>
<script type="text/javascript" src="../js/iljung.add.js"></script>
<script language='javascript'>
<!--
var opener = window.dialogArguments
var retVal = 'cancel';

function _currnetRow(value1, value2, value3, value4, value5){
	var currentItem = new Array();

	currentItem[0] = value1;
	currentItem[1] = value2;
	currentItem[2] = value3;
	currentItem[3] = value4;
	currentItem[4] = value5;

	window.returnValue = currentItem;
	window.close();
}
//-->
</script>
<form name="f" method="post">

<div id="win_add_body">

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

</div>

<input name='addDay'   type='hidden' value='<?=$day;?>'>
<input name='addIndex' type='hidden' value='<?=$index;?>'>
<input name='addDate'  type='hidden' value='<?=$date;?>'>
<input name='addWeek'  type='hidden' value="<?=date('w', mktime(0,0,0,$month,$day,$year));?>">

</form>

<script language='javascript'>
	_set_center_info('<?=$code;?>','<?=$key;?>','<?=$year;?>','<?=$month;?>','<?=$svc_id;?>','<?=$mode;?>','<?=$day?>');
</script>
<?
	include_once("../inc/_footer.php");
?>