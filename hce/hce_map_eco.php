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
	$IPIN	= $hce->IPIN;
	$rcpt	= $hce->rcpt;
	$sr		= $_POST['sr'];
	$rstFile= $IPIN.'_'.$rcpt.'.jpg';//Date('YmdHis').'.jpg';
	$rstImg	= '../hce/eco/'.$orgNo.'/'.$hce->SR.'/'.$rstFile;

	//배경 크기
	$width	= 600;
	$height = 300;

	//객체크기
	$S = 50;

	//제공서비스
	/*
	$sql = 'SELECT	offer_svc_gbn
			FROM	hce_interview
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$tmp = $conn->get_data($sql);
	$tmp = Str_Replace('/','&',$tmp);
	$tmp = Str_Replace(':','=',$tmp);

	Parse_Str($tmp,$arr);
	*/

	$sql = 'SELECT	other_svc_nm
			FROM	hce_interview
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$arr = Explode(',',$conn->get_data($sql));

	$cnt = SizeOf($arr);

	//제공 가능 서비스
	$sql = 'SELECT	DISTINCT
					care.suga_cd AS cd
			,		suga.nm1 AS mst_nm
			,		suga.nm2 AS pro_nm
			,		suga.nm3 AS svc_nm
			FROM	care_suga AS care
			INNER	JOIN	suga_care AS suga
					ON		suga.cd1 = SUBSTR(care.suga_cd,1,1)
					AND		suga.cd2 = SUBSTR(care.suga_cd,2,2)
					AND		suga.cd3 = SUBSTR(care.suga_cd,4,2)
			WHERE	care.org_no	= \''.$orgNo.'\'
			AND		care.suga_sr= \''.$hce->SR.'\'';

	$suga = $conn->_fetch_array($sql,'cd');

	if (Is_Array($arr)){
		$liAg = 90;

		/*
		foreach($arr as $cd => $yn){
			if ($yn == 'Y'){
				$svc[] = Array(
						'str'=>$suga[$cd]['svc_nm']
					,	'x1'=>$width / 2
					,	'y1'=>$height / 2
					,	'x2'=>$width / 2
					,	'y2'=>$height / 2
					,	'theta'=>0
				);
			}
		}
		*/
		foreach($arr as $svcNm){
			$svc[] = Array(
					'str'=>$svcNm
				,	'x1'=>$width / 2
				,	'y1'=>$height / 2
				,	'x2'=>$width / 2
				,	'y2'=>$height / 2
				,	'theta'=>0
			);
		}

		$cnt = SizeOf($svc);
		@$ag = 360 / $cnt;
		$A = $liAg;

		$PI = pi();	//파이값
		$IMG_RAD = 130;

		for($i=0; $i<$cnt; $i++){
			$theta1 = deg2rad($A);	 //라디안 변환

			if ($A >= 0 && $A < 90){	 //위치 산출
				$X2 = $IMG_RAD * cos($PI/2 - $theta1) + $IMG_RAD;
				$Y2 = $IMG_RAD - $IMG_RAD * sin($PI/2 - $theta1);
			}else if($A >= 90 && $A < 180){
				$X2 = $IMG_RAD * sin($PI - $theta1) + $IMG_RAD;
				$Y2 = $IMG_RAD * cos($PI - $theta1) + $IMG_RAD;
			}else if($A >= 180 && $A < 270){
				$X2 = $IMG_RAD - $IMG_RAD * cos($PI*3/2 - $theta1);
				$Y2 = $IMG_RAD * sin($PI*3/2 - $theta1) + $IMG_RAD;
			}else{
				$X2 = $IMG_RAD - $IMG_RAD * sin($PI*2 - $theta1);
				$Y2 = $IMG_RAD - $IMG_RAD * cos($PI*2 - $theta1);
			}

			$svc[$i]['x1'] = $IMG_RAD + $width / 2 - $IMG_RAD;
			$svc[$i]['y1'] = $IMG_RAD + $height / 2 - $IMG_RAD;
			$svc[$i]['x2'] = $X2 + $width / 2 - $IMG_RAD;
			$svc[$i]['y2'] = $Y2 + $height / 2 - $IMG_RAD;

			$A += $ag;
		}

		Unset($tmp);
		Unset($suga);
	}

	//폰트설정
	$font = '../font/MALGUN.TTF';

	//폰트 크기
	$fontSize = 11;

	//앵글
	$angle = 0;

	//이미지 리소스 생성
	$image = ImageCreateTrueColor($width, $height);

	// 색 지정
	$white = ImageColorAllocate($image,255,255,255);
	$balck = ImageColorAllocate($image, 0, 0, 0);
	$blue  = ImageColorAllocate($image, 0, 0, 255);

	//배경지정
	ImageFillEdrectAngle($image, 0, 0, $width -1, $height -1, $white);

	//본인좌표
	$mX = $width / 2;
	$mY = $height / 2;

	if (Is_Array($svc)){
		foreach($svc as $row){
			$X1 = $row['x1'];
			$Y1 = $row['y1'];
			$X2 = $row['x2'];
			$Y2 = $row['y2'];

			// 경계선 그리기
			ImageLine($image, $X1, $Y1, $X2, $Y2, $balck);
		}
	}

	$X = $mX;
	$Y = $mY;

	//본인
	ImageArc($image, $X, $Y, $S, $S, 0, 360, $balck);
	ImageFillEdrectAngle($image, $X - 15, $Y - 15, $X + 15, $Y + 15, $white);

	//글자길이
	$bbox = ImageTTFBBox($fontSize, $angle, $font, 'Ct.');
	$textW = $bbox[4];
	$textH = $bbox[5];

	$X = $mX - $S / 2 + ($S - $textW) / 2;
	$Y = $mY - $S / 2 + ($S - $textH) / 2;;

	//글쓰기
	ImageTTFText($image,$fontSize,$angle,$X,$Y,$balck,$font,'Ct.');

	if (Is_Array($svc)){
		$W = $width / 2;
		$H = $height / 2;
		$SX = 100;
		$SY = 25;

		//서비스 출력
		foreach($svc as $row){
			$X2 = Floor($row['x2']);
			$Y2 = Floor($row['y2']);
			$str = $row['str'];

			//글자길이
			$bbox = ImageTTFBBox($fontSize, $angle, $font, $str);
			$TW = $bbox[4];
			$TH = $bbox[5];

			$SX = $TW;

			if ($X2 >= $W){
				if ($X2 >= $W - $SX && $X2 <= $W + $SY){
					$X = $X2 - $SX / 2;
				}else{
					$X = $X2;
				}
			}else{
				$X = $X2 - $SX;
			}

			if ($Y2 >= $H){
				$Y = $Y2 - $SY / 2;
			}else{
				$Y = $Y2 - $SY + $SY / 2;
			}

			$TX = $X + 2;
			$TY = $Y + ($SY - $TH) / 2;

			ImageRectangle($image, $X, $Y, $X+$SX, $Y+$SY, $black);
			ImageFillEdrectAngle($image, $X+1, $Y+1, $X+$SX-1, $Y+$SY-1, $white);
			ImageTTFText($image,$fontSize-1,$angle,$TX,$TY,$balck,$font,$str);
		}
	}

	Unset($arr);

	if (!Is_Dir('../hce/eco/'.$orgNo)) MkDir('../hce/eco/'.$orgNo);
	if (!Is_Dir('../hce/eco/'.$orgNo.'/'.$sr)) MkDir('../hce/eco/'.$orgNo.'/'.$sr);

	imagejpeg($image,$rstImg);
	ImageDestroy($image);


	if ($cnt > 0){
		$sql = 'UPDATE	hce_map
				SET		eco_path = \''.$rstImg.'\'
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
				,eco_path) VALUES (
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
?>