<?
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$host = $myF->host();

	/*
	 *	계약등록
	 */

	//최근 메모
	$sql = 'SELECT	reg_nm, subject, contents, insert_dt, update_dt
			FROM	cv_memo
			WHERE	memo_type=\'1\'
			AND		org_no	= \''.$orgNo.'\'
			AND		del_flag= \'N\'
			ORDER	BY insert_dt DESC
			LIMIT	1';

	$row = $conn->get_array($sql);

	if ($row) $memo = '작성일시 : '.str_replace('-','.',$row['insert_dt']).' / 최종수정일시 : '.str_replace('-','.',$row['update_dt']).' / 제목 : '.stripslashes($row['subject']).' / 작성자 : '.$row['reg_nm'].'<br>'.nl2br(stripslashes($row['contents']));

	Unset($row);

	//최종 계약일
	$sql = 'SELECT	from_dt
			FROM	cv_reg_info
			WHERE	org_no	= \''.$orgNo.'\'
			ORDER	BY from_dt DESC
			LIMIT 1';

	$contLockDt = $conn->get_data($sql);


	//기관정보
	$sql = 'SELECT	DISTINCT
					m00_store_nm AS org_nm
			,		m00_mname AS mg_nm
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'';

	$row = $conn->get_array($sql);

	$orgNm = $row['org_nm'];
	$mgNm	= $row['mg_nm'];

	Unset($row);

	//이전기간정보
	$sql = 'SELECT	dt, val
			FROM	center_his
			WHERE	org_no	= \''.$orgNo.'\'
			AND		gbn		= \'01\'';
	$R = $conn->get_array($sql);
	$oldOrgDt = $R['dt'];
	$oldOrgNo = $R['val'];

	if ($oldOrgNo){
		$sql = 'SELECT	DISTINCT m00_store_nm
				FROM	m00center
				WHERE	m00_mcode = \''.$oldOrgNo.'\'';
		$oldOrgNm = $conn->get_data($sql);
	}

	$pos	= $_REQUEST['pos']; //-1:이전, 1:다음
	$posDt	= $_REQUEST['posDt'];

	$sql = 'SELECT	b02_other
			,		b02_branch
			,		b02_person
			,		b02_date
			FROM	b02center
			WHERE	b02_center = \''.$orgNo.'\'';

	$R = $conn->get_array($sql);
	//$memo = $R['b02_other'];
	$branch = $R['b02_branch'];
	$person = $R['b02_person'];
	$startDt= $R['b02_date'];
	Unset($R);


	if ($pos == -1){
		$sql = 'SELECT	from_dt
				FROM	cv_reg_info
				WHERE	org_no	= \''.$orgNo.'\'
				AND		from_dt < \''.$posDt.'\'
				ORDER	BY from_dt DESC
				LIMIT 1';

		$curDt = $conn->get_data($sql);
	}else if ($pos == 1){
		$sql = 'SELECT	from_dt
				FROM	cv_reg_info
				WHERE	org_no	= \''.$orgNo.'\'
				AND		from_dt > \''.$posDt.'\'
				ORDER	BY from_dt
				LIMIT 1';

		$curDt = $conn->get_data($sql);
	}else if ($pos == 0){
		$curDt = $posDt;
	}else{
		$curDt = $today;
	}

	if (!$curDt) $curDt = $posDt;

	$sql = 'SELECT	*
			FROM	cv_reg_info
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		from_dt <= \''.$curDt.'\'
			AND		CASE WHEN to_dt != \'\' THEN to_dt ELSE from_dt END >= \''.$curDt.'\'';

	$R = $conn->get_array($sql);

	if (!$R){
		$sql = 'SELECT	from_dt
				FROM	cv_reg_info
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		from_dt <= \''.$today.'\'
				ORDER	BY CASE WHEN to_dt != \'\' THEN to_dt ELSE from_dt END DESC
				LIMIT	1';
		$tmpDt = $conn->get_data($sql);
		if ($tmpDt) $curDt = $tmpDt;

		$sql = 'SELECT	from_dt
				FROM	cv_reg_info
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		from_dt	>= \''.$today.'\'
				ORDER	BY from_dt
				LIMIT	1';
		$tmpDt = $conn->get_data($sql);
		if ($tmpDt) $curDt = $tmpDt;

		$sql = 'SELECT	*
				FROM	cv_reg_info
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		from_dt <= \''.$curDt.'\'
				AND		CASE WHEN to_dt != \'\' THEN to_dt ELSE from_dt END >= \''.$curDt.'\'';

		$R = $conn->get_array($sql);
	}

	$IsNew = false;

	if (!$R['link_company']){
		$sql = 'SELECT	DISTINCT m00_domain, m00_cont_date
				FROM	m00center
				WHERE	m00_mcode = \''.$orgNo.'\'';
		$t = $conn->get_array($sql);
		$s = $t['m00_domain'];

		$sql = 'SELECT	b00_code
				FROM	b00branch
				WHERE	b00_domain = \''.$s.'\'
				AND		b00_com_yn = \'Y\'';
		$R['link_company'] = $conn->get_data($sql);

		$R["link_branch"] = $branch;
		$R["link_person"] = $person;

		$R['start_dt'] = $startDt;
		$R['cont_dt'] = $t['m00_cont_date'];

		$sql = 'SELECT	care_area, care_group
				FROM	b02center
				WHERE	b02_center = \''.$orgNo.'\'';
		$t = $conn->get_array($sql);

		$R['area_cd'] = $t['care_area'];
		$R['group_cd'] = $t['care_group'];

		if (!$R['area_cd']) $R['area_cd'] = '99';
		if (!$R['group_cd']) $R['group_cd'] = '99';

		$IsNew = true;
	}

	if (!$R['nurse_area_cd']) $R['nurse_area_cd'] = '99';
	if (!$R['nurse_group_cd']) $R['nurse_group_cd'] = '99';

	if ($R['rs_cd'] == '2' || $R['rs_cd'] == '4'){
		if (!$R['cont_dt']) $R['cont_dt'] = $R['from_dt'];

		if ($R['rs_dtl_cd'] != '06'){
			$R['from_dt'] = '';
			$R['to_dt'] = '';
		}
	}

	//if (!$R['cont_com']) $R['cont_com'] = '3';


	//CMS 리스트
	/*
	$sql = 'SELECT	GROUP_CONCAT(cms_no) AS cms_list
			FROM	cv_cms_list
			WHERE	org_no  = \''.$orgNo.'\'
			AND		cms_no != \''.$R['cms_no'].'\'';
	$CMSList = $conn->get_data($sql);
	*/
	$sql = 'SELECT	cms_no, cms_com
			FROM	cv_cms_list
			WHERE	org_no  = \''.$orgNo.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$CMSList[$row['cms_no']] = $row['cms_com'];
	}

	$conn->row_free();


	$CMSNo = $R['cms_no'];

	if (StrLen($CMSNo) < 8){
		$CMSNo = '00000000'.$CMSNo;
		$CMSNo = SubStr($CMSNo, StrLen($CMSNo) - 8, StrLen($CMSNo));
	}

	$sql = 'SELECT	COUNT(*)
			FROM	cv_cms_reg
			WHERE	cms_no = \''.$CMSNo.'\'';
	$CMSRegCnt = $conn->get_data($sql);


	//가상계좌
	$sql = 'SELECT	a.vr_no, a.bank_cd, b.bank_nm, a.key_yn
			FROM	cv_vr_list AS a
			INNER	JOIN	cv_bank AS b
					ON		b.bank_cd = a.bank_cd
			WHERE	a.org_no = \''.$orgNo.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$vrList[$row['vr_no']] = Array('bankCd'=>$row['bank_cd'],'bankNm'=>$row['bank_nm'], 'keyYn'=>$row['key_yn']);
	}

	$conn->row_free();

	//계약정보
	$sql = 'SELECT	acct_bedt, cms_start_ym
			FROM	center_cont_info
			WHERE	org_no = \''.$orgNo.'\'';

	$row = $conn->get_array($sql);

	$acctBeDt = $row['acct_bedt'];
	$cmsStartYm = $row['cms_start_ym'];

	Unset($row);
