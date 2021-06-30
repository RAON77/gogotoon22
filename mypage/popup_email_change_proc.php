<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

	$params['new_email']   = chkReqRpl("new_email", "", 50, "POST", "STR");
	$params['curr_passwd'] = chkReqRpl("curr_passwd", "", 20, "POST", "STR");


    if (chkBlank($params['new_email']) || !isDataCheck($params['new_email'], "email")) fnMsgJson(502, getTransLangMsg("이메일을 정확하게 입력해주세요."), array("id"=>"pop_new_email"));
    if (chkBlank($params['curr_passwd'])) fnMsgJson(503, getTransLangMsg("현재 비밀번호를 입력해주세요."), array("id"=>"pop_curr_passwd"));
    if (mb_strlen($params['curr_passwd'])<8 || mb_strlen($params['curr_passwd'])>20) fnMsgJson(504, getTransLangMsg("비밀번호는 8~20자 사이로 입력해주세요."), array("id"=>"pop_curr_passwd"));

	$cls_member = new CLS_MEMBER;
    $cls_jwt = new CLS_JWT;

    //이메일 중복검사
    if ($cls_member->is_email_check($params['new_email'])) fnMsgJson(505, getTransLangMsg("이미 가입된 계정입니다. 이메일 주소를 다시 입력해 주세요."), array("id"=>"pop_new_email"));

    //사용자 정보 불러오기
    $user_view = $cls_member->user_view($MEM_USR['usr_idx']);
    if ($user_view == false) fnMsgJson(506, getTransLangMsg("사용자 데이터를 찾을 수 없습니다.\n고객센터로 문의해주세요."), "");


    //현재 비밀번호 체크
    if ($user_view['usr_pwd'] != encryption($params['curr_passwd'])) fnMsgJson(507, getTransLangMsg("현재 비밀번호가 일치하지 않습니다."), "");


    //이메일 계정 변경 안내 토큰 생성
    $auth_num = returnAuthNum(6);
    $token = $cls_jwt->hashing(array(
        "usr_idx" => $MEM_USR['usr_idx'],
        "usr_email" => $params['new_email'],
        "auth_num"  => $auth_num
    ));


    //토큰 데이터 저장 처리
    if (!$cls_jwt->token_save($token, 'email_change')) fnMsgJson(508, getTransLangMsg("일시적인 문제가 발생했습니다.\n고객센터에 문의주세요."), "");

    //이메일 안내 발송
    $auth_content = "
        <p style=\"margin:0;padding:0;padding-bottom:40px;line-height:1.5;font-size:14px;color:#888;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\">
            ". getTransLangMsg("이 메일은 고고툰 서비스를 원활히 이용하기 위한 인증메일 입니다.") ."<br>
            <font style=\"color: #ef293a;\">". getTransLangMsg("아래 인증번호를 입력해주세요.") ."</font><br>
            <font style=\"color: #ef293a;\">". getTransLangMsg("인증번호는 1시간 뒤 만료됩니다.") ."</font>
        </p>
        <p style=\"margin:0;padding:30px; line-height:1.5;text-align:center;font-size:24px;letter-spacing:0.8em;border: 1px solid #e6e6e6;background-color: #fcfcfc;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\">
            <strong>$auth_num</strong>
        </p>
    ";

    $subject = getTransLangMsg("고고툰 이메일 인증");
    $content = getEmailSendFile("/module/email/email.change.auth.send.html");
    $content = str_replace('{{site_url}}', SITE_URL, $content);
    $content = str_replace('{{site_cdn_url}}', SITE_CDN_URL, $content);
    $content = str_replace('{{title}}', getTransLangMsg("고고툰 이메일 인증"), $content);
    $content = str_replace('{{content}}', $auth_content, $content);
    $content = str_replace('{{copyright}}', getTransLangMsg("본 메일은 발신전용 메일입니다."), $content);

    sendEmail($subject, $params['new_email'], "", SITE_EMAIL, getTransLangMsg(SITE_NAME), $content);
?>
{"result": 200, "message": "OK", "token": "<?=$token?>"}