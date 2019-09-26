<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_const.php');
	include_once('../inc/_ed.php');

	/*
	 * 게시판 조회
	 */

	$orgNo	= $_SESSION['userCenterCode'];
	$type	= $_POST['type'];
	$cd		= $_POST['cd'];
	$page	= $_POST['page'];

	$itemCnt = 20;
	$pageCnt = 10;
	$listCnt = (intVal($page) - 1) * $itemCnt;

	//기관리스트
	$sql = 'SELECT	DISTINCT
					m00_mcode AS org_no
			,		m00_store_nm AS org_nm
			FROM	m00center
			INNER	JOIN	b02center
					ON		b02_center = m00_mcode';

	$center = $conn->_fetch_array($sql,'org_no');

	//공지 5건 우선 출력
	/*
	$sql = 'SELECT	a.org_no
				,		a.brd_cd
				,		a.brd_id
				,		a.reg_name
				,		a.reg_dt
				,		a.subject
				,		a.count
				,		a.notice_yn
				,		COUNT(b.file_id) AS file_cnt
				FROM	board_list AS a
				LEFT	JOIN	board_file AS b
						ON		b.org_no	= a.org_no
						AND		b.brd_type	= a.brd_type
						AND		b.dom_id	= a.dom_id
						AND		b.brd_cd	= a.brd_cd
						AND		b.brd_id	= a.brd_id
				WHERE	a.org_no	= \''.$orgNo.'\'
				AND		a.brd_type	= \''.$type.'\'
				AND		a.dom_id	= \''.$gDomainID.'\'
				AND		a.brd_cd	= \''.$cd.'\'
				AND		a.notice_yn	= \'Y\'
				AND		a.del_yn	= \'N\'
				GROUP	BY a.org_no, a.brd_id
				ORDER	BY reg_dt DESC
				LIMIT	5';
	 */
	if ($_SESSION['userLevel'] == 'A'){
		$sql = 'SELECT	org_no
				,		brd_cd
				,		brd_id
				,		reg_name
				,		reg_dt
				,		subject
				,		count
				,		notice_yn
				FROM	board_list
				WHERE	brd_type	= \''.$type.'\'
				AND		dom_id		= \''.$gDomainID.'\'
				AND		brd_cd		= \''.$cd.'\'
				AND		notice_yn	= \'Y\'
				AND		del_yn		= \'N\'';
	}else{
		$sql = 'SELECT	org_no
				,		brd_cd
				,		brd_id
				,		reg_name
				,		reg_dt
				,		subject
				,		count
				,		notice_yn
				FROM	board_list
				WHERE	org_no		= \''._COM_CD_.'\'
				AND		brd_type	= \''.$type.'\'
				AND		dom_id		= \''.$gDomainID.'\'
				AND		brd_cd		= \''.$cd.'\'
				AND		notice_yn	= \'Y\'
				AND		del_yn		= \'N\'
				UNLON	ALL
				SELECT	org_no
				,		brd_cd
				,		brd_id
				,		reg_name
				,		reg_dt
				,		subject
				,		count
				,		notice_yn
				FROM	board_list
				WHERE	org_no		= \''.$orgNo.'\'
				AND		brd_type	= \''.$type.'\'
				AND		dom_id		= \''.$gDomainID.'\'
				AND		brd_cd		= \''.$cd.'\'
				AND		notice_yn	= \'Y\'
				AND		del_yn		= \'N\'';
	}

	$sql = 'SELECT	a.org_no
			,		a.brd_cd
			,		a.brd_id
			,		a.reg_name
			,		a.reg_dt
			,		a.subject
			,		a.count
			,		a.notice_yn
			,		COUNT(b.file_id) AS file_cnt
			FROM	('.$sql.') AS a
			LEFT	JOIN	board_file AS b
					ON		b.org_no	= a.org_no
					AND		b.brd_type	= a.brd_type
					AND		b.dom_id	= a.dom_id
					AND		b.brd_cd	= a.brd_cd
					AND		b.brd_id	= a.brd_id
			GROUP	BY a.org_no, a.brd_id
			ORDER	BY reg_dt DESC
			LIMIT	5';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$notice[] = $conn->select_row($i);
	}

	$conn->row_free();

	/*
	$sql = 'SELECT	COUNT(*)
			FROM	board_list
			WHERE	org_no	= \''.$orgNo.'\'
			AND	brd_type	= \''.$type.'\'
			AND		dom_id		= \''.$gDomainID.'\'
			AND		brd_cd		= \''.$cd.'\'
			AND		notice_yn	= \'N\'
			AND		del_yn		= \'N\'';
	 */
	if ($_SESSION['userLevel'] == 'A'){
		$sql = 'SELECT	COUNT(*)
				FROM	board_list
				WHERE	brd_type	= \''.$type.'\'
				AND		dom_id		= \''.$gDomainID.'\'
				AND		brd_cd		= \''.$cd.'\'
				AND		notice_yn	= \'N\'
				AND		del_yn		= \'N\'';

		$maxCnt = $conn->get_data($sql);
	}else{
		$sql = 'SELECT	COUNT(*)
				FROM	board_list
				WHERE	org_no		= \''._COM_CD_.'\'
				AND		brd_type	= \''.$type.'\'
				AND		dom_id		= \''.$gDomainID.'\'
				AND		brd_cd		= \''.$cd.'\'
				AND		notice_yn	= \'N\'
				AND		del_yn		= \'N\'';

		$maxCnt1 = $conn->get_data($sql);

		$sql = 'SELECT	COUNT(*)
				FROM	board_list
				WHERE	org_no		= \''.$orgNo.'\'
				AND		brd_type	= \''.$type.'\'
				AND		dom_id		= \''.$gDomainID.'\'
				AND		brd_cd		= \''.$cd.'\'
				AND		notice_yn	= \'N\'
				AND		del_yn		= \'N\'';

		$maxCnt2 = $conn->get_data($sql);

		$maxCnt = $maxCnt1 + $maxCnt2;
	}

	/*
	$sql = 'SELECT	a.org_no
			,		a.brd_cd
			,		a.brd_id
			,		a.reg_name
			,		a.reg_dt
			,		a.subject
			,		a.count
			,		a.notice_yn
			,		COUNT(b.file_id) AS file_cnt
			FROM	board_list AS a
			LEFT	JOIN	board_file AS b
					ON		b.org_no	= a.org_no
					AND		b.brd_type	= a.brd_type
					AND		b.dom_id	= a.dom_id
					AND		b.brd_cd	= a.brd_cd
					AND		b.brd_id	= a.brd_id
			WHERE	a.org_no	= \''.$orgNo.'\'
			AND		a.brd_type	= \''.$type.'\'
			AND		a.dom_id	= \''.$gDomainID.'\'
			AND		a.brd_cd	= \''.$cd.'\'
			AND		a.del_yn	= \'N\'
			GROUP	BY a.org_no, a.brd_id
			ORDER	BY reg_dt DESC
			LIMIT	'.$listCnt.','.$itemCnt;
	*/

	if ($_SESSION['userLevel'] == 'A'){
		$sql = 'SELECT	org_no
				,		brd_type
				,		dom_id
				,		brd_cd
				,		brd_id
				,		reg_name
				,		reg_dt
				,		subject
				,		count
				,		notice_yn
				FROM	board_list AS a
				WHERE	brd_type= \''.$type.'\'
				AND		dom_id	= \''.$gDomainID.'\'
				AND		brd_cd	= \''.$cd.'\'
				AND		del_yn	= \'N\'';
	}else{
		$sql = 'SELECT	org_no
				,		brd_type
				,		dom_id
				,		brd_cd
				,		brd_id
				,		reg_name
				,		reg_dt
				,		subject
				,		count
				,		notice_yn
				FROM	board_list AS a
				WHERE	org_no	= \''._COM_CD_.'\'
				AND		brd_type= \''.$type.'\'
				AND		dom_id	= \''.$gDomainID.'\'
				AND		brd_cd	= \''.$cd.'\'
				AND		del_yn	= \'N\'
				UNION	ALL
				SELECT	org_no
				,		brd_type
				,		dom_id
				,		brd_cd
				,		brd_id
				,		reg_name
				,		reg_dt
				,		subject
				,		count
				,		notice_yn
				FROM	board_list AS a
				WHERE	org_no	= \''.$orgNo.'\'
				AND		brd_type= \''.$type.'\'
				AND		dom_id	= \''.$gDomainID.'\'
				AND		brd_cd	= \''.$cd.'\'
				AND		del_yn	= \'N\'';
	}

	$sql = 'SELECT	a.org_no
			,		a.brd_cd
			,		a.brd_id
			,		a.reg_name
			,		a.reg_dt
			,		a.subject
			,		a.count
			,		a.notice_yn
			,		COUNT(b.file_id) AS file_cnt
			FROM	('.$sql.') AS a
			LEFT	JOIN	board_file AS b
					ON		b.org_no	= a.org_no
					AND		b.brd_type	= a.brd_type
					AND		b.dom_id	= a.dom_id
					AND		b.brd_cd	= a.brd_cd
					AND		b.brd_id	= a.brd_id
			GROUP	BY a.org_no, a.brd_id
			ORDER	BY reg_dt DESC
			LIMIT	'.$listCnt.','.$itemCnt;

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$data[] = $conn->select_row($i);
	}

	$conn->row_free();

	if (is_array($notice)){
		//공지
		foreach($notice as $row){
			if ($_SESSION['userLevel'] == 'A'){
				if ($center[$row['org_no']]['org_nm']){
					$writer = $center[$row['org_no']]['org_nm'];
				}else{
					$writer = $row['reg_name'];
				}
			}else{
				$writer = $row['reg_name'];
			}

			drawHtml($row, '공지', $writer, true);
		}
	}

	if (is_array($data)){
		//리스트
		$no = $listCnt + 1;
		foreach($data as $row){
			if ($_SESSION['userLevel'] == 'A'){
				if ($center[$row['org_no']]['org_nm']){
					$writer = $center[$row['org_no']]['org_nm'];
				}else{
					$writer = $row['reg_name'];
				}
			}else{
				$writer = $row['reg_name'];
			}

			$no = drawHtml($row, $no, $writer) + 1;
		}?>
		<script type="text/javascript">
			_lfSetPageList('<?=$maxCnt;?>', '<?=$page;?>', '<?=$pageCnt;?>', '<?=$itemCnt;?>');
		</script><?
	}else{?>
		<tr>
			<td class="center" colspan="6">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	Unset($notice);
	Unset($data);

	function drawHtml($row, $no, $writer, $notice = false){
		if ($notice){
			$style = 'background-color:#EAEAEA;';
		}else{
			$style = '';
		}

		if ($row['file_cnt'] > 0){
			$attach = $row['file_cnt'].'건';
		}else{
			$attach = '';
		}?>
		<tr style="<?=$style;?>">
			<td class="center"><?=$no;?></td>
			<td class="center"><div class="left" onclick="lfReg('<?=$row['org_no'];?>','<?=$row['brd_id'];?>','VIEW');"><a href="#" onclick="return false;"><?=StripSlashes($row['subject']);?></a></div></td>
			<td class="center"><?=str_replace('-','.',$row['reg_dt']);?></td>
			<td class="center"><div class="left"><?=$writer;?></div></td>
			<td class="center"><div class="right"><?=$attach;?></div></td>
			<td class="center">
				<div class="left"><?
					if ($_SESSION['userLevel'] == 'A'){?>
						<span class="btn_pack small"><button onclick="lfSetNotice(this,'<?=$row['org_no'];?>','<?=$row['brd_id'];?>');" style="color:<?=$row['notice_yn'] == 'Y' ? 'RED' : 'BLACK';?>;"><?=($row['notice_yn'] == 'Y' ? '취소' : '공지');?></button></span><?
					}

					if ($_SESSION['userLevel'] == 'A' || ($_SESSION['userCenterCode'] == $row['org_no'] && $_SESSION['userLevel'] == 'C')){?>
						<span class="btn_pack small"><button onclick="lfReg('<?=$row['org_no'];?>','<?=$row['brd_id'];?>');" style="color:BLUE;">수정</button></span>
						<span class="btn_pack small"><button onclick="lfRemove(this,'<?=$row['org_no'];?>','<?=$row['brd_id'];?>');" style="color:RED;">삭제</button></span><?
					}?>
				</div>
			</td>
		</tr><?

		return $no;
	}

	Unset($center);

	include_once('../inc/_db_close.php');
?>