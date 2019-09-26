<script type="text/javascript">
	function _lfSetPageList(maxCnt, page, pCnt, iCnt){
		var itemCnt = iCnt;
		var pageCnt = pCnt;

		if (!itemCnt) itemCnt = 20;
		if (!pageCnt) pageCnt = 10;

		var pageCount  = Math.ceil(maxCnt / itemCnt);
		var totalBlock = Math.ceil(pageCount / pageCnt);
		var block      = Math.ceil(page / pageCnt);
		var firstPage  = (block - 1) * pageCnt;
		var lastPage   = totalBlock <= block ? pageCount : block * pageCnt;

		//alert(pageCount+'/'+totalBlock+'/'+block+'/'+firstPage+'/'+lastPage);

		$('span[id^="lblPage"]').hide();

		//첫페이지

		//끝페이지

		//이전페이지
		if (block > 1){
			$('#lblPagePrev').unbind('click').bind('click',function(){
				lfSearch((block - 1) * pageCnt);
			}).show();
		}

		//다음페이지
		if (block < totalBlock){
			$('#lblPageNext').unbind('click').bind('click',function(){
				lfSearch(block * pageCnt + 1);
			}).show();
		}

		for(var i=(firstPage+1); i<=lastPage; i++){
			$('#lblPage'+(i%10)).text(i).unbind('click').bind('click',function(){
				lfSearch(__str2num($(this).text()));
			}).show();

			if (i == page){
				$('#lblPage'+(i%10)).attr('class','page_list_2');
			}else{
				$('#lblPage'+(i%10)).attr('class','page_list_1');
			}
		}
	}
</script>
<span id="lblPageFirst" style="cursor:pointer; display:none;">[처음]</span>
<span id="lblPagePrev" style="cursor:pointer; display:none;">[이전]</span>
<span id="lblPage1" class="page_list_1" style="cursor:pointer; display:none; padding:0 3px 0 3px;">&nbsp;</span>
<span id="lblPage2" class="page_list_1" style="cursor:pointer; display:none; padding:0 3px 0 3px;">&nbsp;</span>
<span id="lblPage3" class="page_list_1" style="cursor:pointer; display:none; padding:0 3px 0 3px;">&nbsp;</span>
<span id="lblPage4" class="page_list_1" style="cursor:pointer; display:none; padding:0 3px 0 3px;">&nbsp;</span>
<span id="lblPage5" class="page_list_1" style="cursor:pointer; display:none; padding:0 3px 0 3px;">&nbsp;</span>
<span id="lblPage6" class="page_list_1" style="cursor:pointer; display:none; padding:0 3px 0 3px;">&nbsp;</span>
<span id="lblPage7" class="page_list_1" style="cursor:pointer; display:none; padding:0 3px 0 3px;">&nbsp;</span>
<span id="lblPage8" class="page_list_1" style="cursor:pointer; display:none; padding:0 3px 0 3px;">&nbsp;</span>
<span id="lblPage9" class="page_list_1" style="cursor:pointer; display:none; padding:0 3px 0 3px;">&nbsp;</span>
<span id="lblPage0" class="page_list_1" style="cursor:pointer; display:none; padding:0 3px 0 3px;">&nbsp;</span>
<span id="lblPageNext" style="cursor:pointer; display:none;">[다음]</span>
<span id="lblPageEnd" style="cursor:pointer; display:none;">[끝]</span>