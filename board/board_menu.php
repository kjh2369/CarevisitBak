<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*
	 * 게시판 조회
	 */

	$type = $_POST['type'];
	$parent = $_POST['parent'];

	$sql = 'SELECT	cd
			,		name
			FROM	board_category
			WHERE	brd_type= \''.$type.'\'
			AND		dom_id	= \''.$gDomainID.'\'
			AND		parent	= \''.$parent.'\'
			AND		use_yn	= \'Y\'
			AND		del_yn	= \'N\'
			ORDER	BY seq, name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$style .= 'line-height:23px;';

		if ($i > 0){
			if (!$parent){
				$style .= 'border-top:1px solid #CCCCCC;';
			}else{
				$style .= 'border-top:1px dashed #CCCCCC;';
			}
		}else{
			if ($parent){
				$style .= 'border-top:1px dashed #CCCCCC;';
			}
		}

		if ($parent){
			$style .= 'margin-left:10px;';
		}?>
		<div id="ID_BRD" cd="<?=$row['cd'];?>" style="<?=$style;?>"><?
			if ($parent){?>
				<div id="ID_BRD_ROW" class="left" selYn="N" onclick="lfLoadBoard(this);"><a href="#" onclick="return false;"><?=$row['name'];?></a></div><?
			}else{?>
				<div class="left" style="cursor:default;"><?=$row['name'];?></div><?
			}

			if (!$parent){?>
				<div id="ID_BRD_SUB"></div><?
			}?>
		</div><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>