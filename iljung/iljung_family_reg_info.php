<script type="text/javascript">
function closeInfoLayer(){
	$('#cLayer').css('width',0).css('height',0);
	$('#iljung_family_info').hide();
}

function moveFamilyReg(){
	try{
		opener._moveFamilyReg($('#jumin').attr('value'));
		self.close();
	}catch(e){
	}
}

$(document).ready(function(){
	var h = $('#layerFamilyRegTitle').parent().height() - $('#layerFamilyRegTitle').height();

	$('#layerFamilyRegBody').height(h);
});

</script>
<div id="layerFamilyRegTitle" style="clear:both;">
	<div class="title title_border">가족요양보호사 등록안내</div>
	<div style="position:absolute; top:12px; right:5px; width:auto;"><img src="../image/btn_close.gif" style="cursor:pointer;" onclick="closeInfoLayer();"></div>
</div>
<div id="layerFamilyRegBody" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;">
	<p style="padding-top:5px; text-align:justify; font-weight:bold; line-height:1.3em; text-align:left;">
		<ul style="list-style-type:square; font-weight:bold; line-height:1.3em; text-align:left; margin-left:25px;">
			<li>등록된 일정을 건보공단으로 업로드 하기위해 <span style="color:#0000ff;">수급자</span>와 <span style="color:#0000ff;">요양보호사</span>간의 <span style="color:#ff0000;">가족관계</span>를 등록해야 합니다.</li>
			<li style="margin-top:10px;">가족관계를 등록하지 않으면 건보에 일정 업로드 후 가족관계자 등록되지 않습니다.</li>
		</ul>

		<div style="margin-top:20px; text-align:center;"><span style="cursor:pointer; font-weight:bold;" onclick="moveFamilyReg();">[<span style="color:#0000ff;">가족요양보호사</span> 등록 <span style="color:#ff0000;">바로가기</span>]</span></div>
	</p>
</div>