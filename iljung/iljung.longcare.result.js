/*********************************************************

	�Է°��

*********************************************************/
function _iljungCareResultExe(YYMM, payCtrNo, longTermMgmtNo, svcKind, longTermMgmtSeq,tgtDemoChasu,jumin,admtGradeCd){
	var style = 'height:25px; line-height:27px;';

	if (svcKind == '002'){
		style = 'height:50px;';
	}

	var familyRel = new Array();
		familyRel['S031'] = 'ó';
		familyRel['S032'] = '����';
		familyRel['S033'] = '��';
		familyRel['S034'] = '�ں�';
		familyRel['S035'] = '����';
		familyRel['S036'] = '�����ڸ�';
		familyRel['S037'] = '��';
		familyRel['S038'] = '������������ڸ�';
		familyRel['S039'] = '�ܼ�';
		familyRel['S040'] = '�θ�';
		familyRel['S041'] = '��Ÿ';

	clearInterval(g_Timer);
	g_Timer = null;
	g_Int   = 0;

	try{
		$.ajax({
			type: 'POST',
			url : 'http://www.longtermcare.or.kr/portal/nypa/nypa_Nctrpay.do?method=JU',
			data: {
			/*
				'payCtrNo'		: payCtrNo
			,	'payMm'			: YYMM
			,	'longTermMgmtNo': longTermMgmtNo
			,	'longTermMgmtSeq':longTermMgmtSeq
			,	'serviceKind'	: svcKind
			,	'tgtDemoChasu':	tgtDemoChasu
			,	'tgtJuminNo':jumin
			,	'fnc'			: 'select'
			*/
			
				'longTermAdminSym'	: $('#giho').attr('value')
			,	'payCtrNo'			: payCtrNo
			,	'payMm'				: YYMM
			,	'longTermMgmtNo'	: longTermMgmtNo
			,	'longTermMgmtSeq'	: longTermMgmtSeq
			,	'tgtJuminNo'		: jumin
			,	'serviceKind'		: svcKind
			,	'adminDemoChasu'	: '01'
			,	'tgtDemoChasu'		: tgtDemoChasu
			,	'admtGradeCd'		: admtGradeCd
			,	'fnc'				: 'select'
			},
			beforeSend: function(){
			},
			success: function(data){
				if ($('#TableData3 tr', data).length > 1){
					if (g_Timer != null){
						clearInterval(g_Timer);
						g_Timer = null;
						g_Int   = 0;
					}
				}else{
					/*
					g_Int ++;

					if (g_Int > 10){
						clearInterval(g_Timer);
						g_Timer = null;
						g_Int   = 0;
						alert('false');
					}
					*/
				}

				data = data.split('<b>').join('').split('</b>').join('');

				var tbl   = $('.default', $('#TableData1', data));
				var cNM   = $('tr:first td', tbl).eq(0).text(); //������
				var appDT = $('tr:first td', tbl).eq(1).text(); //�����Ⱓ
				var conDT = $('tr:first td', tbl).eq(2).text(); //���Ⱓ

				//var cNM   = $('#TableData1 tr:first td', data).eq(1).text(); //������
				//var appDT = $('#TableData1 tr:first td', data).eq(2).text(); //�����Ⱓ
				//var conDT = $('#TableData1 tr:first td', data).eq(3).text(); //���Ⱓ
				var html = '<div id=\'longcareDataBody\'>'
						 + '<div style=\'clear:both; height:30px; line-height:30px; padding-left:10px; font-weight:bold; border-bottom:2px solid #0000ff;\'>> �Ǻ� �޿���೻��(�簡)</div>'
						 + '<div style=\'clear:both;\'>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; border-left:1px solid #cccccc; background-color:#efefef; font-weight:bold;\'>������</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+cNM+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>�����Ⱓ</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+appDT+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>���Ⱓ</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+conDT+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; float:right; border:none;\'>[<a href=\'#\' onclick=\'_iljungCareResultExe("'+YYMM+'","'+payCtrNo+'","'+longTermMgmtNo+'","'+svcKind+'","'+longTermMgmtSeq+'","'+tgtDemoChasu+'");\'>���ΰ�ħ</a>]</div>'
						 + '</div>'
						 + '<div style=\'clear:both; height:30px; line-height:30px; padding-left:10px; font-weight:bold; border-bottom:2px solid #0000ff;\'>> ������ϳ���</div>'
						 + '<div id=\'longcareDataBodyList\' style=\'width:1050px;\'>'
						 + '<div style=\'clear:both; width:auto;\'>'
							+ '<div class=\'lcDiv\' style=\'width:70px; border-left:1px solid #cccccc; background-color:#efefef; font-weight:bold;\'>����</div>'
							+ '<div class=\'lcDiv\' style=\'width:80px; background-color:#efefef; font-weight:bold;\'>������</div>'
							+ '<div class=\'lcDiv\' style=\'width:40px; background-color:#efefef; font-weight:bold;\'>����</div>'
							+ '<div class=\'lcDiv\' style=\'width:50px; background-color:#efefef; font-weight:bold;\'>����</div>'
							+ '<div class=\'lcDiv\' style=\'width:70px; background-color:#efefef; font-weight:bold;\'>�ð�</div>'
							+ '<div class=\'lcDiv\' style=\'width:40px; background-color:#efefef; font-weight:bold;\'>Ƚ��</div>';

				var w = 45+80+40+50+70+40+50;
				
				$('#TableData3 tr:first [id~="dt"]', data).each(function(){
					var lsDt = $(this).text();
						lsDt = lsDt.split('(��)').join('');
						lsDt = lsDt.split('(ȭ)').join('');
						lsDt = lsDt.split('(��)').join('');
						lsDt = lsDt.split('(��)').join('');
						lsDt = lsDt.split('(��)').join('');
						lsDt = lsDt.split('(��)').join('');
						lsDt = lsDt.split('(��)').join('');

					html += '<div class=\'lcDiv\' style=\'width:20px; background-color:#efefef; font-weight:bold; color:'+$(this).css('color')+';\'>'+lsDt+'</div>';
					w += 20;
				});

				html += '</div>';
				
				var i = -1;

				$('#TableData3 tr', data).each(function(){
					if (i < 0){
					}else{
						html += '<div style=\'clear:both;\'>';				

						//����
						html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:25px;' : '')+'width:70px; border-left:1px solid #cccccc;\'>';
						html += $('#careNm'+i, this).text();

						if ($('#careNm2'+i, this).text() != ''){
							html += '<br>'+$('#careNm2'+i, this).text();
						}

						html += '</div>';
						
						//����
						html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:17px;' : '')+'width:80px;\'>'+$('#sugaNm'+i, this).text().split('(�湮��)').join('')+'</div>';

						//��������
						html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:25px;' : '')+'width:40px;\'>';
						html += $('#familyYn1'+i, this).attr('checked') ? 'Y' : '';

						if ($('#careNm2'+i, this).text() != ''){
							html += '<br>'+($('#familyYn2'+i, this).attr('checked') ? 'Y' : '');
						}

						html += '</div>';
						
						//��������
						html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:25px;' : '')+'width:50px;\'>';
						html += ($('#_familyRel1'+i, this).val() != '00' ? '<span title=\''+familyRel[$('#_familyRel1'+i, this).val()]+'\'>'+$('#_familyRel1'+i, this).val()+'</span>' : '');

						if ($('#careNm2'+i, this).text() != ''){
							html += '<br>'+($('#_familyRel2'+i, this).val() != '00' ? '<span title=\''+familyRel[$('#_familyRel2'+i, this).val()]+'\'>'+$('#_familyRel2'+i, this).val()+'</span>' : '');
						}

						html += '</div>';

						//�ð�
						html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:50px;' : '')+'width:70px;\'>'+$('#serviceTm'+i, this).text()+'</div>';
						
						//����
						var cnt = 0;
						var day = '';
						var htmlSub = '';
						$('input:checkbox[name="payDt'+i+'"]', this).each(function(){
							if ($(this).attr('checked')){
								day = 'Y';
								cnt ++;
							}else{
								day = '&nbsp;';
							}

							htmlSub += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:50px;' : '')+'width:20px;\'>'+day+'</div>';
						});

						//Ƚ��
						html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:50px;' : '')+'width:40px;\'>'+cnt+'</div>'+htmlSub;
						
						html += '</div>';
					}

					i ++;
				});

				html += '</div>';
		
				html += '<div style=\'clear:both; height:30px; line-height:30px; padding-left:10px; font-weight:bold; border-bottom:2px solid #0000ff;\'>> �̿�����</div>'
					 +  '<div style=\'clear:both;\'>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; border-left:1px solid #cccccc; background-color:#efefef; font-weight:bold;\'>�̿�Ƚ��</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#useCount', data).text()+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>���ݾ�</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#mmAmt', data).text()+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>Ÿ������� ���ݾ�</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#mmAmtAll', data).text()+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>���ѵ���</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#mmLimitAmt', data).text()+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>�ʰ��ݾ�</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#mmExcsAmt', data).text()+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; float:right; border:none;\'>[<a href=\'#\' onclick=\'self.close();\'>�ݱ�</a>]</div>'
						 + '</div>'
						 + '</div>';

				$('#longcareData')
					.css('left','0')
					.css('top','0')
					.css('width','100%')
					.css('height','auto')
					.html(html)
					.show();

				var h = $('#longcareDataBody').height() + 110;
				
				w += 50;

				window.resizeTo(w, h);
			},
			error: function (){
			}
		}).responseXML;
	}catch(e){
	}
}

