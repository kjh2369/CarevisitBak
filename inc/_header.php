<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_function.php");
	include_once("../inc/_myFun.php");

	if ($_GET['join'] != 'YES'){
		$urlPage = explode("/",$_SERVER["REQUEST_URI"]);

		if ($urlPage[sizeOf($urlPage)-1] == 'temp_doctor.php' || $urlPage[sizeOf($urlPage)-1] == 'temp_nurse.php'){
		}else{
			if ($urlPage[sizeOf($urlPage)-1] != "main.php" && $urlPage[sizeOf($urlPage)-1] != "login.php"){
				if (!isset($_SESSION["userCode"])){
					echo "
						<script>
							top.location.replace('../index.html');
						</script>
						 ";
					exit;
				}
			}
		}
	}


	if ($gDomain == 'thegoodjob.net' && $gHostNm == 'admin' && empty($_SESSION['userCode'])){
		//다케어본사 admin 로그인페이지일 경우
	}else{?>
		<!DOCTYPE html>
		<html>
		<head><?
			if ($gDomain == 'kacold.net'){?>
				<title>::재가지원서비스::</title><?
			}else{?>
				<title>::케어비지트 방문서비스시스템::</title><?
			}?>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta http-equiv='imagetoolbar' content='no'>
			<meta http-equiv="Content-Script-Type" content="text/javascript" />
			<meta http-equiv="Content-Style-Type" content="text/css" />
			<meta name="keywords" content="방문서비스 관리 시스템, 케어,돌봄, 재가, 요양보호사, 방문요양, 방문목욕, 방문간호" />
			<!--meta http-equiv="X-UA-Compatible" content="IE=8" /-->
			<!--meta http-equiv="X-UA-Compatible" content="EmulateIE8" /-->
			<!--meta http-equiv="X-UA-Compatible" content="edge" /-->
			<link href="../css/style.css" rel="stylesheet" type="text/css">
			<link href="../css/head.css" rel="stylesheet" type="text/css">
			<link href="../css/main_contents.css" rel="stylesheet" type="text/css">
			<link href="../css/left_menu.css" rel="stylesheet" type="text/css">
			<link href="../css/layer_pop.css" rel="stylesheet" type="text/css">
			<link rel="stylesheet" type="text/css" href="../css/jqueryslidemenu.css" /><!--menu-->
			<base target="_self">
		</head>
		<script type="text/javascript" src="../js/prototype.js"	></script>
		<script type="text/javascript" src="../js/xmlHTTP.js"	></script>
		<script type="text/javascript" src="../js/script.js"	></script>
		<script type='text/javascript' src='../js/pass.js'></script>
		<script type="text/javascript" src="../js/center.js"	></script>
		<script type="text/javascript" src="../js/iljung.js"	></script>
		<script type="text/javascript" src="../js/suga.js"		></script>
		<script type="text/javascript" src="../js/other.js"		></script>
		<script type="text/javascript" src="../js/cal.js"		></script>
		<script type="text/javascript" src="../js/kjw.work.js"	></script>
		<script type="text/javascript" src="../js/table_class.js"></script>
		<!--menu-->
		<script type="text/javascript" src="../js/jquery.min.js"></script>
		<script type="text/javascript" src="../js/jqueryslidemenu.js"></script>
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/jquery.form.js"></script>
		<script type='text/javascript' src='../longcare/longcare.js'></script>
		
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

		<link href="../common/monthpicker/MonthPicker.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="../common/monthpicker/MonthPicker.js"></script>

		<!-------->


		<script language='javascript'>
			$(document).ready(function(){
				$.datepicker.setDefaults({
					dateFormat: 'yy-mm-dd' //Input Display Format 변경
					,showOtherMonths: true //빈 공간에 현재월의 앞뒤월의 날짜를 표시
					,showMonthAfterYear:true //년도 먼저 나오고, 뒤에 월 표시
					//,changeYear: true //콤보박스에서 년 선택 가능
					//,changeMonth: true //콤보박스에서 월 선택 가능
					//,showOn: "both" //button:버튼을 표시하고,버튼을 눌러야만 달력 표시 ^ both:버튼을 표시하고,버튼을 누르거나 input을 클릭하면 달력 표시
					//,buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif" //버튼 이미지 경로
					//,buttonImageOnly: true //기본 버튼의 회색 부분을 없애고, 이미지만 보이게 함
					,buttonText: "선택" //버튼에 마우스 갖다 댔을 때 표시되는 텍스트
					,yearSuffix: "년" //달력의 년도 부분 뒤에 붙는 텍스트
					,monthNamesShort: ['1','2','3','4','5','6','7','8','9','10','11','12'] //달력의 월 부분 텍스트
					,monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'] //달력의 월 부분 Tooltip 텍스트
					,dayNamesMin: ['<span style="color:red;">일</span>','월','화','수','목','금','<span style="color:blue;">토</span>'] //달력의 요일 부분 텍스트
					,dayNames: ['일요일','월요일','화요일','수요일','목요일','금요일','토요일'] //달력의 요일 부분 Tooltip 텍스트
					//,minDate: "-1M" //최소 선택일자(-1D:하루전, -1M:한달전, -1Y:일년전)
					//,maxDate: "+1M" //최대 선택일자(+1D:하루후, -1M:한달후, -1Y:일년후)
				});
			});

			$(document).unbind('keypress').bind('keypress',function(){
				if (parent.opener){
					if (window.event.keyCode == 27){
						//self.close();
					}
				}
			});


			if( navigator.userAgent.indexOf( "MSIE 7" ) > 0 && navigator.userAgent.indexOf( "Trident" ) ) {
				// 호환성 보기 활성화
			}else {
				// 호환성 보기 활성화
			}

			if (navigator.appName.indexOf("Microsoft") > -1 || navigator.appName.indexOf("Netscape") > -1){
			}else{
				if ('<?=$debug;?>' != '1'){
					location.replace('../error.html');
				}
			}

			function SetMouseMove(caption, body){
				if (!body) body = $(caption).parent();

				$(body).css('border', '2px solid red');

				$(caption).css('cursor', 'default');
				$(body).draggable({'containment':[0, 0, 1600, 800], 'handle': caption});
			}
		</script><?

		if ($debug){
			echo '<body>';
		}else{
			//echo '<body oncontextmenu="return false">';
			echo '<body>';
		}
	}?>