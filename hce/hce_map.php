<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_myImage.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$orgType= $_POST['sr'];
	$userCd = $_SESSION['userCode'];
	$type	= $_POST['type'];
	$sr		= $_POST['sr'];
	$IPIN	= $hce->IPIN;
	$rcpt	= $hce->rcpt;
	$rstFile= $IPIN.'_'.$rcpt.'.jpg';//Date('YmdHis').'.jpg';
	$rstImg	= '../hce/map/'.$orgNo.'/'.$hce->SR.'/'.$rstFile;


	//주민번호
	$sql = 'SELECT	m03_jumin AS jumin
			,		m03_name AS name
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$IPIN.'\'';

	$row = $conn->get_array($sql);

	$sql = 'SELECT	jumin
			FROM	mst_jumin
			WHERE	org_no	= \''.$orgNo.'\'
			AND		gbn		= \'1\'
			AND		code	= \''.$row['jumin'].'\'';

	$realJumin = $conn->get_data($sql);

	//성별
	if (SubStr($realJumin,6,1) % 2 == 1){
		$gender = 'M';
	}else{
		$gender = 'W';
	}

	$me = Array(
		'id'=>'0_0'
	,	'name'=>$row['name']
	,	'rel'=>'95'
	,	'age'=>$myF->issToAge($realJumin)
	,	'gender'=>$gender
	,	'partner'=>'N'
	,	'main'=>'Y'
	);

	Unset($row);

	//가족
	$sql = 'SELECT	family_rel AS rel
			,		family_age AS age
			,		family_nm AS nm
			,		family_remark AS rmk
			FROM	hce_family
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$orgType.'\'
			AND		IPIN	= \''.$IPIN.'\'
			AND		rcpt_seq= \''.$rcpt.'\'
			ORDER	BY age DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$partner = 'N';

		if ($row['rel'] == '01' || $row['rel'] == '02'){
			$idx = 1;
			$j = SizeOf($family[$idx]);

			if ($row['rel'] == '01'){
				$gender = 'M';
			}else if ($row['rel'] == '02'){
				$gender = 'W';
			}
		}else if ($row['rel'] == '09' || $row['rel'] == '10' || $row['rel'] == '96'){
			$idx = 0;

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
			}else if ($row['rel'] == '10'){
				$gender = 'W';
			}else if ($row['rel'] == '96'){
				$partner = 'Y';
				if ($me['gender'] == 'M'){
					$gender = 'W';
				}else{
					$gender = 'M';
				}
			}
		}else if ($row['rel'] == '05' || $row['rel'] == '06'){
			$idx = 2;
			$j = SizeOf($family[$idx]);

			if ($row['rel'] == '05'){
				$gender = 'M';
			}else if ($row['rel'] == '06'){
				$gender = 'W';
			}
		}else if ($row['rel'] == '07' || $row['rel'] == '08'){
			$idx = 3;
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
		,	'partner'=>$partner
		,	'rmk'=>$row['rmk']
		);
	}

	if (!$family[0]){
		$family[0][0] = $me;
	}

	$conn->row_free();

	if (!Is_Array($family)) exit;

	//배경 크기
	$width	= 600;
	$height = 300;

	//객체크기
	$S = 50;

	//간격
	$G = 30;

	$lineUpYn = 'N';
	$lineDwYn = 'N';
	$lineBrYn = 'N';

	//정리
	$cnt = SizeOf($family[0]);

	#$manY = ($height - $S) / 2;
	#$womanY = $height / 2;
	$manY = $S + $S / 2;
	$womanY = $S * 2 + 5;

	foreach($family[0] as $seq => $row){
		if ($seq == 0){
			if ($row['gender'] == 'M'){
				#$X = ($width - ($S * $cnt + ($G * ($cnt - 1)))) / 2;
				$X = $S / 2;
				$Y = $manY;
			}else{
				#$X = ($width - $S * $cnt) / 2;
				$X = $S / 2;
				$Y = $womanY;
			}
		}else{
			if ($row['gender'] == 'M'){
				$X = $X + $S - $S / 2 + 30;
				$Y = $manY;
			}else{
				$X = $X + $S + $S / 2 + 30;
				$Y = $womanY;
			}
		}

		if ($row['main'] == 'Y'){
			$mX = $X;
			$mY = $Y;
			$mG = $row['gender'];

			if ($mG == 'W'){
				$mX = $mX - $S / 2;
				$mY = $mY - $S / 2;
			}
		}

		if ($row['main'] != 'Y' && $row['partner'] != 'Y'){
			$lineBrYn = 'Y';
		}

		$map[] = Array(
				'main'=>($row['main'] == 'Y' ? 'Y' : 'N')
			,	'partner'=>$row['partner']
			,	'name'=>$row['name']
			,	'gender'=>$row['gender']
			,	'age'=>$row['age']
			,	'idx'=>0
			,	'seq'=>($row['main'] == 'Y' ? 0 : $seq)
			,	'x'=>$X,'y'=>$Y
			,	'rmk'=>$row['rmk']
		);
	}

	foreach($family as $idx => $arr){
		if ($idx == 0) continue;

		$cnt = SizeOf($arr);

		if ($idx != 1) $arr = $myF->sortArray($arr,'age');

		foreach($arr as $seq => $row){
			if ($seq == 0){
				if ($idx == 1){
					#$X = $mX - ($S * ($cnt - 1)) / 2 - ($G * ($cnt - 1)) / 2;
					#$Y = $mY - $S - $G;
					$X = 0;
					$Y = 0;
				}else{
					#$X = $mX - ($S * ($cnt - 1) + $G * ($cnt - 1)) / 2;
					#$Y = $mY + $S + $G;
					$X = $mX;
					$Y = ($S + $G) * $idx;
				}
			}else{
				$X = $X + $S + $G;
			}

			if ($idx == 1) $lineUpYn = 'Y';

			$lineDwYn = 'Y';

			$map[] = Array(
					'main'=>'N'
				,	'gender'=>$row['gender']
				,	'partner'=>$row['partner']
				,	'name'=>$row['name']
				,	'age'=>$row['age']
				,	'idx'=>$idx
				,	'seq'=>$seq
				,	'x'=>($row['gender'] == 'M' ? $X : $X + $S / 2)
				,	'y'=>($row['gender'] == 'M' ? $Y : $Y + $S / 2)
				,	'rmk'=>$row['rmk']
			);
		}
	}

	//$font = '../font/MALGUN.TTF';
	$font = '../font/batang.ttc';

	//폰트 크기
	$fontSize = 11;

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


	#imageArc($image, 100, 100, 50, 50, 0, 360, $balck);
	#imageArc($image, 100, 100, 47, 47, 0, 360, $balck);

	#ImageRectangle($image,200,100,250,150,$black);
	#ImageRectangle($image,202,102,248,148,$black);

	$mapCnt = SizeOf($map);

	foreach($map as $row){
		$X = $row['x'];
		$Y = $row['y'];

		if ($row['gender'] == 'M'){
			ImageRectangle($image, $X, $Y, $X+$S, $Y+$S, $black);
			if ($row['main'] == 'Y') ImageRectangle($image, $X+3, $Y+3, $X+$S-3, $Y+$S-3, $black);
		}else{
			ImageArc($image, $X, $Y, $S, $S, 0, 360, $balck);
			if ($row['main'] == 'Y') ImageArc($image, $X, $Y, $S-5, $S-5, 0, 360, $balck);
		}

		//글자길이
		$bbox = ImageTTFBBox($fontSize, $angle, $font, $row['age']);
		$textW = $bbox[4];
		$textH = $bbox[5];

		if ($row['gender'] == 'M'){
			$X = $row['x'];
			$Y = $row['y'];
		}else{
			$X = $row['x'] - $S / 2;
			$Y = $row['y'] - $S / 2;
		}

		$X = $X + ($S - $textW) / 2;
		$Y = $Y + ($S - $textH) / 1.5;

		ImageTTFText($image,$fontSize,$angle,$X,$Y,$balck,$font,$row['age']);


		$bbox = ImageTTFBBox(9, $angle, $font, $row['name']);
		$textW = $bbox[4];
		$textH = $bbox[5];

		if ($row['gender'] == 'M'){
			$X = $row['x'];
			$Y = $row['y'];
		}else{
			$X = $row['x'] - $S / 2;
			$Y = $row['y'] - $S / 2;
		}

		$X = $X + ($S - $textW) / 2;
		$Y = $Y + ($S - $textH) / 2.5;

		ImageTTFText($image,9,$angle,$X,$Y,$balck,$font,$row['name']); //성명
		ImageTTFText($image,9,$angle,$row['x'],$Y + 15,$balck,$font,$row['rmk']); //비고

		if ($mapCnt == 1) continue;

		if ($row['partner'] == 'Y'){
			//배우자
			if ($row['gender'] == 'M'){
				$X1 = $mX + $S;
				$Y1 = $row['y'] + $S / 2;
				$X2 = $row['x'];
				$Y2 = $Y1;
			}else{
				$X1 = $row['x'] - $S / 2 - $G;
				$Y1 = $row['y'];
				$X2 = $X1 + $G;
				$Y2 = $row['y'];
			}
		}else{
			$X1 = $row['x'] + ($row['gender'] == 'M' ? $S / 2 : 0);
			$X2 = $X1;

			if ($row['idx'] == 1){
				if ($row['gender'] == 'M'){
					$Y1 = $row['y'] + $S;
				}else{
					$Y1 = $row['y'] + $S / 2;
				}
				$Y2 = $Y1 + $G / 2;
			}else{
				if ($row['gender'] == 'M'){
					$Y1 = $row['y'] - $G / 2;
					$Y2 = $row['y'];
				}else{
					$Y1 = $row['y'] - $S / 2;
					$Y2 = $Y1 - $G / 2;
				}
			}
		}

		if ($row['main'] == 'Y'){
			if ($lineUpYn == 'Y' || $lineBrYn == 'Y') ImageLine($image, $X1, $Y1, $X2, $Y2, $balck);
		}else{
			ImageLine($image, $X1, $Y1, $X2, $Y2, $balck);
		}

		if ($row['main'] == 'Y'){
			//본인 자손 직계라인
			if ($lineDwYn == 'Y') ImageLine($image, $X1, $Y1 + $S + $G / 2, $X2, $Y2 + $S + $G / 2, $balck);
		}else if ($row['seq'] == 0){
			if ($row['idx'] == '3') ImageLine($image, $X1, $Y1 - $G / 2, $X2, $Y2, $balck);
		}

		if ($row['seq'] > 0){
			if ($row['gender'] == 'M'){
				$Y = $Y1;
			}else{
				$Y = $Y2;
			}

			if ($row['partner'] == 'Y'){
				$Y = $Y - $S / 2 - $G / 2;
				$X1 = $X1 - $S / 2;
				$X2 = $X2 + $S;

				if ($lineBrYn == 'Y') ImageLine($image, $X1, $Y, $X2, $Y, $balck);
			}else{
				ImageLine($image, $X1 - $S - $G, $Y, $X2, $Y, $balck);
			}
		}
	}

	if (!Is_Dir($_SERVER['DOCUMENT_ROOT'].'/hce/map/'.$orgNo)) MkDir($_SERVER['DOCUMENT_ROOT'].'/hce/map/'.$orgNo);
	if (!Is_Dir($_SERVER['DOCUMENT_ROOT'].'/hce/map/'.$orgNo.'/'.$sr)) MkDir($_SERVER['DOCUMENT_ROOT'].'/hce/map/'.$orgNo.'/'.$sr);

	imagejpeg($image,$rstImg);
	ImageDestroy($image);


	if ($cnt > 0){
		$sql = 'UPDATE	hce_map
				SET		family_path = \''.$rstImg.'\'
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'
				AND		ispt_seq= \'1\'';
	}else{
		$sql = 'INSERT INTO hce_map (
				 org_no
				,org_type
				,IPIN
				,rcpt_seq
				,ispt_seq
				,family_path) VALUES (
				 \''.$orgNo.'\'
				,\''.$hce->SR.'\'
				,\''.$hce->IPIN.'\'
				,\''.$hce->rcpt.'\'
				,\'1\'
				,\''.$rstImg.'\'
				)';
	}

	$conn->begin();
	$conn->execute($sql);
	$conn->commit();


	echo $rstImg;

	include_once('../inc/_db_close.php');
	exit;











	$orgType = '40';
	$orgNo	= $_SESSION['userCenterCode'];
	$userCd = $_SESSION['userCode'];
	$type	= $_POST['type'];
	$sr		= $_POST['sr'];
	$IPIN	= $hce->IPIN;
	$rcpt	= $hce->rcpt;
	$rstFile= 'test.jpg';//Date('YmdHis').'.jpg';
	$rstImg	= '../hce/map/'.$orgNo.'/'.$sr.'/'.$rstFile;

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
			}else if ($row['rel'] == '10'){
				$gender = 'W';
			}else if ($row['rel'] == '96'){
				if ($me['gender'] == 'M'){
					$gender = 'W';
				}else{
					$gender = 'M';
				}
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
	$manPath = '../hce/map/bg_man.gif';
	$manImg = GetImageSize($manPath);
	$man = ImageCreateFromGif($manPath);

	//여자 이미지
	$womanPath = '../hce/map/bg_woman.gif';
	$womanImg = GetImageSize($womanPath);
	$woman = ImageCreateFromGif($womanPath);

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

	if (!Is_Dir('../hce/map/'.$orgNo)) MkDir('../hce/map/'.$orgNo);
	if (!Is_Dir('../hce/map/'.$orgNo.'/'.$sr)) MkDir('../hce/map/'.$orgNo.'/'.$sr);

	//imagepng($image,'../hce/map/family_'.$hce->IPIN.'.png');
	imagejpeg($image,$rstImg);
	ImageDestroy($image);

	//imagefilledrectangle ( resource $image , int $x1 , int $y1 , int $x2 , int $y2 , int $white )


	// base64 데이터 가져오기
	//$imgBase64 = $myImage->getImageData($rstImg);

	// mime type 가져오기
	//$imgData = $myImage->getHeader($rstImg);

	// 이미지 출력
	//echo '<img src="data:'.$imgData.';base64,'.$imgBase64.'" />';

	echo $rstImg;

	include_once('../inc/_db_close.php');
?>