<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$gbn	= '01'; //서비스 묶음별 카테고리
	$show	= $_POST['show'];

	$para['show'] = $show;

	/*
	$sql = 'SELECT	code
			,		name
			,		parent
			FROM	mst_category
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		gbn		= \''.$gbn.'\'
			AND		del_flag= \'N\'
			AND		parent IS NULL
			ORDER	BY seq, name';

	$category = $conn->_fetch_array($sql);

	for($i=0; $i<SizeOf($category); $i++){
		$row = $category[$i];
		$data .= '<div style="margin-left:5px;">'.$row['name'].'</div>';
		$data .= lfGetCategory($conn, $orgNo, $SR, $gbn, $row['code']);
	}

	$conn->row_free();
	*/

	if ($para['show'] == 'LIST'){
		$data = '<tr code="ALL"><td style="padding:0 5px 0 5px;">전체</td><td></td></tr>';
	}else{
		$data = '';
	}

	$data .= lfGetCategory($para, $conn, $orgNo, $SR, $gbn);
	echo $data;


	function lfGetCategory($para, $conn, $orgNo, $SR, $gbn, $parent = '0', $pos = 0){
		$sql = 'SELECT	code
				,		name
				,		parent
				,		seq
				FROM	mst_category
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		gbn		= \''.$gbn.'\'
				AND		IFNULL(parent,\'0\') = \''.$parent.'\'
				AND		del_flag= \'N\'
				ORDER	BY seq, name';

		$category = $conn->_fetch_array($sql);

		for($i=0; $i<SizeOf($category); $i++){
			$row = $category[$i];
			//$data .= '<div style="margin-left:'.($pos * 20).'px;">'.$row['name'];
			//$data .= ' <span class="btn_pack small"><button onclick="">추가</button></span>';
			//$data .= ' <span class="btn_pack small"><button onclick="">수정</button></span>';
			//$data .= ' <span class="btn_pack small"><button onclick="">삭제</button></span>';
			//$data .= '</div>';

			if ($para['show'] == 'LIST'){
				$data .= '<tr code="'.$row['code'].'">';
			}else{
				$data .= '<tr>';
			}

			$data .= '<td class="" style="padding:0 5px 0 5px;"><div class="nowrap" style="margin-left:'.($pos * 20).'px;">'.$row['name'].'</div></td>';

			if ($para['show'] == 'LIST'){
				$sql = 'SELECT	group_nm, COUNT(seq) - 1 AS cnt
						FROM	care_svc_group
						WHERE	org_no	= \''.$orgNo.'\'
						AND		org_type= \''.$SR.'\'
						AND		del_flag= \'N\'
						AND		category= \''.$row['code'].'\'';

				$R = $conn->get_array($sql);
				$str = $R['group_nm'];

				if ($R['cnt'] > 0){
					$str = '"'.$str.'" 외 '.$R['cnt'].'건이 등록되어 있습니다.';
				}

				Unset($R);

				$data .= '<td><div style="padding-left:5px;">'.$str.'</div></td>';
			}else{
				//등록 묶음
				$sql = 'SELECT	COUNT(*)
						FROM	care_svc_group
						WHERE	org_no	= \''.$orgNo.'\'
						AND		org_type= \''.$SR.'\'
						AND		del_flag= \'N\'
						AND		category= \''.$row['code'].'\'';

				$cnt = $conn->get_data($sql);

				$data .= '<td class="center">'.$row['seq'].'</td>';
				$data .= '<td class="left last">';
				$data .= ' <span class="btn_pack small"><button onclick="lfCategoryReg(\''.$row['code'].'\');">추가</button></span>';
				$data .= ' <span class="btn_pack small"><button onclick="lfCategoryReg(\'\',\''.$row['code'].'\',\''.$row['name'].'\',\''.$row['seq'].'\')">수정</button></span>';
				$data .= ' <span class="btn_pack small"><button onclick="lfCategoryRemove(\''.$row['code'].'\');">삭제</button></span>';
				$data .= ' <span class="btn_pack small"><button onclick="lfCategorySet(this, \''.$row['code'].'\');">묶음('.$cnt.'건)</button></span>';
				$data .= '</td>';
			}
			$data .= '</tr>';
			$data .= lfGetCategory($para, $conn, $orgNo, $SR, $gbn, $row['code'], $pos + 1);
		}

		return $data;
	}

	include_once('../inc/_db_close.php');
?>