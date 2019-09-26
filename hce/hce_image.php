<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgType = '40';
	$orgNo	= $_SESSION['userCenterCode'];
	$userCd = $_SESSION['userCode'];
	$type	= $_POST['type'];
	$sr		= $_POST['sr'];
	$IPIN	= $hce->IPIN;
	$rcpt	= $hce->rcpt;
	$rstImg	= '../hce/map/family_'.$IPIN.'.jpg';

	//가족코드
	$sql = 'SELECT	code,name
			FROM	hce_gbn
			WHERE	type	=\'HR\'
			AND		use_yn	= \'Y\'';

	$rel = $conn->_fetch_array($sql,'code');

	//주민번호
	$sql = 'SELECT	m03_name AS name
			,		m03_jumin AS jumin
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$IPIN.'\'';

	$row = $conn->get_array($sql);

	//성별
	if (SubStr($row['jumin'],6,1) % 2 == 1){
		$gender = 'M';
	}else{
		$gender = 'W';
	}

	$me = Array(
		'id'=>'2_0'
	,	'name'=>$row['name']
	,	'rel'=>'95'
	,	'age'=>$myF->issToAge($row['jumin'])
	,	'gender'=>$gender
	);

	Unset($row);

	//가족
	$sql = 'SELECT	family_rel AS rel
			,		family_age AS age
			,		family_nm AS nm
			FROM	hce_family
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$orgType.'\'
			AND		IPIN	= \''.$IPIN.'\'
			AND		rcpt_seq= \''.$rcpt.'\'
			ORDER	BY rel';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['rel'] == '01' || $row['rel'] == '02'){
			$idx = 1;
			$j = SizeOf($family[$idx]);

			if ($row['rel'] == '01'){
				$gender = 'M';
			}else if ($row['rel'] == '02'){
				$gender = 'W';
			}
		}else if ($row['rel'] == '09' || $row['rel'] == '10' || $row['rel'] == '96'){
			$idx = 2;

			if (!$family[$idx]){
				$family[$idx][0] = $me;
				$family[$idx][1] = null;
			}

			if ($row['rel'] == '96'){
				$j = 1;
			}else{
				$j = SizeOf($family[$idx]);
			}

			if ($row['rel'] == '09'){
				$gender = 'M';
			}else if ($row['rel'] == '10' || $row['rel'] == '96'){
				$gender = 'W';
			}
		}else if ($row['rel'] == '05' || $row['rel'] == '06'){
			$idx = 3;
			$j = SizeOf($family[$idx]);

			if ($row['rel'] == '05'){
				$gender = 'M';
			}else if ($row['rel'] == '06'){
				$gender = 'W';
			}
		}else if ($row['rel'] == '07' || $row['rel'] == '08'){
			$idx = 4;
			$j = SizeOf($family[$idx]);

			if ($row['rel'] == '07'){
				$gender = 'M';
			}else if ($row['rel'] == '08'){
				$gender = 'W';
			}
		}else{
			continue;
		}

		$family[$idx][$j] = Array(
			'id'=>$idx.'_'.$j
		,	'name'=>$row['nm']
		,	'rel'=>$row['rel']
		,	'age'=>$row['age']
		,	'gender'=>$gender
		);
	}

	if (!$family[2]){
		$family[2][0] = $me;
	}

	$conn->row_free();

	//배경 크기
	$width	= 400;
	$height = 300;

	//폰트설정
	$font = '../font/MALGUN.TTF';

	//폰트 크기
	$fontSize = 11;

	//남자 이미지
	$manPath = '../hce/map/bg_1.jpg';
	$manImg = GetImageSize($manPath);
	$man = ImageCreateFromJpeg($manPath);

	//여자 이미지
	$womanPath = '../hce/map/bg_2.jpg';
	$womanImg = GetImageSize($womanPath);
	$woman = ImageCreateFromJpeg($womanPath);

	//앵글
	$angle = 0;

	//이미지 리소스 생성
	$image = ImageCreateTrueColor($width, $height);

	// 색 지정
	$white = ImageColorAllocate($image,255,255,255);
	$balck = ImageColorAllocate($image, 0, 0, 0);

	//배경지정
	ImageFillEdrectAngle($image, 0, 0, $width -1, $height -1, $white);

	// 블렌딩 모드 FALSE 설정
	//imagealphablending($image, 0);

	// PNG 이미지의 투명컬러 알파체널 정보저장
	//imagesavealpha($image, 1);

	// 이미지 복사 원본,소스,원본x,원본y,소스x,소스y,원본가로,원본세로,소스가로,소스세로)
	//ImageCopyReSampled($new_img, $original_img, 0, 0, 0, 0, 351, 371,  351, 371);

	// 선그리기
	//imageline($img, $start_x, $start_y, $IMG_RAD, $IMG_RAD, $bd_color);

	$left = 0;
	$top  = 0;
	$meLeft = 0;
	$meFirst = false;

	for($i=1; $i<=10; $i++){
		$meFirst = false;

		if (Is_Array($family[$i])){
			foreach($family[$i] as $idx => $row){
				if (!$meFirst){
					$left = $meLeft;
					$meFirst = true;
				}

				if ($row){
					//그리기
					if ($row['gender'] == 'M'){
						ImageCopyReSampled($image,$man,$left,$top,0,0,$manImg[0],$manImg[1],$manImg[0],$manImg[1]);

						$w = $manImg[0];

						if ($t < $manImg[1]) $t = $manImg[1];
					}else{
						ImageCopyReSampled($image,$woman,$left,$top,0,0,$womanImg[0],$womanImg[1],$womanImg[0],$womanImg[1]);

						$w = $womanImg[0];

						if ($t < $womanImg[1]) $t = $womanImg[1];
					}

					if ($row['rel'] == '01'){
						$meLeft = $left;
					}else if ($row['rel'] == '02'){
						$meLeft = $left - $w / 2;
					}else if ($row['rel'] == '96'){
						$meLeft = $left - $w / 2;
					}

					//관계
					$str = $rel[$row['rel']]['name'];
					ImageTTFText($image,$fontSize,$angle,$left+($w-$myF->len($str)*15)/2,$top+25,$balck,$font,$str);

					//나이
					$str = $row['age'];
					ImageTTFText($image,$fontSize,$angle,$left+($w-$myF->len($str)*8)/2,$top+45,$balck,$font,$str);

					$left += $w;
				}
			}

			$top += $t;
		}
	}

	/*
	// 색 지정
	$white = ImageColorAllocate($image,255,255,255);
	$balck = ImageColorAllocate($image, 0, 0, 0);

	// 투명색 지정
	$transparent = 0x7fffffff;

	// 투명배경 칠하기
	imagefilledrectangle($image, 0, 0, $width -1, $height -1, $white); //$transparent);

	// 블렌딩 모드 TRUE 설정
	//imagealphablending($image, 1);

	// 투명배경 위에 글자쓰기
	//imagettftext($image, $fontSize, $angle, 0, $height - 5, $white, $font, $text);

	imageArc($image, 100, 100, 50, 50, 0, 360, $balck);
	//imageString($image, 3, 98, 94, '1', $text_color);
	imagettftext($image, $fontSize, $angle, 98, 94, $balck, $font, $text);
	*/

	//imagepng($image,'../hce/map/family_'.$hce->IPIN.'.png');
	imagejpeg($image,$rstImg);
	ImageDestroy($image);

	//imagefilledrectangle ( resource $image , int $x1 , int $y1 , int $x2 , int $y2 , int $white )

	echo '<img src="'.$rstImg.'" border="0" style="margin:5px;">';

	include_once('../inc/_db_close.php');
?>