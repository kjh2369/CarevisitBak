<?
	include_once('../inc/_db_open2.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myImage.php');

	$prtSQL = false;
	
	
	if (!$prtSQL){
		header( "Content-type: application/haansofthwp" );
		header( "Content-Disposition: attachment; filename=test.hml" );
		header( "Content-Transfer-Encoding: binary" );
		header( "Content-Description: PHP4 Generated Data" );
	}


	$hHD = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
			<HWPML Style="embed" SubVersion="7.0.0.0" Version="2.7">
				<HEAD SecCnt="1">
					<DOCSUMMARY>
						<TITLE>폐기물처리시설설치 촉진 및 주변지역지원등에 관한 법률</TITLE>
						<DATE>1999년 10월 22일 금요일, 14시 41분</DATE>
					</DOCSUMMARY>
					<DOCSETTING>
						<BEGINNUMBER Endnote="1" Equation="1" Footnote="1" Page="1" Picture="1" Table="1"/>
						<CARETPOS List="0" Para="0" Pos="24"/>
					</DOCSETTING>
					<MAPPINGTABLE>
						<BINDATALIST Count="1"><BINITEM BinData="1" Format="png" Type="Embedding"/></BINDATALIST>
						<FACENAMELIST>
							<FONTFACE Count="7" Lang="Hangul">
								<FONT Id="0" Name="한컴바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="1" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="2" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="3" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
								<FONT Id="4" Name="한양견명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
								<FONT Id="5" Name="신명 태고딕" Type="hft"><SUBSTFONT Name="휴먼고딕" Type="hft"/></FONT>
								<FONT Id="6" Name="HY태고딕" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/></FONT>
							</FONTFACE>
							<FONTFACE Count="8" Lang="Latin">
								<FONT Id="0" Name="한컴바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="1" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="2" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="3" Name="산세리프" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="2" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
								<FONT Id="4" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
								<FONT Id="5" Name="한양견명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
								<FONT Id="6" Name="신명 태고딕" Type="hft"><SUBSTFONT Name="한양중고딕" Type="hft"/></FONT>
								<FONT Id="7" Name="HY태고딕" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/></FONT>
							</FONTFACE>
							<FONTFACE Count="8" Lang="Hanja">
								<FONT Id="0" Name="한컴바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="1" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="2" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="3" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
								<FONT Id="4" Name="신명조 약자" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
								<FONT Id="5" Name="신명 견명조" Type="hft"><SUBSTFONT Name="한양신명조" Type="hft"/></FONT>
								<FONT Id="6" Name="신명 태고딕" Type="hft"><SUBSTFONT Name="한양중고딕" Type="hft"/></FONT>
								<FONT Id="7" Name="HY태고딕" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/></FONT>
							</FONTFACE>
							<FONTFACE Count="7" Lang="Japanese">
								<FONT Id="0" Name="한컴바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="1" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="2" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="3" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
								<FONT Id="4" Name="신명 견명조" Type="hft"><SUBSTFONT Name="한양신명조" Type="hft"/></FONT>
								<FONT Id="5" Name="신명 태고딕" Type="hft"><SUBSTFONT Name="한양중고딕" Type="hft"/></FONT>
								<FONT Id="6" Name="HY태고딕" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/></FONT>
							</FONTFACE>
							<FONTFACE Count="5" Lang="Other">
								<FONT Id="0" Name="한컴바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="1" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="2" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="3" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
								<FONT Id="4" Name="HY태고딕" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/></FONT>
							</FONTFACE>
							<FONTFACE Count="7" Lang="Symbol">
								<FONT Id="0" Name="한컴바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="1" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="2" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="3" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
								<FONT Id="4" Name="신명 견명조" Type="hft"><SUBSTFONT Name="한양신명조" Type="hft"/></FONT>
								<FONT Id="5" Name="#태고딕" Type="hft"><SUBSTFONT Name="한양중고딕" Type="hft"/></FONT>
								<FONT Id="6" Name="HY태고딕" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/></FONT>
							</FONTFACE>
							<FONTFACE Count="5" Lang="User">
								<FONT Id="0" Name="한컴바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="1" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="2" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
								<FONT Id="3" Name="명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
								<FONT Id="4" Name="HY태고딕" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/></FONT>
							</FONTFACE>
						</FACENAMELIST>
						<BORDERFILLLIST Count="8">
							<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="1" Shadow="false" Slash="0" ThreeD="false"><LEFTBORDER Type="Solid" Width="0.12mm"/><RIGHTBORDER Type="Solid" Width="0.12mm"/><TOPBORDER Type="Solid" Width="0.12mm"/><BOTTOMBORDER Type="Solid" Width="0.12mm"/></BORDERFILL>
							<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="2" Shadow="false" Slash="0" ThreeD="false"><LEFTBORDER Type="None" Width="0.1mm"/><RIGHTBORDER Type="None" Width="0.1mm"/><TOPBORDER Type="None" Width="0.1mm"/><BOTTOMBORDER Type="None" Width="0.1mm"/><DIAGONAL Type="Solid" Width="0.1mm"/><FILLBRUSH><WINDOWBRUSH Alpha="0" FaceColor="4294967295" HatchColor="4278190080"/></FILLBRUSH></BORDERFILL>
							<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="3" Shadow="false" Slash="0" ThreeD="false"><LEFTBORDER Type="Solid" Width="0.4mm"/><RIGHTBORDER Type="Solid" Width="0.4mm"/><TOPBORDER Type="Solid" Width="0.12mm"/><BOTTOMBORDER Type="Solid" Width="0.4mm"/></BORDERFILL>
							<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="4" Shadow="false" Slash="0" ThreeD="false"><LEFTBORDER Type="Solid" Width="0.4mm"/><RIGHTBORDER Type="Solid" Width="0.12mm"/><TOPBORDER Type="Solid" Width="0.4mm"/><BOTTOMBORDER Type="Solid" Width="0.12mm"/></BORDERFILL>
							<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="5" Shadow="false" Slash="0" ThreeD="false"><LEFTBORDER Type="Solid" Width="0.12mm"/><RIGHTBORDER Type="Solid" Width="0.4mm"/><TOPBORDER Type="Solid" Width="0.4mm"/><BOTTOMBORDER Type="Solid" Width="0.12mm"/></BORDERFILL>
							<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="6" Shadow="false" Slash="0" ThreeD="false"><LEFTBORDER Type="Solid" Width="0.4mm"/><RIGHTBORDER Type="Solid" Width="0.12mm"/><TOPBORDER Type="Solid" Width="0.12mm"/><BOTTOMBORDER Type="Solid" Width="0.12mm"/></BORDERFILL>
							<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="7" Shadow="false" Slash="0" ThreeD="false"><LEFTBORDER Type="Solid" Width="0.12mm"/><RIGHTBORDER Type="Solid" Width="0.4mm"/><TOPBORDER Type="Solid" Width="0.12mm"/><BOTTOMBORDER Type="Solid" Width="0.12mm"/></BORDERFILL>
							<BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="8" Shadow="false" Slash="0" ThreeD="false"><LEFTBORDER Type="Solid" Width="0.4mm"/><RIGHTBORDER Type="Solid" Width="0.4mm"/><TOPBORDER Type="Solid" Width="0.12mm"/><BOTTOMBORDER Type="Solid" Width="0.12mm"/></BORDERFILL>
						</BORDERFILLLIST>
						<CHARSHAPELIST Count="34">
							<CHARSHAPE BorderFillId="0" Height="1000" Id="0" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
								<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1000" Id="1" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1000" Id="2" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="3" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1000" Id="3" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2"/>
								<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1400" Id="4" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="5" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1100" Id="6" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="7" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHARSPACING Hangul="-4" Hanja="-4" Japanese="-4" Latin="-4" Other="-4" Symbol="-4" User="-4"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1100" Id="8" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="4" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
								<BOLD/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="9" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-5" Hanja="-5" Japanese="-5" Latin="-5" Other="-5" Symbol="-5" User="-5"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="10" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-2" Hanja="-2" Japanese="-2" Latin="-2" Other="-2" Symbol="-2" User="-2"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="11" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="6" Hanja="7" Japanese="6" Latin="7" Other="4" Symbol="6" User="4"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="2" Height="1100" Id="12" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="13" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-4" Hanja="-4" Japanese="-4" Latin="-4" Other="-4" Symbol="-4" User="-4"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="14" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-9" Hanja="-9" Japanese="-9" Latin="-9" Other="-9" Symbol="-9" User="-9"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="15" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-7" Hanja="-7" Japanese="-7" Latin="-7" Other="-7" Symbol="-7" User="-7"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="900" Id="16" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-2" Hanja="-2" Japanese="-2" Latin="-2" Other="-2" Symbol="-2" User="-2"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1000" Id="17" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1100" Id="18" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="5" Hanja="6" Japanese="5" Latin="6" Other="3" Symbol="5" User="3"/>
								<RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="19" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="5" Hanja="6" Japanese="5" Latin="6" Other="3" Symbol="5" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="20" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="6" Hanja="7" Japanese="6" Latin="7" Other="4" Symbol="6" User="4"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-50" Hanja="-50" Japanese="-50" Latin="-50" Other="-50" Symbol="-50" User="-50"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1100" Id="21" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="5" Hanja="6" Japanese="5" Latin="6" Other="3" Symbol="5" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1100" Id="22" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
								<BOLD/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="23" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-11" Hanja="-11" Japanese="-11" Latin="-11" Other="-11" Symbol="-11" User="-11"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="24" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="4" Hanja="5" Japanese="4" Latin="5" Other="3" Symbol="4" User="3"/>
								<RATIO Hangul="95" Hanja="95" Japanese="95" Latin="95" Other="95" Symbol="95" User="95"/>
								<CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
								<BOLD/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="2" Height="1100" Id="25" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="26" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-1" Hanja="-1" Japanese="-1" Latin="-1" Other="-1" Symbol="-1" User="-1"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="27" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-22" Hanja="-22" Japanese="-22" Latin="-22" Other="-22" Symbol="-22" User="-22"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="2" Height="1100" Id="28" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="29" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-25" Hanja="-25" Japanese="-25" Latin="-25" Other="-25" Symbol="-25" User="-25"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="30" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-17" Hanja="-17" Japanese="-17" Latin="-17" Other="-17" Symbol="-17" User="-17"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1200" Id="31" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-16" Hanja="-16" Japanese="-16" Latin="-16" Other="-16" Symbol="-16" User="-16"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="1100" Id="32" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="3" Hanja="3" Japanese="3" Latin="4" Other="3" Symbol="3" User="3"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-19" Hanja="-19" Japanese="-19" Latin="-19" Other="-19" Symbol="-19" User="-19"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
							<CHARSHAPE BorderFillId="0" Height="700" Id="33" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
								<FONTID Hangul="4" Hanja="4" Japanese="4" Latin="5" Other="4" Symbol="4" User="4"/>
								<RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98"/>
								<CHARSPACING Hangul="-4" Hanja="-4" Japanese="-4" Latin="-4" Other="-4" Symbol="-4" User="-4"/>
								<RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100"/>
								<CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0"/>
							</CHARSHAPE>
						</CHARSHAPELIST>
						<TABDEFLIST Count="4">
							<TABDEF AutoTabLeft="false" AutoTabRight="false" Id="0"/>
							<TABDEF AutoTabLeft="true" AutoTabRight="false" Id="1"/>
							<TABDEF AutoTabLeft="false" AutoTabRight="true" Id="2"><TABITEM Leader="None" Pos="0" Type="Left"/></TABDEF>
							<TABDEF AutoTabLeft="false" AutoTabRight="false" Id="3"><TABITEM Leader="None" Pos="84592" Type="Right"/></TABDEF>
						</TABDEFLIST>
						<PARASHAPELIST Count="33">
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="0" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="1" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="2" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="-7680" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Center" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="3" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Center" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="4" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Center" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="5" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="100" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="6" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="7" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="110" LineSpacingType="Percent" Next="0" Prev="0" Right="1000"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Center" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="8" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="560" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="9" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="-2600" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="10" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="100" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Center" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="11" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="110" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="12" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="3000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="Outline" Id="13" KeepLines="false" KeepWithNext="false" Level="1" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="1" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="Outline" Id="14" KeepLines="false" KeepWithNext="false" Level="2" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="15" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="2" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="150" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="16" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="-2800" Left="0" LineSpacing="150" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="DistributeSpace" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="17" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="10000" LineSpacing="110" LineSpacingType="Percent" Next="0" Prev="0" Right="10000"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Right" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="18" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="140" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Center" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="19" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="1704" Prev="2832" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="20" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="3" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="140" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="21" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="-2500" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="22" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="-4200" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Center" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="23" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Left" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="24" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Center" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="25" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="100" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="26" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="100" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="27" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="1000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Center" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="28" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="200" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="29" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="110" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="30" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="-2456" Left="0" LineSpacing="110" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Justify" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="31" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="-2456" Left="0" LineSpacing="110" LineSpacingType="Percent" Next="0" Prev="0" Right="0"/><PARABORDER BorderFill="2" Connect="false" IgnoreMargin="false"/></PARASHAPE>
							<PARASHAPE Align="Right" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="32" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false"><PARAMARGIN Indent="0" Left="0" LineSpacing="140" LineSpacingType="Percent" Next="0" Prev="568" Right="0"/><PARABORDER Connect="false" IgnoreMargin="false"/></PARASHAPE>
						</PARASHAPELIST>
						<STYLELIST Count="52">
							<STYLE CharShape="0" EngName="Normal" Id="0" LangId="1042" LockForm="0" Name="바탕글" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="17" EngName="Normal" Id="1" LangId="1042" LockForm="0" Name="바탕글" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="0" Id="2" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="6" Id="3" LangId="1042" LockForm="0" Name="본문(1)" NextStyle="1" ParaShape="0" Type="Para"/>
							<STYLE CharShape="0" Id="4" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="18" Id="5" LangId="1042" LockForm="0" Name="제목" NextStyle="2" ParaShape="11" Type="Para"/>
							<STYLE CharShape="0" Id="6" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="1" EngName="Page Number" Id="7" LangId="1042" LockForm="0" Name="쪽 번호" NextStyle="3" ParaShape="0" Type="Para"/>
							<STYLE CharShape="0" Id="8" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="19" Id="9" LangId="1042" LockForm="0" Name="장" NextStyle="4" ParaShape="3" Type="Para"/>
							<STYLE CharShape="0" Id="10" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="20" Id="11" LangId="1042" LockForm="0" Name="표미다시" NextStyle="5" ParaShape="17" Type="Para"/>
							<STYLE CharShape="0" Id="12" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="17" Id="13" LangId="1042" LockForm="0" Name="제개정(10)" NextStyle="6" ParaShape="18" Type="Para"/>
							<STYLE CharShape="0" Id="14" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="21" Id="15" LangId="1042" LockForm="0" Name="부칙" NextStyle="7" ParaShape="19" Type="Para"/>
							<STYLE CharShape="0" Id="16" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="11" Id="17" LangId="1042" LockForm="0" Name="별표 제목" NextStyle="8" ParaShape="3" Type="Para"/>
							<STYLE CharShape="0" Id="18" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="22" Id="19" LangId="1042" LockForm="0" Name="1." NextStyle="9" ParaShape="0" Type="Para"/>
							<STYLE CharShape="0" Id="20" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="17" Id="21" LangId="1042" LockForm="0" Name="각주" NextStyle="10" ParaShape="0" Type="Para"/>
							<STYLE CharShape="0" Id="22" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="6" Id="23" LangId="1042" LockForm="0" Name="맨밑" NextStyle="11" ParaShape="20" Type="Para"/>
							<STYLE CharShape="0" Id="24" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="2" Id="25" LangId="1042" LockForm="0" Name="선그리기" NextStyle="12" ParaShape="0" Type="Para"/>
							<STYLE CharShape="0" Id="26" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="7" Id="27" LangId="1042" LockForm="0" Name="법령" NextStyle="13" ParaShape="16" Type="Para"/>
							<STYLE CharShape="0" Id="28" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="5" Id="29" LangId="1042" LockForm="0" Name="10급" NextStyle="14" ParaShape="7" Type="Para"/>
							<STYLE CharShape="0" Id="30" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="8" Id="31" LangId="1042" LockForm="0" Name="본문(신명조11)" NextStyle="15" ParaShape="3" Type="Para"/>
							<STYLE CharShape="0" Id="32" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="11" Id="33" LangId="1042" LockForm="0" Name="표제목" NextStyle="16" ParaShape="8" Type="Para"/>
							<STYLE CharShape="0" Id="34" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="3" EngName="Body" Id="35" LangId="1042" LockForm="0" Name="본문" NextStyle="17" ParaShape="12" Type="Para"/>
							<STYLE CharShape="0" Id="36" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="3" EngName="Outline 3" Id="37" LangId="1042" LockForm="1" Name="개요 3" NextStyle="18" ParaShape="14" Type="Para"/>
							<STYLE CharShape="0" Id="38" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="3" EngName="Outline 2" Id="39" LangId="1042" LockForm="1" Name="개요 2" NextStyle="19" ParaShape="13" Type="Para"/>
							<STYLE CharShape="0" Id="40" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="16" EngName="Header" Id="41" LangId="1042" LockForm="1" Name="머리말" NextStyle="20" ParaShape="15" Type="Para"/>
							<STYLE CharShape="0" Id="42" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="4" EngName="Hang" Id="43" LangId="1042" LockForm="0" Name="항" NextStyle="21" ParaShape="9" Type="Para"/>
							<STYLE CharShape="0" Id="44" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="4" EngName="Jo" Id="45" LangId="1042" LockForm="0" Name="조" NextStyle="22" ParaShape="21" Type="Para"/>
							<STYLE CharShape="0" Id="46" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="4" EngName="Ho" Id="47" LangId="1042" LockForm="0" Name="호" NextStyle="23" ParaShape="22" Type="Para"/>
							<STYLE CharShape="0" Id="48" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="4" EngName="lawdefault" Id="49" LangId="1042" LockForm="0" Name="법령기본스타일" NextStyle="24" ParaShape="1" Type="Para"/>
							<STYLE CharShape="0" Id="50" LangId="1042" LockForm="0" Name="" NextStyle="0" ParaShape="0" Type="Para"/>
							<STYLE CharShape="4" EngName="Mok" Id="51" LangId="1042" LockForm="0" Name="목" NextStyle="25" ParaShape="2" Type="Para"/>
						</STYLELIST>
					</MAPPINGTABLE>
				</HEAD>
				<BODY>';

	$hFT = '	</BODY>
				<TAIL>
					<BINDATASTORAGE><BINDATA Encoding="Base64" Id="1" Size="11967">__PICTURE_JIKIN__</BINDATA></BINDATASTORAGE>
					<SCRIPTCODE Type="JScript" Version="1.0">
						<SCRIPTHEADER></SCRIPTHEADER>
						<SCRIPTSOURCE></SCRIPTSOURCE>
					</SCRIPTCODE>
				</TAIL>
			</HWPML>';

	$code = $_GET['code']; //기관기호
	$kind = $_GET['kind']; //
	$year = $_GET['year'];
	$month = $_GET['month'];
	$month = (IntVal($month) < 10 ? '0' : '').IntVal($month);
	$IPIN = $_GET['key']; //주민번호
	$svcKind = $_GET['showSvcCd']; //서비스코드
	$bipayYn = $_GET['showBipayYn'] != 'N' ? 'Y' : 'N'; //비급여여부
	$opt1 = $_GET['opt1']; //공단급여금액 출력여부
	$printDT = $_GET['printDT']; //출력일자
	$printDT = Str_Replace('-','.',$printDT);
	
	$sql = 'select m03_jumin
			from   m03sugupja
			where  m03_ccode = \''.$code.'\'
			and    m03_mkind = \''.$kind.'\'
			and    m03_key = \''.$IPIN.'\'';
	$jumin = $conn -> get_data($sql);

	if (!$code || !$jumin){
		exit;
	}

	$org1 = '	<SECTION Id="__SECTION_ID__">';
	$org2 = '	<P ColumnBreak="false" PageBreak="false" ParaShape="24" Style="0">
					<TEXT CharShape="24">
						<SECDEF CharGrid="0" FirstBorder="false" FirstFill="false" LineGrid="0" SpaceColumns="1134" TabStop="8000" TextDirection="0" TextVerticalWidthHead="0">
							<STARTNUMBER Equation="0" Figure="0" Page="0" PageStartsOn="Both" Table="0"/>
							<HIDE Border="false" EmptyLine="false" Fill="false" Footer="false" Header="false" MasterPage="false" PageNumPos="false"/>
							<PAGEDEF GutterType="LeftOnly" Height="84188" Landscape="0" Width="59528">
								<PAGEMARGIN Bottom="4535" Footer="3600" Gutter="0" Header="3600" Left="7087" Right="7087" Top="4535"/>
							</PAGEDEF>
							<FOOTNOTESHAPE>
								<AUTONUMFORMAT SuffixChar=")" Superscript="false" Type="Digit"/>
								<NOTELINE Length="5cm" Type="Solid" Width="0.12mm"/>
								<NOTESPACING AboveLine="852" BelowLine="568" BetweenNotes="284"/>
								<NOTENUMBERING NewNumber="1" Type="Continuous"/>
								<NOTEPLACEMENT BeneathText="false" Place="EachColumn"/>
							</FOOTNOTESHAPE>
							<ENDNOTESHAPE>
								<AUTONUMFORMAT SuffixChar=")" Superscript="false" Type="Digit"/>
								<NOTELINE Length="0" Type="None" Width="0.12mm"/>
								<NOTESPACING AboveLine="864" BelowLine="576" BetweenNotes="0"/>
								<NOTENUMBERING NewNumber="1" Type="Continuous"/>
								<NOTEPLACEMENT BeneathText="false" Place="EndOfDocument"/>
							</ENDNOTESHAPE>
							<PAGEBORDERFILL FillArea="Paper" FooterInside="false" HeaderInside="false" TextBorder="true" Type="Both">
								<PAGEOFFSET Bottom="1417" Left="1417" Right="1417" Top="1417"/>
							</PAGEBORDERFILL>
							<PAGEBORDERFILL FillArea="Paper" FooterInside="false" HeaderInside="false" TextBorder="true" Type="Even">
								<PAGEOFFSET Bottom="1417" Left="1417" Right="1417" Top="1417"/>
							</PAGEBORDERFILL>
							<PAGEBORDERFILL FillArea="Paper" FooterInside="false" HeaderInside="false" TextBorder="true" Type="Odd">
								<PAGEOFFSET Bottom="1417" Left="1417" Right="1417" Top="1417"/>
							</PAGEBORDERFILL>
						</SECDEF>
						<COLDEF Count="1" Layout="Left" SameGap="0" SameSize="true" Type="Newspaper"><COLUMNLINE Type="1" Width="1"/></COLDEF>
						<PAGENUM FormatType="Digit" Pos="BottomCenter" SideChar="-"/>
						__PICTURE_AREA__
					</TEXT>
					<TEXT CharShape="12"><CHAR>노인장기요양보험법 시행규칙 [별지 제24호서식]</CHAR></TEXT>
					<TEXT CharShape="10">
						<CHAR> &lt;개정 2013.6.10&gt; </CHAR>
						<TABLE BorderFill="1" CellSpacing="0" ColCount="15" PageBreak="Cell" RepeatHeader="true" RowCount="29">
							<SHAPEOBJECT InstId="1872298809" Lock="false" NumberingType="Table" ZOrder="0">
								<SIZE Height="63306" HeightRelTo="Absolute" Protect="false" Width="44822" WidthRelTo="Absolute"/>
								<POSITION AffectLSpacing="false" AllowOverlap="false" FlowWithText="true" HoldAnchorAndSO="false" HorzAlign="Left" HorzOffset="0" HorzRelTo="Para" TreatAsChar="true" VertAlign="Top" VertOffset="0" VertRelTo="Para"/>
								<OUTSIDEMARGIN Bottom="141" Left="141" Right="141" Top="141"/>
							</SHAPEOBJECT>
							<INSIDEMARGIN Bottom="141" Left="141" Right="141" Top="141"/>
							<ROW>
								<CELL BorderFill="4" ColAddr="0" ColSpan="12" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2964" Protect="false" RowAddr="0" RowSpan="2" Width="36839">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="13"><CHAR>장기요양급여비용 명세서 </CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="5" ColAddr="12" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1482" Protect="false" RowAddr="0" RowSpan="1" Width="7983">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="13"><CHAR>□ 퇴소</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="7" ColAddr="12" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1482" Protect="false" RowAddr="1" RowSpan="1" Width="7983">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center"><P ParaShape="3" Style="0">
										<TEXT CharShape="13"><CHAR>□ 중간</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="6" ColAddr="0" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="2" RowSpan="1" Width="6202">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="5" Style="0"><TEXT CharShape="13"><CHAR>장기요양</CHAR></TEXT></P>
										<P ParaShape="5" Style="0"><TEXT CharShape="13"><CHAR>기관기호</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="2" ColSpan="6" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="2" RowSpan="1" Width="21672">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="5" Style="0"><TEXT CharShape="13"><CHAR>__CT_CD__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="8" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="2" RowSpan="1" Width="8965">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="5" Style="0"><TEXT CharShape="31"><CHAR>장기요양기관명</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="12" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="2" RowSpan="1" Width="7983">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="33"><CHAR>__CT_NM__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="6" ColAddr="0" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2530" Protect="false" RowAddr="3" RowSpan="1" Width="6202">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="25" Style="0"><TEXT CharShape="12"><CHAR>주소</CHAR></TEXT><TEXT CharShape="12"/></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="2" ColSpan="6" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="3" RowSpan="1" Width="21672">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="26" Style="0"><TEXT CharShape="1"><CHAR>__ADDR__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="8" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2530" Protect="false" RowAddr="3" RowSpan="1" Width="8965">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="25" Style="0"><TEXT CharShape="12"><CHAR>사업자등록번호</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="12" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2530" Protect="false" RowAddr="3" RowSpan="1" Width="7983">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="25" Style="0"><TEXT CharShape="12"><CHAR>__BZ_NO__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="6" ColAddr="0" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2467" Protect="false" RowAddr="4" RowSpan="1" Width="6202">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="25"><CHAR>성명</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="2" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="4" RowSpan="1" Width="7335">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="5" Style="0"><TEXT CharShape="25"><CHAR>장기요양</CHAR></TEXT></P>
										<P ParaShape="5" Style="0"><TEXT CharShape="25"><CHAR>인정번호</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2467" Protect="false" RowAddr="4" RowSpan="1" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="12"><CHAR>급여제공기간</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="8" ColSpan="7" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2467" Protect="false" RowAddr="4" RowSpan="1" Width="16948">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="12"><CHAR>영수증 번호</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="6" ColAddr="0" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2353" Protect="false" RowAddr="5" RowSpan="1" Width="6202">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="12"><CHAR>__NM__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="2" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2353" Protect="false" RowAddr="5" RowSpan="1" Width="7335">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="12"><CHAR>__NO__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2353" Protect="false" RowAddr="5" RowSpan="1" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="23"><CHAR>__DATE__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="8" ColSpan="7" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2353" Protect="false" RowAddr="5" RowSpan="1" Width="16948">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="12"><CHAR>__BILL_NO__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="6" ColAddr="0" ColSpan="5" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2048" Protect="false" RowAddr="6" RowSpan="1" Width="13537">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="4" Style="0"><TEXT CharShape="12"><CHAR>항목</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2048" Protect="false" RowAddr="6" RowSpan="1" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="4" Style="0"><TEXT CharShape="26"><CHAR>금액</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="8" ColSpan="7" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2048" Protect="false" RowAddr="6" RowSpan="1" Width="16948">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="4" Style="0"><TEXT CharShape="12"><CHAR>금액산정내역</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="6" ColAddr="0" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="7344" Protect="false" RowAddr="7" RowSpan="3" Width="2806">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="4" Style="0"><TEXT CharShape="12"><CHAR>급</CHAR></TEXT></P>
										<P ParaShape="4" Style="0"><TEXT CharShape="12"><CHAR>여</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="1" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2331" Protect="false" RowAddr="7" RowSpan="1" Width="10731">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="27" Style="0"><TEXT CharShape="12"><CHAR>본인부담금①</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2331" Protect="false" RowAddr="7" RowSpan="1" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__PAY_1__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="8" ColSpan="5" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="4662" Protect="false" RowAddr="7" RowSpan="2" Width="9524">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="25"><CHAR>총액(급여+비급여) </CHAR></TEXT><TEXT CharShape="25"/></P>
										<P ParaShape="3" Style="0"><TEXT CharShape="28"><CHAR>⑨(③+⑧)</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="13" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="4662" Protect="false" RowAddr="7" RowSpan="2" Width="7424">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__TOT1__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="1" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2331" Protect="false" RowAddr="8" RowSpan="1" Width="10731">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="27" Style="0"><TEXT CharShape="12"><CHAR>공단부담금②</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2331" Protect="false" RowAddr="8" RowSpan="1" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__PAY_2__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="1" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="9" RowSpan="1" Width="10731">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="27" Style="0"><TEXT CharShape="12"><CHAR>급여 계③(①+②)</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="9" RowSpan="1" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__PAY_3__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="8" ColSpan="5" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="0" Protect="false" RowAddr="9" RowSpan="1" Width="9524">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="5" Style="0"><TEXT CharShape="15"><CHAR>본인부담총액</CHAR></TEXT></P>
										<P ParaShape="5" Style="0"><TEXT CharShape="15"><CHAR>⑩(①+⑧)</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="13" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="9" RowSpan="1" Width="7424">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__TOT2__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="6" ColAddr="0" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="20746" Protect="false" RowAddr="10" RowSpan="15" Width="2806">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="4" Style="0"><TEXT CharShape="12"><CHAR>비</CHAR></TEXT></P>
										<P ParaShape="4" Style="0"><TEXT CharShape="12"><CHAR>급</CHAR></TEXT></P>
										<P ParaShape="4" Style="0"><TEXT CharShape="12"><CHAR>여</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="1" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2331" Protect="false" RowAddr="10" RowSpan="1" Width="10731">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="26" Style="0"><TEXT CharShape="12"><CHAR> 식사재료비④</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2331" Protect="false" RowAddr="10" RowSpan="1" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="0" Style="0"><TEXT CharShape="12"/></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="8" ColSpan="5" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2331" Protect="false" RowAddr="10" RowSpan="1" Width="9524">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="5" Style="0"><TEXT CharShape="30"><CHAR>이미</CHAR></TEXT><TEXT CharShape="29"><CHAR> 납부한 금액⑪</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="13" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2331" Protect="false" RowAddr="10" RowSpan="1" Width="7424">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__INPAY__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="1" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="11" RowSpan="2" Width="10731">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="26" Style="0"><TEXT CharShape="12"><CHAR> 상급침실 이용에</CHAR></TEXT></P>
										<P ParaShape="26" Style="0"><TEXT CharShape="12"><CHAR> 따른 추가비용⑤  </CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="11" RowSpan="2" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="0" Style="0"><TEXT CharShape="12"/></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="8" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="7740" Protect="false" RowAddr="11" RowSpan="7" Width="3350">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="6" Style="0"><TEXT CharShape="12"><CHAR>수납금액</CHAR></TEXT></P>
										<P ParaShape="4" Style="0"><TEXT CharShape="12"><CHAR>⑫</CHAR></TEXT><TEXT CharShape="12"/></P>
										<P ParaShape="4" Style="0"><TEXT CharShape="32"><CHAR>(⑩-⑪)</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="9" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1935" Protect="false" RowAddr="11" RowSpan="1" Width="6174">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="23" Style="0"><TEXT CharShape="12"><CHAR>카드</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="13" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1935" Protect="false" RowAddr="11" RowSpan="1" Width="7424">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__INPAY1__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="9" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1935" Protect="false" RowAddr="12" RowSpan="2" Width="6174">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="23" Style="0"><TEXT CharShape="14"><CHAR>현금영수증</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="13" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1935" Protect="false" RowAddr="12" RowSpan="2" Width="7424">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__INPAY2__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="1" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2115" Protect="false" RowAddr="13" RowSpan="2" Width="10731">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="26" Style="0"><TEXT CharShape="12"><CHAR> 이ㆍ미용비⑥</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2115" Protect="false" RowAddr="13" RowSpan="2" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="0" Style="0"><TEXT CharShape="12"/></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="9" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1935" Protect="false" RowAddr="14" RowSpan="2" Width="6174">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="23" Style="0"><TEXT CharShape="12"><CHAR>현금</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="13" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1935" Protect="false" RowAddr="14" RowSpan="2" Width="7424">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__INPAY3__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="1" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="10657" Protect="false" RowAddr="15" RowSpan="9" Width="3679">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="12"><CHAR>기타⑦</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="3" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2132" Protect="false" RowAddr="15" RowSpan="2" Width="7052">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="26" Style="0"><TEXT CharShape="12"><CHAR>__BI_NM1__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2132" Protect="false" RowAddr="15" RowSpan="2" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__BI_PAY1__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="9" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1935" Protect="false" RowAddr="16" RowSpan="2" Width="6174">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="12"><CHAR>합계</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="13" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1935" Protect="false" RowAddr="16" RowSpan="2" Width="7424">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__INPAY4__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="3" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2132" Protect="false" RowAddr="17" RowSpan="2" Width="7052">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="26" Style="0"><TEXT CharShape="12"><CHAR>__BI_NM2__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2132" Protect="false" RowAddr="17" RowSpan="2" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__BI_PAY2__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="7" ColAddr="8" ColSpan="7" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1861" Protect="false" RowAddr="18" RowSpan="2" Width="16948">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="12"><CHAR>현금영수증</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="3" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2131" Protect="false" RowAddr="19" RowSpan="2" Width="7052">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="26" Style="0"><TEXT CharShape="12"><CHAR>__BI_NM3__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2131" Protect="false" RowAddr="19" RowSpan="2" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__BI_PAY3__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="8" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1861" Protect="false" RowAddr="20" RowSpan="2" Width="7908">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="0" Style="0"><TEXT CharShape="12"><CHAR>신분확인번호</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="11" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1861" Protect="false" RowAddr="20" RowSpan="2" Width="9040">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
										<P ParaShape="0" Style="0"><TEXT CharShape="12"/></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="3" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2131" Protect="false" RowAddr="21" RowSpan="2" Width="7052">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="26" Style="0"><TEXT CharShape="12"><CHAR>__BI_NM4__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2131" Protect="false" RowAddr="21" RowSpan="2" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__BI_PAY4__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="8" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1861" Protect="false" RowAddr="22" RowSpan="1" Width="7908">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
										<P ParaShape="0" Style="0"><TEXT CharShape="12"><CHAR>현금승인번호</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="11" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1861" Protect="false" RowAddr="22" RowSpan="1" Width="9040">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
										<P ParaShape="0" Style="0"><TEXT CharShape="12"/></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="3" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2131" Protect="false" RowAddr="23" RowSpan="1" Width="7052">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="26" Style="0"><TEXT CharShape="12"><CHAR>__BI_NM5__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2131" Protect="false" RowAddr="23" RowSpan="1" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__BI_PAY5__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="8" ColSpan="7" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="5092" Protect="false" RowAddr="23" RowSpan="2" Width="16948">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
										<P ParaShape="0" Style="0"><TEXT CharShape="16"><CHAR>__OTHER1__</CHAR></TEXT></P>
										<P ParaShape="0" Style="0"><TEXT CharShape="16"><CHAR>__OTHER2__</CHAR></TEXT></P>
										<P ParaShape="0" Style="0"><TEXT CharShape="16"><CHAR>__OTHER3__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="1" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="24" RowSpan="1" Width="10731">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="5" Style="0"><TEXT CharShape="31"><CHAR>비급여 계</CHAR></TEXT></P>
										<P ParaShape="5" Style="0"><TEXT CharShape="12"><CHAR>⑧(④+⑤+⑥+⑦)</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="5" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="24" RowSpan="1" Width="14337">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="18" Style="0"><TEXT CharShape="12"><CHAR>__BI_TOT__</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="6" ColAddr="0" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="5364" Protect="false" RowAddr="25" RowSpan="2" Width="6485">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="3" Style="0"><TEXT CharShape="25"><CHAR>신용카드를 사용하실 때</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="3" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="25" RowSpan="1" Width="4222">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="5" Style="0"><TEXT CharShape="25"><CHAR>회원</CHAR></TEXT></P>
										<P ParaShape="5" Style="0"><TEXT CharShape="25"><CHAR>번호</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="4" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="25" RowSpan="1" Width="6319">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="5" Style="0"><TEXT CharShape="12"/></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="6" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="25" RowSpan="1" Width="5160">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="5" Style="0"><TEXT CharShape="25"><CHAR>승인번호</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="7" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="25" RowSpan="1" Width="5688">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="5" Style="0"><TEXT CharShape="12"/></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="8" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="25" RowSpan="1" Width="5752">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="5" Style="0"><TEXT CharShape="25"><CHAR>할부</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="10" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="25" RowSpan="1" Width="5470">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="5" Style="0"><TEXT CharShape="12"/></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="14" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="25" RowSpan="1" Width="5726">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="5" Style="0"><TEXT CharShape="12"><CHAR>사용금액</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="1" ColAddr="3" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="26" RowSpan="1" Width="4222">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="25" Style="0"><TEXT CharShape="10"><CHAR>카드</CHAR></TEXT></P>
										<P ParaShape="25" Style="0"><TEXT CharShape="10"><CHAR>종류</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="4" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="26" RowSpan="1" Width="6319">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="10" Style="0"><TEXT CharShape="10"/></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="6" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="26" RowSpan="1" Width="5160">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="25" Style="0"><TEXT CharShape="12"><CHAR>유효기간</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="7" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="26" RowSpan="1" Width="5688">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="10" Style="0"><TEXT CharShape="10"/></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="8" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="26" RowSpan="1" Width="5752">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="25" Style="0"><TEXT CharShape="27"><CHAR>가맹점번호</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="1" ColAddr="10" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="26" RowSpan="1" Width="5470">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="10" Style="0"><TEXT CharShape="10"/></P>
									</PARALIST>
								</CELL>
								<CELL BorderFill="7" ColAddr="14" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2682" Protect="false" RowAddr="26" RowSpan="1" Width="5726">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="10" Style="0"><TEXT CharShape="12"/></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="8" ColAddr="0" ColSpan="15" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="3907" Protect="false" RowAddr="27" RowSpan="1" Width="44822">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="28" Style="0"><TEXT CharShape="12"><CHAR>             __PRT_DATE__  </CHAR></TEXT></P>
										<P ParaShape="28" Style="0"><TEXT CharShape="12"><CHAR>장기요양기관명: __PRT_NM__ 대표자명: __PRT_MG__ 󰃡</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
							<ROW>
								<CELL BorderFill="3" ColAddr="0" ColSpan="15" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="10534" Protect="false" RowAddr="28" RowSpan="1" Width="44822">
									<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
										<P ParaShape="30" Style="0"><TEXT CharShape="12"><CHAR> * </CHAR></TEXT><TEXT CharShape="26"><CHAR>이 명세서(영수증)는 「소득세법」에 따른 의료비 또는 「조세특례제한법」에</CHAR></TEXT><TEXT CharShape="12"><CHAR> 따른 현금영수증(현금영수증 승인번호가 기재된 경우) 공제신청에 사용할 수 </CHAR></TEXT><TEXT CharShape="9"><CHAR>있습니다. 다만, 지출증빙용으로 발급된 현금영수증(지출증빙)은 공제신청에</CHAR></TEXT><TEXT CharShape="12"><CHAR> 사용할 수 없습니다.</CHAR></TEXT></P>
										<P ParaShape="29" Style="0"><TEXT CharShape="12"><CHAR> * 이 명세서(영수증)에 대한 세부내역을 요구할 수 있습니다.</CHAR></TEXT></P>
										<P ParaShape="31" Style="0"><TEXT CharShape="12"><CHAR> * </CHAR></TEXT><TEXT CharShape="25"><CHAR>비고란은 장기요양기관의 임의활용 란으로 사용합니다. 다만, 복지용구의 경우 품목과</CHAR></TEXT><TEXT CharShape="12"><CHAR> 구입ㆍ대여를 구분하여 적으시기 바랍니다.</CHAR></TEXT></P>
									</PARALIST>
								</CELL>
							</ROW>
						</TABLE>
						<CHAR/>
					</TEXT>
				</P>
				<P ParaShape="32" Style="0"><TEXT CharShape="12"><CHAR>210mm×297mm[백상지 80g/㎡]</CHAR></TEXT></P>';
	$org3 = '	</SECTION>';

	//기관정보
	$sql = 'SELECT	m00_cname	AS nm
			,		m00_mname	AS manager
			,		m00_ccode	AS biz_no
			,		m00_cpostno	AS postno
			,		m00_caddr1	AS addr
			,		m00_caddr2	AS addr_dtl
			,		m00_jikin	AS jikin
			,		m00_ctel AS phone
			,		m00_bank_name AS bank_nm
			,		m00_bank_no AS bank_no
			,		m00_bank_depos AS bank_acct
			FROM	m00center
			WHERE	m00_mcode = \''.$code.'\'
			AND		m00_mkind = \'0\'';
	$arrCT = $conn->get_array($sql);
	

	//개인정보
	if (Is_File('../mem_picture/'.$arrCT['jikin'])){
		//직인처리
		//이미지 링크가 http://example.com/img1.jpg 일 경우를 가정
		$img = 'http://www.carevisit.net/mem_picture/'.$arrCT['jikin'];

		//base64 데이터 가져오기
		$imgBase64 = $myImage->getImageData($img);

		//mime type 가져오기
		//$imgData = getHeader($img);

		$imgInfo = GetImageSize('../mem_picture/'.$arrCT['jikin']);
		$rate = 74.823529411764705882352941176471; //6360

		$imgW = $imgInfo[0];
		$imgH = $imgInfo[1];

		$rateW = 1.00000;
		$rateH = 1.00000;

		if ($imgInfo[0] > 90){
			$rateW = 90 / $imgInfo[0];
			$imgW = 90;
		}

		if ($imgInfo[1] > 90){
			$rateH = 90 / $imgInfo[1];
			$imgH = 90;
		}

		$imgW = Floor($imgW * $rate);
		$imgH = Floor($imgH * $rate);
		$orgW = Floor($imgInfo[0] * $rate);
		$orgH = Floor($imgInfo[1] * $rate);

		$picArea = '<PICTURE Reverse="false">
						<SHAPEOBJECT InstId="1288554417" Lock="false" NumberingType="None" TextWrap="BehindText" ZOrder="1">
							<SIZE Height="'.$imgH.'" HeightRelTo="Absolute" Protect="false" Width="'.$imgW.'" WidthRelTo="Absolute"/>
							<POSITION AffectLSpacing="false" AllowOverlap="false" FlowWithText="false" HoldAnchorAndSO="false" HorzAlign="Left" HorzOffset="36732" HorzRelTo="Para" TreatAsChar="false" VertAlign="Top" VertOffset="48900" VertRelTo="Para"/>
							<OUTSIDEMARGIN Bottom="0" Left="0" Right="0" Top="0"/>
						</SHAPEOBJECT>
						<SHAPECOMPONENT CurHeight="'.$imgH.'" CurWidth="'.$imgW.'" GroupLevel="0" HorzFlip="false" InstID="214812594" OriHeight="'.$orgH.'" OriWidth="'.$orgW.'" VertFlip="false" XPos="0" YPos="0">
							<ROTATIONINFO Angle="0" CenterX="'.($imgW/2).'" CenterY="'.($imgH/2).'"/>
							<RENDERINGINFO>
								<TRANSMATRIX E1="1.00000" E2="0.00000" E3="0.00000" E4="0.00000" E5="1.00000" E6="0.00000"/>
								<SCAMATRIX E1="'.$rateW.'" E2="0.00000" E3="0.00000" E4="0.00000" E5="'.$rateH.'" E6="0.00000"/>
								<ROTMATRIX E1="1.00000" E2="0.00000" E3="0.00000" E4="0.00000" E5="1.00000" E6="0.00000"/>
							</RENDERINGINFO>
						</SHAPECOMPONENT>
						<IMAGERECT X0="0" X1="'.$orgW.'" X2="'.$orgW.'" X3="0" Y0="0" Y1="0" Y2="'.$orgH.'" Y3="'.$orgH.'"/>
						<IMAGECLIP Bottom="'.$orgW.'" Left="0" Right="'.$orgH.'" Top="0"/>
						<INSIDEMARGIN Bottom="0" Left="0" Right="0" Top="0"/>
						<IMAGE Alpha="0" BinItem="1" Bright="0" Contrast="0" Effect="RealPic"/>
					</PICTURE>';
	}else{
		$img = '';
		$picArea = '';
	}

	$hFT = Str_Replace('__PICTURE_JIKIN__',$imgBase64,$hFT); //직인
	$org2 = Str_Replace('__PICTURE_AREA__',$picArea,$org2); //직인영역

	if (StrLen($arrCT['biz_no'])){
		$arrCT['biz_no'] = substr($arrCT['biz_no'],0,3).'-'.substr($arrCT['biz_no'],3,2).'-'.substr($arrCT['biz_no'],5,5);
	}


	//기관서비스 리스트
	$svcList = $conn->kind_list($code, true);

	if ($_POST['jumin'] == 'ALL'){
		
		$arrList = explode('?', $_POST['data']);			
		
	}else{
		$arrList[0] = $jumin;
	}

	foreach($arrList as $idx => $clientCd){
		parse_str($clientCd,$R);
			
		$cltCd = $ed->de($R['cltCd']);
		$clientCd = $cltCd != '' ? $cltCd : $clientCd;
		
		$sql   = '';
		if($clientCd){
			foreach($svcList as $i => $k){
				$sql .= (!empty($sql) ? ' union all ' : '');
				$sql .= 'select t13_ccode as k_cd
						 ,      t13_mkind as k_kind
						 ,      t13_jumin as c_cd';

				if ($k['code'] > '0') $sql .= ', 0 as svc0_bonin, 0 as svc0_over, 0 as svc0_public, 0 as svc0_suga';
				if ($k['code'] > '1') $sql .= ', 0 as svc1_bonin, 0 as svc1_over, 0 as svc1_public, 0 as svc1_suga';
				if ($k['code'] > '2') $sql .= ', 0 as svc2_bonin, 0 as svc2_over, 0 as svc2_public, 0 as svc2_suga';
				if ($k['code'] > '3') $sql .= ', 0 as svc3_bonin, 0 as svc3_over, 0 as svc3_public, 0 as svc3_suga';
				if ($k['code'] > '4') $sql .= ', 0 as svc4_bonin, 0 as svc4_over, 0 as svc4_public, 0 as svc4_suga';
				if ($k['code'] > 'A') $sql .= ', 0 as svcA_bonin, 0 as svcA_over, 0 as svcA_public, 0 as svcA_suga';
				if ($k['code'] > 'B') $sql .= ', 0 as svcB_bonin, 0 as svcB_over, 0 as svcB_public, 0 as svcB_suga';

				if ($svcKind == '200' || $svcKind == '500' || $svcKind == '800'){
					if ($k['code'] == '0'){
						switch($svcKind){
							case '200':
								$lsVal = '1';
								break;

							case '500':
								$lsVal = '2';
								break;

							case '800':
								$lsVal = '3';
								break;

							default:
								$lsVal = '4';
						}
					}else{
						$lsVal = '4';
					}

					if ($code == '31141000043' /* 예사랑 */){
						$sql .= ', sum(t13_bonin_amt'.$lsVal.') as svc'.$k['code'].'_bonin
								 , 0 as svc'.$k['code'].'_over
								 , '.($opt1 == 'Y' ? 'sum(t13_chung_amt'.$lsVal.')' : '0').' as svc'.$k['code'].'_public
								 , '.($opt1 == 'Y' ? 'sum(t13_suga_tot'.$lsVal.')' : 'sum(t13_bonin_amt'.$lsVal.')').'  as svc'.$k['code'].'_suga';
					}else{
						$sql .= ', sum(t13_bonin_amt'.$lsVal.') as svc'.$k['code'].'_bonin
								 , sum(t13_over_amt'.$lsVal.($bipayYn == 'Y' ? ' + t13_bipay'.$lsVal : '').') as svc'.$k['code'].'_over
								 , '.($opt1 == 'Y' ? 'sum(t13_chung_amt'.$lsVal.')' : '0').' as svc'.$k['code'].'_public';
						//$sql .= ', '.($opt1 == 'Y' ? 'sum(t13_suga_tot'.$lsVal.' - t13_over_amt'.$lsVal.' - t13_bipay'.$lsVal.')' : 'sum(t13_bonin_amt'.$lsVal.')').'  as svc'.$k['code'].'_suga';
						$sql .= ', '.($opt1 == 'Y' ? 'sum(t13_chung_amt'.$lsVal.' + t13_bonin_amt'.$lsVal.')' : 'sum(t13_bonin_amt'.$lsVal.')').'  as svc'.$k['code'].'_suga';
					}
				}else{
					if ($code == '31141000043' /* 예사랑 */){
						$sql .= ', sum(t13_bonin_amt4) as svc'.$k['code'].'_bonin
								 , 0 as svc'.$k['code'].'_over
								 , '.($opt1 == 'Y' ? 'sum(t13_chung_amt4)' : '0').' as svc'.$k['code'].'_public
								 , '.($opt1 == 'Y' ? 'sum(t13_suga_tot4)' : 'sum(t13_bonin_amt4)').'  as svc'.$k['code'].'_suga';
					}else{
						$sql .= ', sum(t13_bonin_amt4) as svc'.$k['code'].'_bonin
								 , sum(t13_over_amt4'.($bipayYn == 'Y' ? ' + t13_bipay4' : '').') as svc'.$k['code'].'_over
								 , '.($opt1 == 'Y' ? 'sum(t13_chung_amt4)' : '0').' as svc'.$k['code'].'_public';
						//$sql .= ', '.($opt1 == 'Y' ? 'sum(t13_suga_tot4 - t13_over_amt4 - t13_bipay4)' : 'sum(t13_bonin_amt4)').'  as svc'.$k['code'].'_suga';
						$sql .= ', '.($opt1 == 'Y' ? 'sum(t13_chung_amt4 + t13_bonin_amt4)' : 'sum(t13_bonin_amt4)').'  as svc'.$k['code'].'_suga';
					}
				}

				if ($k['code'] < '1') $sql .= ', 0 as svc1_bonin, 0 as svc1_over, 0 as svc1_public, 0 as svc1_suga';
				if ($k['code'] < '2') $sql .= ', 0 as svc2_bonin, 0 as svc2_over, 0 as svc2_public, 0 as svc2_suga';
				if ($k['code'] < '3') $sql .= ', 0 as svc3_bonin, 0 as svc3_over, 0 as svc3_public, 0 as svc3_suga';
				if ($k['code'] < '4') $sql .= ', 0 as svc4_bonin, 0 as svc4_over, 0 as svc4_public, 0 as svc4_suga';
				if ($k['code'] < 'A') $sql .= ', 0 as svcA_bonin, 0 as svcA_over, 0 as svcA_public, 0 as svcA_suga';
				if ($k['code'] < 'B') $sql .= ', 0 as svcB_bonin, 0 as svcB_over, 0 as svcB_public, 0 as svcB_suga';
				if ($k['code'] < 'C') $sql .= ', 0 as svcC_bonin, 0 as svcC_over, 0 as svcC_public, 0 as svcC_suga';

				$sql .= ',      concat(t13_pay_date,\'-\',t13_bill_no) as bill_no
						   from t13sugupja
						  where t13_ccode    = \''.$code.'\'
							and t13_mkind    = \''.$k['code'].'\'
							and t13_pay_date = \''.$year.$month.'\'
							and t13_jumin    = \''.$clientCd.'\'
							and t13_type     = \'2\'
						  group by t13_ccode, t13_mkind, t13_jumin, t13_mkind, t13_pay_date, t13_bill_no';
			}

			$sql = 'select c_cd
					,      m03_name as c_nm
					,      lvl.app_no as c_no

					,      sum(svc0_bonin) as svc0_bonin, sum(svc0_over) as svc0_over, sum(svc0_public) as svc0_public, sum(svc0_suga) as svc0_suga
					,      sum(svc1_bonin) as svc1_bonin, sum(svc1_over) as svc1_over, sum(svc1_public) as svc1_public, sum(svc1_suga) as svc1_suga
					,      sum(svc2_bonin) as svc2_bonin, sum(svc2_over) as svc2_over, sum(svc2_public) as svc2_public, sum(svc2_suga) as svc2_suga
					,      sum(svc3_bonin) as svc3_bonin, sum(svc3_over) as svc3_over, sum(svc3_public) as svc3_public, sum(svc3_suga) as svc3_suga
					,      sum(svc4_bonin) as svc4_bonin, sum(svc4_over) as svc4_over, sum(svc4_public) as svc4_public, sum(svc4_suga) as svc4_suga
					,      sum(svcA_bonin) as svcA_bonin, sum(svcA_over) as svcA_over, sum(svcA_public) as svcA_public, sum(svcA_suga) as svcA_suga
					,      sum(svcB_bonin) as svcB_bonin, sum(svcB_over) as svcB_over, sum(svcB_public) as svcB_public, sum(svcB_suga) as svcB_suga
					,      sum(svcC_bonin) as svcC_bonin, sum(svcC_over) as svcC_over, sum(svcC_public) as svcC_public, sum(svcC_suga) as svcC_suga

					,     (select ifnull(sum(t13_bonbu_tot4), 0)
							 from t13sugupja
							where t13_ccode = k_cd
							  and t13_jumin = c_cd
							  and t13_type  = \'2\')
					-     (select ifnull(sum(deposit_amt), 0)
							 from unpaid_deposit
							where org_no        = k_cd
							  and deposit_jumin = c_cd
							  and del_flag      = \'N\') as unpaid
					,      bill_no
					  from ('.$sql.') as t
					 inner join m03sugupja
						on m03_ccode = k_cd
					   and m03_mkind = k_kind
					   and m03_jumin = c_cd
					  left join	(select distinct
										org_no
								 ,		jumin
								 ,      svc_cd
								 ,      app_no
								   from client_his_lvl
								  where org_no = \''.$code.'\'
									and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
									and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
								 ) as lvl
						on lvl.org_no = k_cd
					   and lvl.svc_cd = k_kind
					   and lvl.jumin  = c_cd
					 group by c_cd
					 order by c_cd';

			if ($prtSQL){
				echo nl2br($sql).'<br>';
				exit;
			}

			$arrSvc = $conn->get_array($sql);

			if (StrLen($arrSvc['c_no']) == 11){
				$arrSvc['c_no'] = SubStr($arrSvc['c_no'],0,6).'*****';
			}


			//급여제공기간
			$sql = 'select min(t01_sugup_date) AS from_dt
					,      max(t01_sugup_date) AS to_dt
					  from t01iljung
					 where t01_ccode               = \''.$code.'\'
					   and t01_jumin               = \''.$clientCd.'\'
					   and left(t01_sugup_date, 6) = \''.$year.$month.'\'
					   and t01_mkind               = \'0\'
					   and t01_del_yn              = \'N\'';

			$tmp = $conn->get_array($sql);

			$arrSvc['from_dt'] = SubStr($tmp['from_dt'],0,4).'.'.SubStr($tmp['from_dt'],4,2).'.'.SubStr($tmp['from_dt'],6,2);
			$arrSvc['to_dt'] = SubStr($tmp['to_dt'],0,4).'.'.SubStr($tmp['to_dt'],4,2).'.'.SubStr($tmp['to_dt'],6,2);

			unset($tmp);


			//입금정보
			$sql = 'select case cd when \'카드\' then 1 when \'현금영수증\' then 2 else 3 end as id, cd, sum(pay) as pay, no, max(ent_dt) as ent_dt
					  from (
							select case unpaid_deposit.deposit_type when \'01\' then \'현금\'
																	when \'02\' then \'현금\'
																	when \'03\' then \'현금\'
																	when \'04\' then \'카드\'
																	when \'05\' then \'현금\'
																	when \'06\' then \'현금영수증\' else \'현금\' end as cd
							,      unpaid_deposit_list.deposit_amt as pay
							,      unpaid_deposit.cash_bill_no as no
							,      unpaid_deposit.deposit_reg_dt as ent_dt
							  from unpaid_deposit
							 inner join unpaid_deposit_list
								on unpaid_deposit_list.org_no         = unpaid_deposit.org_no
							   and unpaid_deposit_list.deposit_ent_dt = unpaid_deposit.deposit_ent_dt
							   and unpaid_deposit_list.deposit_seq    = unpaid_deposit.deposit_seq
							 where unpaid_deposit.org_no              = \''.$code.'\'
							   and unpaid_deposit.deposit_jumin       = \''.$clientCd.'\'
							   and unpaid_deposit_list.unpaid_yymm    = \''.$year.$month.'\'
							   and unpaid_deposit.deposit_type       != \'99\'
							   and unpaid_deposit.del_flag            = \'N\'
						   ) as t
					 group by id, cd, no
					 order by id';

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				$inPay[$row['id']] = array('cd'=>$row['cd'], 'pay'=>$row['pay'], 'no'=>$row['no'], 'dt'=>$row['ent_dt']);
			}

			$conn->row_free();


			/**************************************************
				금액계산
			**************************************************/
			foreach($svcList as $i => $k){
				if ($k['id'] == 11){
					$amt['tot'] = $arrSvc['svc0_suga']+$arrSvc['svc0_over'];
					$amt['my']  = $arrSvc['svc0_bonin']+$arrSvc['svc0_over'];
				}else{
					$amt['tot'] += $arrSvc['svc'.$k['code'].'_bonin'];
					$amt['my']  += $arrSvc['svc'.$k['code'].'_bonin'];
				}
			}

			$arrSvcNm = Array(
					'0'=>'재가요양'
				,	'1'=>'가사간병'
				,	'2'=>'노인돌봄'
				,	'3'=>'산모신생아'
				,	'4'=>'장애인활동지원'
				,	'A'=>'산모유료'
				,	'B'=>'병원간병'
				,	'C'=>'기타비급여'
			);

			//비급여
			for($i=0; $i<=7; $i++){
				if ($i == 0){
					$id = 0;
				}else{
					if ($i >= 1 && $i <= 4){
						$id = $i;
					}else{
						$id = chr(60 + $i);
					}

					if ($arrSvc['svc'.$id.'_bonin'] > 0){
						$idx = SizeOf($bipayName);
						$arrBipay[$idx] = Array('name'=>$arrSvcNm[$id],'pay'=>$arrSvc['svc'.$id.'_bonin']);
					}
				}

				if ($arrSvc['svc'.$id.'_over'] > 0){
					$idx = SizeOf($bipayName);
					$arrBipay[$idx] = Array('name'=>$arrSvcNm[$id],'pay'=>$arrSvc['svc'.$id.'_over']);
				}
			}

			$hwp = Str_Replace('__SECTION_ID__',$idx,$org1).$org2.$org3;

			$hwp = Str_Replace('__CT_CD__',$code,$hwp); //기관기호
			$hwp = Str_Replace('__CT_NM__',$arrCT['nm'],$hwp); //기관명
			$hwp = Str_Replace('__ADDR__',$arrCT['addr'].' '.$arrCT['addr_dtl'],$hwp); //주소
			$hwp = Str_Replace('__BZ_NO__',$arrCT['biz_no'],$hwp); //사업자번호

			$hwp = Str_Replace('__NM__',$arrSvc['c_nm'],$hwp); //성명
			$hwp = Str_Replace('__NO__',$arrSvc['c_no'],$hwp); //인정번호
			$hwp = Str_Replace('__DATE__',$arrSvc['from_dt'].'~'.$arrSvc['to_dt'],$hwp); //급여제공기간
			$hwp = Str_Replace('__BILL_NO__',$arrSvc['bill_no'],$hwp); //영수증번호

			$hwp = Str_Replace('__PAY_1__',Number_Format($arrSvc['svc0_bonin']),$hwp); //본인부담금
			$hwp = Str_Replace('__PAY_2__',Number_Format($arrSvc['svc0_public']),$hwp); //공단부담금
			$hwp = Str_Replace('__PAY_3__',Number_Format($arrSvc['svc0_suga']),$hwp); //급여계

			//비급여
			for($i=0; $i<5; $i++){
				$bipayNm = $arrBipay[$i]['name'];
				$bipayAmt = ($arrBipay[$i]['pay'] > 0 ? Number_Format($arrBipay[$i]['pay']) : '');

				$hwp = Str_Replace('__BI_NM'.($i+1).'__',$bipayNm,$hwp);
				$hwp = Str_Replace('__BI_PAY'.($i+1).'__',$bipayAmt,$hwp);
				$bipayTot += $arrBipay[$i]['pay'];
			}

			if ($bipayTot == 0){
				$bipayTot = '';
			}
			
			if($bipayTot){
				$bipayTot = Number_Format($bipayTot);
			}else {
				$bipayTot = '';
			}

			$hwp = Str_Replace('__BI_TOT__',$bipayTot,$hwp); //비급여계

			$hwp = Str_Replace('__INPAY__','',$hwp); //이미 납부한금액

			$hwp = Str_Replace('__INPAY1__',$inPay[1]['pay'] > 0 ? Number_Format($inPay[1]['pay']) : '',$hwp); //카드
			$hwp = Str_Replace('__INPAY2__',$inPay[2]['pay'] > 0 ? Number_Format($inPay[2]['pay']) : '',$hwp); //현금영수증
			$hwp = Str_Replace('__INPAY3__',$inPay[3]['pay'] > 0 ? Number_Format($inPay[3]['pay']) : '',$hwp); //현금
			$hwp = Str_Replace('__INPAY4__',$inPay[1]['pay']+$inPay[2]['pay']+$inPay[3]['pay'] > 0 ? Number_Format($inPay[1]['pay']+$inPay[2]['pay']+$inPay[3]['pay']) : '',$hwp); //합계

			$hwp = Str_Replace('__TOT1__',Number_Format($amt['tot']),$hwp); //총액
			$hwp = Str_Replace('__TOT2__',Number_Format($amt['my']),$hwp); //총액

			$other1 = '연락처:'.lfPhoneStyle($arrCT['phone'],'.');
			$other2 = '계좌:'.lfBankList($arrCT['bank_nm']).' '.$arrCT['bank_no'];
			$other3 = '예금주:'.$arrCT['bank_acct'];

			$hwp = Str_Replace('__OTHER1__',$other1 ,$hwp); //비고
			$hwp = Str_Replace('__OTHER2__',$other2 ,$hwp);
			$hwp = Str_Replace('__OTHER3__',$other3 ,$hwp);

			$hwp = Str_Replace('__PRT_DATE__',$printDT,$hwp); //출력일자
			$hwp = Str_Replace('__PRT_NM__',$arrCT['nm'],$hwp); //기관명
			$hwp = Str_Replace('__PRT_MG__',$arrCT['manager'],$hwp); //대표자명

			$hBD .= $hwp;
		}

		Unset($arrBipay);
		Unset($inPay);
		Unset($bipayTot);
	}

	$conn->close();

	if (!$prtSQL){
		echo $hHD.$hBD.$hFT;
	}

	function lfPhoneStyle($phone, $split = '-'){
		$phone = Trim($phone);
		$phone = str_replace("-","",$phone);
		$phone = str_replace(")","",$phone);
		$phone = str_replace(".","",$phone);

		if (substr($phone, 0, 2) == "02"){
			$phone_1 = substr($phone,0,2);
			$phone   = substr($phone,2,strLen($phone));
			$phone_3 = substr($phone,strLen($phone)-4,4);
			$phone   = substr($phone,0,strLen($phone)-4);
			$phone_2 = $phone;
		}else{
			$phone_1 = substr($phone,0,3);
			$phone   = substr($phone,3,strLen($phone));
			$phone_3 = substr($phone,strLen($phone)-4,4);
			$phone   = substr($phone,0,strLen($phone)-4);
			$phone_2 = $phone;
		}

		if ($phone_1 == "02" or
			$phone_1 == "051" or
			$phone_1 == "053" or
			$phone_1 == "032" or
			$phone_1 == "062" or
			$phone_1 == "042" or
			$phone_1 == "052" or
			$phone_1 == "031" or
			$phone_1 == "033" or
			$phone_1 == "043" or
			$phone_1 == "041" or
			$phone_1 == "063" or
			$phone_1 == "061" or
			$phone_1 == "054" or
			$phone_1 == "055" or
			$phone_1 == "064"){
			$temp_phone_no = $phone_1.$split.$phone_2.$split.$phone_3;
		}else{
			$temp_phone_no = $phone_1.$split.$phone_2.$split.$phone_3;
		}

		$temp_phone_no = str_replace('--','',$temp_phone_no);
		$temp_phone_no = str_replace('..','',$temp_phone_no);

		return $temp_phone_no;
	}

	function lfBankList($cd){
		if ($cd == '001'){
			return '한국은행';
		}else if ($cd == '002'){
			return '산업은행';
		}else if ($cd == '003'){
			return '기업은행';
		}else if ($cd == '004'){
			return '국민은행';
		}else if ($cd == '005'){
			return '외환은행';
		}else if ($cd == '007'){
			return '수협중앙회';
		}else if ($cd == '008'){
			return '수출입은행';
		}else if ($cd == '011'){
			return '농협중앙회';
		}else if ($cd == '012'){
			return '농협회원조합';
		}else if ($cd == '020'){
			return '우리은행';

		}else if ($cd == '023'){
			return 'SC제일은행';
		}else if ($cd == '027'){
			return '한국씨티은행';
		}else if ($cd == '031'){
			return '대구은행';
		}else if ($cd == '032'){
			return '부산은행';
		}else if ($cd == '034'){
			return '광주은행';
		}else if ($cd == '035'){
			return '제주은행';
		}else if ($cd == '037'){
			return '전북은행';
		}else if ($cd == '039'){
			return '경남은행';
		}else if ($cd == '045'){
			return '새마을금고연합회';
		}else if ($cd == '048'){
			return '신협중앙회';

		}else if ($cd == '050'){
			return '상호저축은행';
		}else if ($cd == '071'){
			return '우체국';
		}else if ($cd == '081'){
			return '하나은행';
		}else if ($cd == '088'){
			return '신한은행';
		}
	}
?>