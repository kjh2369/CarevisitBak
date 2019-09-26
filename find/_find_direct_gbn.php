<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');


	ob_start();

	
	echo '<script type="text/javascript">
			var opener = null;

			function search(flag){
				try{
					$.ajax({
						type: "POST",
						url : "./_find_direct_gbn_list.php",
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
				
				
				$("#yymm").attr("value", $(".setY_yymm").text().split(".").join("-") );
				if($(".setY_gbn").text().split(".").join("-")==\'간접인건비\'){
					$(\'#directGbn_1\').attr("checked", false);
					$(\'#directGbn_2\').attr("checked", true);
					
				}else {
					$(\'#directGbn_1\').attr("checked", true);
					$(\'#directGbn_2\').attr("checked", false);					
				}
				
			}

			function setReturnData(){
				opener.direct_gbn = $(\':radio[name="directGbn"]:checked\').attr("value");
				opener.from   = $("#yymm").attr("value");
				opener.result = true;
			}

			function setApply(){
				
				if (!confirm("입력하신 정보를 적용하시겠습니까?")) return;
				try{
					$.ajax({
						type: "POST",
						url : "./_find_direct_gbn_apply.php",
						data: {
							"code":opener.code
						,	"jumin":opener.jumin
						,   "direct_gbn":$(\':radio[name="directGbn"]:checked\').attr("value")
						,   "yymm":$("#yymm").attr("value")
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

			function rowDelete(yymm){
				if (!confirm("선택하신 데이타를 정말로 삭제하시겠습니까?")) return;
				try{
					$.ajax({
						type: "POST",
						url : "./_find_direct_gbn_del.php",
						data: {
							"code":opener.code
						,	"jumin":opener.jumin
						,   "yymm":yymm
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
				
				
				$("#directGbn").attr("value", opener.direct_gbn);
				$("#yymm").attr("value", opener.from);

				search(true);
			});
		  </script>';
	
	
	echo '<base target="_self">';



	echo '<div class="title title_border">직,간접 인건비 구분 변경</div>
		  <form id="f" name="f" method="post">
		  <table class="my_table" style="width:100%;">
			<colgroup>
				<col width="60px">
				<col width="150px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th class="center">구분</th>
					<td>
						<input id="directGbn_1" name="directGbn" type="radio" value="1" class="radio">직접
						<input id="directGbn_2" name="directGbn" type="radio" value="2" class="radio">간접
					</td>
					<td rowspan="2" style="padding-left:3px;">
						<span class="btn_pack m"><button type="button" onclick="setApply();">적용</button></span></br>
						<span class="btn_pack m"><button type="button" onclick="self.close();">닫기</button></span>
					</td>
				</tr>
				<tr>
					<th class="center">적용기간</th>
					<td >
						<input id="yymm" name="yymm" type="text" value="" class="yymm">
					</td>
				</tr>
			</tbody>
		  </table>
		  </form>';


	echo '<div class="title title_border">변경내역</div>
		  <table class="my_table" style="width:100%;">
			<colgroup>
				<col width="60px">
				<col width="90px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">기준년월</th>
					<th class="head">구분</th>
					<th class="head">비고</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="center last" colspan="5">
						<div id="listBody" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:235px;"></div>
					</td>
				</tr>
			</tbody>
		  </table>';

	$html = ob_get_contents();

	ob_clean();

	$html = $myF->_gabSplitHtml($html);

	echo $html;


	include_once('../inc/_footer.php');
?>