function _iljungCareResultTest(){
	var style = 'height:25px; line-height:27px;';
	var svcKind = '001';
	
	var familyRel = new Array();
		familyRel['S031'] = 'ó';
		familyRel['S032'] = '����';
		familyRel['S033'] = '��';
		familyRel['S034'] = '�ں�';
		familyRel['S035'] = '����';
		familyRel['S036'] = '�����ڸ�';
		familyRel['S037'] = '��';
		familyRel['S038'] = '������������ڸ�';
		familyRel['S039'] = '�ܼ�';
		familyRel['S040'] = '�θ�';
		familyRel['S041'] = '��Ÿ';

	clearInterval(g_Timer);
	g_Timer = null;
	g_Int   = 0;

	try{
		$.ajax({
			type: 'POST',
			url : './lc_test.html',
			data: {
			},
			beforeSend: function(){
			},
			success: function(data){
				data = data.split('<b>').join('').split('</b>').join('');

				var tbl   = $('.default', $('#TableData1', data));
				var cNM   = $('tr:first td', tbl).eq(0).text(); //������
				var appDT = $('tr:first td', tbl).eq(1).text(); //�����Ⱓ
				var conDT = $('tr:first td', tbl).eq(2).text(); //���Ⱓ
				var html = '<div id=\'longcareDataBody\'>'
						 + '<div style=\'clear:both; height:30px; line-height:30px; padding-left:10px; font-weight:bold; border-bottom:2px solid #0000ff;\'>> �Ǻ� �޿���೻��(�簡)</div>'
						 + '<div style=\'clear:both;\'>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; border-left:1px solid #cccccc; background-color:#efefef; font-weight:bold;\'>������</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+cNM+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>�����Ⱓ</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+appDT+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>���Ⱓ</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+conDT+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; float:right; border:none;\'>[<a href=\'#\' onclick=\'\'>���ΰ�ħ</a>]</div>'
						 + '</div>'
						 + '<div style=\'clear:both; height:30px; line-height:30px; padding-left:10px; font-weight:bold; border-bottom:2px solid #0000ff;\'>> ������ϳ���</div>'
						 + '<div id=\'longcareDataBodyList\' style=\'width:1050px;\'>'
						 + '<div style=\'clear:both; width:auto;\'>'
							+ '<div class=\'lcDiv\' style=\'width:70px; border-left:1px solid #cccccc; background-color:#efefef; font-weight:bold;\'>����</div>'
							+ '<div class=\'lcDiv\' style=\'width:80px; background-color:#efefef; font-weight:bold;\'>������</div>'
							+ '<div class=\'lcDiv\' style=\'width:40px; background-color:#efefef; font-weight:bold;\'>����</div>'
							+ '<div class=\'lcDiv\' style=\'width:50px; background-color:#efefef; font-weight:bold;\'>����</div>'
							+ '<div class=\'lcDiv\' style=\'width:70px; background-color:#efefef; font-weight:bold;\'>�ð�</div>'
							+ '<div class=\'lcDiv\' style=\'width:40px; background-color:#efefef; font-weight:bold;\'>Ƚ��</div>';

				var w = 45+80+40+50+70+40+50;
				
				$('#TableData3 tr:first [id~="dt"]', data).each(function(){
					var lsDt = $(this).text();
						lsDt = lsDt.split('(��)').join('');
						lsDt = lsDt.split('(ȭ)').join('');
						lsDt = lsDt.split('(��)').join('');
						lsDt = lsDt.split('(��)').join('');
						lsDt = lsDt.split('(��)').join('');
						lsDt = lsDt.split('(��)').join('');
						lsDt = lsDt.split('(��)').join('');

					html += '<div class=\'lcDiv\' style=\'width:20px; background-color:#efefef; font-weight:bold; color:'+$(this).css('color')+';\'>'+lsDt+'</div>';
					w += 20;
				});

				html += '</div>';
				
				var i = -1;

				$('#TableData3 tr', data).each(function(){
					if (i < 0){
					}else{
						html += '<div style=\'clear:both;\'>';				

						//����
						html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:25px;' : '')+'width:70px; border-left:1px solid #cccccc;\'>';
						html += $('#careNm'+i, this).text();

						if ($('#careNm2'+i, this).text() != ''){
							html += '<br>'+$('#careNm2'+i, this).text();
						}

						html += '</div>';
						
						//����
						html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:17px;' : '')+'width:80px;\'>'+$('#sugaNm'+i, this).text().split('(�湮��)').join('')+'</div>';

						//��������
						html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:25px;' : '')+'width:40px;\'>';
						html += $('#familyYn1'+i, this).attr('checked') ? 'Y' : '';

						if ($('#careNm2'+i, this).text() != ''){
							html += '<br>'+($('#familyYn2'+i, this).attr('checked') ? 'Y' : '');
						}

						html += '</div>';
						
						//��������
						html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:25px;' : '')+'width:50px;\'>';
						html += ($('#_familyRel1'+i, this).val() != '00' ? '<span title=\''+familyRel[$('#_familyRel1'+i, this).val()]+'\'>'+$('#_familyRel1'+i, this).val()+'</span>' : '');

						if ($('#careNm2'+i, this).text() != ''){
							html += '<br>'+($('#_familyRel2'+i, this).val() != '00' ? '<span title=\''+familyRel[$('#_familyRel2'+i, this).val()]+'\'>'+$('#_familyRel2'+i, this).val()+'</span>' : '');
						}

						html += '</div>';

						//�ð�
						html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:50px;' : '')+'width:70px;\'>'+$('#serviceTm'+i, this).text()+'</div>';
						
						//����
						var cnt = 0;
						var day = '';
						var htmlSub = '';
						$('input:checkbox[name="payDt'+i+'"]', this).each(function(){
							if ($(this).attr('checked')){
								day = 'Y';
								cnt ++;
							}else{
								day = '&nbsp;';
							}

							htmlSub += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:50px;' : '')+'width:20px;\'>'+day+'</div>';
						});

						//Ƚ��
						html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:50px;' : '')+'width:40px;\'>'+cnt+'</div>'+htmlSub;
						
						html += '</div>';
					}

					i ++;
				});

				html += '</div>';
		
				html += '<div style=\'clear:both; height:30px; line-height:30px; padding-left:10px; font-weight:bold; border-bottom:2px solid #0000ff;\'>> �̿�����</div>'
					 +  '<div style=\'clear:both;\'>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; border-left:1px solid #cccccc; background-color:#efefef; font-weight:bold;\'>�̿�Ƚ��</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#useCount', data).text()+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>���ݾ�</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#mmAmt', data).text()+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>Ÿ������� ���ݾ�</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#mmAmtAll', data).text()+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>���ѵ���</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#mmLimitAmt', data).text()+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>�ʰ��ݾ�</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#mmExcsAmt', data).text()+'</div>'
							+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; float:right; border:none;\'>[<a href=\'#\' onclick=\'self.close();\'>�ݱ�</a>]</div>'
						 + '</div>'
						 + '</div>';

				$('#longcareData')
					.css('left','0')
					.css('top','0')
					.css('width','100%')
					.css('height','auto')
					.html(html)
					.show();

				var h = $('#longcareDataBody').height() + 110;
				
				w += 50;

				window.resizeTo(w, h);
			},
			error: function (){
			}
		}).responseXML;
	}catch(e){
	}
}

