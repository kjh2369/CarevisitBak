<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_mySalary.php');

	//'.$mySalary->_getSalarySvc(11, $salaryHourIf[0]['11'], false).'


	ob_start();

	echo '<base target="_self">';
	echo '<script type="text/javascript" src="../yoyangsa/mem.js"></script>';
	echo '<div class="title title_border">시급제 이력관리</div>
		  <form id="f" name="f" method="post">
			<table class="my_table" style="width:100%;">
				<colgroup>
					<col width="50px">
					<col>
					<col width="50px">
				</colgroup>
				<tbody>
					<tr>
						<th class="center bold" rowspan="4">현재<br>기준</th>
						<td calss="left"><div id="hourlyBody"></div></td>
						<td class="center">
							<div><span class="btn_pack m"><button type="button" onclick="setApply();">적용</button></span></div>
							<div><span class="btn_pack m"><button type="button" onclick="self.close();">닫기</button></span></div>
						</td>
					</tr>
				</tbody>
			</table>
		  </form>';


	echo '<div class="title title_border">변경내역</div>
		  <table class="my_table" style="width:100%;">
			<colgroup>
				<col width="60px">
				<col width="60px">
				<col width="70px">
				<col width="200px">
				<col width="70px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">적용일</th>
					<th class="head">종료일</th>
					<th class="head">산정방식</th>
					<th class="head">금액</th>
					<th class="head">수당포함</th>
					<th class="head">비고</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="center last" colspan="6">
						<div id="listBody" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
					</td>
				</tr>
			</tbody>
		  </table>';


	echo '<script type="text/javascript">
			var opener = null;

			function setHourlyInfo(){
				try{
					$.ajax({
						type: "POST",
						url : "./_find_hourly_info.php",
						data: {
							"code":opener.code
						,	"jumin":opener.jumin
						,	"svcID":opener.svcID
						,	"seq":opener.seq
						},
						beforeSend: function (){
						},
						success: function (xmlHttp){
							$("#hourlyBody").html(xmlHttp);

							var height = $(document).height();
							var top    = __getObjectTop(listBody);

							$("#listBody").height(height - top - 2);

							_memSalarySetSvcSub(opener.svcID);
							__init_form(document.f);
						},
						error: function (){
						}
					}).responseXML;
				}catch(e){
				}
			}

			function search(flag){
				try{
					$.ajax({
						type: "POST",
						url : "./_find_hourly_list.php",
						data: {
							"code":opener.code
						,	"jumin":opener.jumin
						,	"svcID":opener.svcID
						},
						beforeSend: function (){
						},
						success: function (xmlHttp){
							$("#listBody").html(xmlHttp);

							if (flag){
								$seq = setNowData();
								setReturnData($seq);
							}
						},
						error: function (){
						}
					}).responseXML;
				}catch(e){
				}
			}

			function setNowData(){
				$type = $(".setY_type").text();
				$hourly = $(".setY_hourly").text();

				if ($type == "") $type = "0";

				$("#salaryKind_"+opener.svcID+"_"+$type).attr("checked", "checked");

				if ($type == "2"){
					$hourly = $hourly.split("/");
					for($i=0; $i<$hourly.length; $i++){
						$id = ($i < 3 ? $i+1 : 9);
						$("#salaryAmt_"+opener.svcID+"_"+$type+"_"+$id).attr("value", $hourly[$i]);
					}
				}else{
					$("#salaryAmt_"+opener.svcID+"_"+$type).attr("value", $hourly);
				}

				$("#salaryExtra_"+opener.svcID+"_"+$type).attr("checked", $(".setY_extraYN").text() );
				$("#salaryFrom_"+opener.svcID).attr("value", $(".setY_fromDt").text().split(".").join("-") );
				$("#salaryTo_"+opener.svcID).attr("value", $(".setY_toDt").text().split(".").join("-") );

				_memSalarySetSvcSub(opener.svcID);

				return $(".setY_seq").text();
			}

			function setReturnData(seq){
				opener.seq    = seq;
				opener.result = true;
			}

			function setApply(){
				if (!$("#salaryFrom_"+opener.svcID).attr("value")){
					alert("적용일을 입력하여 주십시오..");
					$("#salaryFrom_"+opener.svcID).focus();
					return;
				}

				if (!confirm("입력하신 정보를 적용하시겠습니까?")) return;
				try{
					$.ajax({
						type: "POST",
						url : "./_find_hourly_apply.php",
						data: {
							"code":opener.code
						,	"jumin":opener.jumin
						,	"svcID":opener.svcID
						,	"seq":opener.seq
						,	"type":$(":radio[name=\'salaryKind_"+opener.svcID+"\']:checked").attr("value")
						,	"hourly":__getObjectValue($("#salaryAmt_"+opener.svcID+"_1"))
						,	"varyHourly_1":__getObjectValue($("#salaryAmt_"+opener.svcID+"_2_1"))
						,	"varyHourly_2":__getObjectValue($("#salaryAmt_"+opener.svcID+"_2_2"))
						,	"varyHourly_3":__getObjectValue($("#salaryAmt_"+opener.svcID+"_2_3"))
						,	"varyHourly_4":__getObjectValue($("#salaryAmt_"+opener.svcID+"_2_4"))
						,	"varyHourly_5":__getObjectValue($("#salaryAmt_"+opener.svcID+"_2_5"))
						,	"varyHourly_6":__getObjectValue($("#salaryAmt_"+opener.svcID+"_2_6"))
						,	"varyHourly_7":__getObjectValue($("#salaryAmt_"+opener.svcID+"_2_7"))
						,	"varyHourly_8":__getObjectValue($("#salaryAmt_"+opener.svcID+"_2_8"))
						,	"varyHourly_9":__getObjectValue($("#salaryAmt_"+opener.svcID+"_2_9"))
						,	"hourlyRate":__getObjectValue($("#salaryAmt_"+opener.svcID+"_4"))
						,	"hourlyRateSub":__getObjectValue($("#salaryAmt_"+opener.svcID+"_4_Sub"))
						,	"fixedPay":__getObjectValue($("#salaryAmt_"+opener.svcID+"_3"))
						,	"dailyPay_1":__getObjectValue($("#salaryAmt_"+opener.svcID+"_6_1"))
						,	"dailyPay_2":__getObjectValue($("#salaryAmt_"+opener.svcID+"_6_2"))
						,	"dailyPay_3":__getObjectValue($("#salaryAmt_"+opener.svcID+"_6_3"))
						,	"extraYN":($(":radio[name=\'salaryKind_"+opener.svcID+"\']:checked").attr("value") == "3" ? ($("#salaryExtra_"+opener.svcID+"_3").attr("checked") ? "Y" : "N") : "N")
						,	"fromDT":$("#salaryFrom_"+opener.svcID).attr("value")
						,	"toDT":$("#salaryTo_"+opener.svcID).attr("value")
						},
						beforeSend: function (){
						},
						success: function (xmlHttp){
							if (xmlHttp == "date"){
								alert("등록내역중 입력하신 적용일이 포함된 내역이 있습니다.");
							}else if (xmlHttp == "error"){
								alert("데이타 저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.");
							}else if (!isNaN(xmlHttp)){
								alert("데이타가 정삭적으로 저장되었습니다.");
								setReturnData( xmlHttp );
								self.close();
							}else{
								alert(xmlHttp);
							}
						},
						error: function (){
						}
					}).responseXML;
				}catch(e){
				}
			}

			function rowDelete(seq){
				if (!confirm("선택하신 데이타를 정말로 삭제하시겠습니까?")) return;
				try{
					$.ajax({
						type: "POST",
						url : "./_find_hourly_del.php",
						data: {
							"code":opener.code
						,	"jumin":opener.jumin
						,	"svcID":opener.svcID
						,	"seq":seq
						},
						beforeSend: function (){
						},
						success: function (xmlHttp){
							if (xmlHttp == "error"){
								alert("데이타 삭제중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.");
							}else{
								alert("데이타가 정삭적으로 삭제되었습니다.");
								search(true);
							}
						},
						error: function (){
						}
					}).responseXML;
				}catch(e){
				}
			}

			$(document).ready(function(){
				opener = window.dialogArguments;
				opener.result = false;

				setHourlyInfo();
				search();
			});
		  </script>';

	$html = ob_get_contents();

	ob_clean();

	$html = $myF->_gabSplitHtml($html);

	echo $html;


	include_once('../inc/_footer.php');
?>