<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_body_header.php');

	$code = $_SESSION['userCenterCode'];
?>
<form id="f" name="f">
<div class="title title_border">배상책임보험료 등록</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="160px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">배상책임보험 가입기간 선택</th>
			<td class="last">
				<select id="cboInsu" name="cbo" style="width:auto;"><?
				$sql = 'SELECT lst.seq AS seq
						,      lst.insu_cd AS cd
						,      g01_name AS nm
						,      lst.pay
						,      lst.from_dt
						,      lst.to_dt
						  FROM insu_center AS lst
						 INNER JOIN g01ins AS mst
						   ON mst.g01_code = lst.insu_cd
						 WHERE org_no = \''.$code.'\'
						 ORDER BY seq DESC';

				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);?>
					<option value="<?=$row['seq'];?>" insuNm="<?=$row['nm'];?>" pay="<?=$row['pay'];?>" from="<?=$row['from_dt'];?>" to="<?=$row['to_dt'];?>"><?=$myF->dateStyle($row['from_dt'],'.');?> ~ <?=$myF->dateStyle($row['to_dt'],'.');?></option><?
				}

				$conn->row_free();
				?>
				</select>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="55px">
		<col width="200px">
		<col width="45px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">보험사명</th>
			<td class="left"><span id="lblInsuNm"></span></td>
			<th class="center">보험료</th>
			<td class="left last"><span id="lblInsuPay">0</span></td>
			<td class="right last"><span class="btn_pack m"><button type="button" onclick="">적용</button></span></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="70px" span="2">
		<col width="460px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">보험가입일</th>
			<th class="head">보험해지일</th>
			<th class="head">급여공제적용</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="list"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<script type="text/javascript">
	$(document).ready(function(){
		$('#cboInsu').change();
	});

	$('#cboInsu').unbind('change').bind('change',function(){
		var name = $(this).children('option:selected').attr('insuNm');
		var pay  = $(this).children('option:selected').attr('pay');

		$('#lblInsuNm').text(name);
		$('#lblInsuPay').text(__num2str(pay));

		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./mem_insu_search.php'
		,	data :{
				'seq':$('#cboInsu').children('option:selected').val()
			,	'from':$('#cboInsu').children('option:selected').attr('from')
			,	'to':$('#cboInsu').children('option:selected').attr('to')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var html = '';
				var list = data.split(String.fromCharCode(1));

				for(var i=0; i<list.length; i++){
					if (list[i]){
						var val  = list[i].split(String.fromCharCode(2));

						html += '<tr onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">'
							 +  '<td class="center">'+(i+1)+'</td>'
							 +  '<td class="center"><div class="left nowrap" style="width:70px;">'+val[1]+'</div></td>'
							 +  '<td class="center">'+__replace(__getDate(val[2]),'-','.')+'</td>'
							 +  '<td class="center">'+__replace(__getDate(val[3]),'-','.')+'</td>'
							 +  '<td class="left">';

						var mon = __str2num(val[2].substring(4,6));

						for(var m=1; m<=12; m++){
							var cls = 'my_month';
							var stl = 'float:left; margin-right:3px; cursor:default;';
							var app = '';

							if (mon <= m){
								cls += ' my_month_1'; //' my_month_y';
								stl += ' color:#000000;';
								app  = 'N';
							}else{
								cls += ' my_month_1';
								stl += ' color:#cccccc;';
							}
							html += '<div class="'+cls+'" style="'+stl+'" value="'+m+'" apply="'+app+'" onclick="lfApplyMonth(this);">'+m+'월</div>';
						}

						html += '</td>'
							 +  '<td class="center last"></td>'
							 +  '</tr>';
					}
				}

				if (!html){
					 html = '<tr><td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('#list').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfApplyMonth(obj){
		if ($(obj).attr('apply') == 'Y'){
			$(obj).attr('apply','N').removeClass('my_month_y').addClass('my_month_1');
		}else if ($(obj).attr('apply') == 'N'){
			$(obj).attr('apply','Y').removeClass('my_month_1').addClass('my_month_y');
		}
	}
</script>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>