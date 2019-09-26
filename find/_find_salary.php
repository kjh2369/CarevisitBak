<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');


	ob_start();

	echo '<base target="_self">';

	echo '<div class="title title_border">월급제 이력관리</div>
		  <form id="f" name="f" method="post">
			<table class="my_table" style="width:100%;">
				<colgroup>
					<col width="50px">
					<col width="150px">
					<col>
					<col width="90px">
				</colgroup>
				<tbody>
					<tr>
						<th class="center bold" rowspan="6">현재<br>기준</th>
						<th>기본급</th>
						<td><input id="pay" name="pay" type="text" value="0" class="number" style="width:70px;"></td>
						<td class="left" rowspan="6">
							<div><span class="btn_pack m"><button type="button" onclick="setApply();">적용</button></span></div>
							<div><span class="btn_pack m"><button type="button" onclick="self.close();">닫기</button></span></div>
						</td>
					</tr>
					<tr>
						<th>케어금액포함여부</th>
						<td>
							<input id="careY" name="careYN" type="radio" value="Y" class="radio"><label for="careY">예</label>
							<input id="careN" name="careYN" type="radio" value="N" class="radio"><label for="careN">아니오</label>
						</td>
					</tr>
					<tr>
						<th>목욕,간호수당포함여부</th>
						<td>
							<input id="extraY" name="extraYN" type="radio" value="Y" class="radio"><label for="extraY">예</label>
							<input id="extraN" name="extraYN" type="radio" value="N" class="radio"><label for="extraN">아니오</label>
						</td>
					</tr>
					<tr>
						<th>근무20일(X기준근로시간)</th>
						<td>
							<input id="day20Y" name="day20YN" type="radio" value="Y" class="radio"><label for="day20Y">예</label>
							<input id="day20N" name="day20YN" type="radio" value="N" class="radio"><label for="day20N">아니오</label>
						</td>
					</tr>';

	//월급제 처우개선비 관리
	echo '		<tr>
					<th>처우개선비</th>
					<td><input id="dealPay" name="dealPay" type="text" value="0" class="number" style="width:70px;"></td>
				</tr>';

	echo '			<tr>
						<th>적용기간</th>
						<td>
							<input id="fromDT" name="fromDT" type="text" value="" class="yymm">~
							<input id="toDT" name="toDT" type="text" value="" class="yymm">
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
				<col width="60px">
				<col width="50px">
				<col width="50px">
				<col width="50px">
				<col width="60px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">적용일</th>
					<th class="head">종료일</th>
					<th class="head">기본급</th>
					<th class="head">케어</th>
					<th class="head">목/간</th>
					<th class="head">20일</th>
					<th class="head">처우비</th>
					<th class="head">비고</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="center last" colspan="8">
						<div id="listBody" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
					</td>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td class="center bottom last" colspan="8">
						<div class="left" style="line-height:1.5em;">
							- 근무 20일<br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;월근무일수를 20일로 산정하며 근무시간은 20일 X 기준근로시간으로 산정합니다.<br><br>
							ex&nbsp;&nbsp;기준근로시간이 2.5시간인 경우 근무시간은 50시간으로 산정되며<br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;기준근로시간이 8시간인 경우 근무시간 160시간으로 산정됩니다.
						</div>
					</td>
				</tr>
			</tbody>
		  </table>';


	echo '<script type="text/javascript">
			var opener = null;

			function search(flag){
				try{
					$.ajax({
						type: "POST",
						url : "./_find_salary_list.php",
						data: {
							"code":opener.code
						,	"jumin":opener.jumin
						},
						beforeSend: function (){
						},
						success: function (xmlHttp){
							$("#listBody").html(xmlHttp);

							if (flag){
								setNowData();
								setReturnData();
							}
						},
						error: function (){
						}
					}).responseXML;
				}catch(e){
				}
			}

			function setNowData(){
				$("#pay").attr("value", $(".setY_pay").text() );
				$(":radio[name=\'careYN\']:radio[value=\'"+$(".setY_careYN").text()+"\']").attr("checked","checked");
				$(":radio[name=\'extraYN\']:radio[value=\'"+$(".setY_extraYN").text()+"\']").attr("checked","checked");
				$(":radio[name=\'day20YN\']:radio[value=\'"+$(".setY_day20YN").text()+"\']").attr("checked","checked");
				$("#dealPay").val($(".setY_dealPay").text());
				$("#fromDT").attr("value", $(".setY_fromDt").text().split(".").join("-") );
				$("#toDT").attr("value", $(".setY_toDt").text().split(".").join("-") );
			}

			function setReturnData(){
				opener.pay     = $("#pay").attr("value");
				opener.careYN  = $(":radio[name=\'careYN\']:checked").attr("value");
				opener.extraYN = $(":radio[name=\'extraYN\']:checked").attr("value");
				opener.day20YN = $(":radio[name=\'day20YN\']:checked").attr("value");
				opener.dealPay = $("#dealPay").val();
				opener.from    = $("#fromDT").attr("value");
				opener.to      = $("#toDT").attr("value");
				opener.result  = true;
			}

			function setApply(){
				if ($("#pay").attr("value") == 0){
					alert("기본급을 입력하여 주십시오.");
					$("#pay").focus();
					return;
				}
				
				
				if (!$("#fromDT").attr("value").replace("-","")){
					alert("적용일을 입력하여 주십시오..");
					$("#fromDT").focus();
					return;
				}

				if (!$("#toDT").attr("value").replace("-","")){
					alert("종료일을 입력하여 주십시오..");
					$("#toDT").focus();
					return;
				}
				
				var diffDt = __DateDiff(__getDate($("#fromDT").val()+"-01"), __getDate($("#toDT").val()+"-01"));
				
				if (diffDt < 0){
					alert("적용기간 입력 오류입니다. 종료일을 적용일보다 크게 입력하여 주십시오.");
					$("#toDT").focus();
					return;
				}

				if (!confirm("입력하신 월급정보를 적용하시겠습니까?")) return;
				try{
					$.ajax({
						type: "POST",
						url : "./_find_salary_apply.php",
						data: {
							"code":opener.code
						,	"jumin":opener.jumin
						,   "pay":$("#pay").attr("value")
						,   "careYN":$(":radio[name=\'careYN\']:checked").attr("value")
						,   "extraYN":$(":radio[name=\'extraYN\']:checked").attr("value")
						,	"day20YN":$(":radio[name=\'day20YN\']:checked").attr("value")
						,	"dealPay":$("#dealPay").val()
						,   "fromDT":$("#fromDT").attr("value")
						,   "toDT":$("#toDT").attr("value")
						},
						beforeSend: function (){
						},
						success: function (xmlHttp){
							if (xmlHttp == "date"){
								alert("등록내역중 입력하신 적용일이 포함된 내역이 있습니다.");
							}else if (xmlHttp == "error"){
								alert("데이타 저장중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.");
							}else{
								alert("데이타가 정삭적으로 저장되었습니다.");
								setReturnData();
								self.close();
							}
						},
						error: function (){
						}
					}).responseXML;
				}catch(e){
				}
			}

			function rowDelete(){
				if (!confirm("선택하신 데이타를 정말로 삭제하시겠습니까?")) return;
				try{
					$.ajax({
						type: "POST",
						url : "./_find_salary_del.php",
						data: {
							"code":opener.code
						,	"jumin":opener.jumin
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
				var height = $(document).height();
				var top    = __getObjectTop(listBody);

				$("#listBody").height(height - top - 2 - 100);

				opener = window.dialogArguments;
				opener.result = false;

				$("#pay").attr("value", __str2num(opener.pay));
				$(":radio[name=\'careYN\']:radio[value=\'"+opener.careYN+"\']").attr("checked","checked");
				$(":radio[name=\'extraYN\']:radio[value=\'"+opener.extraYN+"\']").attr("checked","checked");
				$(":radio[name=\'day20YN\']:radio[value=\'"+opener.day20YN+"\']").attr("checked","checked");
				$("#dealPay").val(opener.dealPay);

				$("#fromDT").attr("value", opener.from);
				$("#toDT").attr("value", opener.to);

				__init_form(document.f);

				document.body.focus();

				search();
			});
		  </script>';

	$html = ob_get_contents();

	ob_clean();

	$html = $myF->_gabSplitHtml($html);

	echo $html;


	include_once('../inc/_footer.php');
?>