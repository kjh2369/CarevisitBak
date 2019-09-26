var _TIMER_ADD_ = null;

function _set_care_values(){
	var day   = document.f.addDay.value;
	var index = document.f.addIndex.value;
	
	try{
		var mDate       = eval('opener.document.f.mDate_'+day+'_'+index).value;
		var mSvcSubCode = eval('opener.document.f.mSvcSubCode_'+day+'_'+index).value;
		var mSvcSubCD   = eval('opener.document.f.mSvcSubCD_'+day+'_'+index).value;
		var mFmTime     = eval('opener.document.f.mFmTime_'+day+'_'+index).value;
		var mToTime     = eval('opener.document.f.mToTime_'+day+'_'+index).value;
		var mProcTime   = eval('opener.document.f.mProcTime_'+day+'_'+index).value;
		var mTogeUmu    = eval('opener.document.f.mTogeUmu_'+day+'_'+index).value;
		var mBiPayUmu   = eval('opener.document.f.mBiPayUmu_'+day+'_'+index).value;
		var mYoy1       = eval('opener.document.f.mYoy1_'+day+'_'+index).value;
		var mYoy2       = eval('opener.document.f.mYoy2_'+day+'_'+index).value;
		var mYoyNm1     = eval('opener.document.f.mYoyNm1_'+day+'_'+index).value;
		var mYoyNm2     = eval('opener.document.f.mYoyNm2_'+day+'_'+index).value;
		var mYoyTA1     = eval('opener.document.f.mYoyTA1_'+day+'_'+index).value;
		var mYoyTA2     = eval('opener.document.f.mYoyTA2_'+day+'_'+index).value;
		var mSValue     = eval('opener.document.f.mSValue_'+day+'_'+index).value;
		var mEValue     = eval('opener.document.f.mEValue_'+day+'_'+index).value;
		var mNValue     = eval('opener.document.f.mNValue_'+day+'_'+index).value;
		var mTValue     = eval('opener.document.f.mTValue_'+day+'_'+index).value;
		var mSugaCode   = eval('opener.document.f.mSugaCode_'+day+'_'+index).value;
		var mSugaName   = eval('opener.document.f.mSugaName_'+day+'_'+index).value;
		var mEGubun     = eval('opener.document.f.mEGubun_'+day+'_'+index).value;
		var mNGubun     = eval('opener.document.f.mNGubun_'+day+'_'+index).value;
		var mETime      = eval('opener.document.f.mETime_'+day+'_'+index).value;
		var mNTime      = eval('opener.document.f.mNTime_'+day+'_'+index).value;

		var mWeekDay    = eval('opener.document.f.mWeekDay_'+day+'_'+index).value;
		var mSubject    = eval('opener.document.f.mSubject_'+day+'_'+index).value;
		var mUse        = eval('opener.document.f.mUse_'+day+'_'+index).value;
		var mDuplicate  = eval('opener.document.f.mDuplicate_'+day+'_'+index).value;
		var mCarNo      = eval('opener.document.f.mCarNo_'+day+'_'+index).value;
		var mSudangYN   = eval('opener.document.f.mSudangYN_'+day+'_'+index).value;
		var mSudang     = eval('opener.document.f.mSudang_'+day+'_'+index).value;
		var mSudangYul1 = eval('opener.document.f.mSudangYul1_'+day+'_'+index).value;
		var mSudangYul2 = eval('opener.document.f.mSudangYul2_'+day+'_'+index).value;
		var mOldDate    = eval('opener.document.f.mOldDate_'+day+'_'+index).value;

		var bipay1 = eval('opener.document.f.mBipay1_'+day+'_'+index).value;
		var bipay2 = eval('opener.document.f.mBipay2_'+day+'_'+index).value;
		var bipay3 = eval('opener.document.f.mBipay3_'+day+'_'+index).value;
		
		var exp_yn  = eval('opener.document.f.mExpenseYn_'+day+'_'+index).value;
		var exp_pay = eval('opener.document.f.mExpensePay_'+day+'_'+index).value;
		
		
		
		/**************************************************
		
			기타
		
		**************************************************/
			var other_if = eval('opener.document.f.mOther_'+day+'_'+index).value.split('&');
			
			for(var i=0; i<other_if.length; i++){
				var val = other_if[i].split('=');
				
				other_if[val[0]] = val[1];
			}
			
			var bipay_kind = document.getElementsByName('bipay_kind');
			
			for(var i=0; i<bipay_kind.length; i++){
				if (bipay_kind[i].value == other_if['bipay_kind']){
					bipay_kind[i].checked = true;
					break;
				}
			}
		/*************************************************/
		
		

		switch(mSvcSubCode){
			case '200':
				document.f.svcSubCode[0].checked  = true;
				document.f.svcSubCode[1].disabled = true;
				document.f.svcSubCode[2].disabled = true;

				if (mProcTime >= 300) mProcTime = 0;
				if (mProcTime == 0){
					document.f.ttHour.readOnly = false;
					document.f.ttMin.readOnly  = false;
					document.f.ttHour.style.backgroundColor = '#ffffff';
					document.f.ttMin.style.backgroundColor  = '#ffffff';
					document.f.ttHour.onfocus = function(){document.f.ttHour.select();}
					document.f.ttMin.onfocus  = function(){document.f.ttMin.select();}
				}else{
					document.f.ttHour.readOnly = true;
					document.f.ttMin.readOnly  = true;
					document.f.ttHour.style.backgroundColor = '#eeeeee';
					document.f.ttMin.style.backgroundColor  = '#eeeeee';
					document.f.ttHour.onfocus = function(){document.f.procTime.focus();}
					document.f.ttMin.onfocus  = function(){document.f.procTime.focus();}
				}

				break;
			case '500':
				document.f.svcSubCode[1].checked  = true;
				document.f.svcSubCode[0].disabled = true;
				document.f.svcSubCode[2].disabled = true;

				_setSvc2();
				_setSvc2Sub();

				mProcTime = mSugaCode;
				mProcTime = mProcTime.substring(0,3);
				mProcTime = mProcTime.substring(2,3);

				if (mProcTime == 'F'){
				//	txtCarNo.style.display = 'none';
				}
				break;
			case '800':
				document.f.svcSubCode[2].checked  = true;
				document.f.svcSubCode[0].disabled = true;
				document.f.svcSubCode[1].disabled = true;
				try{
					_setSvc3();
				}catch(e){}
				break;
		}

		if (mWeekDay == '7'){
			mWeekDay = '0';
		}
		document.f.ftHour.value   = mFmTime.substring(0,2);
		document.f.ftMin.value    = mFmTime.substring(2,4);
		document.f.ttHour.value   = mToTime.substring(0,2);
		document.f.ttMin.value    = mToTime.substring(2,4);
		document.f.procTime.value = mProcTime;

		document.f.togeUmu.checked  = (mTogeUmu  == 'Y' ? true : false);
		document.f.bipayUmu.checked = (mBiPayUmu == 'Y' ? true : false);

		document.f.weekDay1.checked = (mWeekDay == '1' ? true : false);
		document.f.weekDay2.checked = (mWeekDay == '2' ? true : false);
		document.f.weekDay3.checked = (mWeekDay == '3' ? true : false);
		document.f.weekDay4.checked = (mWeekDay == '4' ? true : false);
		document.f.weekDay5.checked = (mWeekDay == '5' ? true : false);
		document.f.weekDay6.checked = (mWeekDay == '6' ? true : false);
		document.f.weekDay0.checked = (mWeekDay == '0' ? true : false);

		document.f.svcSubCD.value = mSvcSubCD;

		document.f.yoyNm1.value = mYoyNm1;
		document.f.yoyNm2.value = mYoyNm2;
		document.f.yoy1.value = mYoy1;
		document.f.yoy2.value = mYoy2;
		document.f.yoyTA1.value = mYoyTA1;
		document.f.yoyTA2.value = mYoyTA2;

		document.f.sPrice.value = mSValue;
		document.f.ePrice.value = mEValue;
		document.f.nPrice.value = mNValue;
		document.f.tPrice.value = mTValue;

		document.f.sugaCode.value = mSugaCode;
		document.f.sugaName.value = mSugaName;
		document.f.Egubun.value = mEGubun;
		document.f.Ngubun.value = mNGubun;
		document.f.Etime.value = mETime;
		document.f.Ntime.value = mNTime;
		document.f.carNo.value = mCarNo;
		document.f.visitSudangCheck.checked = (mSudangYN == 'Y' ? true : false);
		document.f.visitSudang.value = mSudang;
		document.f.sudangYul1.value = mSudangYul1;
		document.f.sudangYul2.value = mSudangYul2;
		document.f.oldDate.value = mOldDate; //mDate + mFmTime + mToTime;
		
		document.f.bipay_cost1.value = bipay1;
		document.f.bipay_cost2.value = bipay2;
		document.f.bipay_cost3.value = bipay3;
		
		if (exp_yn == 'Y')
			document.f.exp_yn[0].checked = true;
		else
			document.f.exp_yn[1].checked = true;
			
		document.f.exp_pay.value = exp_pay;

		checkVisitSugang(document.f.visitSudangCheck.checked);

		sugaCont.innerHTML = mSugaName;
	}catch(e){
		if (e instanceof Error){
			alert('System Error : ' + e.description);
		}else if (typeof(e) == 'string'){
			alert('String Error : ' + e.description);
		}else{
			alert('Error Number : '+e.number+'\n Description : '+e.description);
		}
	}

	clearInterval(_TIMER_ADD_);
	_set_bipay_pay('ADD');
}