function _iljungCareResultData(YYMM, payCtrNo, longTermMgmtNo, svcKind, data,longTermMgmtSeq,tgtDemoChasu){
	var style = 'height:25px; line-height:27px;';
	
	if (svcKind == '002'){
		style = 'height:50px;';
	}
	

	var familyRel = new Array();
		familyRel['S031'] = 'ó';
		familyRel['S032'] = '����';
		familyRel['S033'] = '��';
		familyRel['S034'] = '�ں�';
		familyRel['S035'] = '����';
		familyRel['S036'] = '�����ڸ�';
		familyRel['S037'] = '��';
		familyRel['S038'] = '������������ڸ�';
		familyRel['S039'] = '�ܼ�';
		familyRel['S040'] = '�θ�';
		familyRel['S041'] = '��Ÿ';


	var cNM   = $('#TableData1 tr:first td', data).eq(1).text(); //������
	var appDT = $('#TableData1 tr:first td', data).eq(2).text(); //�����Ⱓ
	var conDT = $('#TableData1 tr:first td', data).eq(3).text(); //���Ⱓ
	var html = '<div id=\'longcareDataBody\'>'
			 + '<div style=\'clear:both; height:30px; line-height:30px; padding-left:10px; font-weight:bold; border-bottom:2px solid #0000ff;\'>> �Ǻ� �޿���೻��(�簡)</div>'
			 + '<div style=\'clear:both;\'>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; border-left:1px solid #cccccc; background-color:#efefef; font-weight:bold;\'>������</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+cNM+'</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>�����Ⱓ</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+appDT+'</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>���Ⱓ</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+conDT+'</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; float:right; border:none;\'>[<a href=\'#\' onclick=\'_iljungCareResultExe("'+YYMM+'","'+payCtrNo+'","'+longTermMgmtNo+'","'+svcKind+'","'+longTermMgmtSeq+'","'+tgtDemoChasu+'");\'>���ΰ�ħ</a>]</div>'
			 + '</div>'
			 + '<div style=\'clear:both; height:30px; line-height:30px; padding-left:10px; font-weight:bold; border-bottom:2px solid #0000ff;\'>> ������ϳ���</div>'
			 + '<div id=\'longcareDataBodyList\' style=\'width:1000px;\'>'
			 + '<div style=\'clear:both; width:auto;\'>'
				+ '<div class=\'lcDiv\' style=\'width:70px; border-left:1px solid #cccccc; background-color:#efefef; font-weight:bold;\'>����</div>'
				+ '<div class=\'lcDiv\' style=\'width:80px; background-color:#efefef; font-weight:bold;\'>������</div>'
				+ '<div class=\'lcDiv\' style=\'width:40px; background-color:#efefef; font-weight:bold;\'>����</div>'
				+ '<div class=\'lcDiv\' style=\'width:50px; background-color:#efefef; font-weight:bold;\'>����</div>'
				+ '<div class=\'lcDiv\' style=\'width:70px; background-color:#efefef; font-weight:bold;\'>�ð�</div>'
				+ '<div class=\'lcDiv\' style=\'width:40px; background-color:#efefef; font-weight:bold;\'>Ƚ��</div>';

	var w = 45+80+40+50+70+40+50;

	
	$('#TableData3 tr:first [id~="dt"]', data).each(function(){
		var lsDt = $(this).text();
			lsDt = lsDt.split('(��)').join('');
			lsDt = lsDt.split('(ȭ)').join('');
			lsDt = lsDt.split('(��)').join('');
			lsDt = lsDt.split('(��)').join('');
			lsDt = lsDt.split('(��)').join('');
			lsDt = lsDt.split('(��)').join('');
			lsDt = lsDt.split('(��)').join('');

		html += '<div class=\'lcDiv\' style=\'width:20px; background-color:#efefef; font-weight:bold; color:'+$(this).css('color')+';\'>'+lsDt+'</div>';
		w += 20;
	});

	html += '</div>';
	
	var i = -1;

	$('#TableData3 tr', data).each(function(){
		if (i < 0){
		}else{
			html += '<div style=\'clear:both;\'>';				

			//����
			html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:25px;' : '')+'width:70px; border-left:1px solid #cccccc;\'>';
			html += $('#careNm'+i, this).text();

			if ($('#careNm2'+i, this).text() != ''){
				html += '<br>'+$('#careNm2'+i, this).text();
			}

			html += '</div>';
			
			//����
			html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:17px;' : '')+'width:80px;\'>'+$('#sugaNm'+i, this).text().split('(�湮��)').join('')+'</div>';

			//��������
			html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:25px;' : '')+'width:40px;\'>';
			html += $('#familyYn1'+i, this).attr('checked') ? 'Y' : '';

			if ($('#careNm2'+i, this).text() != ''){
				html += '<br>'+($('#familyYn2'+i, this).attr('checked') ? 'Y' : '');
			}

			html += '</div>';
			
			//��������
			html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:25px;' : '')+'width:50px;\'>';
			html += ($('#_familyRel1'+i, this).val() != '00' ? '<span title=\''+familyRel[$('#_familyRel1'+i, this).val()]+'\'>'+$('#_familyRel1'+i, this).val()+'</span>' : '');

			if ($('#careNm2'+i, this).text() != ''){
				html += '<br>'+($('#_familyRel2'+i, this).val() != '00' ? '<span title=\''+familyRel[$('#_familyRel2'+i, this).val()]+'\'>'+$('#_familyRel2'+i, this).val()+'</span>' : '');
			}

			html += '</div>';

			//�ð�
			html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:50px;' : '')+'width:70px;\'>'+$('#serviceTm'+i, this).text()+'</div>';
			
			//����
			var cnt = 0;
			var day = '';
			var htmlSub = '';
			$('input:checkbox[name="payDt'+i+'"]', this).each(function(){
				if ($(this).attr('checked')){
					day = 'Y';
					cnt ++;
				}else{
					day = '&nbsp;';
				}

				htmlSub += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:50px;' : '')+'width:20px;\'>'+day+'</div>';
			});

			//Ƚ��
			html += '<div class=\'lcDiv\' style=\''+style+(svcKind == '002' ? 'line-height:50px;' : '')+'width:40px;\'>'+cnt+'</div>'+htmlSub;
			
			html += '</div>';
		}

		i ++;
	});

	html += '</div>';

	html += '<div style=\'clear:both; height:30px; line-height:30px; padding-left:10px; font-weight:bold; border-bottom:2px solid #0000ff;\'>> �̿�����</div>'
		 +  '<div style=\'clear:both;\'>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; border-left:1px solid #cccccc; background-color:#efefef; font-weight:bold;\'>�̿�Ƚ��</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#useCount', data).text()+'</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>���ݾ�</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#mmAmt', data).text()+'</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>Ÿ������� ���ݾ�</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#mmAmtAll', data).text()+'</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>���ѵ���</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#mmLimitAmt', data).text()+'</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; background-color:#efefef; font-weight:bold;\'>�ʰ��ݾ�</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px;\'>'+$('#mmExcsAmt', data).text()+'</div>'
				+ '<div class=\'lcDiv\' style=\'padding:0 5px 0 5px; float:right; border:none;\'>[<a href=\'#\' onclick=\'self.close();\'>�ݱ�</a>]</div>'
			 + '</div>'
			 + '</div>';

	$('#longcareData')
		.css('left','0')
		.css('top','0')
		.css('width','100%')
		.css('height','auto')
		.html(html)
		.show();

	var h = $('#longcareDataBody').height() + 110;
	
	w += 50;

	window.resizeTo(w, h);
}