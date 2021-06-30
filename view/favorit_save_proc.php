<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

    $params['webtoon_idx'] = chkReqRpl("wt", null, "", "POST", "INT");

    $cls_member = new CLS_MEMBER;
    $cls_wt = new CLS_WEBTOON;

	$wt_view = $cls_wt->wt_view($params['webtoon_idx'], SITE_SAVE_LANG, 'Y');
	if ($wt_view == false) fnMsgJson(502, getTransLangMsg("일치하는 웹툰 데이터가 없습니다."),  "");

    //웹툰 좋아요 저장
    $params['usr_idx']  = $MEM_USR['usr_idx'];
    $params['wt_title'] = $wt_view['title'];
    $params['upt_ip']   = NOW_IP;
    $params['upt_id']   = $MEM_USR['usr_idx'];
    $params['reg_ip']   = NOW_IP;
    $params['reg_id']   = $MEM_USR['usr_idx'];
    if ($cls_wt->favorit_save($params, $point_save) == false) fnMsgJson(503, getTransLangMsg("저장 처리중 문제가 발생했습니다.\n고객센터에 문의주세요."), "");
?>
{"result": 200, "message": "OK", "point_save": "<?=$point_save?>"}