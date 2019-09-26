<?
	include_once('../inc/_header.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];
	$year  = date('Y', mktime());
	$month = date('m', mktime());
	$ownerId = $_GET['ownerId'];
	$week  = 1;

	if ($ownerId) $code = $ownerId;

?>
<script type="text/javascript">
	function PDF_MONTH(){
		var f = document.f;

		window.open("calendar_pdf_show.php?code=<?=$code?>&year=" + document.getElementById('year').value + "&month=" + document.getElementById('month').value,"REPORT","width=900,height=700,scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no");
	}

	function PDF_WEEK(){
		var f = document.f;

		window.open("calendar_pdf_weekly.php?code=<?=$code?>&mode=" + document.getElementById('mode').value + "&year=" + document.getElementById('year').value + "&month=" + document.getElementById('month').value + "&week=" + document.getElementById('week').value + "&fromDt=" + document.getElementById('fromDt').value + "&toDt=" + document.getElementById('toDt').value,"REPORT","width=900,height=700,scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no");
	}

	function lfExcel(){
		var mode = $('input:hidden[name="mode"]').val();

		if (mode == '' || mode == 'month'){
		}else {
			alert('엑셀 출력은 월별만 가능합니다.');
			return;
		}

		var parm = new Array();
			parm = {
				'year'	:$('input:hidden[name="year"]').val()
			,	'month'	:$('input:hidden[name="month"]').val()
			};

		var form = document.createElement('form');
		var objs;

		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('method', 'post');
		form.setAttribute('action', './calendar_excel.php');

		document.body.appendChild(form);

		form.submit();
	}

	function lfPrint(){
		var mode = $('input:hidden[name="mode"]').val();

		if (mode == 'list'){
			var para = 'root=calendar'
					 + '&dir=p'
					 + '&fileName=calendar_list'
					 + '&fileType=pdf'
					 + '&target=show.php'
					 + '&showForm=CALN_LIST'
					 + '&code=<?=$code;?>'
					 + '&year='+$('input:hidden[name="year"]').val()
					 + '&month='+$('input:hidden[name="month"]').val()
					 + '&param=';

			__printPDF(para);
		}else if (mode == 'week'){
			var weekly	= 0;
			var fromDt	= '';
			var toDt	= '';

			$('span[id^="lblWeekly"]').each(function(){
				if ($(this).css('font-weight') == '700' || $(this).css('font-weight') == 'bold'){
					weekly	= $(this).attr('id').split('lblWeekly').join('');
					fromDt	= $(this).attr('fromDt');
					toDt	= $(this).attr('toDt');
					return false;
				}
			});

			var para = 'root=calendar'
					 + '&dir=l'
					 + '&fileName=calendar_weekly'
					 + '&fileType=pdf'
					 + '&target=show.php'
					 + '&showForm=CALN_WEEKLY'
					 + '&code=<?=$code;?>'
					 + '&year='+$('input:hidden[name="year"]').val()
					 + '&month='+$('input:hidden[name="month"]').val()
					 + '&weekly='+weekly
					 + '&fromDt='+fromDt
					 + '&toDt='+toDt
					 + '&param=';

			__printPDF(para);
		}else{
			PDF_MONTH();
		}
	}
</script>
<?
	ob_start();

	echo '<script type=\'text/javascript\' src=\'./calendar.js\'></script>';
	echo '<script language=\'javascript\'>';
	echo 'window.onload = function(){';
	echo '_doc=document.getElementById(\'left_box\');';
	echo '_menu=document.getElementById(\'this_menu\');';
	echo '_body=document.getElementById(\'this_body\');';
	echo '_yymm=document.getElementById(\'this_yymm\');';
	echo '_week=document.getElementById(\'this_week\');';
	echo '_reg=document.getElementById(\'this_reg\');';
	echo '_moveYYMM(0);';
	echo '}';
	echo '</script>';
	echo '<form name=\'f\' method=\'post\'>';
	echo '<div style=\'height:30px; line-height:30px; border-bottom:2px solid #0e69b0;\'>';


	/*********************************************************

		년도 및 월

	*********************************************************/
	echo '<div id=\'this_menu\' style=\'position:absolute; float:center; text-align:center; width:100%; font-weight:bold;\'>';
	echo '<table>';
	echo '<tbody>';
	echo '<tr>';
	echo '<td class=\'noborder\'><img src=\'../image/btn/btn_pre_out.gif\' style=\'cursor:pointer;\' onclick=\'_moveYYMM(-1);\' onmouseover=\'this.src="../image/btn/btn_pre_over.gif";\' onmouseout=\'this.src="../image/btn/btn_pre_out.gif";\'></td>';
	echo '<td class=\'noborder\'><span style=\'margin-left:5px; margin-right:5px; font-weight:bold;\' id=\'this_yymm\'></span></td>';
	echo '<td class=\'noborder\'><img src=\'../image/btn/btn_next_out.gif\' style=\'cursor:pointer;\' onclick=\'_moveYYMM(1);\' onmouseover=\'this.src="../image/btn/btn_next_over.gif";\' onmouseout=\'this.src="../image/btn/btn_next_out.gif";\'></td>';
	echo '</tr>';
	echo '</tbody>';
	echo '</table>';
	echo '</div>';




	/*********************************************************

		메뉴

	*********************************************************/
	echo '<div style=\'position:absolute; float:center; text-align:center; width:auto; padding-top:5px; padding-left:5px; font-weight:bold;\'>';
	echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_getCalendar("month");\'>월별</button></span> ';
	echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_getCalendar("week");\'>주간</button></span> ';
	echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'_getCalendar("list");\'>목록</button></span> ';
	echo '</div>';

	echo '<div id=\'this_menu\' style=\'position:absolute; float:center; margin-top:5px; text-align:right; width:100%; font-weight:bold;\'>';
	echo '<span class="btn_pack m icon" style="text-align:right;"><span class="pdf"></span><button name="btnSearch" type="button" onclick=\'lfPrint();\'>출력</button></span> ';
	echo '<span class="btn_pack m icon" style="text-align:right;"><span class="excel"></span><button id="btnPrtExcel" type="button" onclick=\'lfExcel();\'>엑셀</button></span> ';

	echo '</div>';

	#echo '<div id=\'this_menu\' style=\'position:absolute; float:center; margin-top:5px; text-align:right; width:100%; font-weight:bold;\'>';
	#echo '<span id=\'cal_month\' class="btn_pack m icon" style="text-align:right;"><span class="pdf"></span><button name="btnSearch" type="button" onclick=\'PDF_MONTH();\' >PDF</button></span> ';
	#echo '<span id=\'cal_weekly\' class="btn_pack m icon" style="text-align:right;"><span class="pdf"></span><button name="btnSearch" type="button" onclick=\'PDF_WEEK();\' >PDF</button></span> ';
	#echo '</div>';

	echo '</div>';

	echo '<div id=\'this_body\' style=\'\'></div>';
	echo '<div id=\'this_reg\' style=\'position:absolute; left:0; top:0; width:auto; height:auto; display:none;\'></div>';



	/*********************************************************

		공통

	*********************************************************/
	echo '<input name=\'code\' type=\'hidden\' value=\''.$code.'\'>';
	echo '<input name=\'year\' type=\'hidden\' value=\''.$year.'\'>';
	echo '<input name=\'month\' type=\'hidden\' value=\''.$month.'\'>';
	echo '<input name=\'week\' type=\'hidden\' value=\''.$week.'\'>';
	echo '<input name=\'mode\' type=\'hidden\' value=\'\'>';
	echo '<input name=\'fromDt\' type=\'hidden\' value=\'\'>';
	echo '<input name=\'toDt\' type=\'hidden\' value=\'\'>';


	echo '</form>';

	$html = ob_get_contents();

	ob_end_clean();

	echo $html;


	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>