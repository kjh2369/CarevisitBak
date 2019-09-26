<?
	if (CAL_LAVER != 'NOT'){
		include_once '../inc/_myFun.php';

		$beforeYM = explode("-", $myF->dateAdd("month", -1, date("Y-m-d"), "Y-m"));
		$nowYM    = explode("-", date("Y-m"));
		$nextYM   = explode("-", $myF->dateAdd("month", 1, date("Y-m-d"), "Y-m"));
		?>
		<div id="carlendarLayer" style="z-index:10000; position:absolute; width:175px; height:200px; left:200px; top:200px; display:none;">
			<div style="width:175px; top:10px; left:10px" class="ly_popup">
				<div class="shadow">
					<div class="shadow_side">
						<div class="shadow2">
							<div class="shadow_side2">
								<div class="border_type" id="calBody">

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?
	}
?>