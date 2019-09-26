// 상세근무현황표
function workDetail(p_code, p_kind, p_year, p_month){
	var URL = 'yoyangsa_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_code,
				mKind:p_kind,
				mYear:p_year,
				mMonth:p_month,
				mGubun:'workDetail'
			},
			onSuccess:function (responseHttpObj) {
				myYoyangsa.innerHTML = responseHttpObj.responseText;
				workDetailSub(p_code, p_kind, p_year, p_month, '');
			}
		}
	);
}

// 상세근무현황표 요양사
function workDetailSub(p_code, p_kind, p_year, p_month, p_index){
	try{
		var yoyCode = document.getElementsByName('yoyCode[]')[p_index].value;
		var count = document.getElementById('yoyCount').value;
	}catch(e){
		var yoyCode = '';
		var count = 0;
	}
	var URL = 'work_detail_sub.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_code,
				mKind:p_kind,
				mYear:p_year,
				mMonth:p_month,
				mYoyCode:yoyCode
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
				
				for(var i=0; i<count; i++){
					document.getElementById('yoy_'+i).style.textDecoration = '';
				}
				document.getElementById('yoy_'+p_index).style.textDecoration = 'underline';
			}
		}
	);
}

// 상세근무현황표 출력
function workDetailPrint(){
	window.onbeforeprint = function(){
		document.getElementById('menuTop').style.display    = 'none';
		document.getElementById('menuLeft').style.display   = 'none';
		document.getElementById('myYoyList').style.display  = 'none';
		document.getElementById('myTitle').style.display    = 'none';
		document.getElementById('myWhere').style.display    = 'none';
	};

	window.onafterprint = function(){
		document.getElementById('menuTop').style.display    = '';
		document.getElementById('menuLeft').style.display   = '';
		document.getElementById('myYoyList').style.display  = '';
		document.getElementById('myTitle').style.display    = '';
		document.getElementById('myWhere').style.display    = '';
	};

	window.print();
}

// 개인급여대장
function personPayBook(p_code, p_kind, p_year){
	var URL = '../report/report_list_17.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mMenu:'1',
				mTab:'2',
				mIndex:'17',
				mCode:p_code,
				mKind:p_kind,
				mYear:p_year
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}