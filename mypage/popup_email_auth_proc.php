<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

	$params['token']    = chkReqRpl("token", "", "max", "POST", "STR");
	$params['auth_num'] = chkReqRpl("auth_num", "", 6, "POST", "STR");

    if (chkBlank($params['token'])) fnMsgJson(502, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "");
    if (chkBlank($params['auth_num']) || mb_strlen($params['auth_num'])<6) fnMsgJson(503, getTransLangMsg("인증번호 값이 유효하지 않습니다."), array("id"=>"auth_num"));

	$cls_member = new CLS_MEMBER;
    $cls_jwt = new CLS_JWT;

	$token_data = $cls_jwt->dehashing($params['token'], $error_msg);
	if ($token_data == false) fnMsgJson(504, $error_msg, array("id"=>"new_passwd"));

    $usr_idx   = $token_data['usr_idx'];
    $usr_email = $token_data['usr_email'];
    $auth_num  = $token_data['auth_num'];

    //인증번호 확인
    if ($auth_num != $params['auth_num']) fnMsgJson(505, getTransLangMsg("인증번호가 일치하지 않습니다.\n인증번호를 다시 확인해주세요."), "");

    //이메일 중복검사
    if ($cls_member->is_email_check($usr_email)) fnMsgJson(506, getTransLangMsg("이미 가입된 계정입니다. 이메일 주소를 다시 입력해 주세요."), "");

    //사용자 정보 불러오기
    $user_view = $cls_member->user_view($usr_idx);
    if ($user_view == false) fnMsgJson(507, getTransLangMsg("사용자 데이터를 찾을 수 없습니다.\n고객센터로 문의해주세요."), "");

    //이메일 변경 및 가입구분 변경 저장
    $params['usr_idx']       = $user_view['usr_idx'];
    $params['usr_email']     = $usr_email;
    $params['old_usr_email'] = $user_view['usr_email'];
    $params['reg_gubun']     = $user_view['reg_gubun'];
    $params['upt_ip']        = NOW_IP;
    $params['upt_id']        = $user_view['usr_idx'];
    if (!$cls_member->user_email_save($params)) fnMsgJson(508, getTransLangMsg("저장 처리중 문제가 발생했습니다.\n고객센터에 문의주세요."), "");
?>
{"result": 200, "message": "OK"}