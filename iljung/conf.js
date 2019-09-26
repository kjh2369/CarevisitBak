/*********************************************************
 * 일정등록 팝업
 *********************************************************/
function _confShow(asYear,asMonth,asJumin,asSvcCd){
	var h = 750; //screen.availHeight;
    var w = 1065;
    var t = 0;
	var l = (screen.availWidth - w) / 2;
    
	var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=yes,status=no,resizable=yes';
    var url    = './conf.php';
		gPlanWin = window.open('', 'CONFSHOW', option);
		gPlanWin.opener = self;
		gPlanWin.focus();

	var parm = new Array();
		parm = {
			'code'	: $('#code').attr('value')
		,	'jumin'	: asJumin
		,	'year'	: asYear
		,	'month' : asMonth
		,	'svcCd' : asSvcCd
		};

	var form = document.createElement('form');
    var objs;
    for(var key in parm){
        objs = document.createElement('input');
        objs.setAttribute('type', 'hidden');
        objs.setAttribute('name', key);
        objs.setAttribute('value', parm[key]);
        
		form.appendChild(objs);
    }

    form.setAttribute('target', 'CONFSHOW');
    form.setAttribute('method', 'post');
    form.setAttribute('action', url);
    
	document.body.appendChild(form);
    
	form.submit();
}