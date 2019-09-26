<?
	include("../inc/_header.php");
	include("../inc/_page_list.php");

	$year  = substr($_GET['mDate'],0,4);
	$month = substr($_GET['mDate'],4,2);
?>
<style>
body{
	margin-top:0px;
	margin-left:0px;
}
</style>
<script language='javascript'>
<!--
var opener = window.dialogArguments
var retVal = 'cancel';

function _currnetRow(value1, value2, value3, value4, value5){
	var currentItem = new Array();

	currentItem[0] = value1;
	currentItem[1] = value2;
	currentItem[2] = value3;
	currentItem[3] = value4;
	currentItem[4] = value5;

	window.returnValue = currentItem;
	window.close();
}
//-->
</script>
<form name="f" method="post">
<div id="center_info"></div>
<div id="add_iljung"></div>
</form>
<script language='javascript'>
	_setCenterInfo('<?=$_GET["mCode"];?>', '<?=$_GET["mKind"];?>', '<?=$_GET["mKey"];?>','<?=$year;?>','<?=$month;?>');
	add_iljung.innerHTML = getHttpRequest('su_in_iljung.php?mCode=<?=$_GET["mCode"];?>&mKind=<?=$_GET["mKind"];?>&mKey=<?=$_GET["mKey"];?>&mDay=<?=$_GET["mDay"];?>&mIndex=<?=$_GET["mIndex"];?>&mDate=<?=$_GET["mDate"];?>&mWeek=<?=$_GET["mWeek"];?>&mMode=<?=$_GET["mMode"];?>');
</script>
<?
	if ($_GET["mMode"] == 'MODIFY'){
		$mDay = $_GET["mDay"];
		$mIndex = $_GET["mIndex"];
		?>
		<script language='javascript'>
			document.body.onload = function(){
				setValues();
			}
			function setValues(){
				try{
					var mDate       = eval('opener.document.f.mDate_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mSvcSubCode = eval('opener.document.f.mSvcSubCode_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mSvcSubCD   = eval('opener.document.f.mSvcSubCD_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mFmTime     = eval('opener.document.f.mFmTime_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mToTime     = eval('opener.document.f.mToTime_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mProcTime   = eval('opener.document.f.mProcTime_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mTogeUmu    = eval('opener.document.f.mTogeUmu_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mBiPayUmu   = eval('opener.document.f.mBiPayUmu_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mTimeDoub   = eval('opener.document.f.mTimeDoub_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoy1       = eval('opener.document.f.mYoy1_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoy2       = eval('opener.document.f.mYoy2_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoy3       = eval('opener.document.f.mYoy3_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoy4       = eval('opener.document.f.mYoy4_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoy5       = eval('opener.document.f.mYoy5_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoyNm1     = eval('opener.document.f.mYoyNm1_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoyNm2     = eval('opener.document.f.mYoyNm2_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoyNm3     = eval('opener.document.f.mYoyNm3_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoyNm4     = eval('opener.document.f.mYoyNm4_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoyNm5     = eval('opener.document.f.mYoyNm5_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoyTA1     = eval('opener.document.f.mYoyTA1_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoyTA2     = eval('opener.document.f.mYoyTA2_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoyTA3     = eval('opener.document.f.mYoyTA3_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoyTA4     = eval('opener.document.f.mYoyTA4_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mYoyTA5     = eval('opener.document.f.mYoyTA5_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mSValue     = eval('opener.document.f.mSValue_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mEValue     = eval('opener.document.f.mEValue_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mNValue     = eval('opener.document.f.mNValue_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mTValue     = eval('opener.document.f.mTValue_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mSugaCode   = eval('opener.document.f.mSugaCode_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mSugaName   = eval('opener.document.f.mSugaName_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mEGubun     = eval('opener.document.f.mEGubun_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mNGubun     = eval('opener.document.f.mNGubun_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mETime      = eval('opener.document.f.mETime_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mNTime      = eval('opener.document.f.mNTime_<?=$mDay;?>_<?=$mIndex;?>').value;

					var mWeekDay    = eval('opener.document.f.mWeekDay_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mSubject    = eval('opener.document.f.mSubject_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mUse        = eval('opener.document.f.mUse_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mDuplicate  = eval('opener.document.f.mDuplicate_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mCarNo      = eval('opener.document.f.mCarNo_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mSudangYN   = eval('opener.document.f.mSudangYN_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mSudang     = eval('opener.document.f.mSudang_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mSudangYul1 = eval('opener.document.f.mSudangYul1_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mSudangYul2 = eval('opener.document.f.mSudangYul2_<?=$mDay;?>_<?=$mIndex;?>').value;
					var mOldDate    = eval('opener.document.f.mOldDate_<?=$mDay;?>_<?=$mIndex;?>').value;

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
								txtCarNo.style.display = 'none';
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
					document.f.timeDoub.checked = (mTimeDoub == 'Y' ? true : false);

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
					document.f.yoyNm3.value = mYoyNm3;
					document.f.yoyNm4.value = mYoyNm4;
					document.f.yoyNm5.value = mYoyNm5;
					document.f.yoy1.value = mYoy1;
					document.f.yoy2.value = mYoy2;
					document.f.yoy3.value = mYoy3;
					document.f.yoy4.value = mYoy4;
					document.f.yoy5.value = mYoy5;
					document.f.yoyTA1.value = mYoyTA1;
					document.f.yoyTA2.value = mYoyTA2;
					document.f.yoyTA3.value = mYoyTA3;
					document.f.yoyTA4.value = mYoyTA4;
					document.f.yoyTA5.value = mYoyTA5;

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
			}
		</script>
		<?
	}

	include("../inc/_footer.php");
?>