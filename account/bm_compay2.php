<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$power	= $_SESSION['userLevel'];
	$year	= Date('Y');
	$month	= Date('m');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});
		lfSearch();
	});

	function lfImport(month){
		var objModal = new Object();
		var url = './bm_compay_import.php';
		var style = 'dialogWidth:600px; dialogHeight:400px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.year	= $('#lblYYMM').attr('year');
		objModal.month	= month;

		window.showModalDialog(url, objModal, style);
	}

	function lfExport(month){
		var objModal = new Object();
		var url = './bm_company_import.php';
		var style = 'dialogWidth:400px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.year	= $('#lblYYMM').attr('year');
		objModal.month	= month;
		objModal.val	= __str2num($('#ID_CELL_E_'+month).text())

		window.showModalDialog(url, objModal, style);
	}

	function lfTraget(month){
		var objModal = new Object();
		var url = './bm_company_target.php';
		var style = 'dialogWidth:400px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.year	= $('#lblYYMM').attr('year');
		objModal.month	= month;
		objModal.val	= __str2num($('#ID_CELL_T_'+month).text())

		window.showModalDialog(url, objModal, style);
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_compay2_search.php'
		,	data :{
				'year':$('#lblYYMM').attr('year')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = __parseVal(data);

				for(var i=1; i<=12; i++){
					$('#ID_CELL_E_'+i).text(__num2str(row['E'+i]));
					$('#ID_CELL_I_'+i).text(__num2str(row['I'+i]));
					$('#ID_CELL_T_'+i).text(__num2str(row['T'+i]));
				}

				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSetIE(gbn, month, val){
		if (gbn == 'I'){
			$('#ID_CELL_I_'+month).text(__num2str(val));
		}else if (gbn == 'E'){
			$('#ID_CELL_E_'+month).text(__num2str(val));
		}else if (gbn == 'T'){
			$('#ID_CELL_T_'+month).text(__num2str(val));
		}
	}
</script>
<div class="title title_border">본사수입 및 비용관리</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="90px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td class="last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); lfSearch();" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); lfSearch();" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="right last"></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="100px" span="3">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">월</th>
			<th class="head">본사수입</th>
			<th class="head">본사비용</th>
			<th class="head">목표금액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody><?
		for($i=1; $i<=12; $i++){?>
			<tr>
				<td class="center"><?=$i;?>월</td>
				<td class="right" id="ID_CELL_I_<?=$i;?>"></td>
				<td class="right" id="ID_CELL_E_<?=$i;?>"></td>
				<td class="right" id="ID_CELL_T_<?=$i;?>"></td>
				<td class="left last">
					<span class="btn_pack small"><button onclick="lfImport('<?=$i;?>');">본사수입</button></span>
					<span class="btn_pack small"><button onclick="lfExport('<?=$i;?>');">본사비용</button></span>
					<span class="btn_pack small"><button onclick="lfTraget('<?=$i;?>');">목표금액</button></span>
				</td>
			</tr><?
		}?>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_db_close.php");
?>