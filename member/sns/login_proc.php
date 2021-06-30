<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (isUser()) fnMsgJson(501, getTransLangMsg("이미 로그인 되어 있습니다."), "");

	$params['sns_gubun'] = chkReqRpl("sns_gubun", "", 50, "POST", "STR");
    $params['sns_name']  = getLoginSnsName($params['sns_gubun']);
	$params['email']     = chkReqRpl("email", "", 50, "POST", "STR");
    $params['sns_id']    = chkReqRpl("uid", "N", 50, "POST", "STR");

	if (!isStrpos("google,facebook,apple", $params['sns_gubun'])) getTransLangMsg(502, getTransLangMsg("잘못된 요청 정보 입니다."), "");
	if (chkBlank($params['email'])) fnMsgJson(503, getTransLangMsg($params['sns_name'] ." 이메일 공유를 승인해주셔야 합니다."), "");
	if (chkBlank($params['sns_id'])) fnMsgJson(504, getTransLangMsg($params['sns_name']. " ID로 연결된 정보가 없습니다."), "");

	$cls_member = new CLS_MEMBER;

    //SNS 아이디 체크
    if (!$cls_member->is_login_sns_check($params['sns_id'], $params['usr_idx'])) {
        //이메일 아이디 체크
        if (!$cls_member->is_login_check("", $params['email'], "", $params['usr_idx'])) {
            //회원가입 처리 후 메인 이동

            $params['usr_email']                  = $params['email'];   //이메일
            $params['usr_gubun']                  = '10';               //일빈회원
            $params['reg_gubun']                  = '20';               //SNS 가입
            $params['recv_notice_flag']           = 'Y';
            $params['recv_webtoon_flag']          = 'Y';
            $params['sns_'. $params['sns_gubun']] = $params['sns_id'];  //SNS 아이디
            $params['default_lang']               = SITE_SAVE_LANG;
            $params['reg_ip']                     = NOW_IP;
            $params['reg_id']                     = '';

            //회원가입 처리
            if (!$cls_member->user_save($params, $usr_idx)) fnMsgJson(505, getTransLangMsg("회원가입중 문제가 발생했습니다.\n고객센터로 문의해주세요."), "");

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
                    if (!$cls_member->user_point_save($params_point)) fnMsgJson(506, getTransLangMsg("포인트 적립 중 오류가 발생되었습니다."), "");
                }

                //로그인 로그 저장 및 마지막 로그인 접수일 업데이트
                if ($cls_member->last_visit_update($user_view['usr_idx']) == false) fnMsgJson(507, getTransLangMsg("회원가입중 문제가 발생했습니다.\n고객센터로 문의해주세요."), "");

                //세션저장
                setSession("user_view", $user_view);
            }

            //SNS 로그인은 자동로그인 처리
            setcookie("USR_SAVE_LOGIN", encryption($user_view['usr_idx']), time() + (86400 * 365), "/");

            //회원가입 완료
            fnMsgJson(200, "OK", "");
        }
    }

    //사용자 정보 불러오기
    $user_view = $cls_member->user_view($params['usr_idx']);
    if ($user_view == false) fnMsgJson(508, getTransLangMsg($params['sns_name']." 로그인에 실패하였습니다."), "");

    //SNS연결이 안되어있고 이메일 가입일경우(SNS 이메일 체크) 또는 SNS가입이 다를경우
    if (chkBlank($user_view['sns_gubun']) || $user_view['sns_gubun'] != $params['sns_gubun']) fnMsgJson(509, getTransLangMsg("이메일로 가입된 계정입니다.\n이메일 주소와 비밀번호를 입력해 주세요."), "");

    //상태 확인
    if ($user_view['status'] != 'Y') fnMsgJson(510, getTransLangMsg("이용이 제한된 사용자 입니다.\n고객센터에 문의 주세요."), "");

	//탈퇴회원 확인
	if (isStrpos("80,81", $user_view['usr_gubun'])) fnMsgJson(511, getTransLangMsg("탈퇴 처리된 사용자 입니다.\n고객센터에 문의 주세요."), "");

    //로그인 로그 저장 및 마지막 로그인 접수일 업데이트
    if ($cls_member->last_visit_update($user_view['usr_idx']) == false) fnMsgJson(512, getTransLangMsg("로그인에 문제가 발생했습니다.\n고객센터로 문의해주세요."), "");

    //SNS 로그인은 자동로그인 처리
    setcookie("USR_SAVE_LOGIN", encryption($user_view['usr_idx']), time() + (86400 * 365), "/");

    //기본 언어설정
    if (chkBlank($_COOKIE["SITE_SAVE_LANG"])) $_COOKIE["SITE_SAVE_LANG"] = $user_view['default_lang'];
    $_COOKIE["TRANS_SAVE_LANG"] = $user_view['default_lang'];

    //세션저장
    setSession("user_view", $user_view);
?>
{"result": 200, "message": "OK"}