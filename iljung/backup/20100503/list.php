<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	/*
	 * 기능		: 기관등록 / 수정
	 * 작성자	: 김재용
	 * 일자		: 2011.03.21
	 */

	/*
	 * mode 설정
	 * 1 : 방문일정등록
	 * 2 : 방문일정조회
	 */
	$mode = $_REQUEST['mode'];
	$con2 = new connection();
	$code = $_SESSION["userCenterCode"];
	$year_min_max = $myF->year();

	$year = $_REQUEST['year'] != '' ? $_REQUEST['year'] : date('Y', mktime());
	$kind = $_REQUEST['kind'] != '' ? $_REQUEST['kind'] : $_SESSION["userCenterKind"][0];
?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>
<script type='text/javascript' src='../js/iljung.js'></script>
<script language='javascript'>
<!--
function search(){
	document.f.submit();
}
//-->
</script>
<form name="f" method="post">
<div class="title">방문일정(수급자)</div>
<table class="my_table my_border">
	<colgroup>
		<col width="45px">
		<col width="50px">
		<col width="70px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>년도</th>
			<td>
				<select name="year" style="width:auto;">
				<?
					for($i=$year_min_max[0]; $i<=$year_min_max[1]; $i++){?>
						<option value="<?=$i;?>"<? if($i == $year){echo "selected";}?>><?=$i;?>년</option><?
					}
				?>
				</select>
			</td>
			<th>기관분류</th>
			<td>
				<select name="kind" style="width:auto;">
				<?
					for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){?>
						<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $kind){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option><?
					}
				?>
				</select>
			</td>
			<td class="other" style="line-height:26px; padding-left:5px; vertical-align:top; padding-top:2px;">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="search();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border" style="margin-top:-1px; border-bottom:none;">
	<colgroup>
		<col width="50px">
		<col width="80px">
		<col width="80px">
		<col width="100px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">수급자명</th>
			<th class="head">등급</th>
			<th class="head">요양보호사</th>
			<th class="head last">월별일정</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select m03_name
			,      m03_jumin
			,      LVL.m81_name as lvl_name
			,      STP.m81_name as stp_name
			,      m03_key
			,      m03_gaeyak_fm
			,      m03_gaeyak_to
			,      yoyName
			  from (
				   select m03_name
				   ,      m03_jumin
				   ,      m03_ylvl
				   ,      m03_skind
				   ,      m03_key
				   ,      left(case when ifnull(m03_gaeyak_fm, '') != '' then ifnull(m03_gaeyak_fm, '') else date_format(now(), '%Y%m') end, 6) as m03_gaeyak_fm
				   ,      left(case when ifnull(m03_gaeyak_to, '') != '' then ifnull(m03_gaeyak_to, '') else '999912' end, 6) as m03_gaeyak_to
				   ,      m03_yoyangsa1_nm as yoyName
				   ,      m03_sdate
				   ,      m03_edate
					 from m03sugupja
					where m03_ccode = '$code'
					  and m03_mkind = '$kind'
					union all
				   select m03_name
				   ,      m31_jumin
				   ,      m31_level
				   ,      m31_kind
				   ,      m03_key
				   ,      left(case when ifnull(m03_gaeyak_fm, '') != '' then ifnull(m03_gaeyak_fm, '') else date_format(now(), '%Y%m') end, 6) as m03_gaeyak_fm
				   ,      left(case when ifnull(m03_gaeyak_to, '') != '' then ifnull(m03_gaeyak_to, '') else '999912' end, 6) as m03_gaeyak_to
				   ,      m03_yoyangsa1_nm as yoyName
				   ,      m31_sdate
				   ,      m31_edate
					 from m31sugupja
					inner join m03sugupja
					   on m03_ccode = m31_ccode
					  and m03_mkind = m31_mkind
					  and m03_jumin = m31_jumin
					where m31_ccode = '$code'
					  and m31_mkind = '$kind'
				  ) as sugupja
			 inner join m81gubun as LVL
				on LVL.m81_gbn = 'LVL'
			   and LVL.m81_code = sugupja.m03_ylvl
			 inner join m81gubun as STP
				on STP.m81_gbn = 'STP'
			   and STP.m81_code = m03_skind
			 where '$year' between left(case when ifnull(m03_gaeyak_fm, '') != '' then ifnull(m03_gaeyak_fm, '') else date_format(now(), '%Y') end, 4) and left(case when ifnull(m03_gaeyak_to, '') != '' then ifnull(m03_gaeyak_to, '') else '9999' end, 4)
			   and date_format(now(), '%Y%m%d') between sugupja.m03_sdate and sugupja.m03_edate
			 order by m03_name";

		$conn->query($sql);
		$row = $conn->fetch();
		$row_count = $conn->row_count();

		$cnt_client = $row_count;

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$sql = "select sum(case substring(t01_sugup_date, 5, 2) when '01' then 1 else 0 end) as mon01"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '02' then 1 else 0 end) as mon02"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '03' then 1 else 0 end) as mon03"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '04' then 1 else 0 end) as mon04"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '05' then 1 else 0 end) as mon05"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '06' then 1 else 0 end) as mon06"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '07' then 1 else 0 end) as mon07"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '08' then 1 else 0 end) as mon08"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '09' then 1 else 0 end) as mon09"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '10' then 1 else 0 end) as mon10"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '11' then 1 else 0 end) as mon11"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '12' then 1 else 0 end) as mon12"
				 . "  from t01iljung"
				 . " where t01_ccode = '".$code
				 . "'  and t01_mkind = '".$kind
				 . "'  and t01_jumin = '".$row['m03_jumin']
				 . "'  and left(t01_sugup_date, 4) = '".$year
				 . "'  and t01_del_yn = 'N'";
			$con2->query($sql);
			$iljung = $con2->fetch();
			$con2->row_free();
			?>
			<tr>
				<td class="center"><?=$i+1;?></td>
				<td class="left"><?=$row["m03_name"];?></td>
				<td class="center"><?=$row["lvl_name"];?></td>
				<td class="left"><?=$row["yoyName"];?></td>
				<td class="left last">
					<table>
					<tr>
					<?
						for($j=1; $j<=12; $j++){
							if ($j < 10){
								$curI = '0'.$j;
							}else{
								$curI = $j;
							}

							if (ceil($row['m03_gaeyak_fm']) > ceil($year.$curI)){
							?>
								<td style="width:35px; height:23px; border:none; padding:0,1px; margin:0; text-align:center;">&nbsp;</td>
							<?
							}else{
								if (ceil($row['m03_gaeyak_to']) < ceil($year.$curI)){
								?>
									<td style="width:35px; height:23px; border:none; padding:0,1px; margin:0; text-align:center;">&nbsp;</td>
								<?
								}else{
								?>
									<td style="width:35px; height:23px; border:none; padding:0,1px; margin:0; text-align:center;<? if($iljung['mon'.$curI] > 0){echo('background:url(\'../image/bg_calendar_y.gif\') no-repeat;');}else{echo('background:url(\'../image/bg_calendar_g.gif\') no-repeat;');}?>"><a href="#" onClick="<?=$mode == 1 ? '_setSugupjaReg' : '_setSugupjaSearch';?>('<?=$code;?>', '<?=$kind;?>', '<?=$row["m03_key"];?>', document.f.year.value, '<?=$curI;?>'); return false;"><?=$j;?>월</a></td>
								<?
								}
							}
						}
					?>
					</tr>
					</table>
				</td>
			</tr>
		<?
		}
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="left last bottom" colspan="5">
				<span>검색된 카운트 : <?=$cnt_client;?></span>
			</td>
		</tr>
	</tbody>
</table>

<input name="code" type="hidden" value="<?=$code;?>">
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>