<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (isUser()) fnMsgJson(501, getTransLangMsg("이미 로그인 되어 있습니다."), "");

	$params['usr_email'] = chkReqRpl("join_email", "", 50, "POST", "STR");
	$params['usr_pwd']   = chkReqRpl("join_passwd", "", 20, "POST", "STR");
	$params['nick_name'] = chkReqRpl("join_nick", "", 50, "POST", "STR");
	$params['gender']    = chkReqRpl("join_gender", "", 1, "POST", "STR");

	if (chkBlank($params['usr_email']) || !isDataCheck($params['usr_email'], "email")) fnMsgJson(502, getTransLangMsg("이메일을 정확하게 입력해주세요."), array("id"=>"join_email"));
	if (chkBlank($params['usr_pwd'])) fnMsgJson(503, getTransLangMsg("로그인에 사용하실 비밀번호를 입력해주세요."), array("id"=>"join_passwd"));
    if (mb_strlen($params['usr_pwd'])<8 || mb_strlen($params['usr_pwd'])>20) fnMsgJson(504, getTransLangMsg("비밀번호는 8~20자 사이로 입력해주세요."), array("id"=>"join_passwd"));
    if (chkBlank($params['nick_name'])) fnMsgJson(505, getTransLangMsg("닉네임을 정확하게 입력해주세요."), array("id"=>"join_nick"));
    if (mb_strlen($params['nick_name'])<2 || mb_strlen($params['nick_name'])>20) fnMsgJson(504, getTransLangMsg("닉네임은 2~20자 사이로 입력해주세요."), array("id"=>"join_passwd"));
    if (chkBlank($params['gender']) || !isStrpos("M,F",$params['gender'])) fnMsgJson(506, getTransLangMsg("성별을 정확하게 입력해주세요."), array("id"=>"join_gender_1"));

    //비밀번호 암호화
    $params['usr_pwd'] = encryption($params['usr_pwd']);

	$cls_member = new CLS_MEMBER;

    //이메일 아이디 체크
    if ($cls_member->is_login_check("", $params['usr_email'], "", $usr_idx)) {
        //사용자 정보 불러오기
        $user_view = $cls_member->user_view($usr_idx);
        if ($user_view == false) fnMsgJson(507, getTransLangMsg("이메일 확인중 문제가 발생했습니다.\n고객센터로 문의해주세요."), "");

        //탈퇴회원 확인
        if (isStrpos("80,81", $user_view['usr_gubun'])) fnMsgJson(508, getTransLangMsg("탈퇴 처리된 계정입니다. 이메일 주소를 다시 입력해 주세요."), "");
    }

    //이메일 중복검사
    if ($cls_member->is_email_check($params['usr_email'])) fnMsgJson(509, getTransLangMsg("이미 가입된 계정입니다. 이메일 주소를 다시 입력해 주세요."), array("id"=>"join_email"));

    //닉네임 중복검사
    if ($cls_member->is_nick_check($params['nick_name'])) fnMsgJson(510, getTransLangMsg("이미 가입된 닉네임 입니다. 닉네임을 다시 입력해 주세요."), array("id"=>"join_nick"));

    $params['usr_gubun']         = '10';    //일빈회원
    $params['reg_gubun']         = '10';    //이메일 가입
    $params['recv_notice_flag']  = 'Y';
    $params['recv_webtoon_flag'] = 'Y';
    $params['default_lang']      = SITE_SAVE_LANG;
    $params['reg_ip']            = NOW_IP;
    $params['reg_id']            = '';

    //회원가입 처리
    if (!$cls_member->user_save($params, $usr_idx)) fnMsgJson(511, getTransLangMsg("회원가입중 문제가 발생했습니다.\n고객센터로 문의해주세요."), "");

    //자동로그인 처리 (고유번호가 정상적으로 리턴 되었을경우 실행)
    if (!chkBlank($usr_idx)) {
        //사용자 정보 불러오기
		$user_view = $cls_member->user_view($usr_idx);

        //회원가입 포인트 적립
        if (POINT_MEMBER_JOIN > 0) {
            $params_point['usr_idx']    = $user_view['usr_idx'];
            $params_point['gubun']      = "P";
            $params_point['content']    = getTransLangMsg("회원가입 포인트 적립");
            $params_point['point']      = POINT_MEMBER_JOIN;
            $params_point['point_type'] = "30";
            $params_point['reg_ip']     = NOW_IP;
            $params_point['reg_id']     = $user_view['usr_idx'];
            if (!$cls_member->user_point_save($params_point)) fnMsgJson(512, getTransLangMsg("포인트 적립 중 오류가 발생되었습니다."), "");
        }

        //로그인 로그 저장 및 마지막 로그인 접수일 업데이트
        if ($cls_member->last_visit_update($user_view['usr_idx']) == false) fnMsgJson(513, getTransLangMsg("회원가입중 문제가 발생했습니다.\n고객센터로 문의해주세요."), "");

        //세션저장
        setSession("user_view", $user_view);
    }
?>
{"result": 200, "message": "OK"}