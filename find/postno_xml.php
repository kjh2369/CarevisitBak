<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');

	$key = 'BZoHapiuJMXWTy4xVXM7q3x8Z9QiGSLJ6SMGGLzj8mdHQUT7c5KB1EHiiLYbDSwwANMQ5WJqNbeKUVnEYtbFCg%3D%3D';
	$perCnt = 10;
	$page = $_POST['page'];
	$srchwrd = urlencode($_POST['wrd']);

	function curl($url){
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$g = curl_exec($ch);
		curl_close($ch);
		return $g;
	}

	function xml2str($xml){
		$xml = simplexml_load_string($xml);
		$str = '';

		$totalCount = $xml->cmmMsgHeader->totalCount; //전체 검색수
		$countPerPage = $xml->cmmMsgHeader->countPerPage; //마지막페이지
		$totalPage = $xml->cmmMsgHeader->totalPage; //전체 페이지수
		$currentPage = $xml->cmmMsgHeader->currentPage; //현재페이지

		$row = $xml->newAddressListAreaCdSearchAll;
		$cnt = count($row);

		$str .= 'totCnt='.$totalCount;
		$str .= '&totPag='.$totalPage;
		$str .= '&curPag='.$currentPage;

		for($i=0; $i<$cnt; $i++){
			//echo $val->zipNo.' / '.$val->lnmAdres.' / '.$val->rnAdres.'<br>';
			#print_r($row[$i]);
			#echo '-------------------------------------------<br>';

			#$zipNo = $row[$i]['zipNo'];
			#$lnmAdres = $row[$i]['lnmAdres'];
			#$rnAdres = $row[$i]['rnAdres'];

			$str .= '?zipNo='.$row[$i]->zipNo;
			$str .= '&lnmAdres='.$row[$i]->lnmAdres;
			$str .= '&rnAdres='.$row[$i]->rnAdres;
		}

		return $str;
	}

	$str =  curl("http://openapi.epost.go.kr/postal/retrieveNewAdressAreaCdSearchAllService/retrieveNewAdressAreaCdSearchAllService/getNewAddressListAreaCdSearchAll?ServiceKey=".$key."&countPerPage=".$perCnt."&currentPage=".$page."&srchwrd=".$srchwrd);
	echo $str;

	//echo xml2str($str);

	include_once('../inc/_db_close.php');
?>