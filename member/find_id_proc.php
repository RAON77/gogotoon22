<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (isUser()) fnMsgJson(501, getTransLangMsg("이미 로그인 되어 있습니다."), "");

	$params['usr_email'] = chkReqRpl("email_id", "", 50, "POST", "STR");

	if (chkBlank($params['usr_email']) || !isDataCheck($params['usr_email'], "email")) fnMsgJson(502, getTransLangMsg("이메일을 정확하게 입력해주세요."), "");

	$cls_member = new CLS_MEMBER;
	$cls_jwt = new CLS_JWT;

    //이메일 아이디 체크
    if (!$cls_member->is_login_check("", $params['usr_email'], "", $usr_idx)) fnMsgJson(503, getTransLangMsg("일치하는 이메일 아이디가 없습니다.\n이메일 아이디를 다시 확인해주세요."), "");

    //사용자 정보 불러오기
    $user_view = $cls_member->user_view($usr_idx);
	if ($user_view == false) fnMsgJson(504, getTransLangMsg("일치하는 사용자 데이터가 없습니다."), "");

	//탈퇴회원 확인
	if (isStrpos("80,81", $user_view['usr_gubun'])) fnMsgJson(505, getTransLangMsg("탈퇴 처리된 사용자 입니다.\n고객센터에 문의 주세요."), "");


    //비밀번호 변경 안내 토큰 생성
    $token = $cls_jwt->hashing(array(
        "usr_email"=>$params['usr_email']
    ));

    //토큰 데이터 저장 처리
    if (!$cls_jwt->token_save($token, 'passwd_find')) fnMsgJson(506, getTransLangMsg("일시적인 문제가 발생했습니다.\n고객센터에 문의주세요."), "");

    //이메일 안내 발송
    $passwd_url = SITE_URL."/member/find_pw_change.php?token=".$token;
    $passwd_content = "
        <p style=\"margin:0;padding:0;padding-bottom:40px;line-height:1.5;font-size:14px;color:#888;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\">
            ". getTransLangMsg("이 메일은 고고툰 비밀번호 변경을 원활히 이용하기 위한 인증메일 입니다.") ."<br>
            <font style=\"color: #ef293a;\">". getTransLangMsg("비밀번호를 변경하시려면 아래 링크를 클릭해주세요.") ."</font><br>
            <font style=\"color: #ef293a;\">". getTransLangMsg("링크 주소는 1시간 뒤 만료됩니다.") ."</font>
        </p>
        <p style=\"margin:0;padding:30px;line-height:1.5;font-size:14px;border: 1px solid #e6e6e6;background-color: #fcfcfc;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\">
            <a href=\"". $passwd_url ."\" target=\"_blank\" style=\"display:block;word-break:break-all;color:#ef293a;\">". $passwd_url ."</a>
        </p>
    ";

    $subject = getTransLangMsg("고고툰 비밀번호 변경안내");
    $content = getEmailSendFile("/module/email/find.passwd.send.html");
    $content = str_replace('{{site_url}}', SITE_URL, $content);
    $content = str_replace('{{site_cdn_url}}', SITE_CDN_URL, $content);
    $content = str_replace('{{title}}', getTransLangMsg("고고툰 비밀번호 변경안내"), $content);
    $content = str_replace('{{content}}', $passwd_content, $content);
    $content = str_replace('{{copyright}}', getTransLangMsg("본 메일은 발신전용 메일입니다."), $content);

    sendEmail($subject, $params['usr_email'], "", SITE_EMAIL, getTransLangMsg(SITE_NAME), $content);
?>
{"result": 200, "message": "OK"}