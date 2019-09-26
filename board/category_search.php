<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*
	 * 게시판 카테고리 조회
	 */

	$type = $_POST['type'];
	$parent = $_POST['parent'];

	$sql = 'SELECT	cd
			,		name
			,		parent
			,		use_yn
			,		seq
			FROM	board_category
			WHERE	brd_type= \''.$type.'\'
			AND		dom_id	= \''.$gDomainID.'\'
			AND		parent	= \''.$parent.'\'
			AND		del_yn	= \'N\'
			ORDER	BY seq';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['parent'] > 0){?>
			<tr>
			<td class="center"><div class="left"><?=$row['name'];?></div></td><?
		}else{?>
			<tr cd="<?=$row['cd'];?>">
			<td class="center"><?=$no;?></td>
			<td class="center"><div class="left"><?=$row['name'];?></div></td>
			<td class="center"><span class="btn_pack small"><button onclick="lfMstAdd(this,'0','<?=$row['cd'];?>','1')">게시판생성</button></span></td><?
			$no ++;
		}?>

		<td class="center last">
			<div style="text-align:left;">
				<select id="cboUseYn" style="width:auto;" onchange="lfSet('use','<?=$row['cd'];?>',$(this).val());">
					<option value="Y" <?=$row['use_yn'] == 'Y' ? 'selected' : '';?>>사용</option>
					<option value="N" <?=$row['use_yn'] == 'N' ? 'selected' : '';?>>미사용</option>
				</select>
				<label>순번<input id="txtSeq" type="text" value="<?=$row['seq'];?>" class="no_string" style="width:50px;" onchange="lfSet('seq','<?=$row['cd'];?>',$(this).val());"></label>
			</div>
		</td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>