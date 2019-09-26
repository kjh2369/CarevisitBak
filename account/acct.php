<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$type = $_GET['type'];
?>
<script type="text/javascript">
	$(document).ready(function(){

	});

	function lfMoveYear(aiPos){
		$('#lblYear').text(parseInt($('#lblYear').text()) + aiPos);
		lfSearch();
	}

	function lfMoveMonth(aiMonth){
		$(document).find('.my_month').each(function(){
			if ($(this).attr('id').toString().substr($(this).attr('id').toString().length - aiMonth.toString().length - 1, $(this).attr('id').toString().length) == '_'+aiMonth.toString()){
				if ($(this).attr('css')){
					$(this).removeClass($(this).attr('css'));
				}else{
					$(this).removeClass('my_month_1');
				}

				$(this).addClass('my_month_y');
			}else{
				$(this).removeClass('my_month_y');

				if ($(this).attr('css')){
					$(this).addClass($(this).attr('css'));
				}else{
					$(this).addClass('my_month_1');
				}
			}
		});
		$('#txtMonth').val(aiMonth);
		lfSearch();
	}

	function lfSetMonth(){
		$.ajax({
			type :'POST'
		,	url  :'./acct_search.php'
		,	data :{
				'type':'<?=$type;?>_1'
			,	'year':$('#lblYear').text()
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var val = data.split(String.fromCharCode(2));

				for(var i=0; i<val.length; i++){
					var mon = i+1;
					var obj = $('#btnMonth_'+mon);

					if (val[i] > 0){
						$(obj).removeClass('my_month_1');
						$(obj).addClass('my_month_r');
						$(obj).attr('css','my_month_r');
					}else{
						$(obj).removeClass('my_month_r');
						$(obj).addClass('my_month_1');
						$(obj).attr('css','my_month_1');
					}

					if (mon == $('#txtMonth').val()){
						$(obj).removeClass('my_month_1');
						$(obj).removeClass('my_month_r');
						$(obj).addClass('my_month_y');
					}
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>

<form name="f" id="f">
<?
	if ($gDomain == 'dolvoin.net'){
		if ($type == '2' || $type == 'BM_CHARGE'){
			include_once('./bm_charge.php');
		}else if ($type == 'BM_ADMIN'){
			include_once('./bm_admin.php');
		}else if ($type == 'BM_DISPS'){
			include_once('./bm_disps.php');
		}else if ($type == 'BM_CLOSE'){
			include_once('./bm_close.php');
		}else if ($type == 'BM_STATE'){
			include_once('./bm_state.php');
		}else if ($type == 'BM_COMPAY'){
			//include_once('./bm_compay.php');
			include_once('./bm_compay2.php');
		}else if ($type == 'BM_PSNURSE'){
			include_once('./bm_psnurse.php');
		}else if ($type == 'BM_IE'){
			include_once('./bm_ie.php');
		}else if ($type == 'BM_EXPENSE'){
			include_once('./bm_expense.php');
		}else if ($type == 'BM_RETIRE'){
			include_once('./bm_retire.php');
		}else if ($type == 'BM_TARGET'){
			include_once('./bm_target.php');
		}else if ($type == 'BM_ADMIN_EXPENSE'){
			include_once('./bm_admin_expense.php');
		}else if ($type == 'BM_ADMIN_RETIRE'){
			include_once('./bm_admin_retire.php');
		}else if ($type == 'BM_CLIENT_STAT'){
			include_once('./bm_client_stat.php');
		}else if ($type == 'BM_FO_WORK'){
			include_once('./bm_fo_work.php');
		}else if ($type == 'BM_RETIRECHKLIST'){
			include_once('../salaryNew/retire_chk.php');
		}else if ($type == 'BM_RETIRESEARCH'){
			include_once('../salaryNew/retire_search.php');
		}
	}else{
		if ($type == '1' || $type == '11'){
			//입,출금등록
			include_once('./acct_reg.php');
		}else if ($type == '2' || $type == '12'){
			//입,출금조회
			include_once('./acct_list.php');
		}else if ($type == '3' || $type == '13'){
			//입,출금집계
			include_once('./acct_sum.php');
		}else if ($type == 'BM_FO_WORK'){
			include_once('./bm_fo_work.php');
		}else{
			include('../inc/_http_home.php');
			exit;
		}
	}
?>
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>