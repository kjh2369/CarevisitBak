<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_myImage.php');

	
	header( "Content-type: application/haansofthwp" );
	header( "Content-Disposition: attachment; filename=test.hml" );
	header( "Content-Transfer-Encoding: binary" );
	header( "Content-Description: PHP4 Generated Data" );
	
	$code = $_SESSION['userCenterCode'];	//기관기호
	$kind = '0';							//서비스구분
	$ssn = $ed->de($_GET['jumin']);		//수급자주민번호
	$seq = $_GET['conSeq'];				//키
	
	
	$sql = 'select svc_cd
			,	   seq
			,	   reg_dt
			,	   svc_seq
			,      use_yoil1
			,      from_time1
			,      to_time1
			,      use_yoil2
			,      from_time2
			,      to_time2
			,	   from_dt
			,	   to_dt
			,      bath_weekly
			,	   use_type
			,      from_time
			,      to_time
			,	   pay_day1
			,	   pay_day2
			  from client_contract
			 where org_no   = \''.$code.'\'
			   and svc_cd   = \''.$kind.'\'
			   and jumin    = \''.$ssn.'\'
			   and seq      = \''.$seq.'\'
			   and del_flag = \'N\'';
	
	$ct = $conn->get_array($sql);

	$svc_seq = $ct['svc_seq'] != '' ? $ct['svc_seq'] : $seq;

	$sql =  ' select from_dt 
			  ,		 to_dt 
				from client_his_svc
			   where org_no = \''.$code.'\'
				 and jumin  = \''.$ssn.'\'
				 and seq    = \''.$svc_seq.'\'';
	$svc = $conn->get_array($sql);
	
	$from_dt = ($ct['from_dt'] != '' ? $ct['from_dt'] : $svc['from_dt']);
	$to_dt = ($ct['to_dt'] != '' ? $ct['to_dt'] : $svc['to_dt']);

	$sql = "select m03_jumin as jumin
			,	   m03_name as name
			,	   m03_tel as tel
			,      m03_hp  as hp
			,	   m03_yboho_name as bohoName
			,	   m03_yboho_juminno as bohoJumin
			,	   m03_yboho_gwange as gwange
			,	   m03_yboho_phone as bohoPhone
			,	   m03_yboho_addr as bohoAddr
			,	   lvl.level as level
			,	   lvl.app_no as injungNo
			,	   case lvl.level when '9' then '일반' when 'A' then '인지지원등급'  else concat(lvl.level,'등급') end as level
			,	   case kind.kind when '3' then '기초수급권자' when '2' then '의료수급권자' when '4' then '경감대상자' else '일반' end as m92_cont
			,	   kind.kind
			,	   concat(m03_juso1, ' ', m03_juso2) as juso
			,	   max(lvl.from_dt)
			,	   max(lvl.to_dt)
			,	   max(kind.from_dt)
			,	   max(kind.to_dt)
			  from m03sugupja
			  left join ( select jumin
						  ,		 from_dt 
						  ,		 to_dt 
						  ,		 app_no
						  ,		 level
							from client_his_lvl
						   where org_no = '".$code."'
							 and (from_dt between '".$from_dt."' and '".$to_dt."'
							  or to_dt between '".$from_dt."' and '".$to_dt."')
						   order by from_dt desc
							 ) as lvl
					 on lvl.jumin = m03_jumin
			  left join ( select jumin
						  ,		 from_dt 
						  ,		 to_dt 
						  ,		 kind
							from client_his_kind
						   where org_no = '".$code."'
							 and (from_dt between '".$from_dt."' and '".$to_dt."'
							  or to_dt between '".$from_dt."' and '".$to_dt."')
						   order by from_dt desc 
						   ) as kind
					 on kind.jumin = m03_jumin
			 where m03_ccode = '".$code."'
			   and m03_mkind = '".$kind."'
			   and m03_jumin = '".$ssn."'
			   and m03_del_yn = 'N'";
	
	$su = $conn->get_array($sql);
	
	$juso =  explode('<br />',nl2br($su['juso']));
	
	$sql = "select m00_mname as manager"
			 . ",      concat(m00_caddr1, ' ', m00_caddr2) as address"
			 . ",      m00_cname as centerName"
			 . ",      m00_code1 as centerCode"
			 . ",      m00_ctel as centerTel"
			 . ",      m00_fax_no as centerFax"
			 . ",      m00_bank_no as bankNo"
			 . ",      m00_bank_name as bankCode"
			 . ",      m00_jikin as jikin"
			 . "  from m00center"
			 . " where m00_mcode = '".$code
			 . "'  and m00_mkind = '".$kind."'";
			
	$center = $conn->get_array($sql);
	
	$jikin = $center['jikin'];


	//기관장 휴대폰번호
	$sql = 'select mobile
			  from mst_manager
			 where org_no = \''.$code.'\'';
	$mg_hp = $conn -> get_data($sql); 

	
	if($su['kind'] == '3'){
		$kindChk = ' ■ 기초수급자          □ 기타 의료급여수급자';
	}else if($su['kind'] == '2'){
		$kindChk = ' □ 기초수급자          ■ 기타 의료급여수급자';
	}else {
		$kindChk = ' □ 기초수급자          □ 기타 의료급여수급자';
	}

	//신청인(센터) 전화번호/휴대전화번호
	$cTel = $myF->phoneStyle($center['centerTel']);
	$cHp = $mg_hp != '' ? '( '.$myF->phoneStyle($mg_hp).' )' : '(            ) ';
	
	
	$hTel = $center['centerTel'] != '' ? $centerName.'(T. '.$myF->phoneStyle($center['centerTel']).' )' : $centerName.'(T.               )';
	$hFax = $center['centerFax'] != '' ? '(FAX. '.$myF->phoneStyle($center['centerFax']).' )' : '(fax.              )';

	//수급자(유선/무선)
	$suTel = $myF->phoneStyle($su['tel'],'.');
	$suHp = $myF->phoneStyle($su['hp'],'.');

	
	$title = '입소.이용 신청서 및 재가서비스 이용내역서';

	

	/*
	$sql = 'select m00_caddr1
			,	   m00_caddr2
			,	   m00_ctel
			,      m00_fax_no
			,	   m00_store_nm
			  from m00center
			 where m00_mcode = \''.$_SESSION['userCenterCode'].'\'';
	$mst = $conn -> get_array($sql);

	$addr = $mst['m00_caddr1'].'</CHAR></TEXT></P><P ParaShape="0" Style="0"><TEXT CharShape="7"><CHAR>'.'  '.$mst['m00_caddr2'];
	$tel  = $myF->phoneStyle($mst['m00_ctel']);
	$fax  = '    fax. '.$myF->phoneStyle($mst['m00_fax_no']);
	$name = $mst['m00_store_nm'];
	*/


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

	$result = mysql_query($sql);
	$arrCT = mysql_fetch_assoc($result);
	mysql_free_result($result);

	//개인정보


	if (Is_File('../mem_picture/'.$arrCT['jikin'])){
	
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
		/*
		$picArea = '<PICTURE Reverse="false">
						<SHAPEOBJECT InstId="1288554417" Lock="false" NumberingType="None" TextWrap="BehindText" ZOrder="1">
							<SIZE Height="'.$imgH.'" HeightRelTo="Absolute" Protect="false" Width="'.$imgW.'" WidthRelTo="Absolute"/>
							<POSITION AffectLSpacing="false" AllowOverlap="false" FlowWithText="false" HoldAnchorAndSO="false" HorzAlign="Left" HorzOffset="15100" HorzRelTo="Para" TreatAsChar="false" VertAlign="Top" VertOffset="51150" VertRelTo="Para"/>
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
			*/

		$picArea = '';
		

	}else {
		$picArea = '';
	}