function _set_voucher_values(){
	var day   = document.f.addDay.value;
	var index = document.f.addIndex.value;
	var mode  = document.getElementById('mode').value;

	try{
		/**************************************************
		
			제공서비스
		
		**************************************************/
		var svc_kind = eval('opener.document.f.mSvcSubCode_'+day+'_'+index).value;
		
		if (svc_kind == '200'){
			var id = 0;
		}else if (svc_kind == '500'){
			var id = 1;
		}else if (svc_kind == '800'){
			var id = 2;
		}else{
			var id = -1;
		}
		
		if (id != -1){
			document.getElementsByName('svcSubCode')[id].checked = true;
			_set_voucher_svc();
		}
		
		
		/**************************************************
		
			제공서비스 링크 삭제
		
		**************************************************/
		var svc_link = document.getElementsByName('linkSvcCode');
			
		if (svc_link.length > 0){
			for(var i=0; i<svc_link.length; i++){
				svc_link[i].onclick = null;
				svc_link[i].style.cursor = 'default';
			}
		}
		
		var mYoy1   = eval('opener.document.f.mYoy1_'+day+'_'+index).value;
		var mYoyNm1 = eval('opener.document.f.mYoyNm1_'+day+'_'+index).value;
		var mYoyTA1 = eval('opener.document.f.mYoyTA1_'+day+'_'+index).value;
		
		document.f.yoyNm1.value = mYoyNm1;
		document.f.yoy1.value   = mYoy1;
		document.f.yoyTA1.value = mYoyTA1;
		
		try{
			var mYoy2   = eval('opener.document.f.mYoy2_'+day+'_'+index).value;
			var mYoyNm2 = eval('opener.document.f.mYoyNm2_'+day+'_'+index).value;
			var mYoyTA2 = eval('opener.document.f.mYoyTA2_'+day+'_'+index).value;
			
			document.f.yoyNm2.value = mYoyNm2;
			document.f.yoy2.value   = mYoy2;
			document.f.yoyTA2.value = mYoyTA2;
		}catch(e){
		}

		var mFmTime   = eval('opener.document.f.mFmTime_'+day+'_'+index).value;
		var mToTime   = eval('opener.document.f.mToTime_'+day+'_'+index).value;
		var mProcTime = eval('opener.document.f.mProcStr_'+day+'_'+index).value;
		
		document.f.ftHour.value   = mFmTime.substring(0,2);
		document.f.ftMin.value    = mFmTime.substring(2,4);
		document.f.ttHour.value   = mToTime.substring(0,2);
		document.f.ttMin.value    = mToTime.substring(2,4);
		document.f.procTime.value = mProcTime; //_getTimeDiff(mFmTime,mToTime) / 60;
		
		if (eval('opener.document.f.mKind_'+day+'_'+index).value >= '0' && eval('opener.document.f.mKind_'+day+'_'+index).value <= '4'){
			/**************************************************
			
				비급여 실비처리
			
			**************************************************/
				var bipay1 = eval('opener.document.f.mBipay1_'+day+'_'+index).value;
				var bipay2 = eval('opener.document.f.mBipay2_'+day+'_'+index).value;
				var bipay3 = eval('opener.document.f.mBipay3_'+day+'_'+index).value;
				
				var exp_yn  = eval('opener.document.f.mExpenseYn_'+day+'_'+index).value;
				var exp_pay = eval('opener.document.f.mExpensePay_'+day+'_'+index).value;
				
				document.f.bipay_cost1.value = __num2str(bipay1);
				document.f.bipay_cost2.value = __num2str(bipay2);
				document.f.bipay_cost3.value = __num2str(bipay3);
				
				if (exp_yn == 'Y')
					document.f.exp_yn[0].checked = true;
				else
					document.f.exp_yn[1].checked = true;
					
				document.f.exp_pay.value = __num2str(exp_pay);
			/*************************************************/
			
			document.f.sugaCost.value = __num2str(eval('opener.document.f.mSValue_'+day+'_'+index).value);
			
			var bipay_yn = eval('opener.document.f.mBiPayUmu_'+day+'_'+index).value;
			
			if (bipay_yn == 'Y'){
				document.f.bipayUmu.checked = true;
				_set_bipay_yn();
			}
		}else{
			document.f.sugaCost.value = __num2str(eval('opener.document.f.mSValue_'+day+'_'+index).value);
		}
		
		/**************************************************
		
			추가수당
		
		**************************************************/
		if (eval('opener.document.f.mKind_'+day+'_'+index).value == '3' && eval('opener.document.f.mKind_'+day+'_'+index).value == 'A'){
			var addpay_if = eval('opener.document.f.mAddPay_'+day+'_'+index).value.split('&');
			
			for(var i=0; i<addpay_if.length; i++){
				var val = addpay_if[i].split('=');
				
				addpay_if[val[0]] = val[1];
			}
			
			var school_not_cnt = parseInt(addpay_if['school_not_cnt'],10);
			var school_not_pay = parseInt(addpay_if['school_not_cost'],10);
			var school_cnt     = parseInt(addpay_if['school_cnt'],10);
			var school_pay     = parseInt(addpay_if['school_cost'],10);
			var family_cnt     = parseInt(addpay_if['family_cnt'],10);
			var family_pay     = parseInt(addpay_if['family_cost'],10);
			var home_in_yn     = parseInt(addpay_if['home_in_yn'],10);
			var home_in_pay    = parseInt(addpay_if['home_in_cost'],10);
			var holiday_pay    = parseInt(addpay_if['holiday_cost'],10);
			var tot_pay        = (school_not_cnt * school_not_pay) + (school_cnt * school_pay) + (family_cnt * family_pay) + home_in_pay + holiday_pay;
			
			document.f.school_not_cnt.value = __num2str(school_not_cnt);
			document.f.school_not_pay.value = __num2str(school_not_pay);
			document.f.school_cnt.value     = __num2str(school_cnt);
			document.f.school_pay.value     = __num2str(school_pay);
			document.f.family_cnt.value     = __num2str(family_cnt);
			document.f.family_pay.value     = __num2str(family_pay);
			document.f.home_in_yn.checked   = (home_in_yn == 'Y' ? true : false);
			document.f.home_in_pay.value    = __num2str(home_in_pay);
			document.f.holiday_pay.value    = __num2str(holiday_pay);
			
			if (home_in_yn == 'Y'){
				document.f.home_in_pay.disabled = false;
				document.f.home_in_pay.style.backgroundColor = '#ffffff';
			}
			
			document.getElementById('addpay_tot').innerHTML = __num2str(tot_pay);
		}
		/*************************************************/
		
		
		
		/**************************************************
		
			기타
		
		**************************************************/
			var other_if = eval('opener.document.f.mOther_'+day+'_'+index).value.split('&');
			
			for(var i=0; i<other_if.length; i++){
				var val = other_if[i].split('=');
				
				other_if[val[0]] = val[1];
			}
			
			var bipay_kind = document.getElementsByName('bipay_kind');
			
			for(var i=0; i<bipay_kind.length; i++){
				if (bipay_kind[i].value == other_if['bipay_kind']){
					bipay_kind[i].checked = true;
					break;
				}
			}
		/*************************************************/
		
		
		
		if (mode == 'MODIFY'){
			document.f.ftHour.readOnly = true;
			document.f.ftMin.readOnly  = true;
			document.f.ftHour.style.backgroundColor = '#efefef';
			document.f.ftMin.style.backgroundColor  = '#efefef';
			document.f.ftHour.onfocus = function(){document.f.ttHour.select();}
			document.f.ftMin.onfocus  = function(){document.f.ttHour.select();}

			_get_iljung_suga();
		}
	}catch(e){
		if (e instanceof Error){
			alert('System Error : ' + e.description);
		}else if (typeof(e) == 'string'){
			alert('String Error : ' + e.description);
		}else{
			alert('Error Number : '+e.number+'\n Description : '+e.description);
		}
	}

	clearInterval(_TIMER_ADD_);
	
	if (eval('opener.document.f.mKind_'+day+'_'+index).value >= '0' && eval('opener.document.f.mKind_'+day+'_'+index).value <= '4'){
		_set_bipay_pay('ADD');
	}
}