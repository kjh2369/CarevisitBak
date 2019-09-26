<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$year	= Date('Y');
	$month	= Date('m');
?>
<script type="text/javascript">
	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'../supply/supply_stat.php'
		,	data :{
				'orgNo'	:$('#cboOrg').val()
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#ID_BODY').html(html);
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">수급내역현황</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="40px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td>
				<select id="cboOrg" style="width:auto;" onchange="lfSearch();"><?
				$sql = 'SELECT	DISTINCT m00_mcode AS org_no
						,		REPLACE(m00_store_nm, \'돌보인 방문요양센터 \', \'\') AS org_nm
						FROM	m00center
						INNER	JOIN	b02center
								ON		b02_center = m00_mcode
						WHERE	m00_domain = \''.$gDomain.'\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);?>
					<option value="<?=$row['org_no'];?>"><?=$row['org_nm'];?></option><?
				}

				$conn->row_free();?>
				</select>
			</td>
			<th class="center">년도</th>
			<td class="last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); lfSearch();" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); lfSearch();" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last">
				<div style="float:left; width:auto;"><?=$myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM")); lfSearch();');?></div>
			</td>
		</tr>
	</tbody>
</table>
<div id="ID_BODY"></div>
<?
	include_once("../inc/_db_close.php");
?>