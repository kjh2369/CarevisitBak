/*********************************************************
 * 바우처 생성
 *********************************************************/
function _iljungMakeVoucher(type, code, svcCd, jumin, year, month){
	var h = screen.availHeight;
    var w = screen.availWidth;
    var t = 50;
    
	if(w >= 800) w = 800;
    
	h = 100;
	l = (screen.availWidth - w) / 2;

	var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=yes';
    var url    = './iljung_voucher.php';
	var win    = window.open('', 'ILJUNG_VOUCHER', option);
		win.opener = self;
		win.focus();

	var parm = new Array();
		parm = {
			code  : code
		,	svcCd : svcCd
		,	jumin : jumin
		,	year  : year
		,   month : month
		,	type  : type
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

    form.setAttribute('target', 'ILJUNG_VOUCHER');
    form.setAttribute('method', 'post');
    form.setAttribute('action', url);
    
	document.body.appendChild(form);
    
	form.submit();
}

/*********************************************************
 * 등록가능한 월
 *********************************************************/
function _iljungLoadInfo(mode){
	var html = '';
	
	$.ajax({
		type: 'POST'
	,	url : './iljung_fun.php'
	,	data: {
			code   : $('#code').val()
		,	jumin  : $('#jumin').val()
		,	svcCd  : $('#svcCd').val()
		,	year   : $('#year').val()
		,	month  : $('#month').val()
		,	mode   : mode
		}
	,	success: function (result){
			var val = __parseStr(result);
			
			switch(mode){
				case 1:
					$('#cNm').text(val['name']);
					$('#cJumin').text(val['jumin']);
					$('#cPhone').text(val['phone']);
					$('#cMobile').text(val['mobile']);
					$('#cParent').text(val['parent']);
					$('#cParentTel').text(val['parentTel']);
					$('#cAddr').text(val['addr']);
					
					break;

				case 2:
					$('#disLvl').attr('value',val['lvl']).text(val['lvlNm']);

					if (val['expense'] > 0)
						$('#cExpense').text(__num2str(val['expense']));
					else
						$('#cExpense').text('전액 본인 부담');

					break;

				case 3:
					for(var i=1; i<val.length; i++){
						//if (val[i] == 'Y'){
						$('#loMon'+i).css('color','#000000')
									 .css('cursor','pointer')
									 .attr('value1','Y');

						if (i == parseInt($('#month').val(),10)){
							$('#loMon'+i).removeClass('my_month_1').addClass('my_month_y');
						}else{
							$('#loMon'+i).removeClass('my_month_y').addClass('my_month_1');
						}
						//}
					}

					$('div[id^="loMon"][value1="Y"]').unbind('click').click(function(){
						lfMoveMonth($(this).attr('value'));
					});

					break;

				case 41:
					$('#loSvcStndHtml').attr('value', val['add1']);
					$('#loSvcAddHtml').attr('value', val['add2']);
					$('#sidoTime').val(val['sido']);
					$('#jachTime').val(val['jach']);
					
					lfInitSJPay($('#sidoTime'),'sido');
					lfInitSJPay($('#jachTime'),'jach');

					//_iljungLoadInfo(42);
					_iljungLoadInfo(43);
					break;

				case 42:
					//$('#stndTot').attr('value',val['amt']).text(__num2str(val['amt']));
					//$('#stndTime').attr('value',val['time']).text(val['time']);
					$('#stndSupport').attr('value',val['support']).text(__num2str(val['support']));
					$('#stndExpense').attr('value',val['expense']).text(__num2str(val['expense']));

					html = '<input id="stnd1" name="stnd" type="radio" value="" value1="'+val['lvl']+'" value2="'+val['val']+'" class="radio"><label for="stnd1">해당없음</label>';
					

					if (val['val'] && val['lvl'])
						html += '<input id="stnd2" name="stnd" type="radio" value="'+val['val']+'" value1="'+val['lvl']+'" value2="'+val['val']+'" class="radio"><label for="stnd2">'+(val['val'] == '1' || val['val'] == '3' ? '성인' : '아동')+'/'+val['lvl']+ (val['val'] == '3' ? '구간' : '등급')+'</label>';
						
					//if ($('#code').val() == '1234'){
					//	alert(val['amt']+'/'+val['time']);
					//	alert(result);
					//}
					
					$('#loSvcStndHtml').html(html);
					
					$('input:radio[name="stnd"]:input[value="'+val['val']+'"]').attr('checked',true);
					$('input:radio[name="stnd"]').unbind('click').click(function(){
						
						if (!$(this).val()){
							$('#stndTot').attr('value',0).text('0');
							$('#stndTime').attr('value',0).text('0');
							$('#stndSupport').attr('value',0).text('0');
							$('#stndExpense').attr('value',0).text('0');
							$('#stndTot2').attr('value',0).text('0');
							$('#stndTime2').attr('value',0).text('0');
							$('#stndSupport2').attr('value',0).text('0');
							$('#stndExpense2').attr('value',0).text('0');
						}else{
							$('#stndTot').attr('value',val['amt']).text(__num2str(val['amt']));
							$('#stndTime').attr('value',val['time']).text(val['time']);
							$('#stndSupport').attr('value',val['support']).text(__num2str(val['support']));
							$('#stndExpense').attr('value',val['expense']).text(__num2str(val['expense']));
							$('#stndTot2').attr('value',val['amt']).text(__num2str(val['amt']));
							$('#stndTime2').attr('value',val['time']).text(val['time']);
							$('#stndSupport2').attr('value',val['support']).text(__num2str(val['support']));
							$('#stndExpense2').attr('value',val['expense']).text(__num2str(val['expense']));
						}
						lfTotAddPay();
						//lfTotAddPayNew();
						
					});
					
					break;

				case 43:
					var tmpGbn = '';
					
					for(var i=0; i<val.length; i++){
						str = val[i].split('/');

						if (tmpGbn != str[0]){
							if (tmpGbn == ''){
								html = '<div style="margin-bottom:2px; padding-bottom:3px; border-bottom:1px solid #cccccc;">';
							}else{
								html += '</div><div>';
							}
							tmpGbn = str[0];
						}

						html += '<div style="float:left; width:47%;">';
						html += '<input id="addPay'+str[0]+'_'+str[1]+'" name="addPay'+str[0]+'" type="'+(tmpGbn == '1' ? 'radio' : 'checkbox')+'" value="'+str[1]+'" value1="'+str[3]+'" value2="'+str[4]+'" class="'+(tmpGbn == '1' ? 'radio' : 'checkbox')+' clsAddPay"><label for="addPay'+str[0]+'_'+str[1]+'">'+str[2]+(str[4] != '0' ? '('+str[4]+'시간)' : '')+'</label>';
						html += '</div>';
					}

					html += '</div>';
					
					$('#loSvcAddHtml').html(html);
					
					$('input:radio[name="addPay1"]:input[value="'+$('#loSvcStndHtml').attr('value')+'"]').attr('checked',true);
					
					var str = $('#loSvcAddHtml').attr('value').split('/');

					for(var i=1; i<str.length; i++){
						$('input:checkbox[name="addPay2"]:input[value="'+str[i]+'"]').attr('checked',true);
					}

					$('.clsAddPay').unbind('click').click(function(){
						lfInitAddPay();
					});

					lfInitAddPay();
					
					break;
			}
		}
	}).responseXML;
}

/*********************************************************
 * 일정출력1(금액표시)
 *********************************************************/
function _iljungPDFShow(asType, asCode, asSvcCd, asYear, asMonth, asJumin){
	var lsType = asType; //1:금액표시, 2:금액미표시
	//iljung_pdf.php

	$arguments = 'root=iljung'
		  + '&dir=P'
		  + '&fileName=iljung'
		  + '&fileType=pdf'
		  + '&target=show.php'
		  + '&showForm=Iljung'
		  + '&code='+asCode
		  + '&svcCd='+asSvcCd
		  + '&year='+asYear
		  + '&month='+asMonth
		  + '&jumin='+asJumin;

	__printPDF($arguments);
}