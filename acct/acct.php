<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$type = $_GET['type'];
?>
<script type="text/javascript">
	var gQuickTop = 0;
	var gBodyHeight = 0;

	function lfMoveYear(aiPos){
		$('#lblYear').text(parseInt($('#lblYear').text()) + aiPos);

		if ('<?=$type;?>' == '32'){
			lfSetMonth();
		}

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
		,	url  :'./search.php'
		,	data :{
				'mode':'<?=$type;?>_1'
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

<form id="f" name="f" method="post" enctype="multipart/form-data">
<?
	if ($type == '1'	||
		$type == '11'	||
		$type == '51'	||
		$type == '61'	||
		$type == '62'	||
		$type == '63'	||
		$type == '64'){
		/*
			1	:
			11	:
			51	: 모바일 기관관리
			61	: 회계세무(대영)
			62	: 인사노무
			63	: 회계세무(한림)
			64	: 재무회계 커뮤니티
		 */
		include_once('./acct_center.php');

	}else if ($type == '2'){
		//SMS
		include_once('./sms_acct.php');

	}else if ($type == '12'){
		//스마트폰
		include_once('./smart_acct.php');

	}else if ($type == '21'){
		//은행관리
		include_once('./bank_center.php');

	}else if ($type == '31'){
		//기관관리업무
		include_once('./center_manager.php');
	}else if ($type == '32'){
		//기관요금관리
		include_once('./center_acct.php');
	}else if ($type == '33'){
		//기관교육비관리
		include_once('./center_edu.php');

	}else if ($type == '41'){
		//입금관리
		include_once('./acct_deposit.php');
	}else if ($type == '42'){
		//입금관리(엑셀)
		include_once('./acct_deposit_excel.php');

	}else if ($type == '71'){
		//재가관리 수가
		include_once('./care_suag.php');

	}else if ($type == 'CENTER_USE_STATE'){
		//기관이용현황
		include_once('./center_use_state.php');

	}else if ($type == 'SEMINAR_REQUEST'){
		//세미나 신청내역
		include_once('./seminar_request.php');

	}else if ($type == 'FA_REQUEST'){
		//재무회계 신청내역
		include_once('./fa_request.php');

	}else if ($type == 'UNREG_CMS_VISIT_LOG'){
		//CMS 미등록 기관 접속기록
		include_once('./unreg_cms_visit_log.php');

	}else if ($type == 'HOMEPAGE_REQUEST'){
		//테블릿 신청내역
		include_once('./homepage_request.php');

	}else if ($type == 'TABLET_REQUEST'){
		//테블릿 신청내역
		include_once('./tablet_request.php');

	}else if ($type == 'REPORT2014_REQUEST'){
		//평가자료 신청내역
		include_once('./report2014_request.php');

	}else if ($type == 'REPORT2014_COPY'){
		//팡가자료 복사
		include_once('./report2014_copy.php');

	}else if ($type == 'DAN_LIST' //주야간보호
			||$type == 'WMD_LIST' //복지용구
		){
		include_once('./sub_svc.php');

	}else if ($type == 'KACOLD'){
		include_once('./kacold.php');

	}else if ($type == 'MEDICAL_REQUEST'){
		//의료기관 신청내역
		include_once('./medical_request.php');

	}else if ($type == 'MEDICAL_CONNECT'){
		//의료기관 신청연결
		include_once('./medical_connect.php');

	}else if ($type == 'MEDICAL_REG'){
		//의료기관 등록
		include_once('./medical_list.php');

	}else if ($type == 'DOCTOR_REG'){
		//의사 등록
		include_once('./doctor_list.php');

	}else if ($type == 'MEDICAL_DOCTOR_CONNECT'){
		//의사 등록
		include_once('./medical_doctor_connect.php');

	}else{
		include('../inc/_http_home.php');
		exit;
	}
?>
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>