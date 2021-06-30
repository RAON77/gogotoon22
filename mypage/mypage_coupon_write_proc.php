<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

	$params['coupon_num'] = chkReqRpl("coupon_num", "", 10, "POST", "STR");

    if (chkBlank($params['coupon_num'])) fnMsgJson(502, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "");

    $cls_member = new CLS_MEMBER;
    $cls_coupon = new CLS_SETTING_COUPON;


    //쿠폰 사용 저장
    $params['usr_idx'] = $MEM_USR['usr_idx'];
	$params['upt_ip']  = NOW_IP;
	$params['upt_id']  = $MEM_USR['usr_idx'];
	$params['reg_ip']  = NOW_IP;
	$params['reg_id']  = $MEM_USR['usr_idx'];
    if (!$cls_coupon->coupon_use_save($params, $error_msg)) fnMsgJson(503, iif($error_msg!='', $error_msg, getTransLangMsg("저장 처리중 문제가 발생했습니다.\n고객센터에 문의주세요.")), "");
?>
{"result": 200, "message": "OK"}