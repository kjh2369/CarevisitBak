<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$jumin	= $ed->de($_POST['jumin']);
	$sugaCd	= $_POST['sugaCd'];
	$target	= $ed->de($_POST['target']);
	$date	= $_POST['date'];

	$sql = 'SELECT	a.seq
			,		a.name
			,		c.contents
			,		c.pic_path
			,		c.pic_name
			FROM	care_work_log_item AS a
			LEFT	JOIN	care_work_log AS b
					ON		b.org_no	= a.org_no
					AND		b.org_type	= a.org_type
					AND		b.suga_cd	= a.suga_cd
					AND		b.mem_cd	= \''.$jumin.'\'
					AND		b.date		= \''.$date.'\'
					AND		b.jumin		= \''.$target.'\'
					AND		b.del_flag	= \'N\'
			LEFT	JOIN	care_work_log_sub AS c
					ON		c.org_no	= a.org_no
					AND		c.org_type	= a.org_type
					AND		c.sub_key	= b.sub_key
					AND		c.seq		= a.seq
			WHERE	a.org_no	 = \''.$orgNo.'\'
			AND		a.org_type	 = \''.$SR.'\'
			AND		a.suga_cd	 = \''.$sugaCd.'\'
			AND		a.from_dt	<= \''.$date.'\'
			AND		a.to_dt		>= \''.$date.'\'
			AND     a.del_flag   = \'N\'
			ORDER	BY a.order_no';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if (!$IsExcel){?>
			<tr id="ID_<?=$i;?>">
				<th class="center" rowspan="2"><div class="left"><?=$row['name'];?></div></th>
				<td class="bottom"><textarea style="width:100%; height:35px;" seq="<?=$row['seq'];?>"><?=StripSlashes($row['contents']);?></textarea></td>
			</tr>
			<tr id="ID_<?=$i;?>">
				<td class="center">
					<div style="float:left; width:auto; margin-left:5px;">사진등록</div>
					<div style="float:left; width:auto; margin-left:5px; margin-top:1px; background:url(../image/find_file.gif) no-repeat left 50%;">
						<input type="file" name="picImg_<?=$row['seq'];?>" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin-left:-7px;" onchange="lfCheckImg(this);">
					</div>
					<div style="float:left; width:auto; margin-left:5px; text-align:left;" id="lblPicImg"><?=$row['pic_name'];?></div>
				</td>
			</tr><?
		}else{?>
			<tr>
				<td style="text-align:left; text-valign:top; border:0.5pt solid BLACK; background-color:#EAEAEA;" rowspan="2" colspan="2"><?=$row['name'];?></td>
				<td style="text-align:left; text-valign:top; border:0.5pt solid BLACK; border-bottom:none;"><?=nl2br(StripSlashes($row['contents']));?></td>
			</tr>
			<tr>
				<td style="text-align:left; text-valign:top; border:0.5pt solid BLACK; border-top:none;"><img src="http://<?=$gHostNm?>.<?=$gDomain;?>/care/<?=SubStr($row['pic_path'],1);?>"></td>
			</tr><?
		}
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>