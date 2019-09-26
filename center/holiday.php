<?
	include_once('../inc/_header.php');
	//include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');

	$init_year = $myF->year();
	$code  = $_SESSION['userCenterCode'];
	$year  = $_REQUEST['year']  != '' ? $_REQUEST['year']  : date('Y', mktime());

	$last_day = $myF->dateAdd('day', -1, $myF->dateAdd('month', 1, $year.'-'.$month.'-01', 'Y-m-d'), 'd');

	$data_execute = $_POST['data_execute'];

	if ($data_execute != ''){
		$message = '';

		$conn->begin();

		if ($data_execute == 'save'){
			$date	= $_POST['date'];
			$name	= $_POST['name'];
			$pay_yn	= $_POST['pay_yn'];
			$del_yn = $_POST['delete_yn'];
			$count = sizeof($date);

			for($i=0; $i<$count; $i++){
				if ($del_yn[$i] != 'Y'){
					$sql = "replace into m06holiday (m06_ccode, m06_date, m06_name, m06_pay_yn) values ('".$code."', '".str_replace('-', '', $date[$i])."', '".$name[$i]."', '".$pay_yn[$i]."')";
				}else{
					$sql = "delete from m06holiday where m06_ccode = '".$code."' and m06_date = '".str_replace('-', '', $date[$i])."'";
				}

				if (!$conn->execute($sql)){
					$conn->rollback();
					$message = $myF->message('error','N');
				}
			}
		}else if ($data_execute == 'delete'){
			$date	= $_POST['date'];
			$del_yn = $_POST['delete_yn'];
			$count  = sizeof($date);

			for($i=0; $i<$count; $i++){
				if ($del_yn[$i] == 'Y'){
					$sql = "delete from m06holiday where m06_ccode = '".$code."' and m06_date = '".str_replace('-', '', $date[$i])."'";

					if (!$conn->execute($sql)){
						$conn->rollback();
						$message = $myF->message('error','N');

						break;
					}
				}
			}
		}else if ($data_execute == 'before_save'){
			$n_year = date('Y', mktime());
			$b_year = $n_year - 1;

			$sql = "replace into m06holiday (m06_ccode, m06_date, m06_name, m06_pay_yn)
					 select m06_ccode, concat('$n_year', right(m06_date, 6)), m06_name, m06_pay_yn
					   from m06holiday
					  where m06_ccode = '$code'
					    and m06_date  like '$b_year%'";
			if (!$conn->execute($sql)){
				$conn->rollback();
				$message = $myF->message('error','N');
			}
		}

		if ($message == ''){
			$conn->commit();
			$message = $myF->message('ok','N');
		}

		echo $myF->message($message);
	}
?>
<base target="_self">
<script type="text/javascript" src="../js/table.js"></script>
<script language='javascript'>
<!--
my_column = new Array(new Array('date[]', 'date', 'focus'), new Array('name[]', '', 'focus'), new Array('pay_yn[]', 'select', 'add', new Array(new Array('Y','유급'), new Array('N', '무급'))), new Array('delete','button'));

function search(){
	var f = document.f;

	f.submit();
}

function save(gubun){
	var f = document.f;

	f.data_execute.value = gubun;
	f.submit();
}

window.onload = function(){
	__init_form(document.f);
}
-->
</script>
<form name="f" method="post">
<div class="title">기관약정휴일등록</div>
<table class="my_table my_border">
	<colgroup>
		<col width="50px">
		<col span="2">
	</colgroup>
	<tbody>
		<tr>
			<th class="last">조회</th>
			<td class="left last">
				<select name="year" style="width:auto; margin:0;"><?
				for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
					<option value="<?=$i;?>" <? if($i == $year){?>selected<?} ?>><?=$i;?>년</option><?
				}?>
				</select>
				<span class="btn_pack m"><button type="button" onclick="search();">조회</button></span>
			</td>
			<td class="right last">
				<span class="btn_pack m icon"><span class="add"></span><button type="button" onclick="_t_add_row('my_row')">추가</button></span>
				<span class="btn_pack m icon"><span class="save"></span><button type="button" onclick="save('save');">저장</button></span>
				<span class="btn_pack m"><button type="button" onclick="save('before_save');">전년복사</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border" style="margin-top:-1px;">
	<colgroup>
		<col width="80px">
		<col width="250px">
		<col width="64px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">일자</th>
			<th class="head">명칭</th>
			<th class="head">유/무급</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>
<div style="height:205px; margin-left:0px; overflow-x:hidden; overflow-y:scroll;">
<table id="my_table" class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="250px">
		<col width="64px">
		<col>
	</colgroup>
	<tbody id="my_row">
	<?
		$sql = "select m06_date, m06_name, m06_pay_yn
				  from m06holiday
				 where m06_ccode = '$code'
				   and m06_date  like '$year%'";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count+1; $i++){
			if ($i < $row_count){
				$row = $conn->select_row($i);

				$date   = $myF->dateStyle($row['m06_date']);
				$name   = $row['m06_name'];
				$pay_yn = $row['m06_pay_yn'];
			}else{
				$date   = '';
				$name   = '';
				$pay_yn = 'Y';
			}?>
			<tr id="row_<?=$i+1;?>">
				<td class="center">
					<input name="date[]" type="text" value="<?=$date;?>" maxlength="8" class="date" onKeyDown="_t_check_next('date[]', <?=$i;?>, 'focus','my_row');" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);">
				</td>
				<td class="center">
					<input name="name[]" type="text" value="<?=$name;?>" style="width:100%;" onKeyDown="_t_check_next('name[]', <?=$i;?>, 'focus','my_row');" tag="명칭을 입력하여 주십시오.">
				</td>
				<td class="center">
					<select name="pay_yn[]" style="width:auto;" onKeyDown="_t_check_next('pay_yn[]', <?=$i;?>, 'add','my_row');">
						<option value="Y" <? if ($pay_yn == 'Y'){?>selected<?} ?>>유급</option>
						<option value="N" <? if ($pay_yn != 'Y'){?>selected<?} ?>>무급</option>
					</select>
				</td>
				<td class="left last">
					<span class="btn_pack m"><button type="button" onclick="<? if($date != ''){?>if(confirm('선택하신 휴일을 삭제하시겠습니까?')){_t_real_delete(document.f, <?=$i;?>);}<?}else{?>_t_delete_row('row_<?=$i+1;?>', <?=$i;?>);<?} ?>" onfocus="event.keyCode=9;">삭제</button></span>
				</td>
				<input name="delete_yn[]" type="hidden" value="N">
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
</table>
</div>
<input name="data_execute" type="hidden" value="">
</form>
<script language='javascript'>
	my_row_count = parseInt('<?=$i;?>', 10);
</script>
<?
	include_once('../inc/_footer.php');
?>