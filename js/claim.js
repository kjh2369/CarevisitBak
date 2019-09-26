var popupWork = null;

// 공단청구 월별 리스트
function getMonthBill(myBody, p_ccode, p_mkind, p_year){
	var URL = 'center_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_ccode,
				mKind:p_mkind,
				mYear:p_year
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 공단청구 월별 상세리스트
function getDetailBill(myBody,p_ccode, p_mkind, p_year, p_month){
	var URL = 'center_detail.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				mCode:p_ccode,
				mKind:p_mkind,
				mYear:p_year,
				mMonth:p_month
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

// 청구내역 개이별 출력
function printPerson(p_ccode, p_mkind, p_ym, p_sugupja, p_rate){
	var width  = 900;
	var height = 700;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;
	var popup = window.open('pop_person.php?mCode='+p_ccode+'&mKind='+p_mkind+'&mYM='+p_ym+'&mSugupja='+p_sugupja+'&mRate='+p_rate, 'POPUP_PERSON', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}

// 기관부담금내역 출력
function printCenter(p_code, p_kind, p_year, p_month, p_type, p_sugupja){
	var width  = 900;
	var height = 700;
	var left = (window.screen.width  - width)  / 2;
	var top  = (window.screen.height - height) / 2;
	var popup = window.open('center_amt_pdf.php?code='+p_code+'&kind='+p_kind+'&year='+p_year+'&month='+p_month+'&type='+p_type+'&sugupja='+p_sugupja, 'POPUP_CENTER', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');
}



/**************************************************

	개인별 전체 출력

**************************************************/
function _show_person_print(mode, paper_dir){
	var f = document.f;
	
	switch(paper_dir){
		case 2: //세로
			var width  = 900;
			var height = 700;
			break;
		default: //가로
			var width  = 700;
			var height = 900;
	}
	
	var top    = (window.screen.height - height) / 2;
	var left   = (window.screen.width  - width)  / 2;

	__REPORT_WIN__ = window.open('about:blank','REPORT_SHOW','top='+top+',left='+left+',width='+width+',height='+height+',scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');

	f.target = 'REPORT_SHOW';
	f.action = '../claim/?type=pdf&mode='+mode+'&paper_dir='+paper_dir;
	f.submit();
	f.target = '_self';
}