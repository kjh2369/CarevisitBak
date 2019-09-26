<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$colgroup = '<col width="40px"><col width="80px"><col width="200px"><col width="70px"><col width="310px"><col>';
	$type = $_GET['type'];
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;

		__init_form(document.f);

		if ('<?=$type;?>' == '2' || '<?=$type;?>' == '12'){
			$('#txtEntDt').val(opener.entDt);
			$('#txtSeq').val(opener.seq);
		}else{
			self.close();
			return false;
		}

		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./acct_search.php'
		,	data :{
				'type':'<?=$type;?>_2'
			,	'entDt':opener.entDt
			,	'seq':opener.seq
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var val = data.split(String.fromCharCode(2));

				$('#regDt').val(val[0]);

				$('#proofY').text(val[1].substring(0,4));
				$('#proofM').text(val[1].substring(4,6));
				$('#proofD').text(val[1].substring(6,8));
				$('#proofNo').text(val[1].substring(8,13));

				$('#lblCd1').text(val[2]);
				$('#lblCd2').text(val[3]);
				$('#lblCd3').text(val[4]);
				$('#lblNm1').text(val[5]);
				$('#lblNm2').text(val[6]);
				$('#lblNm3').text(val[7]);

				if (val[2] && val[3] && val[4]){
					$('.clsCateGbn').show();
				}

				$('#optVat'+val[8]).attr('checked',true);
				$('#txtAmt').val(__num2str(val[9]));
				$('#lblVat').text(__num2str(val[10]));
				$('#lblTot').text(__num2str(__str2num(val[9])+__str2num(val[10])));

				$('#txtBizId').val(val[11]);
				$('#txtBizGroup').val(val[12]);
				$('#txtBizType').val(val[13]);

				$('#txtItem').val(val[14]);
				$('#txtOther').val(val[15]);

				lfGetProofNo(val[2]+val[3]+val[4]);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSelect(aiIdx){
		var obj = $('#rowId_'+aiIdx);

		opener.code = $('td', obj).eq(1).text();
		opener.name = $('td', obj).eq(2).text();

		self.close();
	}
</script>

<base target="_self">
<form name="f">
<?
	include_once('./acct_reg.php');
?>
</form>
<div id='divTemp' style='position:absolute; left:0; top:0; display:none; z-index:11;'></div>
<?
	include_once('../inc/_footer.php');
?>