echo '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<HWPML Style="embed" SubVersion="8.0.0.0" Version="2.8">

    <HEAD SecCnt="1">
        <DOCSUMMARY>
            <TITLE>법령안</TITLE>
            <SUBJECT>노인장기요양보험법 시행규칙</SUBJECT>
            <AUTHOR>박지윤</AUTHOR>
            <DATE>2015년 12월 28일</DATE>
            <KEYWORDS>총리령 일부개정령안, 부령 일부개정령안, 대통령훈령 일부개정령안, 국무총리훈령 일부개정령안</KEYWORDS>
            <COMMENTS>법제처 정부입법지원센터 법령안편집기에서 작성된 문서입니다.</COMMENTS>
        </DOCSUMMARY>
        <DOCSETTING>
            <BEGINNUMBER Endnote="1" Equation="1" Footnote="1" Page="1" Picture="1" Table="1" />
            <CARETPOS List="36" Para="0" Pos="0" />
        </DOCSETTING>
        <MAPPINGTABLE>
            <FACENAMELIST>
                <FONTFACE Count="9" Lang="Hangul">
                    <FONT Id="0" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="1" Name="돋움" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="2" Name="돋움체" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="9" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="3" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="4" Name="휴먼명조" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="4" StrokeVariation="1" Weight="5" XHeight="1"/></FONT>
                    <FONT Id="5" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="6" Name="한양견고딕" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="2" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="7" Name="-윤고딕110" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="4" StrokeVariation="1" Weight="5" XHeight="1"/></FONT>
                    <FONT Id="8" Name="산돌명조 L" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="4" StrokeVariation="1" Weight="3" XHeight="1"/></FONT>
                </FONTFACE>
                <FONTFACE Count="8" Lang="Latin">
                    <FONT Id="0" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="1" Name="돋움" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="2" Name="돋움체" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="9" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="3" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="4" Name="Times New Roman" Type="ttf"><TYPEINFO ArmStyle="5" Contrast="5" FamilyType="2" Letterform="2" Midline="3" Proportion="3" StrokeVariation="4" Weight="6" XHeight="4"/></FONT>
                    <FONT Id="5" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="6" Name="한양견고딕" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="2" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="7" Name="-윤고딕310" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="4" StrokeVariation="1" Weight="5" XHeight="1"/></FONT>
                </FONTFACE>
                <FONTFACE Count="9" Lang="Hanja">
                    <FONT Id="0" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="1" Name="돋움" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="2" Name="돋움체" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="9" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="3" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="4" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="5" Name="한양중고딕" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="2" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="6" Name="신명 중명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="7" Name="-윤고딕120" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="4" StrokeVariation="1" Weight="5" XHeight="1"/></FONT>
                    <FONT Id="8" Name="산돌명조 L" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="4" StrokeVariation="1" Weight="3" XHeight="1"/></FONT>
                </FONTFACE>
                <FONTFACE Count="9" Lang="Japanese">
                    <FONT Id="0" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="1" Name="돋움" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="2" Name="돋움체" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="9" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="3" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="4" Name="한양해서" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="5" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="6" Name="한양중고딕" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="2" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="7" Name="-윤고딕120" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="4" StrokeVariation="1" Weight="5" XHeight="1"/></FONT>
                    <FONT Id="8" Name="산돌명조 L" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="4" StrokeVariation="1" Weight="3" XHeight="1"/></FONT>
                </FONTFACE>
                <FONTFACE Count="8" Lang="Other">
                    <FONT Id="0" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="1" Name="돋움" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="2" Name="돋움체" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="9" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="3" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="4" Name="한양해서" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="5" Name="Times New Roman" Type="ttf"><TYPEINFO ArmStyle="5" Contrast="5" FamilyType="2" Letterform="2" Midline="3" Proportion="3" StrokeVariation="4" Weight="6" XHeight="4"/></FONT>
                    <FONT Id="6" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="7" Name="-윤고딕120" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="4" StrokeVariation="1" Weight="5" XHeight="1"/></FONT>
                </FONTFACE>
                <FONTFACE Count="7" Lang="Symbol">
                    <FONT Id="0" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="1" Name="돋움" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="2" Name="돋움체" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="9" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="3" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="4" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="5" Name="한양중고딕" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="2" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="6" Name="-윤고딕120" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="4" StrokeVariation="1" Weight="5" XHeight="1"/></FONT>
                </FONTFACE>
                <FONTFACE Count="8" Lang="User">
                    <FONT Id="0" Name="굴림" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="1" Name="돋움" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="2" Name="돋움체" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="9" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="3" Name="바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="4" Name="한양해서" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="5" Name="명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="6" Name="-윤고딕120" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="4" StrokeVariation="1" Weight="5" XHeight="1"/></FONT>
                    <FONT Id="7" Name="산돌명조 L" Type="ttf"><SUBSTFONT Name="한컴바탕" Type="ttf"/><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="4" StrokeVariation="1" Weight="3" XHeight="1"/></FONT>
                </FONTFACE>
            </FACENAMELIST>
            <BORDERFILLLIST Count="42">
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="1" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.1mm" />
                    <RIGHTBORDER Type="None" Width="0.1mm" />
                    <TOPBORDER Type="None" Width="0.1mm" />
                    <BOTTOMBORDER Type="None" Width="0.1mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                    <FILLBRUSH>
                        <WINDOWBRUSH Alpha="0" FaceColor="4294967295" HatchColor="4278190080" />
                    </FILLBRUSH>
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="2" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Type="Solid" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="3" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="4278190080" Type="None" Width="0.1mm" />
                    <RIGHTBORDER Color="4278190080" Type="None" Width="0.1mm" />
                    <TOPBORDER Color="4278190080" Type="None" Width="0.1mm" />
                    <BOTTOMBORDER Color="4278190080" Type="None" Width="0.1mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                    <FILLBRUSH>
                        <WINDOWBRUSH Alpha="0" FaceColor="4294967295" HatchColor="4278190080" />
                    </FILLBRUSH>
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="4" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.12mm" />
                    <RIGHTBORDER Type="None" Width="0.12mm" />
                    <TOPBORDER Type="None" Width="0.12mm" />
                    <BOTTOMBORDER Type="None" Width="0.4mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="5" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.4mm" />
                    <RIGHTBORDER Type="None" Width="0.4mm" />
                    <TOPBORDER Type="None" Width="0.4mm" />
                    <BOTTOMBORDER Type="None" Width="0.4mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="6" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="7" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.5mm" />
                    <RIGHTBORDER Type="None" Width="0.5mm" />
                    <TOPBORDER Type="None" Width="0.12mm" />
                    <BOTTOMBORDER Type="None" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="8" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.5mm" />
                    <RIGHTBORDER Type="None" Width="0.5mm" />
                    <TOPBORDER Color="9671571" Type="None" Width="0.7mm" />
                    <BOTTOMBORDER Type="None" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="9" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="9671571" Type="None" Width="0.7mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="10" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="11" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="12" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="13" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="14" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="15" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="16" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Color="10066329" Type="Solid" Width="0.15mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="17" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Color="10066329" Type="Solid" Width="0.15mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="18" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="10066329" Type="Solid" Width="0.15mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="19" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.5mm" />
                    <RIGHTBORDER Type="None" Width="0.5mm" />
                    <TOPBORDER Type="None" Width="0.12mm" />
                    <BOTTOMBORDER Color="10066329" Type="Solid" Width="0.7mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="20" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="10066329" Type="None" Width="0.5mm" />
                    <RIGHTBORDER Color="10066329" Type="None" Width="0.5mm" />
                    <TOPBORDER Color="10066329" Type="Solid" Width="0.7mm" />
                    <BOTTOMBORDER Color="10066329" Type="Solid" Width="0.15mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="21" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="7895160" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="7895160" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="22" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="23" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="24" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="25" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="26" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.5mm" />
                    <RIGHTBORDER Type="None" Width="0.5mm" />
                    <TOPBORDER Color="6118749" Type="Solid" Width="0.7mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <FILLBRUSH>
                        <WINDOWBRUSH Alpha="0" FaceColor="12303291" HatchColor="0" />
                    </FILLBRUSH>
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="27" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="28" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.1mm" />
                    <RIGHTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.15mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="29" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                    <FILLBRUSH>
                        <WINDOWBRUSH Alpha="0" FaceColor="12303291" HatchColor="0" />
                    </FILLBRUSH>
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="30" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                    <FILLBRUSH>
                        <WINDOWBRUSH Alpha="0" FaceColor="12303291" HatchColor="0" />
                    </FILLBRUSH>
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="31" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                    <FILLBRUSH>
                        <WINDOWBRUSH Alpha="0" FaceColor="12303291" HatchColor="0" />
                    </FILLBRUSH>
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="32" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="33" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="34" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.1mm" />
                    <RIGHTBORDER Type="None" Width="0.1mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="None" Width="0.4mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="2" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="35" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.1mm" />
                    <RIGHTBORDER Type="None" Width="0.1mm" />
                    <TOPBORDER Color="6118749" Type="Solid" Width="0.7mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <FILLBRUSH>
                        <WINDOWBRUSH Alpha="0" FaceColor="12303291" HatchColor="0" />
                    </FILLBRUSH>
                </BORDERFILL>
                <BORDERFILL BackSlash="2" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="36" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.1mm" />
                    <RIGHTBORDER Type="None" Width="0.1mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="2" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="37" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="None" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="2" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="38" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="39" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="40" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="41" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="42" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="8355711" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="8355711" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
            </BORDERFILLLIST>
            <CHARSHAPELIST Count="44">
                <CHARSHAPE BorderFillId="0" Height="1400" Id="0" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="5" Hanja="4" Japanese="5" Latin="5" Other="6" Symbol="4" User="5" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1500" Id="1" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="5" Hanja="4" Japanese="5" Latin="5" Other="6" Symbol="4" User="5" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <BOLD/>
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1000" Id="2" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="5" Hanja="4" Japanese="5" Latin="5" Other="6" Symbol="4" User="5" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1400" Id="3" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="5" Hanja="4" Japanese="5" Latin="5" Other="6" Symbol="4" User="5" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1000" Id="4" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="3" Hanja="3" Japanese="3" Latin="3" Other="3" Symbol="3" User="3" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1100" Id="5" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="5" Hanja="4" Japanese="5" Latin="5" Other="6" Symbol="4" User="5" />
                    <RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98" />
                    <CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1000" Id="6" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="900" Id="7" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="8" Hanja="8" Japanese="8" Latin="4" Other="5" Symbol="4" User="7" />
                    <RATIO Hangul="98" Hanja="98" Japanese="98" Latin="100" Other="100" Symbol="98" User="98" />
                    <CHARSPACING Hangul="-12" Hanja="-12" Japanese="-12" Latin="-2" Other="-2" Symbol="-15" User="-12" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="100" Id="8" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="5" Hanja="4" Japanese="5" Latin="5" Other="6" Symbol="4" User="5" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="900" Id="9" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="800" Id="10" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1300" Id="11" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <BOLD/>
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1000" Id="12" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="95" Hanja="90" Japanese="90" Latin="97" Other="90" Symbol="90" User="90" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1000" Id="13" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="4" Hanja="6" Japanese="4" Latin="5" Other="4" Symbol="4" User="4" />
                    <RATIO Hangul="98" Hanja="98" Japanese="98" Latin="98" Other="98" Symbol="98" User="98" />
                    <CHARSPACING Hangul="-5" Hanja="0" Japanese="-5" Latin="-5" Other="-5" Symbol="0" User="-5" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1100" Id="14" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="100" Id="15" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="900" Id="16" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <BOLD/>
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="800" Id="17" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="800" Id="18" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-24" Hanja="-24" Japanese="-24" Latin="-24" Other="-24" Symbol="-24" User="-24" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="900" Id="19" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="800" Id="20" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-15" Hanja="-15" Japanese="-15" Latin="-15" Other="-15" Symbol="-15" User="-15" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1000" Id="21" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="900" Id="22" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-4" Hanja="-4" Japanese="-4" Latin="-4" Other="-4" Symbol="-4" User="-4" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="800" Id="23" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-10" Hanja="-10" Japanese="-10" Latin="-10" Other="-10" Symbol="-10" User="-10" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="800" Id="24" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-1" Hanja="-1" Japanese="-1" Latin="-1" Other="-1" Symbol="-1" User="-1" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="800" Id="25" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="800" Id="26" ShadeColor="16777215" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1000" Id="27" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="100" Id="28" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-10" Hanja="-10" Japanese="-10" Latin="-10" Other="-10" Symbol="-10" User="-10" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1000" Id="29" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-7" Hanja="-7" Japanese="-7" Latin="-7" Other="-7" Symbol="-7" User="-7" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1000" Id="30" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-10" Hanja="-10" Japanese="-10" Latin="-10" Other="-10" Symbol="-10" User="-10" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1000" Id="31" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-1" Hanja="-1" Japanese="-1" Latin="-1" Other="-1" Symbol="-1" User="-1" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1000" Id="32" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="980" Id="33" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="7" Hanja="7" Japanese="7" Latin="7" Other="7" Symbol="6" User="6" />
                    <RATIO Hangul="95" Hanja="90" Japanese="90" Latin="97" Other="90" Symbol="90" User="90" />
                    <CHARSPACING Hangul="-7" Hanja="-7" Japanese="-7" Latin="-10" Other="-7" Symbol="-7" User="-7" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1000" Id="34" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="95" Hanja="90" Japanese="90" Latin="97" Other="90" Symbol="90" User="90" />
                    <CHARSPACING Hangul="-7" Hanja="-7" Japanese="-7" Latin="-10" Other="-7" Symbol="-7" User="-7" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="800" Id="35" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-34" Hanja="-34" Japanese="-34" Latin="-34" Other="-34" Symbol="-34" User="-34" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1000" Id="36" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="95" Hanja="90" Japanese="90" Latin="97" Other="90" Symbol="90" User="90" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1600" Id="37" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="6" Hanja="5" Japanese="6" Latin="6" Other="6" Symbol="5" User="5" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-13" Hanja="-13" Japanese="-13" Latin="-13" Other="-13" Symbol="-13" User="-13" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <BOLD/>
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="1000" Id="38" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-21" Hanja="-21" Japanese="-21" Latin="-21" Other="-21" Symbol="-21" User="-21" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="800" Id="39" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-3" Hanja="-3" Japanese="-3" Latin="-3" Other="-3" Symbol="-3" User="-3" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="400" Id="40" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="400" Id="41" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-10" Hanja="-10" Japanese="-10" Latin="-10" Other="-10" Symbol="-10" User="-10" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="800" Id="42" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-2" Hanja="-2" Japanese="-2" Latin="-2" Other="-2" Symbol="-2" User="-2" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="1" Height="800" Id="43" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-39" Hanja="-39" Japanese="-39" Latin="-39" Other="-39" Symbol="-39" User="-39" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
            </CHARSHAPELIST>
            <TABDEFLIST Count="1">
                <TABDEF AutoTabLeft="false" AutoTabRight="false" Id="0" />
            </TABDEFLIST>
            <NUMBERINGLIST Count="1">
                <NUMBERING Id="1" Start="0">
                    <PARAHEAD Alignment="Left" AutoIndent="true" Level="1" NumFormat="Digit" Start="1" TextOffset="50" TextOffsetType="percent" UseInstWidth="false" WidthAdjust="0">^1.</PARAHEAD>
                    <PARAHEAD Alignment="Left" AutoIndent="true" Level="2" NumFormat="HangulSyllable" Start="1" TextOffset="50" TextOffsetType="percent" UseInstWidth="false" WidthAdjust="0">^2.</PARAHEAD>
                    <PARAHEAD Alignment="Left" AutoIndent="true" Level="3" NumFormat="Digit" Start="1" TextOffset="50" TextOffsetType="percent" UseInstWidth="false" WidthAdjust="0">^3)</PARAHEAD>
                    <PARAHEAD Alignment="Left" AutoIndent="true" Level="4" NumFormat="HangulSyllable" Start="1" TextOffset="50" TextOffsetType="percent" UseInstWidth="false" WidthAdjust="0">^4)</PARAHEAD>
                    <PARAHEAD Alignment="Left" AutoIndent="true" Level="5" NumFormat="Digit" Start="1" TextOffset="50" TextOffsetType="percent" UseInstWidth="false" WidthAdjust="0">(^5)</PARAHEAD>
                    <PARAHEAD Alignment="Left" AutoIndent="true" Level="6" NumFormat="HangulSyllable" Start="1" TextOffset="50" TextOffsetType="percent" UseInstWidth="false" WidthAdjust="0">(^6)</PARAHEAD>
                    <PARAHEAD Alignment="Left" AutoIndent="true" Level="7" NumFormat="CircledDigit" Start="1" TextOffset="50" TextOffsetType="percent" UseInstWidth="false" WidthAdjust="0">^7</PARAHEAD>
                </NUMBERING>
            </NUMBERINGLIST>
            <PARASHAPELIST Count="49">
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="0" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="1" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Left" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="2" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="3" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-2000" Left="0" LineSpacing="90" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="4" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="5" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="180" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="6" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="180" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="7" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-2800" Left="0" LineSpacing="180" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="8" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="9" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-2800" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="10" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Left" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="11" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="12" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-4000" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="13" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-6800" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="14" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-6800" Left="0" LineSpacing="180" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="15" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-4000" Left="0" LineSpacing="180" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="20" FontLineHeight="false" HeadingType="None" Id="16" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="3000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="17" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="5" FontLineHeight="false" HeadingType="None" Id="18" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="19" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Left" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="20" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="21" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Right" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="22" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="1500" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="1500" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="23" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="110" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Right" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="24" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="25" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-2312" Left="3240" LineSpacing="162" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Left" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="26" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="100" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="27" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="120" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Left" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="28" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="90" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Right" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="29" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Left" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="true" HeadingType="None" Id="30" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="600" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="600" />
                    <PARABORDER BorderFill="3" Connect="true" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="75" FontLineHeight="false" HeadingType="None" Id="31" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="137" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="true" HeadingType="None" Id="32" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-1000" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="3" Connect="true" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="true" HeadingType="None" Id="33" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="3" Connect="true" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="true" HeadingType="None" Id="34" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="3" Connect="true" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="75" FontLineHeight="false" HeadingType="None" Id="35" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="120" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="true" HeadingType="None" Id="36" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="3" Connect="true" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Left" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="true" HeadingType="None" Id="37" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="150" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="3" Connect="true" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Right" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="38" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="1500" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Left" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="39" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="150" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Left" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="40" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="120" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="41" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="42" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="43" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Right" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="44" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="45" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Right" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="true" HeadingType="None" Id="46" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="3" Connect="true" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Left" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="25" FontLineHeight="false" HeadingType="None" Id="47" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-2610" Left="1500" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="25" FontLineHeight="false" HeadingType="None" Id="48" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="110" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="1" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
            </PARASHAPELIST>
            <STYLELIST Count="22">
                <STYLE CharShape="2" EngName="Normal" Id="0" LangId="1042" LockForm="0" Name="바탕글" NextStyle="0" ParaShape="11" Type="Para" />
                <STYLE CharShape="4" EngName="Body" Id="1" LangId="1042" LockForm="1" Name="본문" NextStyle="1" ParaShape="16" Type="Para" />
                <STYLE CharShape="0" EngName="lawdefault" Id="2" LangId="1042" LockForm="0" Name="법령기본스타일" NextStyle="2" ParaShape="4" Type="Para" />
                <STYLE CharShape="0" EngName="Jo" Id="3" LangId="1042" LockForm="0" Name="조" NextStyle="3" ParaShape="9" Type="Para" />
                <STYLE CharShape="0" EngName="Hang" Id="4" LangId="1042" LockForm="0" Name="항" NextStyle="4" ParaShape="9" Type="Para" />
                <STYLE CharShape="3" EngName="Ho" Id="5" LangId="1042" LockForm="0" Name="호" NextStyle="5" ParaShape="12" Type="Para" />
                <STYLE CharShape="3" EngName="Mok" Id="6" LangId="1042" LockForm="0" Name="목" NextStyle="6" ParaShape="13" Type="Para" />
                <STYLE CharShape="3" EngName="Semok" Id="7" LangId="1042" LockForm="0" Name="세목" NextStyle="7" ParaShape="13" Type="Para" />
                <STYLE CharShape="0" EngName="pyun" Id="8" LangId="1042" LockForm="0" Name="편장절관" NextStyle="8" ParaShape="10" Type="Para" />
                <STYLE CharShape="0" EngName="JOTBL" Id="9" LangId="1042" LockForm="0" Name="조_표" NextStyle="9" ParaShape="7" Type="Para" />
                <STYLE CharShape="0" EngName="HANGTBL" Id="10" LangId="1042" LockForm="0" Name="항_표" NextStyle="10" ParaShape="7" Type="Para" />
                <STYLE CharShape="3" EngName="HOTBL" Id="11" LangId="1042" LockForm="0" Name="호_표" NextStyle="11" ParaShape="15" Type="Para" />
                <STYLE CharShape="3" EngName="MOKTBL" Id="12" LangId="1042" LockForm="0" Name="목_표" NextStyle="12" ParaShape="14" Type="Para" />
                <STYLE CharShape="3" EngName="SEMOKTBL" Id="13" LangId="1042" LockForm="0" Name="세목_표" NextStyle="13" ParaShape="14" Type="Para" />
                <STYLE CharShape="0" EngName="PYUNTBL" Id="14" LangId="1042" LockForm="0" Name="편장절관_표" NextStyle="14" ParaShape="5" Type="Para" />
                <STYLE CharShape="0" EngName="LAWTBL" Id="15" LangId="1042" LockForm="0" Name="제명_표" NextStyle="15" ParaShape="6" Type="Para" />
                <STYLE CharShape="0" Id="16" LangId="1042" LockForm="0" Name="기본GEN" NextStyle="16" ParaShape="1" Type="Para" />
                <STYLE CharShape="13" Id="17" LangId="1042" LockForm="0" Name="가." NextStyle="17" ParaShape="25" Type="Para" />
                <STYLE CharShape="5" Id="18" LangId="1042" LockForm="0" Name="본문(1)" NextStyle="18" ParaShape="17" Type="Para" />
                <STYLE CharShape="33" Id="19" LangId="1042" LockForm="0" Name="#표가운데" NextStyle="19" ParaShape="31" Type="Para" />
                <STYLE CharShape="7" Id="20" LangId="1042" LockForm="0" Name="표_내용(본문)" NextStyle="20" ParaShape="18" Type="Para" />
                <STYLE CharShape="6" EngName="Page Number" Id="21" LangId="1042" LockForm="1" Name="쪽 번호" NextStyle="21" ParaShape="0" Type="Para" />
            </STYLELIST>
        </MAPPINGTABLE>
        <COMPATIBLEDOCUMENT TargetProgram="None">
            <LAYOUTCOMPATIBILITY AdjustBaselineInFixedLinespacing="false" AdjustLineheightToFont="false" AdjustParaBorderOffsetWithBorder="false" AdjustParaBorderfillToSpacing="false" ApplyAtLeastToPercent100Pct="false" ApplyCharSpacingToCharGrid="false" ApplyExtendHeaderFooterEachSection="false" ApplyFontWeightToBold="false" ApplyFontspaceToLatin="false" ApplyNextspacingOfLastPara="false" ApplyParaBorderToOutside="false" ApplyPrevspacingBeneathObject="false" BaseCharUnitOfIndentOnFirstChar="false" BaseCharUnitOnEAsian="false" BaseLinespacingOnLinegrid="false" ConnectParaBorderfillOfEqualBorder="false" DoNotAdjustEmptyAnchorLine="false" DoNotAdjustWordInJustify="false" DoNotAlignWhitespaceOnRight="false" DoNotApplyAutoSpaceEAsianEng="false" DoNotApplyAutoSpaceEAsianNum="false" DoNotApplyExtensionCharCompose="false" DoNotApplyGridInHeaderFooter="false" DoNotApplyImageEffect="false" DoNotApplyShapeComment="false" DoNotApplyStrikeoutWithUnderline="false" DoNotApplyVertOffsetOfForward="false" DoNotFormattingAtBeneathAnchor="false" DoNotHoldAnchorOfTable="false" ExtendLineheightToOffset="false" ExtendLineheightToParaBorderOffset="false" ExtendVertLimitToPageMargins="false" FixedUnderlineWidth="false" OverlapBothAllowOverlap="false" TreatQuotationAsLatin="false" UseInnerUnderline="false" UseLowercaseStrikeout="false" />
        </COMPATIBLEDOCUMENT>
    </HEAD>

    <BODY>
        <SECTION Id="0">
            <P ColumnBreak="false" PageBreak="false" ParaShape="3" Style="0">
                <TEXT CharShape="1">
                    <COLDEF Count="1" Layout="Left" SameGap="0" SameSize="true" Type="Newspaper" />
                    <SECDEF CharGrid="0" FirstBorder="false" FirstFill="false" LineGrid="0" OutlineShape="1" SpaceColumns="1134" TabStop="8000" TextDirection="0" TextVerticalWidthHead="0">
                        <STARTNUMBER Equation="0" Figure="0" Page="0" PageStartsOn="Both" Table="0" />
                        <HIDE Border="false" EmptyLine="false" Fill="false" Footer="false" Header="false" MasterPage="false" PageNumPos="false" />
                        <PAGEDEF GutterType="LeftOnly" Height="84188" Landscape="0" Width="59528">
                            <PAGEMARGIN Bottom="2835" Footer="0" Gutter="0" Header="0" Left="5669" Right="5669" Top="5669" />
                        </PAGEDEF>
                        <FOOTNOTESHAPE>
                            <AUTONUMFORMAT SuffixChar=")" Superscript="false" Type="Digit" />
                            <NOTELINE Length="5cm" Type="Solid" Width="0.1mm" />
                            <NOTESPACING AboveLine="567" BelowLine="567" BetweenNotes="850" />
                            <NOTENUMBERING NewNumber="1" Type="Continuous" />
                            <NOTEPLACEMENT BeneathText="false" Place="EachColumn" />
                        </FOOTNOTESHAPE>
                        <ENDNOTESHAPE>
                            <AUTONUMFORMAT SuffixChar=")" Superscript="false" Type="Digit" />
                            <NOTELINE Length="5cm" Type="Solid" Width="0.1mm" />
                            <NOTESPACING AboveLine="567" BelowLine="567" BetweenNotes="850" />
                            <NOTENUMBERING NewNumber="1" Type="Continuous" />
                            <NOTEPLACEMENT BeneathText="false" Place="EndOfDocument" />
                        </ENDNOTESHAPE>
                        <PAGEBORDERFILL FillArea="Paper" FooterInside="false" HeaderInside="false" TextBorder="true" Type="Both">
                            <PAGEOFFSET Bottom="1417" Left="1417" Right="1417" Top="1417" />
                        </PAGEBORDERFILL>
                        <PAGEBORDERFILL FillArea="Paper" FooterInside="false" HeaderInside="false" TextBorder="true" Type="Even">
                            <PAGEOFFSET Bottom="1417" Left="1417" Right="1417" Top="1417" />
                        </PAGEBORDERFILL>
                        <PAGEBORDERFILL FillArea="Paper" FooterInside="false" HeaderInside="false" TextBorder="true" Type="Odd">
                            <PAGEOFFSET Bottom="1417" Left="1417" Right="1417" Top="1417" />
                        </PAGEBORDERFILL>
                    </SECDEF>
                    <TABLE BorderFill="2" CellSpacing="0" ColCount="28" PageBreak="Cell" RepeatHeader="false" RowCount="32">
                        <SHAPEOBJECT InstId="1292303561" Lock="false" NumberingType="Table" ZOrder="0">
                            <SIZE Height="75849" HeightRelTo="Absolute" Protect="false" Width="47744" WidthRelTo="Absolute" />
                            <POSITION AffectLSpacing="false" AllowOverlap="false" FlowWithText="true" HoldAnchorAndSO="false" HorzAlign="Left" HorzOffset="0" HorzRelTo="Para" TreatAsChar="true" VertAlign="Top" VertOffset="0" VertRelTo="Para" />
                            <OUTSIDEMARGIN Bottom="138" Left="138" Right="138" Top="138" />
                        </SHAPEOBJECT>
                        <INSIDEMARGIN Bottom="138" Left="138" Right="138" Top="138" />
                        <ROW>
                            <CELL BorderFill="4" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2229" Protect="false" RowAddr="0" RowSpan="1" Width="47744">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="26" Style="0">
                                        <TEXT CharShape="26">
                                            <CHAR>■ 노인장기요양보험법 시행규칙 [별지 제10호서식]</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="5" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3581" Protect="false" RowAddr="1" RowSpan="1" Width="47744"><CELLMARGIN Bottom="283" Left="141" Right="141" Top="283"/><PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center"><P ParaShape="8" Style="0"><TEXT CharShape="37"><CHAR>장기요양기관 입소ㆍ이용신청서([  ] 신규신청 [  ]갱신 [  ]<FWSPACE/>변경 [  ]해지)</CHAR></TEXT></P></PARALIST></CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="5" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="1649" Protect="false" RowAddr="2" RowSpan="1" Width="47744">
                                <CELLMARGIN Bottom="283" Left="141" Right="141" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="43" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR>※［ ］에는 해당되는 곳에 √표를 합니다.</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="31" ColAddr="0" ColSpan="2" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2551" Protect="false" RowAddr="3" RowSpan="1" Width="4999">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="28" Style="0">
                                        <TEXT CharShape="9">
                                            <CHAR>접수번호</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="29" ColAddr="2" ColSpan="7" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2551" Protect="false" RowAddr="3" RowSpan="1" Width="11067">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="2" Style="0">
                                        <TEXT CharShape="16" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="30" ColAddr="9" ColSpan="1" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2551" Protect="false" RowAddr="3" RowSpan="1" Width="5016">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="28" Style="0">
                                        <TEXT CharShape="9">
                                            <CHAR>접수일자</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="29" ColAddr="10" ColSpan="9" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2551" Protect="false" RowAddr="3" RowSpan="1" Width="12004">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="28" Style="0">
                                        <TEXT CharShape="9" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="30" ColAddr="19" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2551" Protect="false" RowAddr="3" RowSpan="1" Width="5903">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="2" Style="0">
                                        <TEXT CharShape="9">
                                            <CHAR>처리기간</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="31" ColAddr="25" ColSpan="3" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2551" Protect="false" RowAddr="3" RowSpan="1" Width="8755">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="43" Style="0">
                                        <TEXT CharShape="9">
                                            <CHAR>7일이내</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="21" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="0" Protect="false" RowAddr="4" RowSpan="1" Width="47744">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="0" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="26" Style="0">
                                        <TEXT CharShape="8" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="23" ColAddr="0" ColSpan="1" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="9051" Protect="false" RowAddr="5" RowSpan="3" Width="4638">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="33" Style="0">
                                        <TEXT CharShape="14">
                                            <CHAR>신청인</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="12" ColAddr="1" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3017" Protect="false" RowAddr="5" RowSpan="1" Width="6524">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="34" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>성명</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="12" ColAddr="7" ColSpan="5" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3017" Protect="false" RowAddr="5" RowSpan="1" Width="11335">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="34" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>'.$su['bohoName'].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="12" ColAddr="12" ColSpan="3" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3017" Protect="false" RowAddr="5" RowSpan="1" Width="5675">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="34" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>생년월일</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="12" ColAddr="15" ColSpan="7" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3017" Protect="false" RowAddr="5" RowSpan="1" Width="9071">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="34" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>'.$myF->issToBirthday($su['bohoJumin'],'.').'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="12" ColAddr="22" ColSpan="5" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3166" Protect="false" RowAddr="5" RowSpan="1" Width="5675">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="32" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>수급자와의 관계</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="24" ColAddr="27" ColSpan="1" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3017" Protect="false" RowAddr="5" RowSpan="1" Width="4826">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="34" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>'.$su['gwange'].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="11" ColAddr="1" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3017" Protect="false" RowAddr="6" RowSpan="1" Width="6524">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="34" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>주소</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="22" ColAddr="7" ColSpan="21" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3017" Protect="false" RowAddr="6" RowSpan="1" Width="36582">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="34" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>'.$su['bohoAddr'].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="13" ColAddr="1" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3017" Protect="false" RowAddr="7" RowSpan="1" Width="6524">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="34" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>전화번호</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="39" ColAddr="7" ColSpan="10" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3017" Protect="false" RowAddr="7" RowSpan="1" Width="18291">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="34" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>'.$su['bohoPhone'].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="40" ColAddr="17" ColSpan="11" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3017" Protect="false" RowAddr="7" RowSpan="1" Width="18291">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Bottom">
                                    <P ParaShape="46" Style="0">
                                        <TEXT CharShape="27" />
                                    </P>
                                    <P ParaShape="46" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>(휴대전화: '.$su['bohoPhone'].' )</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="6" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="241" Protect="false" RowAddr="8" RowSpan="1" Width="47744">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="0" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="33" Style="0">
                                        <TEXT CharShape="15" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="23" ColAddr="0" ColSpan="1" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="18000" Protect="false" RowAddr="9" RowSpan="6" Width="4638">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="33" Style="0">
                                        <TEXT CharShape="14">
                                            <CHAR>수급자</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="12" ColAddr="1" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="9" RowSpan="1" Width="6524">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="36" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>성명</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="12" ColAddr="7" ColSpan="9" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="9" RowSpan="1" Width="17679">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="36" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>'.$su['name'].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="12" ColAddr="16" ColSpan="7" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="9" RowSpan="1" Width="9016">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="36" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>주민등록번호</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="24" ColAddr="23" ColSpan="5" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="9" RowSpan="1" Width="9887">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="36" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>'.$myF->issStyle($su['jumin']).'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="11" ColAddr="1" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="10" RowSpan="1" Width="6524">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="36" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>장기요양</CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="36" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>등급</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="11" ColAddr="7" ColSpan="9" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="10" RowSpan="1" Width="17679">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="36" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>'.$su['level'].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="11" ColAddr="16" ColSpan="7" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="10" RowSpan="1" Width="9016">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="36" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>장기요양인정번호</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="22" ColAddr="23" ColSpan="5" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="10" RowSpan="1" Width="9887">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="36" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>'.$su['injungNo'].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="11" ColAddr="1" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="11" RowSpan="1" Width="6524">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="36" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>주소</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="22" ColAddr="7" ColSpan="21" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="11" RowSpan="1" Width="36582">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="34" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>'.$su['juso'].'</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="27" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="11" ColAddr="1" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="12" RowSpan="1" Width="6524">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="36" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>전화번호</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="41" ColAddr="7" ColSpan="10" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="12" RowSpan="1" Width="18291">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="34" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>'.$suTel.'</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="27" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="42" ColAddr="17" ColSpan="11" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="12" RowSpan="1" Width="18291">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Bottom">
                                    <P ParaShape="46" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>(휴대전화: '.$suHp.' )</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="11" ColAddr="1" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="13" RowSpan="1" Width="6524">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="36" Style="0">
                                        <TEXT CharShape="38">
                                            <CHAR>입소·이용 희망 </CHAR>
                                        </TEXT>
                                        <TEXT CharShape="31">
                                            <CHAR>장기요양기관</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="22" ColAddr="7" ColSpan="21" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="13" RowSpan="1" Width="36582">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="36" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>'.$center['centerName'].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="13" ColAddr="1" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="14" RowSpan="1" Width="6524">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="34" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR>구분 </CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="25" ColAddr="7" ColSpan="21" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3000" Protect="false" RowAddr="14" RowSpan="1" Width="36582">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="35" Style="19">
                                        <TEXT CharShape="34">
                                            <CHAR> [ ] </CHAR>
                                        </TEXT>
                                        <TEXT CharShape="36">
                                            <CHAR>「</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="12">
                                            <CHAR>의료급여법」 제3조제1항제1호에 따른 의료급여를 받는 사람</CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="35" Style="19">
                                        <TEXT CharShape="34">
                                            <CHAR> [ ] </CHAR>
                                        </TEXT>
                                        <TEXT CharShape="36">
                                            <CHAR>「</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="12">
                                            <CHAR>의료급여법」 제3조제1항제1호 외의 규정에 따른 의료급여를 받는 사람</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="36" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="9" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2783" Protect="false" RowAddr="15" RowSpan="1" Width="47744">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="37" Style="0">
                                        <TEXT CharShape="27">
                                            <CHAR> 「노인장기요양보험법 시행규칙」 제13조에 따라 장기요양기관 입소·이용을 위와 같이 신청합니다. </CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="8" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1566" Protect="false" RowAddr="16" RowSpan="1" Width="47744">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="22" Style="0">
                                        <TEXT CharShape="19">
                                            <CHAR>'.date('Y').' 년 '.date('m').' 월 '.date('d').' 일</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="7" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="7197" Protect="false" RowAddr="17" RowSpan="1" Width="47744">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="38" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>신청인: '.$su['bohoName'].' </CHAR>
                                        </TEXT>
                                        <TEXT CharShape="39">
                                            <CHAR>(서명 또는 인)</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="21" />
                                    </P>
                                    <P ParaShape="38" Style="0">
                                        <TEXT CharShape="21" />
                                    </P>
                                    <P ParaShape="47" Style="0">
                                        <TEXT CharShape="19">
                                            <CHAR>※ </CHAR>
                                        </TEXT>
                                        <TEXT CharShape="22">
                                            <CHAR>신청인이 수급자 본인·가족, 사회복지전담공무원, 특별자치시장·특별자치도지사·시장·군수·구청장이 지정한 자 외의 이해관계인인 경우에는 수급자의 동의를 받아야 합니다.</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="21" />
                                    </P>
                                    <P ParaShape="38" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>수급자(또는 보호자): '.$su['name'].' </CHAR>
                                        </TEXT>
                                        <TEXT CharShape="39">
                                            <CHAR>(서명 또는 인)</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="21" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="19" ColAddr="0" ColSpan="25" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2708" Protect="false" RowAddr="18" RowSpan="1" Width="38989">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="30" Style="0">
                                        <TEXT CharShape="32">
                                            <CHAR> </CHAR>
                                        </TEXT>
                                        <TEXT CharShape="11">
                                            <CHAR>○○ 특별자치시장·특별자치도지사·시장·군수·구청장</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="19" ColAddr="25" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2708" Protect="false" RowAddr="18" RowSpan="1" Width="8755">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="30" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>귀하</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="20" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1076" Protect="false" RowAddr="19" RowSpan="1" Width="47744">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="30" Style="0">
                                        <TEXT CharShape="25" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="16" ColAddr="0" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2399" Protect="false" RowAddr="20" RowSpan="1" Width="5565">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="19" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR>신청인</CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="19" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR>제출서류</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="17" ColAddr="3" ColSpan="23" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2399" Protect="false" RowAddr="20" RowSpan="1" Width="37070">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="20" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR> 장기요양인정서 사본</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="18" ColAddr="26" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="5838" Protect="false" RowAddr="20" RowSpan="2" Width="5109">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="19" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR>수수료 </CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="19" Style="0">
                                        <TEXT CharShape="17" />
                                    </P>
                                    <P ParaShape="19" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR>없 음</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="10" ColAddr="0" ColSpan="3" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="3439" Protect="false" RowAddr="21" RowSpan="1" Width="5565">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="19" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR>담당 공무원</CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="19" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR>확인사항</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="14" ColAddr="3" ColSpan="23" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="3439" Protect="false" RowAddr="21" RowSpan="1" Width="37070">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="42" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR>1. 주민등록표 등ㆍ초본</CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="42" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR>2.「의료급여법」 제3조제1항제1호에 따른 의료급여를 받는 사람의 경우 의료급여수급자 증명서, </CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="42" Style="0">
                                        <TEXT CharShape="24">
                                            <CHAR> 「의료급여법」 제3조제1항제1호 외의 규정에 따른 의료급여를 받는 사람의 경우 의료보호증</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="28" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="676" Protect="false" RowAddr="22" RowSpan="1" Width="47744">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Bottom">
                                    <P ParaShape="24" Style="0">
                                        <TEXT CharShape="40" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="35" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2074" Protect="false" RowAddr="23" RowSpan="1" Width="45119">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="27" Style="0">
                                        <TEXT CharShape="30">
                                            <CHAR>행정정보 공동이용 동의서</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="37" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2930" Protect="false" RowAddr="24" RowSpan="1" Width="45119">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="39" Style="0">
                                        <TEXT CharShape="20">
                                            <CHAR> 본인은 이 건 업무처리와 관련하여 담당 공무원이 「전자정부법」 제36조제1항에 따른 행정정보의 공동이용 및 사회복지통합전산망을 통하여 위의 담당 공무원 확인사항을 확인하는 것에 동의합니다. *동의하지 아니하거나 확인이 되지 아니하는 경우에는 신청인이 직접 관련 서류를 제출하여야 합니다.</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="38" ColAddr="0" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1076" Protect="false" RowAddr="25" RowSpan="1" Width="5908">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="40" Style="0">
                                        <TEXT CharShape="23" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="38" ColAddr="4" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1076" Protect="false" RowAddr="25" RowSpan="1" Width="5911">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="40" Style="0">
                                        <TEXT CharShape="23" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="38" ColAddr="8" ColSpan="6" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1276" Protect="false" RowAddr="25" RowSpan="1" Width="13518">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>신청인 </CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="38" ColAddr="14" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1276" Protect="false" RowAddr="25" RowSpan="1" Width="6760">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="40" Style="0">
                                        <TEXT CharShape="23" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="38" ColAddr="18" ColSpan="6" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1276" Protect="false" RowAddr="25" RowSpan="1" Width="6760">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="40" Style="0">
                                        <TEXT CharShape="23" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="38" ColAddr="24" ColSpan="4" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1276" Protect="false" RowAddr="25" RowSpan="1" Width="6262">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Bottom">
                                    <P ParaShape="44" Style="0">
                                        <TEXT CharShape="39">
                                            <CHAR>(서명 또는 인)</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="36" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="561" Protect="false" RowAddr="26" RowSpan="1" Width="47744">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="40" Style="0">
                                        <TEXT CharShape="41" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="26" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2190" Protect="false" RowAddr="27" RowSpan="1" Width="44938">
                                <CELLMARGIN Bottom="141" Left="141" Right="141" Top="0" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="19" Style="0">
                                        <TEXT CharShape="29">
                                            <CHAR>처 리 절 차</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="15" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="376" Protect="false" RowAddr="28" RowSpan="1" Width="44938">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="27" Style="0">
                                        <TEXT CharShape="28" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="14" ColAddr="0" ColSpan="5" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2466" Protect="false" RowAddr="29" RowSpan="1" Width="8903">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="27" Style="0">
                                        <TEXT CharShape="10">
                                            <CHAR>신청서 작성</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="27" ColAddr="5" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2466" Protect="false" RowAddr="29" RowSpan="1" Width="2111">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="41" Style="0">
                                        <TEXT CharShape="19">
                                            <CHAR></CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="14" ColAddr="6" ColSpan="5" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2466" Protect="false" RowAddr="29" RowSpan="1" Width="10318">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="8" Style="0">
                                        <TEXT CharShape="10">
                                            <CHAR>접수 및 확인</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="27" ColAddr="11" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2466" Protect="false" RowAddr="29" RowSpan="1" Width="1828">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="41" Style="0">
                                        <TEXT CharShape="19">
                                            <CHAR></CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="14" ColAddr="13" ColSpan="7" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2466" Protect="false" RowAddr="29" RowSpan="1" Width="10601">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="21" Style="0">
                                        <TEXT CharShape="42">
                                            <CHAR>장기요양기관에</CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="21" Style="0">
                                        <TEXT CharShape="10">
                                            <CHAR>의뢰서 송부</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="27" ColAddr="20" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2466" Protect="false" RowAddr="29" RowSpan="1" Width="2111">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="45" Style="0">
                                        <TEXT CharShape="19">
                                            <CHAR></CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="14" ColAddr="21" ColSpan="7" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2466" Protect="false" RowAddr="29" RowSpan="1" Width="9066">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="21" Style="0">
                                        <TEXT CharShape="10">
                                            <CHAR>통지</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="32" ColAddr="0" ColSpan="5" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1962" Protect="false" RowAddr="30" RowSpan="1" Width="8903">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="19" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR>신청인</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="33" ColAddr="5" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1962" Protect="false" RowAddr="30" RowSpan="1" Width="2111">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="45" Style="0">
                                        <TEXT CharShape="20" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="32" ColAddr="6" ColSpan="5" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1962" Protect="false" RowAddr="30" RowSpan="1" Width="10318">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="23" Style="0">
                                        <TEXT CharShape="35">
                                            <CHAR>처 리 기 관</CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="23" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR>(특별자치시·도, </CHAR>
                                        </TEXT>
                                        <TEXT CharShape="35">
                                            <CHAR>시·군·구)</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="33" ColAddr="11" ColSpan="2" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1962" Protect="false" RowAddr="30" RowSpan="1" Width="1828">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="48" Style="0">
                                        <TEXT CharShape="35" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="32" ColAddr="13" ColSpan="7" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1962" Protect="false" RowAddr="30" RowSpan="1" Width="10601">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="23" Style="0">
                                        <TEXT CharShape="35">
                                            <CHAR>처 리 기 관</CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="23" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR>(특별자치시·도, </CHAR>
                                        </TEXT>
                                        <TEXT CharShape="35">
                                            <CHAR>시·군·구)</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="43" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="33" ColAddr="20" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1962" Protect="false" RowAddr="30" RowSpan="1" Width="2111">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="48" Style="0">
                                        <TEXT CharShape="18" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="32" ColAddr="21" ColSpan="7" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1962" Protect="false" RowAddr="30" RowSpan="1" Width="9066">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="27" Style="0">
                                        <TEXT CharShape="10">
                                            <CHAR>신청인</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="34" ColAddr="0" ColSpan="28" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2214" Protect="false" RowAddr="31" RowSpan="1" Width="47744">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Bottom">
                                    <P ParaShape="24" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR>210mm×297mm[백상지(80g/㎡) 또는 중질지(80g/㎡)]</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                    </TABLE>
                    <CHAR/>
                </TEXT>
            </P>
        </SECTION>
    </BODY>
    <TAIL>
        <SCRIPTCODE Type="JScript" Version="1.0">
            <SCRIPTHEADER></SCRIPTHEADER>
            <SCRIPTSOURCE></SCRIPTSOURCE>
        </SCRIPTCODE>
    </TAIL>
