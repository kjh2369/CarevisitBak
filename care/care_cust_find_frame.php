<?
	include_once("../inc/_header.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$sr   = $_GET['sr'];
	$gbn  = $_GET['gbn'];
	$cust = $_GET['cust'];

	if ($sr == 'S'){
	}else if ($sr == 'R'){
	}else{
		exit;
	}
?>
<script type="text/javascript">
	function lfSetCust(row){
		var obj = {};

		obj['gbn'] = $(row).attr('gbn');
		obj['cd'] = $(row).attr('cd');
		obj['nm'] = $(row).attr('nm');
		obj['bizno'] = $(row).attr('bizno');
		obj['manager'] = $(row).attr('manager');
		obj['stat'] = $(row).attr('stat');
		obj['item'] = $(row).attr('item');
		obj['phone'] = $(row).attr('phone');
		obj['fax'] = $(row).attr('fax');
		obj['addr'] = $(row).attr('addr');
		obj['pernm'] = $(row).attr('pernm');
		obj['pertel'] = $(row).attr('pertel');

		parent.lfSetCust(obj);
	}
</script>
<form id="f" name="f" method="post">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="120px">
		<col width="50px">
		<col width="70px">
		<col width="90px" span="2">
		<col width="150px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody><?
		$sql = 'SELECT	*
				FROM	care_cust
				WHERE	org_no	 = \''.$code.'\'
				AND		del_flag = \'N\'';

		if ($sr == 'S'){
			$sql .= '
				AND		support_yn = \'Y\'';
		}else if ($sr == 'R'){
			$sql .= '
				AND		resource_yn = \'Y\'';
		}

		if ($gbn == 'ALL'){
		}else{
			$sql .= '
				AND		cust_gbn = \''.$gbn.'\'';
		}

		if ($cust){
			$sql .= '
				AND		cust_nm LIKE \'%'.$cust.'%\'';
		}

		$sql .= '
				ORDER	BY cust_nm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		if ($rowCnt > 0){
			$no = 1;

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if ($row['cust_gbn'] == '1'){
					$row['cust_gbn'] = '공공';
				}else if ($row['cust_gbn'] == '2'){
					$row['cust_gbn'] = '기업';
				}else if ($row['cust_gbn'] == '3'){
					$row['cust_gbn'] = '단체';
				}else if ($row['cust_gbn'] == '4'){
					$row['cust_gbn'] = '개인';
				}?>
				<tr gbn="<?=$row['cust_gbn'];?>"
					cd="<?=$row['cust_cd'];?>"
					nm="<?=$row['cust_nm'];?>"
					bizno="<?=$row['biz_no'];?>"
					manager="<?=$row['manager'];?>"
					stat="<?=$row['status'];?>"
					item="<?=$row['item'];?>"
					phone="<?=$row['phone'];?>"
					fax="<?=$row['fax'];?>"
					addr="<?=$row['addr'];?>"
					pernm="<?=$row['per_nm'];?>"
					pertel="<?=$row['per_phone'];?>"
					>
					<td class="center"><?=$no;?></td>
					<td class="center"><div class="left"><?=$row['cust_nm'];?></div></td>
					<td class="center"><?=$row['cust_gbn'];?></td>
					<td class="center"><?=$row['manager'];?></td>
					<td class="center"><?=$myF->phoneStyle($row['phone'],'.');?></td>
					<td class="center"><?=$myF->phoneStyle($row['fax'],'.');?></td>
					<td class="center"><div class="left"><?=$row['addr'];?></div></td>
					<td class="center"><?=$row['per_nm'];?></td>
					<td class="center last"><div class="left" style="width:auto;" onclick="lfSetCust($(this).parent().parent());"><a href="#" onclick="return false;">선택</a></div></td>
				</tr><?

				$no ++;
			}
		}else{?>
			<tr><td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td></tr><?
		}

		$conn->row_free();?>
	</tbody>
</table>
</form>
<?
	include_once("../inc/_footer.php");
?>