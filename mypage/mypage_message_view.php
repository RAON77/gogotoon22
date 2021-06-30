<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	setSession("RETURN_URL", "/mypage/mypage_message.php");

	if (!chkReferer()) fnMsgGo(500, getTransLangMsg("잘못된 접근 입니다."), "/", "");
	if (!isUser()) fnMsgGo(501, "", "/member/login.php?return_flag=Y", "");

	$params['idx'] = chkReqRpl("idx", null, "", "GET", "INT");

    if (chkBlank($params['idx'])) fnMsgGo(502, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "");

    $cls_member = new CLS_MEMBER;


    //사용자 메세지함 상세보기 불러오기
    $view = $cls_member->message_view($MEM_USR['usr_idx'], $params['idx'], SITE_SAVE_LANG);
    if ($view == false) fnMsgGo(503, getTransLangMsg("일치하는 데이터가 없습니다."), "");


    //사용자 메시지함 읽음 처리
    if ($view['view_flag'] == 'N') {
        $params['usr_idx'] = $MEM_USR['usr_idx'];
        $params['upt_ip']  = NOW_IP;
        $params['upt_id']  = $MEM_USR['usr_idx'];
        if ($cls_member->message_view_check($params) == false) fnMsgGo(504, getTransLangMsg("일시적인 문제가 발생했습니다.\n고객센터에 문의주세요."), "");
    }


    if ($view['gubun'] == '10') {
        setcookie("SITE_SAVE_LANG", $view['lang'], time() + (86400 * 365), "/");

        fnMsgGo(200, "", "/customer/notice_view.php?idx=". $view['notice_idx'], "");
    } else if ($view['gubun'] == '20') {
        setcookie("SITE_SAVE_LANG", $view['lang'], time() + (86400 * 365), "/");

        fnMsgGo(200, "", "/customer/event_view.php?idx=". $view['event_idx'], "");
    } else if ($view['gubun'] == '30') {
        fnMsgGo(200, "", "/mypage/mypage_coupon_list.php", "");
    } else if ($view['gubun'] == '40') {
        fnMsgGo(200, "", "/view/view.php?wt=". $view['webtoon_idx'], "");
    }
?>