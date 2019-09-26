<?
	include_once('../inc/_http_uri.php');

	$imgpath = $gHostImgPath.'/img_emplem_'.$_SESSION['userArea'].'.jpg';
	if (!is_file($imgpath)) $imgpath = $gHostImgPath.'/img_emplem.jpg';

	$cipath = $gHostImgPath.'/ciL_'.$_SESSION['userArea'].'.png';
	if (!is_file($cipath)) $cipath = $gHostImgPath.'/ciL.png';

	//팝업
	$sql = 'SELECT	DISTINCT
					a.notice_id
			,		b.subject
			,		b.content
			FROM	han_notice_data AS a
			INNER	JOIN han_notice AS b
					ON b.notice_id = a.notice_id
					AND b.del_flag = \'N\'
					AND b.pop9_yn = \'Y\'
			WHERE	a.area_cd = \''.$_SESSION['userArea'].'\'
			AND		CASE WHEN a.group_cd = \'A\' THEN \''.$_SESSION['userGroup'].'\' ELSE a.group_cd END = \''.$_SESSION['userGroup'].'\'
			ORDER	BY a.notice_id DESC
			LIMIT	5';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$noticeID = $row['notice_id'];
		$subject = StripSlashes($row['subject']);
		$content = StripSlashes($row['content']);

		$winX = 100 * $i;
		$winY = 50 * $i;?>

		<script type="text/javascript">
			if (__getCookie("HAN_POPUP_<?=$noticeID;?>") != "done"){
				var left = window.screenLeft + <?=$winX;?>;
				var top = window.screenTop + <?=$winY;?>;
				window.open("../popup/popup_han.php?id=<?=$noticeID;?>","HAN_POPUP_<?=$noticeID;?>","width=400,height=400,left="+left+",top="+top+",scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no");
			}
		</script><?
	}

	$conn->row_free();
?>
<script type="text/javascript">
	function crDataPopup(id){
		var left = 300;
		var top = (screen.availHeight - height) / 2;

		window.open("../goodeos/han_data_text.php?id="+id,"HAN_POPUP_<?=$noticeID;?>","width=400,height=400,left="+left+",top="+top+",scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no");
	}
</script>
<div style="clear:both; width:1440px; height:652px; background:url(<?=$gHostImgPath.'/bg_body.jpg';?>) no-repeat;">
	<!--div style="float:left; width:510px;">
		<div style="clear:both; position:relative; width:390px; height:73px; left:80px; top:185px; background:url(<?=$cipath;?>) no-repeat;"></div>
	</div-->
	<!--div style="float:left; width:510px;">
		<div style="clear:both; position:relative; width:407px; height:188px; right:-50px; top:50px; background:url(<?=$gHostImgPath.'/bg_notice.png';?>) no-repeat;">
			<div style="float:left; position:relative; width:91px; height:16px; left:30px; top:21px; background:url(<?=$gHostImgPath.'/title_notice.png';?>) no-repeat;"></div>
			<div style="float:right; position:relative; width:42px; height:10px; right:30px; top:23px; background:url(<?=$gHostImgPath.'/btn_more.png';?>) no-repeat;"></div>
			<div style="clear:both; position:relative; width:250px; left:130px; top:50px;"><?
				$column = '	notice_id
						,	subject';

				$sql = 'SELECT	'.$column.'
						,		\'1\' AS admin_gbn
						FROM	han_notice
						WHERE	area_cd	= \'\'
						AND		group_cd= \'\'
						AND		del_flag= \'N\'
						UNION	ALL
						SELECT	'.$column.'
						,		\'2\' AS admin_gbn
						FROM	han_notice
						WHERE	area_cd	= \''.$_SESSION['userArea'].'\'
						AND		group_cd= \'\'
						AND		del_flag= \'N\'
						UNION	ALL
						SELECT	'.$column.'
						,		\'3\' AS admin_gbn
						FROM	han_notice
						WHERE	area_cd	= \''.$_SESSION['userArea'].'\'
						AND		group_cd= \''.$_SESSION['userGroup'].'\'
						AND		del_flag= \'N\'
						ORDER	BY notice_id DESC
						LIMIT	4';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				$div1  = '<div class=\'tmp_arrow nowrap\' style=\'width:250px; text-align:left; padding-left:20px;\'>';
				$div2  = '</div>';
				$error = '데이타가 없습니다.';

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);

					$rst .= $div1.'<a href="#" onclick="">'.$row['subject'].'</a>'.$div2;
				}

				$conn->row_free();

				echo $rst;
				Unset($rst);?>
			</div>
		</div>
		<div style="clear:both; position:relative; width:407px; height:188px; right:-50px; top:60px; background:url(<?=$gHostImgPath.'/bg_data.png';?>) no-repeat;">
			<div style="float:left; position:relative; width:91px; height:16px; left:30px; top:21px; background:url(<?=$gHostImgPath.'/title_data.png';?>) no-repeat;"></div>
			<div style="float:right; position:relative; width:42px; height:10px; right:30px; top:23px; background:url(<?=$gHostImgPath.'/btn_more.png';?>) no-repeat;"></div>
			<div style="clear:both; position:relative; width:250px; left:130px; top:50px;"><?
				$column = '	dataroom_id
						,	subject';

				$sql = 'SELECT	'.$column.'
						,		\'1\' AS admin_gbn
						FROM	han_dataroom
						WHERE	area_cd	= \'\'
						AND		group_cd= \'\'
						AND		del_flag= \'N\'
						UNION	ALL
						SELECT	'.$column.'
						,		\'2\' AS admin_gbn
						FROM	han_dataroom
						WHERE	area_cd	= \''.$_SESSION['userArea'].'\'
						AND		group_cd= \'\'
						AND		del_flag= \'N\'
						UNION	ALL
						SELECT	'.$column.'
						,		\'3\' AS admin_gbn
						FROM	han_dataroom
						WHERE	area_cd	= \''.$_SESSION['userArea'].'\'
						AND		group_cd= \''.$_SESSION['userGroup'].'\'
						AND		del_flag= \'N\'
						ORDER	BY dataroom_id DESC
						LIMIT	4';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				$div1  = '<div class=\'tmp_arrow nowrap\' style=\'width:250px; text-align:left; padding-left:20px;\'>';
				$div2  = '</div>';
				$error = '데이타가 없습니다.';

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);


					$rst .= $div1.'<a href="#" onclick="crDataPopup(\''.$row['dataroom_id'].'\');">'.$row['subject'].'</a>'.$div2;
				}

				$conn->row_free();

				echo $rst;
				Unset($rst);?>
			</div>
		</div>
	</div-->
</div>

