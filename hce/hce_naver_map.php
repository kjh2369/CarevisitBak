<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ko" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>샘플코드</title>

<?
  define(NAVER_MAP_KEY, "ddb45388066c66d8114115fa2e21994d");

  function get_navermap_coods($p_str_addr="")
  {
  $int_x = 0;
  $int_y = 0;

  $str_addr = str_replace(" ", "", $p_str_addr);

  // curl 이용해서 지도에 필요한 좌표를 취득
  $dest_url    = "http://openapi.map.naver.com/api/geocode.php?key=" . NAVER_MAP_KEY . "&encoding=utf-8&coord=LatLng&query=" . urlencode($str_addr);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $dest_url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $str_result = curl_exec($ch);
  curl_close($ch);

  $obj_xml = simplexml_load_string($str_result);

  $int_x = $obj_xml->item->point->x;
  $int_y = $obj_xml->item->point->y;

  return array($int_x, $int_y);
  }


  // 주소에서 좌표를 추출한다.
  $str_addr = "서울 강동구 천호2동 123번지 402호";
  list($int_x, $int_y) = get_navermap_coods($str_addr);
?>



<script type="text/javascript">
try {document.execCommand('BackgroundImageCache', false, true);} catch(e) {}
</script>
<script type="text/javascript" src="http://openapi.map.naver.com/openapi/naverMap.naver?ver=2.0&key=<?= NAVER_MAP_KEY; ?>"></script>
</head>
<body style="margin:0;">



<div id = "naverMap" style="margin:1px;width:100%; height:288px;"></div>

<script type="text/javascript">
  var oPoint = new nhn.api.map.LatLng(<?= $int_y; ?>, <?= $int_x; ?>); // -  지도의 중심점을 나타내는 변수 선언
  nhn.api.map.setDefaultPoint('LatLng'); // - 지도에서 기본적으로 사용하는 좌표계를 설정합니다.
  var markerCount = 0;
  oMap = new nhn.api.map.Map('naverMap', {
  point : oPoint,
  zoom : 10, // - 초기 줌 레벨은 10으로 둔다.
  enableWheelZoom : false,
  enableDragPan : true,
  enableDblClickZoom : false,
  mapMode : 0,
  activateTrafficMap : false,
  activateBicycleMap : false,
  minMaxLevel : [ 1, 14 ],
  size : new nhn.api.map.Size(787, 288)
  });
  var mapZoom = new nhn.api.map.ZoomControl(); // - 줌 컨트롤 선언
  //themeMapButton = new nhn.api.map.ThemeMapBtn(); // - 자전거지도 버튼 선언
  //mapTypeChangeButton = new nhn.api.map.MapTypeBtn(); // - 지도 타입 버튼 선언
  //var trafficButton = new nhn.api.map.TrafficMapBtn(); // - 실시간 교통지도 버튼 선언
  //trafficButton.setPosition({top:10, right:110}); // - 실시간 교통지도 버튼 위치 지정
  //mapTypeChangeButton.setPosition({top:10, left:50}); // - 지도 타입 버튼 위치 지정
  //themeMapButton.setPosition({top:10, right:10}); // - 자전거지도 버튼 위치 지정
  mapZoom.setPosition({left:10, top:10}); // - 줌 컨트롤 위치 지정.
  oMap.addControl(mapZoom);
  //oMap.addControl(themeMapButton);
  //oMap.addControl(mapTypeChangeButton);
  //oMap.addControl(trafficButton);

  var oSize = new nhn.api.map.Size(28, 37);
  var oOffset = new nhn.api.map.Size(14, 37);
  var oIcon = new nhn.api.map.Icon('http://static.naver.com/maps2/icons/pin_spot2.png', oSize, oOffset);

  var oMarker = new nhn.api.map.Marker(oIcon, { title : '홍길동 집' });  //마커 생성
  oMarker.setPoint(oPoint);
  oMap.addOverlay(oMarker);
  var oLabel = new nhn.api.map.MarkerLabel(); // - 마커 라벨 선언.
  oMap.addOverlay(oLabel); // - 마커 라벨 지도에 추가. 기본은 라벨이 보이지 않는 상태로 추가됨.
  oLabel.setVisible(true, oMarker); // 마커 라벨 보이기

	//저장 버튼 생성
	var mapSave = new nhn.api.map.CustomControl();
		mapSave.setPosition({left:100, top:10});
		oMap.addControl(mapSave);  //저장버튼을 지도 위에 추가
</script>