?>
<script type="text/javascript">
	var IsLoad = false;
	var gWin = new Array();

	window.onunload = function(){
		for(var i=0; i<gWin.length; i++){
			if (gWin[i]) gWin[i].close();
		}
	}

	$(document).ready(function(){
		lfHisCont();
		lfGetLink('BRANCH',$('#cboCompany option:selected').attr('domain'),false);
		lfSetStatDtl($('#cboRsCd').val(),'<?=$R["rs_dtl_cd"];?>');
		lfSetGroup($('#cboArea'), $('#cboGroup'), '<?=$R["group_cd"];?>');
		lfSetGroup($('#cboNurseArea'), $('#cboNurseGroup'), '<?=$R["nurse_group_cd"];?>');

		$('#cboRsCd, #cboRsDtlCd').unbind('change').bind('change',function(){
			if ($('#cboRsCd').val() == '3'){
				//신규
				//alert('<?=$contLockDt;?>/<?=$R["from_dt"];?>/<?=$R["cont_dt"];?>');
				if ('<?=$R["rs_cd"];?>' == '4'){
					$('#txtFromDt, #txtToDt').css('background-color','#EAEAEA').attr('readonly',true);
				}else{
					//사유권한 해제
					//$('#txtFromDt, #txtToDt, #txtContDt').css('background-color','#EAEAEA').attr('readonly',true);
					$('#txtFromDt, #txtToDt').css('background-color','#EAEAEA').attr('readonly',true);
				}
				$('#txtFromDt').val(__getDate($('#txtFromDt').attr('orgDt')));
				$('#txtToDt').val(__getDate($('#txtToDt').attr('orgDt')));

				if ('<?=$R["rs_cd"];?>' == '4'){
					$('#txtContDt').val('');
				}

				if ('<?=$IsNew;?>' == '1'){
					$('#txtFromDt, #txtToDt').css('background-color','').attr('readonly',false);
				}

				var termDays = 0;

				if ($('#cboRsDtlCd').val() == '01'){
					//신규연결
					termDays = 13;
					$('#txtFromDt').val(__getDate($('#txtFromDt').attr('orgDt')));
				}else if ($('#cboRsDtlCd').val() == '02' || $('#cboRsDtlCd').val() == '03'){
					//기간연장
					//termDays = 29;
					//$('#txtFromDt').val(addDate('d', 1, $('#txtToDt').val()));
					$('#txtFromDt, #txtToDt').css('background-color','').attr('readonly',false);
				}else{
					$('#txtFromDt, #txtToDt').css('background-color','').attr('readonly',false);
				}

				$('#ID_CELL_RS_STR').text('오픈일자');
				$('#ID_CELL_DT_STR').text('오픈기간');

				if (termDays > 0){
					if (IsLoad || $('#cboRsDtlCd').val() == '03')
						$('#txtToDt').val(addDate('d', termDays, $('#txtFromDt').val()));
				}
			}else if ($('#cboRsCd').val() == '1'){
				if ($('#cboRsCd').val() == '<?=$R["rs_cd"];?>' && $('#cboRsDtlCd').val() == '<?=$R["rs_dtl_cd"];?>'){//서비스
					$('#txtContDt').val(__getDate($('#txtContDt').attr('orgDt')));
					//사유권한해제
					//$('#txtFromDt, #txtToDt, #txtContDt').css('background-color','').attr('readonly',false);
					$('#txtFromDt, #txtToDt').css('background-color','').attr('readonly',false);
					$('#txtFromDt').val(__getDate($('#txtFromDt').attr('orgDt')));
					$('#txtToDt').val(__getDate($('#txtToDt').attr('orgDt')));
				}else{
					//사유권한해제
					//$('#txtFromDt, #txtToDt, #txtContDt').val('').css('background-color','').attr('readonly',false);
					$('#txtFromDt, #txtToDt').val('').css('background-color','').attr('readonly',false);
				}

				$('#ID_CELL_RS_STR').text('계약일자');
				$('#ID_CELL_DT_STR').text('계약기간');
			}else{
				//일시정지, 해지
				//$('#txtFromDt').css('background-color','#EAEAEA').attr('readonly',true);
				//$('#txtContDt').val(__getDate($('#txtContDt').attr('orgDt'))).css('background-color','').attr('readonly',false);

				$('#txtFromDt, #txtToDt').css('background-color','#EAEAEA').attr('readonly',true).val('');
				$('#txtContDt').val('');

				if ($('#cboRsCd').val() == '<?=$R["rs_cd"];?>' /*&& $('#cboRsDtlCd').val() == '<?=$R["rs_dtl_cd"];?>'*/){
					$('#txtFromDt').val(__getDate($('#txtFromDt').attr('orgDt')));
					$('#txtToDt').val(__getDate($('#txtToDt').attr('orgDt')));
					$('#txtContDt').val(__getDate($('#txtContDt').attr('orgDt')));
				}else{
					//if ($('#cboRsCd').val() != '<?=$R["rs_cd"];?>' != '2' && '<?=$R["rs_cd"];?>' != '4'){
					//	$('#txtFromDt, #txtToDt').val('');
					//}
					$('#txtContDt').val('<?=$myF->dateStyle($today);?>');
				}

				if ($('#cboRsDtlCd option:selected').attr('fromDtYn') == 'Y'){
					$('#txtFromDt').css('background-color','').attr('readonly',false).val(__getDate($('#txtFromDt').attr('orgDt')));
				}

				if ($('#cboRsDtlCd option:selected').attr('toDtYn') == 'Y'){
					$('#txtToDt').css('background-color','').attr('readonly',false).val(__getDate($('#txtToDt').attr('orgDt')));
				}

				$('#ID_CELL_RS_STR').text('해지일자');
				$('#ID_CELL_DT_STR').text('');
			}
		});

		//if ($('#cboRsCd').val() == '3')
		$('#cboRsDtlCd').change();

		IsLoad = true;
	});

	function lfHisCont(){
		var formDt = '';

		if ('<?=$R["rs_cd"];?>' == '2' || '<?=$R["rs_cd"];?>' == '4'){
			fromDt = '<?=$R["cont_dt"]?>';

			if ('<?=$R["rs_cd"];?>_<?=$R["rs_dtl_cd"];?>' == '4_06'){
				fromDt = '<?=$R["from_dt"]?>';
			}
		}else{
			fromDt = '<?=$R["from_dt"]?>';
		}

		$.ajax({
			type:'POST'
		,	async:false
		,	url:'./center_cont_his.php'
		,	data:{
				'orgNo':'<?=$orgNo;?>'
			,	'fromDt':fromDt //('<?=$R["rs_cd"];?>' != '2' && '<?=$R["rs_cd"];?>' != '4' ? '<?=$R["from_dt"]?>' : '<?=$R["cont_dt"]?>')
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#ID_HIS').html(html);

				//alert($('.CLS_HIS_ROW[selYn="Y"][newCont="Y"]').length+'/'+$('.CLS_HIS_ROW[selYn!="Y"][newCont="Y"]').length+'/'+$('.CLS_HIS_ROW[selYn!="Y"][newCont!="Y"]').length);

				if ($('.CLS_HIS_ROW[selYn="Y"][newCont="Y"]').length > 0){ //신규
					$('#cboRsCd').children('[value="3"]').attr('disabled',false); //신규

					if ($('.CLS_HIS_ROW[selYn!="Y"][newCont!="Y"]').length > 0){
						$('#cboRsCd option').each(function(){
							//if ($(this).val() != '3') $(this).attr('disabled',true);
						});
					}
				}else if ($('.CLS_HIS_ROW[selYn!="Y"][newCont="Y"]').length > 0){ //신규 이외
					//$('#cboRsCd').children('[value="3"]').attr('disabled',true); //신규

					$('#cboRsCd option').each(function(){
						if ('<?=$contLockDt;?>' != '<?=$R["from_dt"] ? $R["from_dt"] : $R["cont_dt"];?>' && '<?=$R["rs_cd"];?>' != $(this).val()){
							//$(this).attr('disabled',true);
						}
					});
				}else{
					$('#cboRsCd option').each(function(){
						/*
						if ($(this).val() == '3'){
							$(this).attr('disabled',true);
						}else{
							$(this).attr('disabled',false);
						}
						*/
					});

					if ('<?=$R["rs_cd"];?>' == '4'){
						$('#cboRsCd').children('[value="3"]').attr('disabled',false); //신규
					}
				}

				$('.CLS_HIS_ROW',$('#ID_HIS')).unbind('mouseover').bind('mouseover',function(){
					$(this).css('background-color','#EAEAEA');
				}).unbind('mouseout').bind('mouseout',function(){
					$(this).css('background-color','');
				}).unbind('click').bind('click',function(){
					if ($(this).attr('selYn') == 'Y' || $(this).attr('delYn') == 'Y') return;

					var left = (screen.availWidth - (width = 900)) / 2, top = (screen.availHeight - (height = 650)) / 2;
					var winIdx = gWin.length;
					gWin[winIdx] = window.open('./center_connect_reg.php?orgNo=<?=$orgNo;?>&type=Contract&pos=0&posDt='+$(this).attr('fromDt'),'ORGCONT_'+winIdx,'left='+left+',top='+top+', width='+width+', height='+height+', scrollbars=no, status=no, resizable=no');
					gWin[winIdx].focus();
				});
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfSetStatDtl(gbn,val){
		/*
			3	신규
			1	서비스
			2	일시중지
			4	해지
			9	기타
		 */

		var html = '';

		/*
		if (gbn == '1' || gbn == '2' || gbn == '3'){
			html = '<option value="01">신규연결</option>'
				 + '<option value="02">재연결</option>'
				 + '<option value="11">뮤료기간 종료</option>'
				 + '<option value="12">계약해지</option>'
				 + '<option value="31">사용료 미납</option>'
				 + '<option value="32">기관 이전</option>';
		}else{
			html = '<option value="10">미계약</option>'
				 + '<option value="20">장기간 미사용</option>'
				 + '<option value="30">사용료 미납</option>'
				 + '<option value="40">계약기간 종료</option>'
				 + '<option value="50">계약해지 요청</option>'
				 + '<option value="60">CMS 해지</option>'
				 + '<option value="70">기관불만</option>';
		}

		html += '<option value="90">기관폐쇄</option>'
			 +  '<option value="99">기타</option>';
		*/

		var tmpVal = '';

		if (gbn == '3'){
			//alert('<?=$contLockDt;?>/<?=$R["from_dt"];?>/<?=$R["rs_dtl_cd"];?>/'+$('.CLS_HIS_ROW[stat="1_01"]').length);
			/*
			if ('<?=$contLockDt;?>' == '' && '<?=$R["from_dt"];?>' == '' && '<?=$R["rs_dtl_cd"];?>' == ''){
				html = '<option value="01">신규연결</option>'
					 + '<option value="02" disabled="true">기간연장</option>'
					 + '<option value="03" disabled="true">재연결</option>';
				//tmpVal = '03';
			}else{
				html = '<option value="01" '+('<?=$R["rs_cd"];?>' == '4' ? '' : '<?=$R["rs_dtl_cd"];?>' != '01' ? 'disabled="true"' : '')+'>신규연결</option>'
					 + '<option value="02" '+('<?=$contLockDt;?>' != '<?=$R["from_dt"];?>' && '<?=$R["rs_dtl_cd"];?>' != '02' ? 'disabled="true"' : '')+'>기간연장</option>'
					 + '<option value="03" '+('<?=$contLockDt;?>' != '<?=$R["from_dt"];?>' && '<?=$R["rs_dtl_cd"];?>' != '03' ? 'disabled="true"' : '')+'>재연결</option>';
			}
			*/
			html = '<option value="01">신규연결</option>'
				 + '<option value="02">기간연장</option>'
				 + '<option value="03">재연결</option>'
				 + '<option value="04">케어비지트</option>';
		}else if (gbn == '1'){
			//alert('<?=$contLockDt;?>/<?=$R["cont_dt"];?>/<?=$R["from_dt"];?>/<?=$R["rs_dtl_cd"];?>/'+$('.CLS_HIS_ROW[stat="1_01"]').length);
			if (('<?=$contLockDt;?>' != '<?=$R["from_dt"];?>' && '<?=$R["rs_cd"];?>_<?=$R["rs_dtl_cd"];?>' != '1_01') ||
				('<?=$contLockDt;?>' == '<?=$R["from_dt"];?>' && $('.CLS_HIS_ROW[stat^="1_"][stat!="1_01"]').length > 0)){
				//html = '<option value="01" disabled="true">신규계약</option>';
				html = '<option value="01">신규계약</option>';
				tmpVal = '02';
			}else{
				html = '<option value="01">신규계약</option>';
				tmpVal = '01';
			}

			html +='<option value="02" '+('<?=$contLockDt;?>' != '<?=$R["rs_cd"] == "2" || $R["rs_cd"] == "4" ? $R["cont_dt"] : $R["from_dt"];?>' && '<?=$R["rs_dtl_cd"];?>' != '02' ? 'disabled="true"' : '')+'>재계약</option>'
				 + '<option value="03" '+('<?=$contLockDt;?>' != '<?=$R["rs_cd"] == "2" || $R["rs_cd"] == "4" ? $R["cont_dt"] : $R["from_dt"];?>' && '<?=$R["rs_dtl_cd"];?>' != '03' ? 'disabled="true"' : '')+'>기간연장</option>'
				 + '<option value="04" '+('<?=$contLockDt;?>' != '<?=$R["rs_cd"] == "2" || $R["rs_cd"] == "4" ? $R["cont_dt"] : $R["from_dt"];?>' && '<?=$R["rs_dtl_cd"];?>' != '04' ? 'disabled="true"' : '')+'>중지해제</option>';
		}else if (gbn == '2'){
			html = '<option value="01">사용료미납</option>'
				 + '<option value="02">기관요청</option>'
				 + '<option value="03">장기미사용</option>';
		}else if (gbn == '4'){
			html = '<option value="01">계약기간만기</option>'
				 + '<option value="02">기관요청</option>'
				 + '<option value="03">사용료미납</option>'
				 + '<option value="04">미계약</option>'
				 + '<option value="05">장기미사용</option>'
				 + '<option value="06" fromDtYn="Y" toDtYn="Y">무료기간연장</option>';
		}else if (gbn == '9'){
			html = '<option value="99">기타</option>';
		}

		if (!val){
			try{
				var v = $('.CLS_HIS_ROW[selYn="Y"]',$('#ID_HIS')).attr('stat').split('_');
				if (gbn == v[0]) val = v[1];
			}catch(e){}
		}

		if (!val) val = tmpVal;

		$('#cboRsDtlCd').html(html);
		$('#cboRsDtlCd').val(val);
	}

	function lfGetLink(type,cd,IsLoad){
		if (IsLoad == undefined) IsLoad = true;

		$.ajax({
			type:'POST'
		,	url:'./center_get_value.php'
		,	data:{
				'type':type
			,	'code':cd
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				if (type == 'GROUP'){
					$('#cboGroup').html(html);
				}else if (type == 'BRANCH'){
					$('#cboBranch').html(html);
					if (!IsLoad){
						$('#cboBranch').val('<?=$R["link_branch"];?>');
						lfGetLink('PERSON',$('#cboBranch').val(),IsLoad);
					}else{
						$('#cboBranch').change();
					}
				}else if (type == 'PERSON'){
					$('#cboPerson').html(html);
					if (!IsLoad){
						$('#cboPerson').val('<?=$R["link_person"];?>');
					}else{
						$('#cboPerson').change();
					}
				}
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfSave(){
		if ($('#cboRsCd').val() == '1'){
			//서비스
			if (!$('#txtContDt').val()){
				alert('계약일자를 입력하여 주십시오.');
				$('#txtContDt').focus();
				return;
			}

			if (!$('#cboContCom').val()){
				alert('계약회사를 선택하여 주십시오.');
				$('#cboContCom').focus();
				return;
			}
		}

		if (!$('#txtStartDt').val()){
			alert('시작일자 입력오류입니다. 확인하여 주십시오.');
			$('#txtStartDt').focus();
			return;
		}

		if (!$('#cboCompany').val()){
			alert('담당회사를 선택하여 주십시오.');
			$('#cboCompany').focus();
			return;
		}

		if (!$('#cboBranch').val()){
			alert('담당지사를 선택하여 주십시오.');
			$('#cboBranch').focus();
			return;
		}

		if (!$('#cboPerson').val()){
			alert('담당자를 선택하여 주십시오.');
			$('#cboPerson').focus();
			return;
		}

		if ($('#cboRsCd').val() == '2' || $('#cboRsCd').val() == '4'){
			if (!$('#txtContDt').val()){
				alert('해지일자 입력오류입니다. 확인하여 주십시오.');
				$('#txtContDt').focus();
				return;
			}

			//if ($('#txtContDt').val() <= getToday()){
			//	alert('해지일자를 오늘 이후의 일자로 입력하여 주십시오.');
			//	$('#txtContDt').focus();
			//	return;
			//}

			if ($('#txtContDt').val() < __getDate($('#txtFromDt').attr('orgDt'))){
				alert('계약시작일 보다 과거의 일자을 입력할 수 없습니다.\n확인하여 주십시오.');
				return;
			}
		}else{
			if (!$('#txtFromDt').val()){
				alert('적용일 입력오류입니다. 확인하여 주십시오.');
				$('#txtFromDt').focus();
				return;
			}

			if (!$('#txtToDt').val()){
				alert('종료일 입력오류입니다. 확인하여 주십시오.');
				$('#txtToDt').focus();
				return;
			}
		}

		var acctGbn = $('input:radio[name="optAcctGbn"]:checked').val();

		if (acctGbn == '1'){
			if ($('.CLS_CMS[CMSNo!=""]').length < 1){
				alert('CMS 번호를 등록 후 저장하여 주십시오.');
				return;
			}
		}else if (acctGbn == '3'){
			if ($('.CLS_VR[vrNo!=""]').length < 1){
				alert('가상계좌 번호를 등록 후 저장하여 주십시오.');
				return;
			}
		}

		/*
		if (!$('#txtRqtDt').val()){
			alert('요청일 입력오류입니다. 확인하여 주십시오.');
			$('#txtRqtDt').focus();
			return;
		}

		if (!$('#txtRqtNm').val()){
			alert('요청자 입력오류입니다. 확인하여 주십시오.');
			$('#txtRqtNm').focus();
			return;
		}

		if (!$('#txtRegDt').val()){
			alert('등록일 입력오류입니다. 확인하여 주십시오.');
			$('#txtRegDt').focus();
			return;
		}

		if (!$('#txtRegNm').val()){
			alert('등록자 입력오류입니다. 확인하여 주십시오.');
			$('#txtRegNm').focus();
			return;
		}
		*/

		var data = {};

		data['orgNo'] = '<?=$orgNo;?>';
		data['reCont']= 'N'; //추가계약여부 //($('#chkReCont').attr('checked') ? 'Y' : 'N');
		data['orgContDt']= $('#txtFromDt').attr('orgDt');

		if ($('#cboRsCd').val() != '<?=$R["rs_cd"];?>' || $('#cboRsDtlCd').val() != '<?=$R["rs_dtl_cd"];?>'){
			data['reCont'] = 'Y';
		}

		if ($('#cboRsCd').val() == '1' && $('#cboRsDtlCd').val() == '02'){
			if ($('#txtFromDt').val() > __getDate($('#txtToDt').attr('orgDt'))) data['reCont'] = 'Y';
		}

		data['taxbillYn'] = $('#chkTaxbillYn').attr('checked') ? 'Y' : 'N';
		data['popYn'] = $('#chkPopupYn').attr('checked') ? 'Y' : '';

		$('input, select, textarea').each(function(){
			var type = $(this).attr('type').toUpperCase();

			if (type == 'RADIO'){
				if ($(this).attr('checked')){
					data[$(this).attr('name')] = $(this).val();
				}
			}else{
				data[$(this).attr('id')] = $(this).val();
			}
		});

		data['CMSList'] = '';
		$('.CLS_CMS').each(function(){
			data['CMSList'] += (data['CMSList'] ? '?' : '');
			data['CMSList'] += ('CMSNo='+$(this).attr('CMSNo'));
			data['CMSList'] += ('&CMSCom='+$(this).attr('CMSCom'));
		});


		var vrCnt = 0;

		$('.CLS_VR').each(function(){
			vrCnt ++;
		});


		data['VrList'] = '';
		$('.CLS_VR').each(function(){
			var keyYn = '';

			if (vrCnt == 1){
				keyYn = ($(this).attr('keyYn') ? $(this).attr('keyYn') : 'Y');
			}else{
				keyYn = $(this).attr('keyYn');
			}

			data['VrList'] += (data['VrList'] ? '?' : '');
			data['VrList'] += ('vrNo='+$(this).attr('vrNo'));
			data['VrList'] += ('&bankCd='+$(this).attr('bankCd'));
			data['VrList'] += ('&keyYn='+keyYn);
		});

		data['oldOrgCd'] = $('#ID_CELL_ORG_CD').text();

		//계약예정일
		data['acctBeDt'] = $('#txtAcctBeDt').val();

		//자동이체 시작년월
		data['cmsStartYm'] = $('#txtCmsStartYm').val();

		//조정요금관리기관여부
		data['adjustFeeYn'] = $('#chkAdjustFeeYn').attr('checked') ? 'Y' : 'N';
		data['adjustFeeNote'] = $('#txtAdjustFeeNote').val();

		$.ajax({
			type:'POST'
		,	url:'./center_connect_reg_contract_save.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
					lfHisCont();
				}else{
					alert(result);
				}
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfSetGroup(obj, tgt, val, IsLoad){
		if (!val) val = '';

		$.ajax({
			type :'POST'
		,	url  :'../acct/find_group.php'
		,	data :{
				'area':$(obj).val() //$('#cboArea option:selected').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$(tgt).html(html); //$('#cboGroup').html(html);

				if (val){
					//$('#cboGroup').val(val);
					$(tgt).val(val);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfCMSAdd(){
		var CMSNo = $('#txtAddCMS').val();
		var comCd = $('#cboCMSCom').val();
		var comNm = $('#cboCMSCom option:selected').text();

		if (!CMSNo){
			alert('CMS 번호를 입력하여 주십시오.');
			$('#txtAddCMS').focus();
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./center_cms_duplicate.php'
		,	data :{
				'CMSNo':CMSNo
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				var cnt = __str2num(result);

				if (cnt > 0){
					alert('이미 사용중인 CMS번호입니다. 확인하여 주십시오.');
					return;
				}

				var html = '';

				html = '<div class="CLS_CMS" CMSNo="'+CMSNo+'" CMSCom="'+comCd+'" class="nowrap" style="float:left; width:95%; margin-left:5px; margin-right:5px;">'
					 + '<span>'+CMSNo+'</span>[<span>'+comNm+'</span>]'
					 + '<img src="../image/btn_del.png" style="cursor:pointer;" onclick="lfCMSRemove(\''+CMSNo+'\');">'
					 + '</div>';

				if ($('.CLS_CMS').length > 0){
					$('.CLS_CMS:last').after(html);
				}else{
					$('#ID_CMS_LIST').html(html);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfCMSRemove(CMSNo){
		//if (!confirm('삭제후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./center_cms_remove.php'
		,	data :{
				'orgNo':'<?=$orgNo;?>'
			,	'CMSNo':CMSNo
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					$('.CLS_CMS[CMSNo="'+CMSNo+'"]').remove();
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	/*
	function lfChkReCont(){
		if (!$('#chkReCont').attr('checked')){
			$('#txtFromDt').val(__getDate($('#txtFromDt').attr('orgDt')));
			$('#txtToDt').val(__getDate($('#txtToDt').attr('orgDt')));
			return;
		}

		if ('<?=$R["rs_cd"];?>' != '4'){
			$('#chkReCont').attr('checked',false);
			alert('기존의 계약을 해지후 재계약을 실행하여 주십시오.');
			return;
		}

		$('#txtFromDt').val('');
		$('#txtToDt').val('');
	}
	*/

	function lfAsk(type){
		$.ajax({
			type :'POST'
		,	url  :'./center_connect_info_ask.php'
		,	data :{
				'orgNo':'<?=$orgNo;?>'
			,	'contDt':$('#txtFromDt').attr('orgDt')
			,	'type':type
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfFindCenter(){
		var objModal = new Object();
		var url = '../find/_find_center.php';
		var style = 'dialogWidth:800px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

		objModal.mode = '99';
		objModal.code = '';
		objModal.name = '';

		window.showModalDialog(url, objModal, style);

		if (!objModal.code) return;

		$('#ID_CELL_ORG_CD').text(objModal.code);
		$('#ID_CELL_ORG_NM').text(objModal.name);
	}

	function lfDocCurr(gbn){
		//$('#ID_POP').show();
		$.ajax({
			type :'POST'
		,	url  :'./center_connect_doc.php'
		,	data :{
				'id':'ID_POP'
			,	'orgNo':'<?=$orgNo;?>'
			,	'contDt':$('#txtFromDt').attr('orgDt')
			,	'gbn':gbn
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#ID_POP').html(html).show();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSetContTerm(){
		if ($('#txtFromDt').val() || $('#txtToDt').val()) return;
		if ($('#cboRsCd').val() == '2' || $('#cboRsCd').val() == '4') return;

		$('#txtFromDt').val($('#txtContDt').val());

		if ($('#cboRsCd').val() == '3' && $('#cboRsDtlCd').val() == '01'){
			$('#txtToDt').val(addDate('d', 13, $('#txtFromDt').val()));
		}else{
			$('#txtToDt').val('9999-12-31');
		}
	}

	function lfVrAdd(){
		var vrNo = $('#txtVrAcctNo').val();
		var bankCd = $('#cboVrBankCd option:selected').val();
		var bankNm = $('#cboVrBankCd option:selected').text();

		if (!vrNo){
			alert('가상계좌번호를 입력하여 주십시오.');
			$('#txtVrAcctNo').focus();
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./center_vr_duplicate.php'
		,	data :{
				'vrNo':vrNo
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				var cnt = __str2num(result);

				if (cnt > 0){
					alert('이미 사용중인 가상계좌번호입니다. 확인하여 주십시오.');
					return;
				}

				var html = '';

				html = '<div class="CLS_VR" vrNo="'+vrNo+'" bankCd="'+bankCd+'" class="nowrap" style="float:left; width:47%; margin-left:5px; margin-right:5px;">'
					 + '<span>'+vrNo+'</span>[<span>'+bankNm+'</span>]'
					 + '<img src="../image/btn_del.png" style="cursor:pointer;" onclick="lfVrRemove(\''+vrNo+'\');">'
					 + '</div>';

				if ($('.CLS_VR').length > 0){
					$('.CLS_VR:last').after(html);
				}else{
					$('#ID_VR_ACCT').html(html);
				}

				$('#txtVrAcctNo').val('');
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfVrRemove(vrNo){
		$.ajax({
			type :'POST'
		,	url  :'./center_vr_remove.php'
		,	data :{
				'orgNo':'<?=$orgNo;?>'
			,	'vrNo':vrNo
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					$('.CLS_VR[vrNo="'+vrNo+'"]').remove();
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfVrSetKey(vrNo){
		$.ajax({
			type :'POST'
		,	url  :'./center_vr_setkey.php'
		,	data :{
				'orgNo':'<?=$orgNo;?>'
			,	'vrNo':vrNo
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				$('.CLS_VR').each(function(){
					if ($(this).attr('vrNo') == vrNo){
						$('#ID_CELL_KEYPT',this).text('★');
					}else{
						$('#ID_CELL_KEYPT',this).text('');
					}
				});
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSetRqtRegDt(){
		$('#txtRqtDt').val($('#txtStartDt').val());
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="90px">
		<col width="70px">
		<col width="195px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left bold" colspan="7">- 기관정보</th>
		</tr>
		<tr>
			<th class="head">기관기호</th>
			<td class="left"><?=$orgNo;?></td>
			<th class="head">기관명</th>
			<td class="left"><?=$orgNm;?></td>
			<th class="head">대표자</th>
			<td class="left" colspan="2"><?=$mgNm;?></td>
		</tr>
		<tr>
			<th class="left bold" colspan="7">
				<span>- 계약정보</span><?
				$sql = 'SELECT	cont_dt, from_dt, rs_cd
						FROM	cv_reg_info
						WHERE	org_no = \''.$orgNo.'\'
						ORDER	BY cont_dt';

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);

					if ($row['from_dt'] == ($R['rs_cd'] == '2' || $R['rs_cd'] == '4' ? $R['cont_dt'] : $R['from_dt'])){
						$contIdx = $i + 1;
						break;
					}
				}

				$conn->row_free();?>
				<span>(<span style="color:RED;"><?=$rowCnt;?>건</span>의 계약중 <span style="color:RED;"><?=$contIdx;?>번째</span> 계약입니다.)</span>
			</th>
		</tr>
		<tr>
			<th class="head">시작일자</th>
			<td><input id="txtStartDt" type="text" value="<?=$myF->dateStyle($R['start_dt']);?>" class="date" onchange="lfSetRqtRegDt();"></td>
			<th class="head">연결지사</th>
			<td colspan="4">
				<select id="cboCompany" style="width:auto; margin-right:0;" onchange="lfGetLink('BRANCH',$('#cboCompany option:selected').attr('domain'));">
					<option value="">-회사선택-</option><?
					$sql = 'SELECT	b00_code AS cd
							,		b00_name AS nm
							,		b00_manager AS manager
							,		b00_domain AS domain
							FROM	b00branch
							WHERE	b00_com_yn	= \'Y\'
							AND		b00_stat	= \'1\'
							ORDER	BY nm';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['cd'];?>" domain="<?=$row['domain'];?>" <?=$R['link_company'] == $row['cd'] ? 'selected' : '';?>><?=$row['nm'];?></option><?
					}

					$conn->row_free();?>
				</select>
				<select id="cboBranch" style="width:auto; margin-left:0; margin-right:0;" onchange="lfGetLink('PERSON',this.value);">
					<option value="">-지사선택-</option>
				</select>
				<select id="cboPerson" style="width:auto; margin-left:0;">
					<option value="">-담당자-</option>
				</select>
			</td>
		</tr>
		<tr>
			<th class="head">사유코드</th>
			<td>
				<select id="cboRsCd" style="width:auto;" onchange="lfSetStatDtl($(this).val());">
					<option value="3" <?=$R['rs_cd'] == '3' ? 'selected' : '';?>>신규</option>
					<option value="1" <?=$R['rs_cd'] == '1' ? 'selected' : '';?>>서비스</option>
					<option value="2" <?=$R['rs_cd'] == '2' ? 'selected' : '';?>>일시중지</option>
					<option value="4" <?=$R['rs_cd'] == '4' ? 'selected' : '';?>>해지</option>
					<!--option value="9" <?=$R['rs_cd'] == '9' ? 'selected' : '';?>>기타</option-->
				</select>
			</td>
			<th class="head">상세코드</th>
			<td>
				<select id="cboRsDtlCd" style="width:auto;"></select>
			</td>
			<th class="head">계약회사</th>
			<td class="last">
				<select id="cboContCom" style="width:auto;">
					<option value="1" <?=$R['cont_com'] == '1' ? 'selected' : '';?>>굿이오스</option>
					<option value="2" <?=$R['cont_com'] == '2' ? 'selected' : '';?>>지케어</option><?
					if ($host == 'admin' && $_SESSION["userCode"] != 'geecare'){?>
						<option value="3" <?=$R['cont_com'] == '3' ? 'selected' : '';?>>케어비지트</option><?
					}?>
					<option value="" <?=$R['cont_com'] == '' ? 'selected' : '';?>>기타</option>
				</select>
			</td>
		</tr>
		<tr>
			<th class="head" id="ID_CELL_RS_STR">계약일자</th>
			<td><input id="txtContDt" type="text" value="<?=$myF->dateStyle($R['cont_dt'] ? $R['cont_dt'] : $R['start_dt']);?>" orgDt="<?=$myF->dateStyle($R['cont_dt']);?>" orgDt="<?=$R['cont_dt'];?>" class="date" onchange="lfSetContTerm();"></td>
			<th class="head" id="ID_CELL_DT_STR">계약기간</th>
			<td>
				<input id="txtFromDt" type="text" value="<?=$myF->dateStyle($R['from_dt']);?>" class="date" orgDt="<?=$R['from_dt'];?>" onchange="lfSetContTerm();"> ~
				<input id="txtToDt" type="text" value="<?=$myF->dateStyle($R['to_dt']);?>" class="date" orgDt="<?=$R['to_dt'];?>">
			</td>
			<td class="" colspan="2">
				<label><input id="chkTaxbillYn" type="checkbox" class="checkbox" <?=$R['taxbill_yn'] == 'Y' ? 'checked' : '';?>>세금계산서 발행기관</label>
				<label><input id="chkPopupYn" type="checkbox" class="checkbox" <?=$R['pop_yn'] == 'Y' ? 'checked' : '';?>>팝업해제</label>
			</td>
		</tr>
		<tr>
			<th class="head" colspan="3">지역(한재협외에는 기타로 설정)</th>
			<td>
				<select id="cboArea" name="cbo" style="width:auto; margin-right:0;" onchange="lfSetGroup(this, $('#cboGroup'));"><?
					$sql = 'SELECT	area_cd,area_nm
							FROM	care_area';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['area_cd'];?>" <?=$row['area_cd'] == $R['area_cd'] ? 'selected' : '';?>><?=$row['area_nm'];?></option><?
					}

					$conn->row_free();?>
				</select>
				<select id="cboGroup" name="cbo" style="width:auto; margin-left:0;"></select>
			</td>
			<th class="head">문서요청</th>
			<td class="left">
				<span class="btn_pack small"><button onclick="lfDocCurr('1');">문서1</button></span>
				<!--
				<span class="btn_pack small"><button onclick="lfAsk('01');">계약서</button></span>
				<span class="btn_pack small"><button onclick="lfAsk('02');">등록증</button></span>
				-->
			</td>
		</tr>
		<tr>
			<th class="head" colspan="3">지역(방문간호지역)</th>
			<td colspan="3">
				<select id="cboNurseArea" name="cbo" style="width:auto; margin-right:0;" onchange="lfSetGroup(this, $('#cboNurseGroup'));"><?
					$sql = 'SELECT	area_cd,area_nm
							FROM	care_area';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['area_cd'];?>" <?=$row['area_cd'] == $R['nurse_area_cd'] ? 'selected' : '';?>><?=$row['area_nm'];?></option><?
					}

					$conn->row_free();?>
				</select>
				<select id="cboNurseGroup" name="cbo" style="width:auto; margin-left:0;"></select>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="60px">
		<col width="100px">
		<col width="40px">
		<col width="60px">
		<col width="100px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left bold" colspan="6">- 등록정보</th>
			<th class="left bold" colspan="2">- 이전기관</th>
		</tr>
		<tr>
			<th class="head" rowspan="2">요청</th>
			<th class="head">요청일자</th>
			<td><input id="txtRqtDt" type="text" value="<?=$myF->dateStyle($R['rqt_dt']);?>" class="date"></td>
			<th class="head" rowspan="2">등록</th>
			<th class="head">등록일자</th>
			<td class="center"><div class="left"><?=$myF->dateStyle($R['insert_dt'],'.');?></div></td>
			<th class="head">기관명</th>
			<td>
				<div class="left" style="float:left; width:auto; height:24px;"><span class="btn_pack find" onclick="lfFindCenter();"></span></div>
				<div id="ID_CELL_ORG_CD" class="left" style="float:left; width:auto; display:none;"><?=$oldOrgCd;?></div>
				<div id="ID_CELL_ORG_NM" class="left nowrap" style="float:left; width:150px;"><?=$oldOrgNm;?></div>
			</td>
		</tr>
		<tr>
			<th class="head">요청자</th>
			<td><input id="txtRqtNm" type="text" value="<?=$R['rqt_nm'];?>"></td>
			<th class="head">등록자</th>
			<td><input id="txtRegNm" type="text" value="<?=$R['reg_nm'];?>"></td>
			<th class="head">변경일자</th>
			<td><input id="txtOldOrgDt" type="text" value="<?=$myF->dateStyle($oldOrgDt);?>" class="date"></td>
		</tr>
		<tr>
			<th class="head">전달<br>사항</th>
			<td colspan="7">
				<textarea id="txtRsStr" style="width:620px; height:35px;"><?=StripSlashes($R['rs_str']);?></textarea>
			</td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
	function lfBillCng(){
		var width = 650;
		var height = 500;
		var left = window.screenLeft;
		var top = window.screenTop;
		var option = 'left='+left+'px, top='+top+'px, width='+width+'px,height='+height+'px,scrollbars=no,status=no,resizable=no';
		var url = './center_bill_change.php';
		var win = window.open('about:blank', 'BILL_CNG', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'orgNo':'<?=$ed->en($orgNo);?>'
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

		form.setAttribute('target', 'BILL_CNG');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfBillChangeSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_bill_change_search.php'
		,	data:{
				'orgNo':'<?=$ed->en($orgNo)?>'
			,	'nowGbn':'Y'
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#ID_BILL_CNG tbody').html(html);
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	$(document).ready(function(){
		lfBillChangeSearch();
	});
</script>
<table class="my_table" style="width:671px;">
	<colgroup>
		<col width="80px" span="2">
		<col width="70px">
		<col width="60px">
		<col width="150px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th colspan="7">
				<div style="float:right; width:auto; margin-right:5px;">
					<span class="btn_pack m"><button onclick="lfBillCng();">변경</button></span>
				</div>
				<div class="bold" style="float:left; width:auto;">- 청구정보</div>
			</th>
		</tr>
		<tr>
			<th class="center">적용일자</th>
			<th class="center">종료일자</th>
			<th class="center">청구구분</th>
			<th class="center">선/후불</th>
			<th class="center">CMS 번호</th>
			<th class="center">CMS 회사</th>
			<th class="center">비고</th>
		</tr>
	</tbody>
</table>
<div id="ID_BILL_CNG" style="overflow-x:hidden; overflow-y:scroll; height:56px; border-bottom:2px solid #0e69b0;">
	<table class="my_table" style="width:671px;">
		<colgroup>
			<col width="80px" span="2">
			<col width="70px">
			<col width="60px">
			<col width="150px">
			<col width="100px">
			<col>
		</colgroup>
		<tbody></tbody>
	</table>
</div>

<!--table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="240px">
		<col width="130px">
		<col width="150px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left bold" colspan="5">- 청구정보</th>
		</tr>
		<tr>
			<th class="head">청구구분</th>
			<td>
				<label><input id="optAcctGbn1" name="optAcctGbn" type="radio" class="radio" value="1" <?=$R['acct_gbn'] == '1' ? 'checked' : '';?>>CMS</label>
				<label><input id="optAcctGbn2" name="optAcctGbn" type="radio" class="radio" value="2" <?=$R['acct_gbn'] == '2' ? 'checked' : '';?>>무통장</label>
				<label><input id="optAcctGbn3" name="optAcctGbn" type="radio" class="radio" value="3" <?=$R['acct_gbn'] == '3' ? 'checked' : '';?>>가상계좌</label>
			</td>
			<th class="head">계산서발행</th>
			<td>
				<label><input id="optBillPrtY" name="optBillPrt" type="radio" class="radio" value="Y" <?=$R['bill_yn'] == 'Y' ? 'checked' : '';?>>예</label>
				<label><input id="optBillPrtN" name="optBillPrt" type="radio" class="radio" value="N" <?=$R['bill_yn'] == 'N' ? 'checked' : '';?>>아니오</label>
			</td>
			<td rowspan="2">
				<label><input id="optBankGbn1" name="optBankGbn" type="radio" class="radio" value="1" <?=$R['bank_gbn'] == '1' ? 'checked' : '';?>>개인</label><br>
				<label><input id="optBankGbn2" name="optBankGbn" type="radio" class="radio" value="2" <?=$R['bank_gbn'] == '2' ? 'checked' : '';?>>법인</label>
			</td>
		</tr>
		<tr>
			<th class="head">은행명</th>
			<td><input id="txtBankNm" type="text" value="<?=$R['bank_nm'];?>"></td>
			<th class="head">계좌번호/구분</th>
			<td>
				<input id="txtBankNo" type="text" value="<?=$R['bank_no'];?>" style="width:150px;">
			</td>
		</tr>
		<tr>
			<th class="head">예금주</th>
			<td><input id="txtBankAcct" type="text" value="<?=$R['bank_acct'];?>"></td>
			<th class="head">생년월일/사업자번호</th>
			<td colspan="2"><input id="txtBirthday" type="text" value="<?=$R['birthday'];?>" class="date">/<input id="txtBizNo" type="text" value="<?=$R['bizno'];?>" class="phone" alt="biz"></td>
		</tr>
		<tr>
			<th class="head">CMS추가</th>
			<td>
				<input id="txtAddCMS" type="text" class="no_string" style="width:80px; margin-right:0;">
				<select id="cboCMSCom" style="width:auto; margin:0;">
					<option value="3">케어비지트</option>
					<option value="2">지케어</option>
					<option value="1">굿이오스</option>
				</select>
				<span class="btn_pack small"><button onclick="lfCMSAdd();">추가</button></span>
			</td>
			<th class="head">이체예정일</th>
			<td colspan="2">
				<div style="float:left; width:auto; padding-left:5px;">매월</div>
				<div style="float:left; width:auto;"><input id="txtTransDt" type="text" value="<?=$R['trans_day'];?>" class="no_string" style="width:30px;" maxlength="2"></div>
				<div style="float:left; width:auto;">일</div>
			</td>
		</tr>
		<tr>
			<th class="head" rowspan="2">CMS번호</th>
			<td rowspan="2">
				<div id="ID_CMS_LIST" style="width:100%; height:60px; overflow-x:hidden; overflow-y:scroll; padding-top:5px;"><?
					if (is_array($CMSList)){
						$CMSComList = Array('1'=>'굿이오스', '2'=>'지케어', '3'=>'케어비지트');
						foreach($CMSList as $CMSNo => $CMSCom){
							if ($CMSNo || $CMSCom){?>
								<div class="CLS_CMS" CMSNo="<?=$CMSNo;?>" CMSCom="<?=$CMSCom;?>" class="nowrap" style="float:left; width:95%; margin-left:5px; margin-right:5px;">
									<span><?=$CMSNo;?></span>[<span><?=$CMSComList[$CMSCom];?></span>]<img src="../image/btn_del.png" style="cursor:pointer;" onclick="lfCMSRemove('<?=$CMSNo;?>');">
								</div><?
							}
						}
					}?>
				</div>
			</td>
			<th class="head" rowspan="2">가상계좌</th>
			<td colspan="2">
				<select id="cboVrBankCd" style="width:auto; margin-right:0;"><?
					$sql = 'SELECT	bank_cd, bank_nm
							FROM	cv_bank
							ORDER	BY seq, bank_cd';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['bank_cd'];?>"><?=$row['bank_nm'];?></option><?
					}

					$conn->row_free();?>
				</select>
				<input id="txtVrAcctNo" type="text" value="" style="margin-left:0; margin-right:0;">
				<span class="btn_pack small"><button onclick="lfVrAdd();">추가</button></span>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="ID_VR_ACCT" style="width:205px; height:35px; overflow-x:hidden; overflow-y:scroll; padding-top:5px;"><?
					if (is_array($vrList)){
						foreach($vrList as $vrNo => $bs){
							if ($vrNo){?>
								<div class="CLS_VR" vrNo="<?=$vrNo;?>" bankCd="<?=$bs['bankCd'];?>" keyYn="<?=$bs['keyYn'];?>" class="nowrap" style="margin-left:5px; margin-right:5px;">
									<span id="ID_CELL_KEYPT"><?=$bs['keyYn'] == 'Y' ? '★' : '';?></span><span onclick="lfVrSetKey('<?=$vrNo;?>');"><a href="#"><?=$vrNo;?></a></span>[<span><?=$bs['bankNm'];?></span>]<img src="../image/btn_del.png" style="cursor:pointer;" onclick="lfVrRemove('<?=$vrNo;?>');">
								</div><?
							}
						}
					}?>
				</div>
			</td>
		</tr>
	</tbody>
</table-->

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="370px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">메모</th>
			<td>
				<div id="divMemo" style="width:100%; height:128px; overflow-x:hidden; overflow-y:scroll; padding:5px;"><?=$memo;?></div>
			</td>
			<td class="top">
				<table class="my_table" style="width:217px;">
					<colgroup>
						<col width="110px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">계약예정일</th>
							<td><input id="txtAcctBeDt" type="text" class="date" value="<?=$myF->dateStyle($acctBeDt);?>"></td>
						</tr>
						<tr>
							<th class="center">자동이체 시작년월</th>
							<td><input id="txtCmsStartYm" type="text" class="yymm" value="<?=$myF->_styleYymm($cmsStartYm);?>"></td>
						</tr>
						<tr>
							<td colspan="2">
								<label><input id="chkAdjustFeeYn" type="checkbox" class="checkbox" value="Y" <?=$R['adjust_fee_yn'] == 'Y' ? 'checked' : '';?>>조정요금관리 기관</label>
							</td>
						</tr>
						<tr>
							<td class="bottom" colspan="2">
								<textarea id="txtAdjustFeeNote" style="width:100%; height:40px;"><?=stripslashes($R['adjust_fee_note']);?></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table><?
Unset($R);
?>
<div id="ID_POP" style="position:absolute; left:0; top:0; width:100%; height:100%; display:none; z-index:11; background:url('../image/tmp_bg.png');"></div>
<div style="position:absolute; right:0; top:39px; width:330px; height:184px; background-color:WHITE; border:2px solid #0e69b0;">
	<table class="my_table" style="width:100%;">
		<tbody>
			<tr>
				<th class="bold last" style="height:27px;">- 계약이력</th>
			</tr>
		</tbody>
	</table>
	<div id="ID_HIS" style="overflow-x:hidden; overflow-y:scroll; height:543px;"></div>
</div>