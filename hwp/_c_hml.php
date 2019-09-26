<?php
	// 종이 크기
	class paper{
		var $gutterType	= 'LeftOnly';
		var $landscape	= 0;
		var $height		= 84188;
		var $width		= 59528;
	}

	// 마진
	class margin{
		var $bottom	= 1417;
		var $footer	= 1417;
		var $head	= 0;
		var $left	= 5669;
		var $right	= 5669;
		var $top	= 7087;
	}

	// 출력물
	class hml{
		var $title		= null;
		var $center_nm	= null;
		var $author		= '(주)굿이오스';
		var $print_dt	= null;

		function hml(){
			$this->print_dt = $this->_print_dt();
		}

		// 출력일자
		function _print_dt(){
			return date('Y.m.d H:i:s', mktime());
		}

		//
		function _xml($version = '1.0', $encoding = 'UTF-8', $standalone = 'no'){
			return '<?xml version="'.$version.'" encoding="'.$encoding.'" standalone="'.$standalone.'" ?>';
		}

		//
		function _hwpml_start($style = 'embed', $subVersion = '7.0.0.0', $version = '2.7'){
			return '<HWPML Style="'.$style.'" SubVersion="'.$subVersion.'" Version="'.$version.'">';
		}

		//
		function _hwpml_end(){
			return '</HWPML>';
		}

		//
		function _head(){
			$head = '	<HEAD SecCnt="1">
							<DOCSUMMARY>
								<TITLE>'.$this->title.'</TITLE>
								<AUTHOR>'.$this->author.'</AUTHOR>
								<DATE>'.$this->print_dt.'</DATE>
							</DOCSUMMARY>
							<DOCSETTING>
								<BEGINNUMBER Endnote="1" Equation="1" Footnote="1" Page="1" Picture="1" Table="1"/>
								<CARETPOS List="0" Para="0" Pos="24"/>
							</DOCSETTING>
							<MAPPINGTABLE>
								<FACENAMELIST>
									<FONTFACE Count="2" Lang="Hangul">
										<FONT Id="0" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
										<FONT Id="1" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
									</FONTFACE>
									<FONTFACE Count="2" Lang="Latin">
										<FONT Id="0" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
										<FONT Id="1" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
									</FONTFACE>
									<FONTFACE Count="2" Lang="Hanja">
										<FONT Id="0" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
										<FONT Id="1" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
									</FONTFACE>
									<FONTFACE Count="2" Lang="Japanese">
										<FONT Id="0" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
										<FONT Id="1" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
									</FONTFACE>
									<FONTFACE Count="2" Lang="Other">
										<FONT Id="0" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
										<FONT Id="1" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
									</FONTFACE>
									<FONTFACE Count="2" Lang="Symbol">
										<FONT Id="0" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
										<FONT Id="1" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
									</FONTFACE>
									<FONTFACE Count="2" Lang="User">
										<FONT Id="0" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
										<FONT Id="1" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
									</FONTFACE>
								</FACENAMELIST>
								<BORDERFILLLIST Count="28">
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="1" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.12mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.12mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="2" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="None" Width="0.1mm"/>
										<RIGHTBORDER Type="None" Width="0.1mm"/>
										<TOPBORDER Type="None" Width="0.1mm"/>
										<BOTTOMBORDER Type="None" Width="0.1mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
										<FILLBRUSH>
											<WINDOWBRUSH Alpha="0" FaceColor="4294967295" HatchColor="4278190080"/>
										</FILLBRUSH>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="3" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.12mm"/>
										<RIGHTBORDER Type="Solid" Width="0.4mm"/>
										<TOPBORDER Type="Solid" Width="0.12mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.4mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="4" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.12mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.4mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
										<FILLBRUSH>
											<WINDOWBRUSH Alpha="0" FaceColor="15198183" HatchColor="4278190080"/>
										</FILLBRUSH>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="5" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.12mm"/>
										<RIGHTBORDER Type="Solid" Width="0.4mm"/>
										<TOPBORDER Type="Solid" Width="0.4mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
										<FILLBRUSH>
											<WINDOWBRUSH Alpha="0" FaceColor="15198183" HatchColor="4278190080"/>
										</FILLBRUSH>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="6" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.12mm"/>
										<RIGHTBORDER Type="Solid" Width="0.4mm"/>
										<TOPBORDER Type="Solid" Width="0.12mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="7" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.12mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.12mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
										<FILLBRUSH>
											<WINDOWBRUSH Alpha="0" FaceColor="15198183" HatchColor="0"/>
										</FILLBRUSH>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="8" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="None" Width="0.12mm"/>
										<RIGHTBORDER Type="None" Width="0.12mm"/>
										<TOPBORDER Type="None" Width="0.5mm"/>
										<BOTTOMBORDER Type="ThickSlim" Width="0.7mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="9" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.12mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.12mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.4mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="10" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.4mm"/>
										<RIGHTBORDER Type="Solid" Width="0.4mm"/>
										<TOPBORDER Type="Solid" Width="0.4mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.4mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="11" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.4mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.12mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="12" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.12mm"/>
										<RIGHTBORDER Type="Solid" Width="0.4mm"/>
										<TOPBORDER Type="Solid" Width="0.4mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="13" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.4mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.12mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.4mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="14" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.4mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.4mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.4mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="15" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.12mm"/>
										<RIGHTBORDER Type="Solid" Width="0.4mm"/>
										<TOPBORDER Type="Solid" Width="0.4mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.4mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="16" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.4mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.4mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
										<FILLBRUSH>
											<WINDOWBRUSH Alpha="0" FaceColor="15198183" HatchColor="4278190080"/>
										</FILLBRUSH>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="17" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.4mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.12mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
										<FILLBRUSH>
											<WINDOWBRUSH Alpha="0" FaceColor="15198183" HatchColor="0"/>
										</FILLBRUSH>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="18" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.4mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.12mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.4mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
										<FILLBRUSH>
											<WINDOWBRUSH Alpha="0" FaceColor="15198183" HatchColor="0"/>
										</FILLBRUSH>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="19" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.12mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.4mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="20" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.4mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.4mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.4mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
										<FILLBRUSH>
											<WINDOWBRUSH Alpha="0" FaceColor="15198183" HatchColor="0"/>
										</FILLBRUSH>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="21" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Dot" Width="0.12mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.12mm"/>
										<BOTTOMBORDER Type="Dot" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="22" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.12mm"/>
										<RIGHTBORDER Type="Dot" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.12mm"/>
										<BOTTOMBORDER Type="Dot" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="23" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Dot" Width="0.12mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Dot" Width="0.12mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="24" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.12mm"/>
										<RIGHTBORDER Type="Dot" Width="0.12mm"/>
										<TOPBORDER Type="Dot" Width="0.12mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="25" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Dot" Width="0.12mm"/>
										<RIGHTBORDER Type="Dot" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.12mm"/>
										<BOTTOMBORDER Type="Dot" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="26" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Dot" Width="0.12mm"/>
										<RIGHTBORDER Type="Dot" Width="0.12mm"/>
										<TOPBORDER Type="Dot" Width="0.12mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="27" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.12mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Dot" Width="0.12mm"/>
										<BOTTOMBORDER Type="Solid" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
										<FILLBRUSH>
											<WINDOWBRUSH Alpha="0" FaceColor="15198183" HatchColor="0"/>
										</FILLBRUSH>
									</BORDERFILL>
									<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="28" Shadow="false" Slash="0" ThreeD="false">
										<LEFTBORDER Type="Solid" Width="0.12mm"/>
										<RIGHTBORDER Type="Solid" Width="0.12mm"/>
										<TOPBORDER Type="Solid" Width="0.12mm"/>
										<BOTTOMBORDER Type="Dot" Width="0.12mm"/>
										<DIAGONAL Type="Solid" Width="0.1mm"/>
										<FILLBRUSH>
											<WINDOWBRUSH Alpha="0" FaceColor="15198183" HatchColor="0"/>
										</FILLBRUSH>
									</BORDERFILL>
								</BORDERFILLLIST>
								<CHARSHAPELIST Count="14">
									<CHARSHAPE BorderFillId="0" Height="1000" Id="0" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
										<FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
									</CHARSHAPE>
									<CHARSHAPE BorderFillId="0" Height="1000" Id="1" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
										<FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1"/>
										<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
									</CHARSHAPE>
									<CHARSHAPE BorderFillId="0" Height="900" Id="2" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
										<FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
									</CHARSHAPE>
									<CHARSHAPE BorderFillId="0" Height="900" Id="3" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
										<FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1"/>
										<RATIO Hangul="95" Hanja="95" Japanese="95" Latin="95" Other="95" Symbol="95" User="95"/>
										<CHARSPACING Hangul="-5" Hanja="-5" Japanese="-5" Latin="-5" Other="-5" Symbol="-5" User="-5"/>
										<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
									</CHARSHAPE>
									<CHARSHAPE BorderFillId="0" Height="900" Id="4" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
										<FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<RATIO Hangul="95" Hanja="95" Japanese="95" Latin="95" Other="95" Symbol="95" User="95"/>
										<CHARSPACING Hangul="-5" Hanja="-5" Japanese="-5" Latin="-5" Other="-5" Symbol="-5" User="-5"/>
										<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
									</CHARSHAPE>
									<CHARSHAPE BorderFillId="0" Height="2000" Id="5" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
										<FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1"/>
										<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<BOLD/>
									</CHARSHAPE>
									<CHARSHAPE BorderFillId="0" Height="1500" Id="6" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
										<FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1"/>
										<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<BOLD/>
									</CHARSHAPE>
									<CHARSHAPE BorderFillId="0" Height="1000" Id="7" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
										<FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1"/>
										<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<BOLD/>
									</CHARSHAPE>
									<CHARSHAPE BorderFillId="0" Height="1000" Id="8" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
										<FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1"/>
										<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<UNDERLINE Color="0" Shape="Solid" Type="Bottom"/>
									</CHARSHAPE>
									<CHARSHAPE BorderFillId="0" Height="1000" Id="9" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
										<FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1"/>
										<RATIO Hangul="90" Hanja="90" Japanese="90" Latin="90" Other="90" Symbol="90" User="90"/>
										<CHARSPACING Hangul="-23" Hanja="-23" Japanese="-23" Latin="-23" Other="-23" Symbol="-23" User="-23"/>
										<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<BOLD/>
									</CHARSHAPE>
									<CHARSHAPE BorderFillId="0" Height="1000" Id="10" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
										<FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1"/>
										<RATIO Hangul="90" Hanja="90" Japanese="90" Latin="90" Other="90" Symbol="90" User="90"/>
										<CHARSPACING Hangul="-18" Hanja="-18" Japanese="-18" Latin="-18" Other="-18" Symbol="-18" User="-18"/>
										<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<BOLD/>
									</CHARSHAPE>
									<CHARSHAPE BorderFillId="0" Height="800" Id="11" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
										<FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1"/>
										<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
									</CHARSHAPE>
									<CHARSHAPE BorderFillId="0" Height="1000" Id="12" ShadeColor="4294967295" SymMark="0" TextColor="9671571" UseFontSpace="false" UseKerning="false">
										<FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
									</CHARSHAPE>
									<CHARSHAPE BorderFillId="0" Height="700" Id="13" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
										<FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1"/>
										<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
										<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
										<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
									</CHARSHAPE>
								</CHARSHAPELIST>
								<TABDEFLIST Count="3">
									<TABDEF AutoTabLeft="false" AutoTabRight="false" Id="0"/>
									<TABDEF AutoTabLeft="true" AutoTabRight="false" Id="1"/>
									<TABDEF AutoTabLeft="false" AutoTabRight="true" Id="2"/>
								</TABDEFLIST>
								<NUMBERINGLIST Count="1">
									<NUMBERING Id="1" Start="0">
										<PARAHEAD Alignment="Left" AutoIndent="true" Level="1" NumFormat="Digit" TextOffset="50" TextOffsetType="percent" UseInstWidth="true" WidthAdjust="0">^1.</PARAHEAD>
										<PARAHEAD Alignment="Left" AutoIndent="true" Level="2" NumFormat="HangulSyllable" TextOffset="50" TextOffsetType="percent" UseInstWidth="true" WidthAdjust="0">^2.</PARAHEAD>
										<PARAHEAD Alignment="Left" AutoIndent="true" Level="3" NumFormat="Digit" TextOffset="50" TextOffsetType="percent" UseInstWidth="true" WidthAdjust="0">^3)</PARAHEAD>
										<PARAHEAD Alignment="Left" AutoIndent="true" Level="4" NumFormat="HangulSyllable" TextOffset="50" TextOffsetType="percent" UseInstWidth="true" WidthAdjust="0">^4)</PARAHEAD>
										<PARAHEAD Alignment="Left" AutoIndent="true" Level="5" NumFormat="Digit" TextOffset="50" TextOffsetType="percent" UseInstWidth="true" WidthAdjust="0">(^5)</PARAHEAD>
										<PARAHEAD Alignment="Left" AutoIndent="true" Level="6" NumFormat="HangulSyllable" TextOffset="50" TextOffsetType="percent" UseInstWidth="true" WidthAdjust="0">(^6)</PARAHEAD>
										<PARAHEAD Alignment="Left" AutoIndent="true" Level="7" NumFormat="CircledDigit" TextOffset="50" TextOffsetType="percent" UseInstWidth="true" WidthAdjust="0">^7</PARAHEAD>
									</NUMBERING>
								</NUMBERINGLIST>
								<PARASHAPELIST Count="18">
									<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="0" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="1" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="-2620" Left="0" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="1" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="2" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="3" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="3000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="20" FontLineHeight="false" HeadingType="Outline" Id="4" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="2000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="20" FontLineHeight="false" HeadingType="Outline" Id="5" KeepLines="false" KeepWithNext="false" Level="1" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="4000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="20" FontLineHeight="false" HeadingType="Outline" Id="6" KeepLines="false" KeepWithNext="false" Level="2" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="6000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="20" FontLineHeight="false" HeadingType="Outline" Id="7" KeepLines="false" KeepWithNext="false" Level="3" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="8000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="20" FontLineHeight="false" HeadingType="Outline" Id="8" KeepLines="false" KeepWithNext="false" Level="4" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="10000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="20" FontLineHeight="false" HeadingType="Outline" Id="9" KeepLines="false" KeepWithNext="false" Level="5" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="12000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="20" FontLineHeight="false" HeadingType="Outline" Id="10" KeepLines="false" KeepWithNext="false" Level="6" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="14000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="11" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="2" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="150" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Right" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="12" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Center" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="13" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Left" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="14" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Distribute" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="15" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Distribute" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="16" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="120" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
									<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="17" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="100" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/>
										<PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/>
									</PARASHAPE>
								</PARASHAPELIST>
								<STYLELIST Count="14">
									<STYLE CharShape="1" EngName="Normal" Id="0" LangId="1042" LockForm="0" Name="바탕글" NextStyle="0" ParaShape="1" Type="Para"/>
									<STYLE CharShape="1" EngName="Body" Id="1" LangId="1042" LockForm="0" Name="본문" NextStyle="1" ParaShape="3" Type="Para"/>
									<STYLE CharShape="1" EngName="Outline 1" Id="2" LangId="1042" LockForm="0" Name="개요 1" NextStyle="2" ParaShape="4" Type="Para"/>
									<STYLE CharShape="1" EngName="Outline 2" Id="3" LangId="1042" LockForm="0" Name="개요 2" NextStyle="3" ParaShape="5" Type="Para"/>
									<STYLE CharShape="1" EngName="Outline 3" Id="4" LangId="1042" LockForm="0" Name="개요 3" NextStyle="4" ParaShape="6" Type="Para"/>
									<STYLE CharShape="1" EngName="Outline 4" Id="5" LangId="1042" LockForm="0" Name="개요 4" NextStyle="5" ParaShape="7" Type="Para"/>
									<STYLE CharShape="1" EngName="Outline 5" Id="6" LangId="1042" LockForm="0" Name="개요 5" NextStyle="6" ParaShape="8" Type="Para"/>
									<STYLE CharShape="1" EngName="Outline 6" Id="7" LangId="1042" LockForm="0" Name="개요 6" NextStyle="7" ParaShape="9" Type="Para"/>
									<STYLE CharShape="1" EngName="Outline 7" Id="8" LangId="1042" LockForm="0" Name="개요 7" NextStyle="8" ParaShape="10" Type="Para"/>
									<STYLE CharShape="0" EngName="Page Number" Id="9" LangId="1042" LockForm="0" Name="쪽 번호" NextStyle="9" ParaShape="1" Type="Para"/>
									<STYLE CharShape="2" EngName="Header" Id="10" LangId="1042" LockForm="0" Name="머리말" NextStyle="10" ParaShape="11" Type="Para"/>
									<STYLE CharShape="3" EngName="Footnote" Id="11" LangId="1042" LockForm="0" Name="각주" NextStyle="11" ParaShape="0" Type="Para"/>
									<STYLE CharShape="3" EngName="Endnote" Id="12" LangId="1042" LockForm="0" Name="미주" NextStyle="12" ParaShape="0" Type="Para"/>
									<STYLE CharShape="4" EngName="Memo" Id="13" LangId="1042" LockForm="0" Name="메모" NextStyle="13" ParaShape="2" Type="Para"/>
								</STYLELIST>
							</MAPPINGTABLE>
						</HEAD>';
			return $head;
		}

		//
		function _tail(){
			$tail = '	<TAIL>
							<SCRIPTCODE Type="JScript" Version="1.0">
								<SCRIPTHEADER>
									var Documents = XHwpDocuments;
									var Document = Documents.Active_XHwpDocument;
								</SCRIPTHEADER>
								<SCRIPTSOURCE>
								function OnDocument_New()
								{
									//todo :
								}
								</SCRIPTSOURCE>
							</SCRIPTCODE>
						</TAIL>';
			return $tail;
		}

		//
		function _out(){
			echo $this->_xml();
			echo $this->_hwpml_start();
			echo $this->_head();

			echo $this->_tail();
			echo $this->_hwpml_end();

		}
	}

	$hml = new hml();
?>