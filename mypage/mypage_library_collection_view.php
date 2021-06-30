<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	setSession("RETURN_URL", "/mypage/mypage_library_collection.php");

	if (!chkReferer()) fnMsgGo(500, getTransLangMsg("잘못된 접근 입니다."), "/", "");
	if (!isUser()) fnMsgGo(501, "", "/member/login.php?return_flag=Y", "");

	$params['webtoon_idx']   = chkReqRpl("wt", null, "", "", "INT");
	$params['translator_id'] = chkReqRpl("tid", "", 100, "", "STR");
	$params['lang']          = chkReqRpl("lang", "", 10, "", "STR");
    $lang_list               = getSiteLangList(SITE_SAVE_LANG);

    if (chkBlank($params['webtoon_idx'])) fnMsgGo(502, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "");
    if (!chkBlank($params['lang']) && array_search($params['lang'], array_column($lang_list, 'code')) === false) fnMsgGo(503, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "");

    if (!chkBlank($params['lang'])) {
        setcookie("SITE_SAVE_LANG", $params['lang'], time() + (86400 * 365), "/");
    }

    if (chkBlank($params['translator_id'])) {
        fnMsgGo(200, "", "/view/view.php?wt=". $params['webtoon_idx'], "");
    } else {
        fnMsgGo(200, "", "/view/view.php?wt=". $params['webtoon_idx'] ."&tid=". $params['translator_id'], "");
    }
?>