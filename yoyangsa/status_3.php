<script language="javascript">

	function _memberStatusList1(){
	//myBody.innerHTML = __loading();
	
	var URL = 'status.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:{
				
				mCode:document.f.mCode.value,
				mKind:document.f.mKind.value,
				mFamily:document.f.familyCare.value,
				mEmployment:document.f.employment.value,
				mInsurance:document.f.insurance.value
			},
			onSuccess:function (responseHttpObj) {
				myBody.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}

function _memberStatusExcel(){
	var f = document.f;

	f.action = 'status_excel.php';
	f.submit();
}
</script>