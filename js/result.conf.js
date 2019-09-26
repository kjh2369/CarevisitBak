var ResultConfirm = function(){
	this.timer	= null;
	this.sec	= 0;
	this.times	= 0;

	this.code	= null;
	this.year	= null;
	this.month	= null;
	this.gubun	= null;
	this.show	= false;
	this.win	= null;

	//this.init();
}
var result_conf = new ResultConfirm();

ResultConfirm.prototype.init = function(){
	this.timer = setInterval('_timer(this)', 1000);
}

ResultConfirm.prototype.close = function(){
	clearInterval(this.timer);

	this.timer = null;
}

function _timer(object){
	/*
	var URL = '../work/result_message.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				code:this.code,
				year:this.year,
				month:this.month,
				gubun:this.gubun
			},
			onSuccess:function (responseHttpObj) {
				alert(responseHttpObj.responseText);
				result_conf.close();
				//this.init();
			}
		}
	);
	*/
}

ResultConfirm.prototype.conf = function(){
	if (this.gubun == 1){
		var URL = '../work/result_confirm.php';
	}else{
		var URL = '../work/result_salary.php';
	}
	
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				pos:1,
				code:this.code,
				year:this.year,
				month:this.month,
				gubun:this.gubun
			},
			onSuccess:function (responseHttpObj) {
				//responseHttpObj.responseText
				//this.init();
			}
		}
	);

	/*
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				pos:1,
				code:this.code,
				year:this.year,
				month:this.month,
				gubun:this.gubun
			},
			onSuccess:function (responseHttpObj) {
				//responseHttpObj.responseText
				//this.init();
			}
		}
	);
	*/
}

ResultConfirm.prototype.show_result = function(){
	this.win.close();
	/*
	var requet = getHttpRequest('../work/result_show_check.php');
	var w = 400;
	var h = 300;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;

	if (requet == 0) return;

	if (this.show == true){
		var win = window.open('../work/result_show_message.php?code='+this.code+'&year='+this.year+'&month='+this.month+'&gubun='+this.gubun, 'WORK_CONFIRM', 'left='+l+', top='+t+', width='+w+', height='+h+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=no, resizable=no');
	}else{
		location.replace('../work/result_show_message.php?code='+this.code+'&year='+this.year+'&month='+this.month+'&gubun='+this.gubun);	
	}
	*/
}

// 수급자 실적확정
function _result_confirm(code, year, month, gubun){
	var w = 900;
	var h = 600;
	var l = (window.screen.width  - w) / 2;
	var t = (window.screen.height - h) / 2;

	var win = window.open('../work/result_confirm.php?code='+code+'&year='+year+'&month='+month+'&gubun='+gubun, 'WORK_CONFIRM', 'left='+l+', top='+t+', width='+w+', height='+h+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
	
	/*
	var URL = 'result_confirm.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				code:code,
				year:year,
				month:month,
				gubun:gubun
			},
			onSuccess:function (responseHttpObj) {
				alert(responseHttpObj.responseText);
			}
		}
	);
	*/
}