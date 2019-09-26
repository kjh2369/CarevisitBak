<?
	class myImage{
		// 이미지 데이터를 가져와서 base64 인코딩으로 변환 후 반환
		function getImageData($imgLink){
			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, $imgLink);

			// referer는 적당히 연구해서 속일 것. 비워도 되는 경우도 있음.
			curl_setopt($curl, CURLOPT_REFERER, '');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			$img = curl_exec($curl);

			curl_close($curl);

			return base64_encode($img);
		}

		// 이미지 링크에서 이미지의 확장자를 읽어  mime type 형태로 반환
		function getHeader($img){
			$extArr = array(
				'jpg' => 'image/jpg',
				'gif' => 'image/gif',
				'bmp' => 'image/bmp',
				'png' => 'image/png'
			);

			$ext = strtolower(substr($img, strrpos($img, '.')+1));

			return $extArr[$ext];
		}

		//이미지 사이즈
		function getImgSize($maxW, $maxH, $objW, $objH){
			$w1 = $maxW;
			$h1 = $maxH;
			$w2 = $objW;
			$h2 = $objH;
			$w = 0;
			$h = 0;
			$r = 1;

			$r1 = $w1 / $w2;
			$r2 = $h1 / $h2;

			if ($r1 < $r2){
				$r = $r1;
			}else{
				$r = $r2;
			}

			$w = $w2 * $r;
			$h = $h2 * $r;

			return Array('w'=>Ceil($w), 'h'=>Ceil($h));
		}
	}

	$myImage = new myImage();