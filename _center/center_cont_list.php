<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_REQUEST['orgNo'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#ID_LIST').height(__GetHeight($('#ID_LIST')));
	});

	function lfSetCont(fromDt){
		var left = (screen.availWidth - (width = 1000)) / 2, top = (screen.availHeight - (height = 650)) / 2;
		var win = window.open('./center_connect_reg.php?orgNo=<?=$orgNo;?>&type=Contract&pos=0&posDt='+fromDt,'ORGCONT_WIN','left='+left+',top='+top+', width='+width+', height='+height+', scrollbars=no, status=no, resizable=no');
		win.focus();

		window.close();
	}
</script>
<div class="title title_border">계약이력</div><?
$colgroup = '
	<col width="40px">
	<col width="70px">
	<col width="100px">
	<col width="100px">
	<col width="140px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">사유코드</th>
			<th class="head">상세코드</th>
			<th class="head">계약(오픈)일자</th>
			<th class="head">계약(오픈)기간</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
</table>
<div id="ID_LIST" style="overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody><?
			include('./center_rs_set.php');

			$sql = 'SELECT	cont_dt, from_dt, to_dt, rs_cd, rs_dtl_cd
					FROM	cv_reg_info
					WHERE	org_no = \''.$orgNo.'\'
					ORDER	BY from_dt DESC';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);?>
				<tr>
					<td class="center"><?=$rowCnt - $i;?></td>
					<td class="center"><?=$setRs[$row['rs_cd']];?></td>
					<td class="center"><?=$setDtlRs[$row['rs_cd']][$row['rs_dtl_cd']];?></td>
					<td class="center"><?=$myF->dateStyle($row['cont_dt'],'.');?></td>
					<td class="center"><?=$row['rs_cd'] != '2' && $row['rs_cd'] != '4' ? $myF->dateStyle($row['from_dt'],'.').' ~ '.$myF->dateStyle($row['to_dt'],'.') : '';?></td>
					<td class="center">
						<div class="left">
							<span class="btn_pack small"><button onclick="lfSetCont('<?=$row['from_dt'];?>');">선택</button></span>
						</div>
					</td>
				</tr><?
			}

			$conn->row_free();?>
		</tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>