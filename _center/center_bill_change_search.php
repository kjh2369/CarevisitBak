<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $ed->de($_POST['orgNo']);
	$nowGbn = $_POST['nowGbn'];
	$today = Date('Ymd');

	$sql = 'SELECT	from_dt, to_dt, cms_no, bill_gbn, cms_com, bill_kind
			,		CASE WHEN bill_gbn = \'1\' THEN \'CMS\' ELSE \'무통장\' END AS bill_str
			,		CASE WHEN bill_kind = \'1\' THEN \'선불\' ELSE \'후불\' END AS bill_kind_str
			,		CASE WHEN cms_com = \'1\' THEN \'굿이오스\'
						 WHEN cms_com = \'2\' THEN \'지케어\'
						 WHEN cms_com = \'3\' THEN \'케어비지트\' ELSE cms_com END AS com_str
			FROM	cv_bill_info
			WHERE	org_no	= \''.$orgNo.'\'
			AND		del_flag= \'N\'';

	/*if ($nowGbn == 'Y'){
		$sql .= '
			AND		from_dt <= DATE_FORMAT(NOW(), \'%Y%m%d\')
			AND		to_dt	>= DATE_FORMAT(NOW(), \'%Y%m%d\')';
	}*/

	$sql .= '
			ORDER	BY from_dt DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($today >= $row['from_dt'] && $today <= $row['to_dt']){
			$bgclr = '#E4F7BA';
			$bold = 'bold';
		}else{
			$bgclr = '';
			$bold = 'normal';
		}
		?>
		<tr style="background-color:<?=$bgclr;?>; font-weight:<?=$bold;?>;" fromDt="<?=$row['from_dt'];?>" toDt="<?=$row['to_dt'];?>" billGbn="<?=$row['bill_gbn'];?>" cmsno="<?=$row['cms_no'];?>" cmsCom="<?=$row['cms_com'];?>" bill_kind="<?=$row['bill_kind'];?>">
			<td class="center"><?=$myF->dateStyle($row['from_dt'], '.');?></td>
			<td class="center"><?=$myF->dateStyle($row['to_dt'], '.');?></td>
			<td class="center"><?=$row['bill_str'];?></td>
			<td class="center"><?=$row['bill_kind_str'];?></td>
			<td class="center"><div class="left"><?=$row['cms_no'];?></div></td>
			<td class="center"><div class="left"><?=$row['com_str'];?></div></td>
			<td class="left last"><?
				if ($nowGbn != 'Y'){?>
					<span class="btn_pack small"><button onclick="lfModify($(this).parent().parent().parent());" style="color:blue;">수정</button></span>
					<span class="btn_pack small"><button onclick="lfDelete($(this).parent().parent().parent());" style="color:red;">삭제</button></span><?
				}?>
			</td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>