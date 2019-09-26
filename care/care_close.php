<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$year	= Date('Y');
	$month	= Date('m');
	$sr		= $_GET['sr'];

	/*
	QUERY
	SELECT yymm
	,      SUM(CASE gbn WHEN '2' THEN cnt ElSE 0 END) AS cnt
	,      SUM(CASE gbn WHEN '1' THEN 1 ELSE 0 END) AS per
	FROM   (
		   SELECT care.yymm
		   ,      care.jumin
		   ,      care.suga
		   ,      care.cnt
		   ,      IFNULL(unit.unit_gbn, '2') AS gbn
		   FROM   (
				  SELECT close_yymm AS yymm
				  ,      close_jumin AS jumin
				  ,      close_suga AS suga
				  ,      SUM(close_cnt) AS cnt
				  FROM   care_close_person
				  WHERE  org_no = '312052200000117'
				  AND    close_sr = 'R'
				  AND    close_yymm >= '201301'
				  AND    close_yymm <= '201312'
				  GROUP  BY close_yymm, close_jumin, close_suga
				  ) AS care
		   LEFT   JOIN care_suga_unit AS unit
				  ON unit.year = '2013'
				  AND unit.suga_cd = care.suga
		   ) AS t
	GROUP  BY yymm
	 */
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfLoad();
	});

	function lfMoveYear(pos){
		var year = __str2num($('#lblYYMM').attr('year'));

		year += pos;

		$('#lblYYMM').attr('year',year).text(year);

		lfLoad();
	}

	function lfMoveMonth(month){
		var obj = $('div[id^="btnMonth_"]');

		$(obj).each(function(){
			if ($(obj).hasClass('my_month_y')){
				$(obj).removeClass('my_month_y').addClass('my_month_1');
				return false;
			}
		});

		obj = $('#btnMonth_'+month);

		$(obj).removeClass('my_month_1').addClass('my_month_y');
		$('#lblYYMM').attr('month',month);
	}

	function lfLoad(){
		$.ajax({
			type: 'POST'
		,	url : './care_find.php'
		,	data: {
				'type':'<?=$type;?>'
			,	'sr':'<?=$sr;?>'
			,	'year':$('#lblYYMM').attr('year')
			}
		,	beforeSend: function (){
			}
		,	success: function(data){
				var col = __parseStr(data);

				for(var i in col){
					$('#lbl'+i).text(col[i]);
				}
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfCloseExec(){
		$('#btnExec').attr('disabled',true);

		$.ajax({
			type: 'POST'
		,	url : './care_apply.php'
		,	data: {
				'type':'<?=$type;?>'
			,	'sr':'<?=$sr;?>'
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			}
		,	beforeSend: function (){
				var w = 300; //$(document).width() - 80;
				var h = 100; //$(document).height() - 200;
				var l = ($(document).width() - w) / 2;
				var t = 270;

				$('#proc')
					.css('top',t+'px')
					.css('left',l+'px')
					.css('width',w+'px')
					.css('height',h+'px')
					.html('<div style="text-align:center; font-weight:bold;"><br>재가지원 및 상담지원 실적을<br>마감하고 있습니다.<br><br>잠시 기다려 주십시오.<br><br></div>')
					.show();
			}
		,	success: function(result){
				$('#proc').hide();

				if (result == 1){
					lfLoad();
					alert('정상적으로 처리되었습니다.');
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					if ('<?=$debug;?>' == '1'){
						document.write(result);
					}else{
						alert(result);
					}
				}

				$('#btnExec').attr('disabled',false);
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">실적마감</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="center">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="left last"><? echo $myF->_btn_month($month,'lfMoveMonth(');?></td>
		</tr>
		<tr>
			<td class="last" colspan="3" style="padding-top:20px; padding-left:300px; padding-bottom:20px;">
				<span class="btn_pack m"><button id="btnExec" type="button" class="bold" onclick="lfCloseExec();">실적마감 실행</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:auto;">
	<colgroup>
		<col width="40px">
		<col width="60px" span="2">
		<col width="40px">
		<col width="60px" span="2">
		<col width="40px">
		<col width="60px" span="2">
		<col width="40px">
		<col width="60px" span="2">
	</colgroup>
	<thead>
		<tr>
			<th class="head">월</th>
			<th class="head">횟수</th>
			<th class="head">명</th>
			<th class="head">월</th>
			<th class="head">횟수</th>
			<th class="head">명</th>
			<th class="head">월</th>
			<th class="head">횟수</th>
			<th class="head">명</th>
			<th class="head">4월</th>
			<th class="head">횟수</th>
			<th class="head">명</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="center">1월</th>
			<td class="right" id="lblCnt1"></td>
			<td class="right" id="lblPer1"></td>
			<th class="center">2월</th>
			<td class="right" id="lblCnt2"></td>
			<td class="right" id="lblPer2"></td>
			<th class="center">3월</th>
			<td class="right" id="lblCnt3"></td>
			<td class="right" id="lblPer3"></td>
			<th class="center">4월</th>
			<td class="right" id="lblCnt4"></td>
			<td class="right" id="lblPer4"></td>
		</tr>
		<tr>
			<th class="center">5월</th>
			<td class="right" id="lblCnt5"></td>
			<td class="right" id="lblPer5"></td>
			<th class="center">6월</th>
			<td class="right" id="lblCnt6"></td>
			<td class="right" id="lblPer6"></td>
			<th class="center">7월</th>
			<td class="right" id="lblCnt7"></td>
			<td class="right" id="lblPer7"></td>
			<th class="center">8월</th>
			<td class="right" id="lblCnt8"></td>
			<td class="right" id="lblPer8"></td>
		</tr>
		<tr>
			<th class="center">9월</th>
			<td class="right" id="lblCnt9"></td>
			<td class="right" id="lblPer9"></td>
			<th class="center">10월</th>
			<td class="right" id="lblCnt10"></td>
			<td class="right" id="lblPer10"></td>
			<th class="center">11월</th>
			<td class="right" id="lblCnt11"></td>
			<td class="right" id="lblPer11"></td>
			<th class="center">12월</th>
			<td class="right" id="lblCnt12"></td>
			<td class="right" id="lblPer12"></td>
		</tr>
	</tbody>
</table>
<div id="proc" style="position:absolute; top:0; left:0; z-index:1010; background-color:#ffffff; border:3px solid #666666; display:none;"></div>
<?
	/*
	SELECT yymm
	,      suga
	,      SUM(CASE gbn WHEN '1' THEN per ELSE 0 END) AS per
	,      SUM(CASE gbn WHEN '2' THEN cnt ELSE 0 END) AS cnt
	FROM   (
		   SELECT person.close_yymm AS yymm
		   ,      person.close_suga AS suga
		   ,      person.close_cnt AS cnt
		   ,      person.close_per AS per
		   ,      IFNULL(unit.unit_gbn,2) AS gbn
		   FROM   care_close_person AS person
		   LEFT   JOIN care_suga_unit AS unit
				  ON unit.year = LEFT(person.close_yymm,4)
				  AND unit.suga_cd = close_suga
		   WHERE  org_no = '201308009'
		   AND    close_sr = 'R'
		   AND    close_yymm >= '201311'
		   AND    close_yymm <= '201311'
		   ) AS t
	GROUP  BY yymm, suga
	 */
	include_once('../inc/_db_close.php');
?>