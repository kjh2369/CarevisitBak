<?
	include_once('../../inc/_db_open.php');
	include_once('../../inc/_myFun.php');
	include_once('../../inc/_ed.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
	<title>CAREVISIT</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv='imagetoolbar' content='no'>
	<link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<base target="_self">
<script type="text/javascript" src="../js/gnb.js"></script>
<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript" src="../js/xmlHTTP.js"></script>
<script type="text/javascript" src="../js/script.js"	></script>
<script type="text/javascript" src="../js/jquery.js"></script>
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
<script type="text/javascript">
function gGetDate(target){
	var ret = null;
	var varDate;
	var y, m, d;
	var ry, rm, rd;

	if (typeof(target) == 'object'){
		varDate = target.value;
	}else{
		varDate = target;
	}

	varDate = __replace(varDate, '-', '');
	varDate = __replace(varDate, '.', '');

	if (varDate.length != 8){
		varDate = '';
	}else{
		y = varDate.substring(0,4);
		m = varDate.substring(4,6);
		d = varDate.substring(6,8);

		if (m.substring(0,1) == '0'){
			m = m.substring(1,2);
		}

		if (d.substring(0,1) == '0'){
			d = d.substring(1,2);
		}

		y = parseInt(y);
		m = parseInt(m);
		d = parseInt(d);

		if (y > 1900 && (m > 1 || m < 12)){
			if (m == 1 || m == 3 || m == 5 || m == 7 || m == 8 || m == 10 || m == 12){
				if (d > 31){
					varDate = '';
				}
			}else if (m == 4 || m == 6 || m == 9 || m == 11){
				if (d > 30){
					varDate = '';
				}
			}else if(m == 2){
				if (d > 29){
					varDate = '';
				}
			}else{
			}
			if (varDate != ''){
				ry = y;
				rm = m < 10 ? '0'+m : m;
				rd = d < 10 ? '0'+d : d;
				varDate = ry+'-'+rm+'-'+rd;
			}

			if (!checkDate(varDate)){
				varDate = '';
			}
		}else{
			varDate = '';
		}
	}

	if (typeof(target) == 'object'){
		target.value = varDate;
	}else{
		return varDate;
	}
}
</script>
<?
	if ($debug){?>
		<body><?
	}else{?>
		<body oncontextmenu="return false"><?
	}?>