</HWPML>';

/*	
echo '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<HWPML Style="embed" SubVersion="8.0.0.0" Version="2.8">

    <HEAD SecCnt="1">
        <DOCSUMMARY>
            <TITLE>初․中等敎育法中改正法律案(代案)</TITLE>
            <DATE>2002년 7월 26일 금요일, 16시 25분</DATE>
        </DOCSUMMARY>
        <DOCSETTING>
            <BEGINNUMBER Endnote="1" Equation="1" Footnote="1" Page="1" Picture="1" Table="1" />
            <CARETPOS List="0" Para="0" Pos="32" />
        </DOCSETTING>
        <MAPPINGTABLE>
            <BINDATALIST Count="1">
                <BINITEM BinData="1" Format="jpg" Type="Embedding" />
            </BINDATALIST>
            <FACENAMELIST>
                <FONTFACE Count="6" Lang="Hangul">
                    <FONT Id="0" Name="돋움" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="1" Name="돋움체" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="9" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="2" Name="맑은 고딕" Type="ttf"><TYPEINFO ArmStyle="0" Contrast="2" FamilyType="2" Letterform="2" Midline="0" Proportion="3" StrokeVariation="0" Weight="5" XHeight="4"/></FONT>
                    <FONT Id="3" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="4" Name="한양중고딕" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="2" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="5" Name="한양견고딕" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="2" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                </FONTFACE>
                <FONTFACE Count="6" Lang="Latin">
                    <FONT Id="0" Name="돋움" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="1" Name="돋움체" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="9" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="2" Name="맑은 고딕" Type="ttf"><TYPEINFO ArmStyle="0" Contrast="2" FamilyType="2" Letterform="2" Midline="0" Proportion="3" StrokeVariation="0" Weight="5" XHeight="4"/></FONT>
                    <FONT Id="3" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="4" Name="한양중고딕" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="2" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="5" Name="한양견고딕" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="2" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                </FONTFACE>
                <FONTFACE Count="5" Lang="Hanja">
                    <FONT Id="0" Name="돋움" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="1" Name="돋움체" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="9" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="2" Name="한컴바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="3" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="4" Name="한양중고딕" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="2" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                </FONTFACE>
                <FONTFACE Count="5" Lang="Japanese">
                    <FONT Id="0" Name="돋움" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="1" Name="돋움체" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="9" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="2" Name="한컴바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="3" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="4" Name="한양중고딕" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="2" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                </FONTFACE>
                <FONTFACE Count="4" Lang="Other">
                    <FONT Id="0" Name="돋움" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="1" Name="돋움체" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="9" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="2" Name="한컴바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="3" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                </FONTFACE>
                <FONTFACE Count="5" Lang="Symbol">
                    <FONT Id="0" Name="돋움" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="1" Name="돋움체" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="9" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="2" Name="한컴바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="3" Name="한양신명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                    <FONT Id="4" Name="한양중고딕" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="2" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                </FONTFACE>
                <FONTFACE Count="4" Lang="User">
                    <FONT Id="0" Name="돋움" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="1" Name="돋움체" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="9" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="2" Name="한컴바탕" Type="ttf"><TYPEINFO ArmStyle="1" Contrast="0" FamilyType="2" Letterform="1" Midline="1" Proportion="0" StrokeVariation="1" Weight="6" XHeight="1"/></FONT>
                    <FONT Id="3" Name="명조" Type="hft"><TYPEINFO ArmStyle="0" Contrast="0" FamilyType="1" Letterform="0" Midline="0" Proportion="0" StrokeVariation="0" Weight="0" XHeight="0"/></FONT>
                </FONTFACE>
            </FACENAMELIST>
            <BORDERFILLLIST Count="42">
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="1" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Type="Solid" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="2" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Type="Solid" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="3" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.1mm" />
                    <RIGHTBORDER Type="None" Width="0.1mm" />
                    <TOPBORDER Type="None" Width="0.1mm" />
                    <BOTTOMBORDER Type="None" Width="0.1mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                    <FILLBRUSH>
                        <WINDOWBRUSH Alpha="0" FaceColor="4294967295" HatchColor="4278190080" />
                    </FILLBRUSH>
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="4" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.4mm" />
                    <RIGHTBORDER Type="None" Width="0.4mm" />
                    <TOPBORDER Type="None" Width="0.4mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.4mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="5" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.5mm" />
                    <RIGHTBORDER Type="None" Width="0.5mm" />
                    <TOPBORDER Type="None" Width="0.12mm" />
                    <BOTTOMBORDER Type="None" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="6" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.4mm" />
                    <RIGHTBORDER Type="None" Width="0.4mm" />
                    <TOPBORDER Type="None" Width="0.4mm" />
                    <BOTTOMBORDER Type="None" Width="0.4mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="7" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="8" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="9" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="26367" Type="Solid" Width="0.7mm" />
                    <TOPBORDER Type="None" Width="0.12mm" />
                    <BOTTOMBORDER Type="None" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="10" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="26367" Type="Solid" Width="0.7mm" />
                    <RIGHTBORDER Color="26367" Type="Solid" Width="0.7mm" />
                    <TOPBORDER Color="26367" Type="Solid" Width="0.7mm" />
                    <BOTTOMBORDER Color="26367" Type="Solid" Width="0.7mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="11" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.1mm" />
                    <RIGHTBORDER Type="None" Width="0.1mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="None" Width="0.4mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="12" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.5mm" />
                    <RIGHTBORDER Type="None" Width="0.5mm" />
                    <TOPBORDER Type="None" Width="0.12mm" />
                    <BOTTOMBORDER Color="6118749" Type="Solid" Width="0.7mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="13" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.5mm" />
                    <RIGHTBORDER Type="None" Width="0.5mm" />
                    <TOPBORDER Color="9671571" Type="None" Width="0.7mm" />
                    <BOTTOMBORDER Type="None" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="14" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.12mm" />
                    <RIGHTBORDER Type="None" Width="0.12mm" />
                    <TOPBORDER Type="None" Width="0.12mm" />
                    <BOTTOMBORDER Type="None" Width="0.4mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="15" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="4278190080" Type="None" Width="0.1mm" />
                    <RIGHTBORDER Color="4278190080" Type="None" Width="0.1mm" />
                    <TOPBORDER Color="4278190080" Type="None" Width="0.1mm" />
                    <BOTTOMBORDER Color="4278190080" Type="None" Width="0.1mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                    <FILLBRUSH>
                        <WINDOWBRUSH Alpha="0" FaceColor="4294967295" HatchColor="4278190080" />
                    </FILLBRUSH>
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="16" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.5mm" />
                    <RIGHTBORDER Type="None" Width="0.5mm" />
                    <TOPBORDER Color="6710886" Type="None" Width="0.7mm" />
                    <BOTTOMBORDER Color="6710886" Type="None" Width="0.7mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="17" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="6710886" Type="None" Width="0.7mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="18" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="19" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="20" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="21" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="22" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="7895160" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="7895160" Type="None" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.4mm" />
                    <BOTTOMBORDER Color="7895160" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                    <FILLBRUSH>
                        <WINDOWBRUSH Alpha="0" FaceColor="12303291" HatchColor="0" />
                    </FILLBRUSH>
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="23" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="7895160" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="7895160" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="7895160" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.1mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="24" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.5mm" />
                    <RIGHTBORDER Type="None" Width="0.5mm" />
                    <TOPBORDER Color="6118749" Type="Solid" Width="0.7mm" />
                    <BOTTOMBORDER Color="6710886" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="25" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Type="None" Width="0.5mm" />
                    <RIGHTBORDER Type="None" Width="0.5mm" />
                    <TOPBORDER Color="6710886" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="6710886" Type="None" Width="0.7mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="26" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="27" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="28" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="29" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="30" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="9671571" Type="None" Width="0.7mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="31" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="6118749" Type="None" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="32" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="6118749" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="33" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="6118749" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="6118749" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="34" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="6118749" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="35" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <RIGHTBORDER Color="6118749" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="36" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="6118749" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="6118749" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="37" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="6118749" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="38" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="39" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="40" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="9671571" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="41" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="6118749" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="6118749" Type="None" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
                <BORDERFILL BackSlash="0" BreakCellSeparateLine="0" CenterLine="0" CounterBackSlash="0" CounterSlash="0" CrookedSlash="0" Id="42" Shadow="false" Slash="0" ThreeD="false">
                    <LEFTBORDER Color="6118749" Type="None" Width="0.12mm" />
                    <RIGHTBORDER Color="9671571" Type="None" Width="0.12mm" />
                    <TOPBORDER Type="Solid" Width="0.12mm" />
                    <BOTTOMBORDER Color="6118749" Type="Solid" Width="0.12mm" />
                    <DIAGONAL Type="Solid" Width="0.12mm" />
                </BORDERFILL>
            </BORDERFILLLIST>
            <CHARSHAPELIST Count="35">
                <CHARSHAPE BorderFillId="0" Height="1000" Id="0" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="3" Hanja="3" Japanese="3" Latin="3" Other="3" Symbol="3" User="3" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="900" Id="1" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="3" Hanja="3" Japanese="3" Latin="3" Other="3" Symbol="3" User="3" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1000" Id="2" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="3" Hanja="3" Japanese="3" Latin="3" Other="3" Symbol="3" User="3" />
                    <RATIO Hangul="95" Hanja="100" Japanese="100" Latin="98" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-5" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1000" Id="3" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="5" Hanja="4" Japanese="4" Latin="5" Other="3" Symbol="4" User="3" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="900" Id="4" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="4" Hanja="4" Japanese="4" Latin="4" Other="3" Symbol="4" User="3" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="948" Id="5" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="3" Hanja="3" Japanese="3" Latin="3" Other="3" Symbol="3" User="3" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1500" Id="6" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="3" Hanja="3" Japanese="3" Latin="3" Other="3" Symbol="3" User="3" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1400" Id="7" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="3" Hanja="3" Japanese="3" Latin="3" Other="3" Symbol="3" User="3" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1100" Id="8" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="2" Hanja="2" Japanese="2" Latin="2" Other="2" Symbol="2" User="2" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1600" Id="9" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="4" Hanja="4" Japanese="4" Latin="4" Other="3" Symbol="4" User="3" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <BOLD/>
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="100" Id="10" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="3" Hanja="3" Japanese="3" Latin="3" Other="3" Symbol="3" User="3" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="800" Id="11" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-12" Hanja="-12" Japanese="-12" Latin="-12" Other="-12" Symbol="-12" User="-12" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="800" Id="12" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1000" Id="13" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="900" Id="14" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1300" Id="15" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <BOLD/>
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="900" Id="16" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="800" Id="17" ShadeColor="16777215" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="800" Id="18" ShadeColor="16777215" SymMark="0" TextColor="16711680" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1600" Id="19" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="5" Hanja="4" Japanese="4" Latin="5" Other="3" Symbol="4" User="3" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="-10" Hanja="-10" Japanese="-10" Latin="-10" Other="-10" Symbol="-10" User="-10" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1100" Id="20" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1000" Id="21" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="200" Id="22" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="900" Id="23" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <BOLD/>
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1000" Id="24" ShadeColor="4294967295" SymMark="0" TextColor="26367" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RATIO Hangul="97" Hanja="97" Japanese="97" Latin="97" Other="97" Symbol="97" User="97" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <BOLD/>
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1300" Id="25" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="3" Hanja="3" Japanese="3" Latin="3" Other="3" Symbol="3" User="3" />
                    <RATIO Hangul="95" Hanja="95" Japanese="95" Latin="95" Other="95" Symbol="95" User="95" />
                    <CHARSPACING Hangul="-5" Hanja="-5" Japanese="-5" Latin="-5" Other="-5" Symbol="-5" User="-5" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <BOLD/>
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1000" Id="26" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <UNDERLINE Color="0" Shape="Solid" Type="Bottom" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1300" Id="27" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <BOLD/>
                    <UNDERLINE Color="0" Shape="Solid" Type="Bottom" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1000" Id="28" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1600" Id="29" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="1" Hanja="1" Japanese="1" Latin="1" Other="1" Symbol="1" User="1" />
                    <RATIO Hangul="97" Hanja="97" Japanese="97" Latin="97" Other="97" Symbol="97" User="97" />
                    <CHARSPACING Hangul="-17" Hanja="-17" Japanese="-17" Latin="-17" Other="-17" Symbol="-17" User="-17" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <BOLD/>
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="0" Height="1000" Id="30" ShadeColor="4294967295" SymMark="0" TextColor="255" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="3" Height="1000" Id="31" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="3" Hanja="3" Japanese="3" Latin="3" Other="3" Symbol="3" User="3" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="3" Height="1200" Id="32" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="3" Height="1100" Id="33" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
                <CHARSHAPE BorderFillId="3" Height="1000" Id="34" ShadeColor="4294967295" SymMark="0" TextColor="0" UseFontSpace="false" UseKerning="false">
                    <FONTID Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RATIO Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHARSPACING Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                    <RELSIZE Hangul="100" Hanja="100" Japanese="100" Latin="100" Other="100" Symbol="100" User="100" />
                    <CHAROFFSET Hangul="0" Hanja="0" Japanese="0" Latin="0" Other="0" Symbol="0" User="0" />
                </CHARSHAPE>
            </CHARSHAPELIST>
            <TABDEFLIST Count="3">
                <TABDEF AutoTabLeft="false" AutoTabRight="false" Id="0" />
                <TABDEF AutoTabLeft="false" AutoTabRight="false" Id="1">
                    <TABITEM Leader="None" Pos="3216" Type="Left" />
                    <TABITEM Leader="Dot" Pos="37296" Type="Left" />
                </TABDEF>
                <TABDEF AutoTabLeft="false" AutoTabRight="false" Id="2">
                    <TABITEM Leader="None" Pos="8064" Type="Left" />
                    <TABITEM Leader="None" Pos="16128" Type="Left" />
                    <TABITEM Leader="None" Pos="24192" Type="Left" />
                    <TABITEM Leader="None" Pos="32256" Type="Left" />
                    <TABITEM Leader="None" Pos="40320" Type="Left" />
                    <TABITEM Leader="None" Pos="48384" Type="Left" />
                    <TABITEM Leader="None" Pos="56448" Type="Left" />
                    <TABITEM Leader="None" Pos="64512" Type="Left" />
                    <TABITEM Leader="None" Pos="72576" Type="Left" />
                    <TABITEM Leader="None" Pos="80640" Type="Left" />
                    <TABITEM Leader="None" Pos="88704" Type="Left" />
                    <TABITEM Leader="None" Pos="104832" Type="Left" />
                    <TABITEM Leader="None" Pos="112896" Type="Left" />
                    <TABITEM Leader="None" Pos="120960" Type="Left" />
                    <TABITEM Leader="None" Pos="129024" Type="Left" />
                    <TABITEM Leader="None" Pos="137088" Type="Left" />
                    <TABITEM Leader="None" Pos="145152" Type="Left" />
                    <TABITEM Leader="None" Pos="153216" Type="Left" />
                    <TABITEM Leader="None" Pos="161280" Type="Left" />
                    <TABITEM Leader="None" Pos="169344" Type="Left" />
                    <TABITEM Leader="None" Pos="177408" Type="Left" />
                    <TABITEM Leader="None" Pos="185472" Type="Left" />
                    <TABITEM Leader="None" Pos="193536" Type="Left" />
                    <TABITEM Leader="None" Pos="201600" Type="Left" />
                    <TABITEM Leader="None" Pos="209664" Type="Left" />
                    <TABITEM Leader="None" Pos="217728" Type="Left" />
                    <TABITEM Leader="None" Pos="225792" Type="Left" />
                    <TABITEM Leader="None" Pos="233856" Type="Left" />
                    <TABITEM Leader="None" Pos="241920" Type="Left" />
                    <TABITEM Leader="None" Pos="249984" Type="Left" />
                    <TABITEM Leader="None" Pos="258048" Type="Left" />
                </TABDEF>
            </TABDEFLIST>
            <PARASHAPELIST Count="36">
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="0" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="1" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="2" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-1488" Left="12000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="3" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-1488" Left="4000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="4" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="20" FontLineHeight="false" HeadingType="None" Id="5" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="3504" LineSpacing="165" LineSpacingType="Percent" Next="848" Prev="848" Right="3504" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="6" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-1488" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="7" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-1488" Left="2000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="8" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-1488" Left="6000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="9" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-1488" Left="8000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="10" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-1488" Left="10000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="11" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-2640" Left="0" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="12" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="1" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Right" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="13" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="2" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="150" LineSpacingType="Percent" Next="0" Prev="0" Right="2000" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Left" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="14" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="100" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="15" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="400" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="16" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="17" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-2800" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="18" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="155" LineSpacingType="Percent" Next="0" Prev="2832" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="19" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="-2992" Left="0" LineSpacing="230" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="BreakWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="20" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="true" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="180" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="21" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="100" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Right" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="22" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="3" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="23" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="90" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="3" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="24" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Right" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="25" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="1500" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="1500" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="26" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="27" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="1000" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="true" HeadingType="None" Id="28" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="15" Connect="true" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Left" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="true" HeadingType="None" Id="29" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="15" Connect="true" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="true" HeadingType="None" Id="30" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="120" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="15" Connect="true" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Center" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="false" HeadingType="None" Id="31" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="1500" LineSpacing="130" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="true" HeadingType="None" Id="32" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="600" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="600" />
                    <PARABORDER BorderFill="15" Connect="true" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="false" HeadingType="None" Id="33" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="3" Connect="false" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Justify" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="true" Condense="0" FontLineHeight="true" HeadingType="None" Id="34" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="15" Connect="true" IgnoreMargin="false" />
                </PARASHAPE>
                <PARASHAPE Align="Right" AutoSpaceEAsianEng="false" AutoSpaceEAsianNum="false" BreakLatinWord="KeepWord" BreakNonLatinWord="false" Condense="0" FontLineHeight="true" HeadingType="None" Id="35" KeepLines="false" KeepWithNext="false" Level="0" LineWrap="Break" PageBreakBefore="false" SnapToGrid="false" TabDef="0" VerAlign="Baseline" WidowOrphan="false">
                    <PARAMARGIN Indent="0" Left="0" LineSpacing="160" LineSpacingType="Percent" Next="0" Prev="0" Right="0" />
                    <PARABORDER BorderFill="15" Connect="true" IgnoreMargin="false" />
                </PARASHAPE>
            </PARASHAPELIST>
            <STYLELIST Count="24">
                <STYLE CharShape="31" EngName="Normal" Id="0" LangId="1042" LockForm="0" Name="바탕글" NextStyle="0" ParaShape="33" Type="Para" />
                <STYLE CharShape="2" Id="1" LangId="1042" LockForm="0" Name="본문" NextStyle="1" ParaShape="5" Type="Para" />
                <STYLE CharShape="0" Id="2" LangId="1042" LockForm="0" Name="개요 1" NextStyle="2" ParaShape="6" Type="Para" />
                <STYLE CharShape="0" Id="3" LangId="1042" LockForm="0" Name="개요 2" NextStyle="3" ParaShape="7" Type="Para" />
                <STYLE CharShape="0" Id="4" LangId="1042" LockForm="0" Name="개요 3" NextStyle="4" ParaShape="3" Type="Para" />
                <STYLE CharShape="0" Id="5" LangId="1042" LockForm="0" Name="개요 4" NextStyle="5" ParaShape="8" Type="Para" />
                <STYLE CharShape="0" Id="6" LangId="1042" LockForm="0" Name="개요 5" NextStyle="6" ParaShape="9" Type="Para" />
                <STYLE CharShape="0" Id="7" LangId="1042" LockForm="0" Name="개요 6" NextStyle="7" ParaShape="10" Type="Para" />
                <STYLE CharShape="0" Id="8" LangId="1042" LockForm="0" Name="개요 7" NextStyle="8" ParaShape="2" Type="Para" />
                <STYLE CharShape="3" EngName="Page Number" Id="9" LangId="1042" LockForm="0" Name="쪽 번호" NextStyle="9" ParaShape="1" Type="Para" />
                <STYLE CharShape="4" Id="10" LangId="1042" LockForm="0" Name="머리말" NextStyle="10" ParaShape="13" Type="Para" />
                <STYLE CharShape="5" Id="11" LangId="1042" LockForm="0" Name="각주" NextStyle="11" ParaShape="11" Type="Para" />
                <STYLE CharShape="4" Id="12" LangId="1042" LockForm="0" Name="그림캡션" NextStyle="12" ParaShape="1" Type="Para" />
                <STYLE CharShape="4" Id="13" LangId="1042" LockForm="0" Name="표캡션" NextStyle="13" ParaShape="1" Type="Para" />
                <STYLE CharShape="4" Id="14" LangId="1042" LockForm="0" Name="수식캡션" NextStyle="14" ParaShape="1" Type="Para" />
                <STYLE CharShape="1" Id="15" LangId="1042" LockForm="0" Name="찾아보기" NextStyle="15" ParaShape="12" Type="Para" />
                <STYLE CharShape="4" Id="16" LangId="1042" LockForm="0" Name="머리말(중고딕9)" NextStyle="16" ParaShape="1" Type="Para" />
                <STYLE CharShape="9" Id="17" LangId="1042" LockForm="0" Name="네모" NextStyle="17" ParaShape="18" Type="Para" />
                <STYLE CharShape="6" Id="18" LangId="1042" LockForm="0" Name="상장안본문" NextStyle="18" ParaShape="19" Type="Para" />
                <STYLE CharShape="7" EngName="pyun" Id="19" LangId="1042" LockForm="0" Name="편장절관" NextStyle="19" ParaShape="16" Type="Para" />
                <STYLE CharShape="7" EngName="Jo" Id="20" LangId="1042" LockForm="0" Name="조" NextStyle="20" ParaShape="17" Type="Para" />
                <STYLE CharShape="7" Id="21" LangId="1042" LockForm="0" Name="대비표GEN" NextStyle="21" ParaShape="20" Type="Para" />
                <STYLE CharShape="7" EngName="Hang" Id="22" LangId="1042" LockForm="0" Name="항" NextStyle="22" ParaShape="17" Type="Para" />
                <STYLE CharShape="8" EngName="xl69" Id="23" LangId="1042" LockForm="0" Name="xl69" NextStyle="23" ParaShape="0" Type="Para" />
            </STYLELIST>
        </MAPPINGTABLE>
        <COMPATIBLEDOCUMENT TargetProgram="None">
            <LAYOUTCOMPATIBILITY AdjustBaselineInFixedLinespacing="false" AdjustLineheightToFont="false" AdjustParaBorderOffsetWithBorder="false" AdjustParaBorderfillToSpacing="false" ApplyAtLeastToPercent100Pct="false" ApplyCharSpacingToCharGrid="false" ApplyExtendHeaderFooterEachSection="false" ApplyFontWeightToBold="false" ApplyFontspaceToLatin="false" ApplyNextspacingOfLastPara="false" ApplyParaBorderToOutside="false" ApplyPrevspacingBeneathObject="false" BaseCharUnitOfIndentOnFirstChar="false" BaseCharUnitOnEAsian="false" BaseLinespacingOnLinegrid="false" ConnectParaBorderfillOfEqualBorder="false" DoNotAdjustEmptyAnchorLine="false" DoNotAdjustWordInJustify="false" DoNotAlignWhitespaceOnRight="false" DoNotApplyAutoSpaceEAsianEng="false" DoNotApplyAutoSpaceEAsianNum="false" DoNotApplyExtensionCharCompose="false" DoNotApplyGridInHeaderFooter="false" DoNotApplyImageEffect="false" DoNotApplyShapeComment="false" DoNotApplyStrikeoutWithUnderline="false" DoNotApplyVertOffsetOfForward="false" DoNotFormattingAtBeneathAnchor="false" DoNotHoldAnchorOfTable="false" ExtendLineheightToOffset="false" ExtendLineheightToParaBorderOffset="false" ExtendVertLimitToPageMargins="false" FixedUnderlineWidth="false" OverlapBothAllowOverlap="false" TreatQuotationAsLatin="false" UseInnerUnderline="false" UseLowercaseStrikeout="false" />
        </COMPATIBLEDOCUMENT>
    </HEAD>

    <BODY>
        <SECTION Id="0">
		<P ColumnBreak="false" PageBreak="false" ParaShape="24" Style="0">
                <TEXT CharShape="25">
                    <SECDEF CharGrid="0" FirstBorder="false" FirstFill="false" LineGrid="0" SpaceColumns="1134" TabStop="8000" TextDirection="0" TextVerticalWidthHead="0">
                        <STARTNUMBER Equation="0" Figure="0" Page="0" PageStartsOn="Both" Table="0" />
                        <HIDE Border="false" EmptyLine="false" Fill="false" Footer="false" Header="false" MasterPage="false" PageNumPos="false" />
                        <PAGEDEF GutterType="LeftOnly" Height="84188" Landscape="0" Width="59528">
                            <PAGEMARGIN Bottom="1417" Footer="4252" Gutter="0" Header="4252" Left="5669" Right="5669" Top="1417" />
                        </PAGEDEF>
                        <FOOTNOTESHAPE>
                            <AUTONUMFORMAT SuffixChar=")" Superscript="false" Type="Digit" />
                            <NOTELINE Length="5cm" Type="Solid" Width="0.12mm" />
                            <NOTESPACING AboveLine="852" BelowLine="568" BetweenNotes="284" />
                            <NOTENUMBERING NewNumber="1" Type="Continuous" />
                            <NOTEPLACEMENT BeneathText="false" Place="EachColumn" />
                        </FOOTNOTESHAPE>
                        <ENDNOTESHAPE>
                            <AUTONUMFORMAT SuffixChar=")" Superscript="false" Type="Digit" />
                            <NOTELINE Length="0" Type="None" Width="0.12mm" />
                            <NOTESPACING AboveLine="864" BelowLine="576" BetweenNotes="0" />
                            <NOTENUMBERING NewNumber="1" Type="Continuous" />
                            <NOTEPLACEMENT BeneathText="false" Place="EndOfDocument" />
                        </ENDNOTESHAPE>
                        <PAGEBORDERFILL FillArea="Paper" FooterInside="false" HeaderInside="false" TextBorder="true" Type="Both">
                            <PAGEOFFSET Bottom="1417" Left="1417" Right="1417" Top="1417" />
                        </PAGEBORDERFILL>
                        <PAGEBORDERFILL FillArea="Paper" FooterInside="false" HeaderInside="false" TextBorder="true" Type="Even">
                            <PAGEOFFSET Bottom="1417" Left="1417" Right="1417" Top="1417" />
                        </PAGEBORDERFILL>
                        <PAGEBORDERFILL FillArea="Paper" FooterInside="false" HeaderInside="false" TextBorder="true" Type="Odd">
                            <PAGEOFFSET Bottom="1417" Left="1417" Right="1417" Top="1417" />
                        </PAGEBORDERFILL>
                    </SECDEF>
                    <COLDEF Count="1" Layout="Left" SameGap="0" SameSize="true" Type="Newspaper" />
                    '.$picArea.'
                    <TABLE BorderFill="2" CellSpacing="0" ColCount="21" PageBreak="Cell" RepeatHeader="false" RowCount="29">
                        <SHAPEOBJECT InstId="1304996007" Lock="false" NumberingType="Table" ZOrder="2">
                            <SIZE Height="71868" HeightRelTo="Absolute" Protect="false" Width="47740" WidthRelTo="Absolute" />
                            <POSITION AffectLSpacing="false" AllowOverlap="false" FlowWithText="true" HoldAnchorAndSO="false" HorzAlign="Left" HorzOffset="0" HorzRelTo="Para" TreatAsChar="true" VertAlign="Top" VertOffset="0" VertRelTo="Para" />
                            <OUTSIDEMARGIN Bottom="140" Left="140" Right="140" Top="140" />
                        </SHAPEOBJECT>
                        <INSIDEMARGIN Bottom="140" Left="140" Right="140" Top="140" />
                        <ROW>
                            <CELL BorderFill="14" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1663" Protect="false" RowAddr="0" RowSpan="1" Width="47740">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="21" Style="0">
                                        <TEXT CharShape="17">
                                            <CHAR>■ 노인장기요양보험법 시행규칙 [별지 제10호서식]</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="18">
                                            <CHAR> &lt;개정 2013.6.10&gt;</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="6" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3077" Protect="false" RowAddr="1" RowSpan="1" Width="47740">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="27" Style="0">
                                        <TEXT CharShape="19">
                                            <CHAR>장기요양기관 입소ㆍ이용의뢰서</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="4" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="0" Protect="false" RowAddr="2" RowSpan="1" Width="47740">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="1" Style="0">
                                        <TEXT CharShape="11" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="22" ColAddr="0" ColSpan="2" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="1985" Protect="false" RowAddr="3" RowSpan="1" Width="8033">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="23" Style="0">
                                        <TEXT CharShape="16">
                                            <CHAR>발급번호</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="22" ColAddr="2" ColSpan="4" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="1985" Protect="false" RowAddr="3" RowSpan="1" Width="9697">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="1" Style="0">
                                        <TEXT CharShape="23" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="22" ColAddr="6" ColSpan="3" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="1985" Protect="false" RowAddr="3" RowSpan="1" Width="6369">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="23" Style="0">
                                        <TEXT CharShape="16">
                                            <CHAR>발급일</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="22" ColAddr="9" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="1985" Protect="false" RowAddr="3" RowSpan="1" Width="8987">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="23" Style="0">
                                        <TEXT CharShape="16" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="22" ColAddr="15" ColSpan="5" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="1985" Protect="false" RowAddr="3" RowSpan="1" Width="7079">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="1" Style="0">
                                        <TEXT CharShape="23" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="22" ColAddr="20" ColSpan="1" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="1985" Protect="false" RowAddr="3" RowSpan="1" Width="7374">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="1" Style="0">
                                        <TEXT CharShape="16" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="23" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="0" Protect="false" RowAddr="4" RowSpan="1" Width="47740">
                                <CELLMARGIN Bottom="141" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="14" Style="0">
                                        <TEXT CharShape="10" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="26" ColAddr="0" ColSpan="1" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="9845" Protect="false" RowAddr="5" RowSpan="4" Width="6336">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="28" Style="0">
                                        <TEXT CharShape="20">
                                            <CHAR>수급자</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="31" ColAddr="1" ColSpan="4" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2600" Protect="false" RowAddr="5" RowSpan="1" Width="7432">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>성명 </CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="41" ColAddr="5" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2600" Protect="false" RowAddr="5" RowSpan="1" Width="13675">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="33">
                                            <CHAR>'.$su['bohoName'].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="41" ColAddr="11" ColSpan="7" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2600" Protect="false" RowAddr="5" RowSpan="1" Width="8760">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="30">
                                            <CHAR>생년월일 </CHAR>
                                        </TEXT>
                                        <TEXT CharShape="21">
                                            <CHAR> </CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="42" ColAddr="18" ColSpan="3" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2600" Protect="false" RowAddr="5" RowSpan="1" Width="11537">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="33">
                                            <CHAR>'.$myF->issToBirthday($su['jumin'],'.').'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="32" ColAddr="1" ColSpan="4" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="6" RowSpan="1" Width="7432">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>장기요양등급</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="33" ColAddr="5" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="6" RowSpan="1" Width="13675">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="33">
                                            <CHAR>'.$su['level'].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="33" ColAddr="11" ColSpan="7" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="6" RowSpan="1" Width="8760">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>장기요양인정번호</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="34" ColAddr="18" ColSpan="3" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="6" RowSpan="1" Width="11537">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="33">
                                            <CHAR>'.$su['injungNo'].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="32" ColAddr="1" ColSpan="3" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="7" RowSpan="1" Width="4602">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>주소</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="34" ColAddr="4" ColSpan="17" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="7" RowSpan="1" Width="36802">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="33">
                                            <CHAR>'.$juso[0].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="35" ColAddr="1" ColSpan="3" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="8" RowSpan="1" Width="4602">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>전화번호 </CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="36" ColAddr="4" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="8" RowSpan="1" Width="16222">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>'.$suTel.'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="36" ColAddr="10" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="8" RowSpan="1" Width="6775">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR> (휴대전화)</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="21" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="37" ColAddr="16" ColSpan="5" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="8" RowSpan="1" Width="13805">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>'.$suHp.'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="27" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="0" Protect="false" RowAddr="9" RowSpan="1" Width="47740">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="14" Style="0">
                                        <TEXT CharShape="10" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="28" ColAddr="0" ColSpan="1" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="10" RowSpan="1" Width="6336">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="28" Style="0">
                                        <TEXT CharShape="20">
                                            <CHAR>보호자</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="29" ColAddr="1" ColSpan="3" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="10" RowSpan="1" Width="4602">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>성명</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="28" ColAddr="4" ColSpan="3" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="10" RowSpan="1" Width="8564">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="33">
                                            <CHAR>'.$su['bohoName'].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="29" ColAddr="7" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="10" RowSpan="1" Width="8790">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>신청인과의 관계</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="28" ColAddr="13" ColSpan="1" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="10" RowSpan="1" Width="3662">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="33">
                                            <CHAR>'.$su['gwange'].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="29" ColAddr="14" ColSpan="5" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="10" RowSpan="1" Width="4815">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>전화번호</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="27" ColAddr="19" ColSpan="2" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="10" RowSpan="1" Width="10971">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="33">
                                            <CHAR>'.$myF->phoneStyle($su['bohoPhone'],'.').'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="27" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="0" Protect="false" RowAddr="11" RowSpan="1" Width="47740">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="14" Style="0">
                                        <TEXT CharShape="10" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="28" ColAddr="0" ColSpan="1" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="12543" Protect="false" RowAddr="12" RowSpan="5" Width="6336">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="28" Style="0">
                                        <TEXT CharShape="20">
                                            <CHAR>대리인</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="21" ColAddr="1" ColSpan="3" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2317" Protect="false" RowAddr="12" RowSpan="1" Width="4602">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>성명</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="38" ColAddr="4" ColSpan="8" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2317" Protect="false" RowAddr="12" RowSpan="1" Width="16788">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="33">
                                            <CHAR>'.$su['bohoName'].'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="38" ColAddr="12" ColSpan="5" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2317" Protect="false" RowAddr="12" RowSpan="1" Width="7345">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>생년월일 </CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="38" ColAddr="17" ColSpan="4" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2317" Protect="false" RowAddr="12" RowSpan="1" Width="12669">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="33">
                                            <CHAR>'.$myF->issToBirthday($su['bohoJumin']).'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="8" ColAddr="1" ColSpan="2" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="5396" Protect="false" RowAddr="13" RowSpan="2" Width="3961">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="28" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>유형</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="7" ColAddr="3" ColSpan="18" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2981" Protect="false" RowAddr="13" RowSpan="1" Width="37443">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR> 1. 가족ㆍ친족ㆍ이해관계인</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="16">
                                            <CHAR>(신청인과의 관계: </CHAR>
                                        </TEXT>
                                        <TEXT CharShape="33">
                                            <CHAR> '.$su['gwange'].'</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="34">
                                            <CHAR> </CHAR>
                                        </TEXT>
                                        <TEXT CharShape="16">
                                            <CHAR> ) </CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="8" ColAddr="3" ColSpan="9" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="14" RowSpan="1" Width="17429">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR> 2. 사회복지전담공무원</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="7" ColAddr="12" ColSpan="9" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="14" RowSpan="1" Width="20014">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR> 3. 시장ㆍ군수ㆍ구청장이 지정한 자</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="7" ColAddr="1" ColSpan="3" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="15" RowSpan="1" Width="4602">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>주소</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="40" ColAddr="4" ColSpan="17" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="15" RowSpan="1" Width="36802">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="33">
                                            <CHAR>'.$su['bohoAddr'].'</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="33" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="19" ColAddr="1" ColSpan="3" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="16" RowSpan="1" Width="4602">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>전화번호 </CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="39" ColAddr="4" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="16" RowSpan="1" Width="16222">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="33">
                                            <CHAR></CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="39" ColAddr="10" ColSpan="5" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="16" RowSpan="1" Width="5926">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR> (휴대전화)</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="39" ColAddr="15" ColSpan="6" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2415" Protect="false" RowAddr="16" RowSpan="1" Width="14654">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="33">
                                            <CHAR>'.$myF->phoneStyle($su['bohoPhone'],'.').'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="27" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="0" Protect="false" RowAddr="17" RowSpan="1" Width="47740">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="14" Style="0">
                                        <TEXT CharShape="10" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="20" ColAddr="0" ColSpan="1" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3351" Protect="false" RowAddr="18" RowSpan="1" Width="6336">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="30" Style="0">
                                        <TEXT CharShape="20">
                                            <CHAR>비용 </CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="30" Style="0">
                                        <TEXT CharShape="20">
                                            <CHAR>부담주체</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="21" ColAddr="1" ColSpan="7" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3351" Protect="false" RowAddr="18" RowSpan="1" Width="15430">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="20" ColAddr="8" ColSpan="4" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3351" Protect="false" RowAddr="18" RowSpan="1" Width="5960">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Bottom">
                                    <P ParaShape="35" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR> 시ㆍ군ㆍ구</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="21" />
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="21" ColAddr="12" ColSpan="9" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="3351" Protect="false" RowAddr="18" RowSpan="1" Width="20014">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Top">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR>협약번호</CHAR>
                                        </TEXT>
                                        <TEXT CharShape="21" />
                                    </P>
                                    <P ParaShape="34" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR> </CHAR>
                                        </TEXT>
                                        <TEXT CharShape="32">
                                            <CHAR>  </CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="18" ColAddr="0" ColSpan="1" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2511" Protect="false" RowAddr="19" RowSpan="1" Width="6336">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="30" Style="0">
                                        <TEXT CharShape="20">
                                            <CHAR>급여개시</CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="30" Style="0">
                                        <TEXT CharShape="20">
                                            <CHAR>일자</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                            <CELL BorderFill="19" ColAddr="1" ColSpan="20" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="2511" Protect="false" RowAddr="19" RowSpan="1" Width="41404">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="33">
                                            <CHAR> </CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="30" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="4481" Protect="false" RowAddr="20" RowSpan="1" Width="47740">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="29" Style="0">
                                        <TEXT CharShape="21">
                                            <CHAR> 「노인장기요양보험법 시행규칙」 제13조에 따라 위 수급자의 귀 장기요양기관 입소ㆍ이용을 의뢰합니다. </CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="13" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1283" Protect="false" RowAddr="21" RowSpan="1" Width="47740">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="25" Style="0">
                                        <TEXT CharShape="14">
                                            <CHAR>'.date('Y').' 년 '.date('m').' 월 '.date('d').' 일</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="5" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="5738" Protect="false" RowAddr="22" RowSpan="1" Width="47740">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="31" Style="0">
                                        <TEXT CharShape="13">
                                            <TABLE BorderFill="1" CellSpacing="0" ColCount="2" PageBreak="Cell" RepeatHeader="true" RowCount="1">
                                                <SHAPEOBJECT InstId="1304996008" Lock="false" NumberingType="Table" ZOrder="0">
                                                    <SIZE Height="5178" HeightRelTo="Absolute" Protect="false" Width="21881" WidthRelTo="Absolute" />
                                                    <POSITION AffectLSpacing="false" AllowOverlap="false" FlowWithText="true" HoldAnchorAndSO="false" HorzAlign="Left" HorzOffset="0" HorzRelTo="Column" TreatAsChar="true" VertAlign="Top" VertOffset="0" VertRelTo="Para" />
                                                    <OUTSIDEMARGIN Bottom="140" Left="140" Right="140" Top="140" />
                                                </SHAPEOBJECT>
                                                <INSIDEMARGIN Bottom="141" Left="141" Right="141" Top="141" />
                                                <ROW>
                                                    <CELL BorderFill="9" ColAddr="0" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="5178" Protect="false" RowAddr="0" RowSpan="1" Width="16742">
                                                        <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                                            <P ParaShape="26" Style="0">
                                                                <TEXT CharShape="29">
                                                                    <CHAR>○○○ 시장ㆍ군수ㆍ구청장</CHAR>
                                                                </TEXT>
                                                            </P>
                                                        </PARALIST>
                                                    </CELL>
                                                    <CELL BorderFill="10" ColAddr="1" ColSpan="1" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="5178" Protect="false" RowAddr="0" RowSpan="1" Width="5139">
                                                        <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                                            <P ParaShape="4" Style="0">
                                                                <TEXT CharShape="24">
                                                                    <CHAR>직인</CHAR>
                                                                </TEXT>
                                                            </P>
                                                        </PARALIST>
                                                    </CELL>
                                                </ROW>
                                            </TABLE>
                                            <CHAR/>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="12" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2981" Protect="false" RowAddr="23" RowSpan="1" Width="47740">
								<PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
									<P ParaShape="32" Style="0">
										<TEXT CharShape="26"><CHAR> </CHAR></TEXT>
										<TEXT CharShape="27"><CHAR> '.$center['manager'].'   </CHAR></TEXT>
										<TEXT CharShape="15"><CHAR>장기요양기관장  </CHAR></TEXT>
										<TEXT CharShape="13"><CHAR>귀하</CHAR></TEXT>
									</P>
								</PARALIST>
							</CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="24" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1046" Protect="false" RowAddr="24" RowSpan="1" Width="47740">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="4" Style="0">
                                        <TEXT CharShape="22" />
                                    </P>
                                    <P ParaShape="4" Style="0">
                                        <TEXT CharShape="22" />
                                    </P>
                                    <P ParaShape="4" Style="0">
                                        <TEXT CharShape="22" />
                                    </P>
                                    <P ParaShape="4" Style="0">
                                        <TEXT CharShape="22" />
                                    </P>
                                    <P ParaShape="4" Style="0">
                                        <TEXT CharShape="22" />
                                    </P>
                                    <P ParaShape="4" Style="0">
                                        <TEXT CharShape="22" />
                                    </P>
                                    <P ParaShape="4" Style="0">
                                        <TEXT CharShape="22" />
                                    </P>
                                    <P ParaShape="4" Style="0">
                                        <TEXT CharShape="22" />
                                    </P>
                                    <P ParaShape="4" Style="0">
                                        <TEXT CharShape="22" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="25" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="1846" Protect="false" RowAddr="25" RowSpan="1" Width="47740">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="15" Style="0">
                                        <TEXT CharShape="13">
                                            <CHAR>※ 시ㆍ군ㆍ구 담당과: 담당자: 전화번호: </CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="16" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="763" Protect="false" RowAddr="26" RowSpan="1" Width="47740">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="4" Style="0">
                                        <TEXT CharShape="22" />
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="17" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="true" Header="false" Height="8314" Protect="false" RowAddr="27" RowSpan="1" Width="47740">
                                <CELLMARGIN Bottom="283" Left="283" Right="283" Top="283" />
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Center">
                                    <P ParaShape="15" Style="0">
                                        <TEXT CharShape="28">
                                            <CHAR> &lt;입소ㆍ이용의뢰 장기요양기관 현황&gt;</CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="15" Style="0">
                                        <TEXT CharShape="28">
                                            <CHAR> ○ 장기요양기관 명칭: '.$center['centerName'].'</CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="15" Style="0">
                                        <TEXT CharShape="28">
                                            <CHAR> ○ 장기요양기관 기호: '.$center['centerCode'].'</CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="15" Style="0">
                                        <TEXT CharShape="28">
                                            <CHAR> ○ 장기요양기관 주소: '.$center['address'].'</CHAR>
                                        </TEXT>
                                    </P>
                                    <P ParaShape="15" Style="0">
                                        <TEXT CharShape="28">
                                            <CHAR> ○ 장기요양기관 전화번호: '.$cTel.'</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                        <ROW>
                            <CELL BorderFill="11" ColAddr="0" ColSpan="21" Dirty="false" Editable="false" HasMargin="false" Header="false" Height="2512" Protect="false" RowAddr="28" RowSpan="1" Width="47740">
                                <PARALIST LineWrap="Break" LinkListID="0" LinkListIDNext="0" TextDirection="0" VertAlign="Bottom">
                                    <P ParaShape="22" Style="0">
                                        <TEXT CharShape="12">
                                            <CHAR>210mm×297mm[백상지 80g/㎡]</CHAR>
                                        </TEXT>
                                    </P>
                                </PARALIST>
                            </CELL>
                        </ROW>
                    </TABLE>
                    <CHAR/>
                </TEXT>
            </P>
        </SECTION>
    </BODY>
    <TAIL>
        <BINDATASTORAGE>
            <BINDATA Encoding="Base64" Id="1" Size="44164">'.$imgBase64.'</BINDATA>
        </BINDATASTORAGE>
        <SCRIPTCODE Type="JScript" Version="1.0">
            <SCRIPTHEADER></SCRIPTHEADER>
            <SCRIPTSOURCE></SCRIPTSOURCE>
        </SCRIPTCODE>
    </TAIL>
</HWPML>';
*/
$conn->row_free();

?>



