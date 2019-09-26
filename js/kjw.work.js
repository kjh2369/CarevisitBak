function dayDiaryOk1(){
	var flag = document.getElementsByName('changeFlag[]');
	var work_fmtime = document.getElementsByName('workFmTime[]');
	var work_totime   = document.getElementsByName('workToTime[]');
	var check = false;

	for(var i=0; i<flag.length; i++){
		if (flag[i].value == 'Y'){
			check = true;

			if (!checkDate(work_fmtime[i].value)){
				alert('실적 시작시간 오류입니다. 확인하여 주십시오.');
				work_fmtime[i].focus();
				return;
			}

			if (!checkDate(work_totime[i].value) || work_fmtime[i].value == work_totime[i].value){
				alert('실적 종료시간 오류입니다. 확인하여 주십시오.');
				work_totime[i].focus();
				return;
			}

			break;
		}
	}

	if (!check){
		alert('변경된 내역이 없습니다.');
		return;
	}
	
	if (!confirm('입력하신 일정을 수정하시겠습니까?')){
		return;
	}

	document.f.action = 'day_diary_ok.php';
	document.f.submit();
}



/****************************************
재무회계서비스 레이어 팝업창 닫기(오늘하루만) 
****************************************/

function closeLayer(flag, layer)
{
	var obj  = window.event.srcElement;


	if ( flag )
	{
		__setCookie( layer, 'hide' , 1 );
	}
	document.all[layer].style.display = 'none';
}
