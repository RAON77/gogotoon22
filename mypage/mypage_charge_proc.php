<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

	$params['goods']      = chkReqRpl("goods", "", 10, "POST", "STR");
	$params['pay_method'] = chkReqRpl("pay_method", "", 10, "POST", "STR");

    if (chkBlank($params['goods'])) fnMsgJson(502, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "");
    if (chkBlank($params['pay_method'])) fnMsgJson(503, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "");

    $cls_member = new CLS_MEMBER;
    $cls_pay = new CLS_PAYMENT;

	//G캐시 상품 카테고리 불러오기
	$goods_list = getGcashChargeList();

	//결제수단 불러오기
	$paymethod_list = getPayMethodList(SITE_SAVE_LANG);

    //상품 카테고리 체크
    if ( array_search($params['goods'], array_column($goods_list, "code")) === false) fnMsgJson(504, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "");

    //결제수단 체크
    if ( array_search($params['pay_method'], array_column($paymethod_list, "code")) === false) fnMsgJson(505, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "");

    //사용자 정보 불러오기
    $user_view = $cls_member->user_view($MEM_USR['usr_idx']);
    if ($user_view == false) fnMsgJson(506, getTransLangMsg("사용자 데이터를 찾을 수 없습니다.\n고객센터로 문의해주세요."), "");


    //결제상품 정보 상세 불러오기
    $cls_pay->payment_goods_view($params['goods'], $gcash, $bonus_point, $pay_amount);

    $params['tid']           = 'DEV'.getCreateRandNum();
    $params['gcash']         = $gcash;
    $params['bonus_point']   = $bonus_point;
    $params['goods_code']    = $params['goods'];
    $params['goods_content'] = getGcashChargeName($params['goods'], 'name', SITE_SAVE_LANG);
    $params['pay_amount']    = $pay_amount;
    $params['pay_status']    = '20';

    $params['usr_idx'] = $MEM_USR['usr_idx'];
    $params['upt_ip']  = NOW_IP;
    $params['upt_id']  = $MEM_USR['usr_idx'];
    $params['reg_ip']  = NOW_IP;
    $params['reg_id']  = $MEM_USR['usr_idx'];

    //결제정보 저장
    if (!$cls_pay->payment_save($params, $error_msg)) fnMsgJson(507, iif($error_msg!='', $error_msg, getTransLangMsg("저장 처리중 문제가 발생했습니다.\n고객센터에 문의주세요.")), "");
?>
{"result": 200, "message": "OK"}