<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_function.php");
	include_once("../inc/_myFun.php");

	define(__ROOT__, TRUE);

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
		<!-------->
		<script language="vbscript">
			Function checkDate(p_date)
				checkDate = IsDate(p_date)
			End Function

			Function getToday()
				Dim y, m, d

				y = Year(Now)
				m = Month(Now)
				d = Day(Now)

				If (m < 10) Then m = "0" & m
				If (d < 10) Then d = "0" & d

				getToday = y & "-" & m & "-" & d
			End Function

			Function getTime()
				Dim time

				getTime = Right("0" & Hour(Now),2) & ":" & Right("0" & Minute(Now), 2)
			End Function

			Function diffDate(p_datepart, p_startDate, p_endDate)
				diffDate = DateDiff(p_datepart, p_startDate, p_endDate)
			End Function

			Function addDate(p_interval, p_number, p_date)
				Dim ls_date

				ls_date = DateAdd(p_interval, p_number, p_date)

				addDate = Year(ls_date) & "-" & Right("0" & Month(ls_date), 2) & "-" & Right("0" & Day(ls_date), 2)
			End Function

			Function addTime(p_interval, p_number, p_time)
				Dim ls_time

				ls_time = DateAdd(p_interval, p_number, p_time)

				addTime = Hour(ls_time) & ":" & Minute(ls_time) & ":" & Second(ls_time)
			End Function

			Function getYear(p_date)
				If (checkDate(p_date)) Then
					getYear = Year(p_date)
				Else
					getYear = 0
				End If
			End Function

			Function getMonth(p_date)
				If (checkDate(p_date)) Then
					getMonth = Month(p_date)
				Else
					getMonth = 0
				End If
			End Function

			Function getDay(p_date)
				If (checkDate(p_date)) Then
					getDay = Day(p_date)
				Else
					getDay = 0
				End If
			End Function

			Function getWeekDay(p_date)
				If (checkDate(p_date)) Then
					getWeekDay = Weekday(p_date)
				Else
					getWeekDay = 0
				End If
			End Function

			Function getLastDay(p_date)
				If (Not checkDate(p_date)) Then
					getLastDay = 0
					Exit Function
				End If

				getLastDay = Day(DateAdd("d", -1, DateAdd("m", 1, p_date)))
			End Function
		</script>

		<script language='javascript'>
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
		</script><?

		if ($debug){
			echo '<body>';
		}else{
			//echo '<body oncontextmenu="return false">';
			echo '<body>';
		}
	}?>