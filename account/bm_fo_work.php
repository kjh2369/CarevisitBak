<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$year	= Date('Y');
	$month	= Date('m');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_fo_work_search.php'
		,	data :{
				'orgNo'	:$('#cboOrgNo').val()
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('tbody',$('#ID_LIST')).html(html);
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>

<div class="title title_border">가족케어요양보호사 타수급 근무현황</div>

<table class="my_table" style="width:100%;">
	<colgroup><?
		if ($_SESSION['userLevel'] == 'A'){?>
			<col width="50px">
			<col width="50px"><?
		}?>
		<col width="50px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr><?
			if ($_SESSION['userLevel'] == 'A'){?>
				<th class="center">기관명</th>
				<td>
					<select id="cboOrgNo" style="width:auto;" onchange="lfSearch();"><?
						$sql = 'SELECT	DISTINCT m00_mcode AS org_no, m00_store_nm AS org_nm
								FROM	m00center
								INNER	JOIN	b02center
										ON		b02_center = m00_mcode
								WHERE	m00_domain = \''.$gDomain.'\'
								AND		m00_del_yn = \'N\'
								ORDER	BY org_no';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<option value="<?=$row['org_no'];?>"><?=$row['org_nm'];?></option><?
						}

						$conn->row_free();?>
					</select>
				</td><?
			}?>
			<th class="center">년월</th>
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
</table><?

$colgroup = '
	<col width="40px">
	<col width="80px">
	<col width="110px">
	<col width="100px">
	<col width="100px">
	<col width="80px">
	<col width="100px">
	<col width="100px">
	<col width="80px">
	<col>';?>

<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">요양보호사</th>
			<th class="head" rowspan="2">수급자(가족)</th>
			<th class="head" colspan="3">계획시간내역</th>
			<th class="head" colspan="3">실적시간내역</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">가족케어 시간</th>
			<th class="head">타수급케어 시간</th>
			<th class="head">수급자수</th>
			<th class="head">가족케어 시간</th>
			<th class="head">타수급케어 시간</th>
			<th class="head">수급자수</th>
		</tr>
	</thead>
</table>
<div id="ID_LIST">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody></tbody>
		<tfoot>
			<tr>
				<td class="bottom last"></td>
			</tr>
		</tfoot>
	</table>
</div>
<?
	include_once("../inc/_db_close.php");
?>