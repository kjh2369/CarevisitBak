<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');


	ob_start();

	echo '<base target="_self">';



	echo '<div class="title title_border">기준근로시간 및 시급 변경</div>
		  <form id="f" name="f" method="post">
		  <table class="my_table" style="width:100%;">
			<colgroup>
				<col width="50px">
				<col width="60px">
				<col width="50px">
				<col width="60px">
				<col width="70px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th class="center bold" rowspan="2">현재<br>기준</th>
					<th class="center">기준시간</th>
					<td><input id="hours" name="hours" type="text" value="0" class="number" style="width:50px;" onkeydown="__onlyNumber(this, \'.\');"></td>
					<th class="center">기준시급</th>
					<td><input id="hourly" name="hourly" type="text" value="0" class="number" style="width:70px;"></td>
					<td rowspan="2" style="padding-left:3px;">
						<span class="btn_pack m"><button type="button" onclick="setApply();">적용</button></span>&nbsp;
						<!--span class="btn_pack m"><button type="button" onclick="setNowData();">현재</button></span-->
						<span class="btn_pack m"><button type="button" onclick="self.close();">닫기</button></span>
					</td>
				</tr>
				<tr>
					<th class="center">적용기간</th>
					<td colspan="3">
						<input id="fromDT" name="fromDT" type="text" value="" class="yymm">~
						<input id="toDT" name="toDT" type="text" value="" class="yymm" readonly>
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
				<col width="70px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">적용일</th>
					<th class="head">종료일</th>
					<th class="head">기준시간</th>
					<th class="head">기준시급</th>
					<th class="head">비고</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="center last" colspan="5">
						<div id="listBody" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
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
						url : "./_find_fixedworks_list.php",
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
				$("#hours").attr("value", $(".setY_hours").text() );
				$("#hourly").attr("value", $(".setY_hourly").text() );
				$("#fromDT").attr("value", $(".setY_fromDt").text().split(".").join("-") );
				$("#toDT").attr("value", $(".setY_toDt").text().split(".").join("-") );
			}

			function setReturnData(){
				opener.hours  = $("#hours").attr("value");
				opener.hourly = $("#hourly").attr("value");
				opener.from   = $("#fromDT").attr("value");
				opener.to     = $("#toDT").attr("value");
				opener.result = true;
			}

			function setApply(){
				if (!confirm("입력하신 기준근로 시간 및 시급을 적용하시겠습니까?")) return;
				try{
					$.ajax({
						type: "POST",
						url : "./_find_fixedworks_apply.php",
						data: {
							"code":opener.code
						,	"jumin":opener.jumin
						,   "hours":$("#hours").attr("value")
						,   "hourly":$("#hourly").attr("value")
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
							}else if (xmlHttp == "ok"){
								alert("데이타가 정삭적으로 저장되었습니다.");
								/*setNowData();*/
								setReturnData();
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

			function rowDelete(){
				if (!confirm("선택하신 데이타를 정말로 삭제하시겠습니까?")) return;
				try{
					$.ajax({
						type: "POST",
						url : "./_find_fixedworks_del.php",
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

				$("#listBody").height(height - top - 2);

				opener = window.dialogArguments;
				opener.result = false;

				__init_form(document.f);

				$("#hours").attr("value", opener.hours);
				$("#hourly").attr("value", opener.hourly);
				$("#fromDT").attr("value", opener.from);
				$("#toDT").attr("value", opener.to);

				search();
			});
		  </script>';

	$html = ob_get_contents();

	ob_clean();

	$html = $myF->_gabSplitHtml($html);

	echo $html;


	include_once('../inc/_footer.php');
?>