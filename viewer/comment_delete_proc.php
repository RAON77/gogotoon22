<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

	$params['webtoon_idx'] = chkReqRpl("wt", null, "", "POST", "INT");
	$params['round_idx']   = chkReqRpl("ep", null, "", "POST", "INT");
	$params['idx']         = chkReqRpl("cmt_idx", null, "", "POST", "INT");
	$params['parent_idx']  = chkReqRpl("parent_idx", null, "", "POST", "INT");

	$cls_wt = new CLS_WEBTOON;
    $cls_comment = new CLS_COMMENT;

    //웹툰 상세정보 불러오기
	$wt_view = $cls_wt->wt_view($params['webtoon_idx'], SITE_SAVE_LANG, 'Y');
	if ($wt_view == false) fnMsgJson(502, getTransLangMsg("일치하는 웹툰 데이터가 없습니다."), "");

    //웹툰 회차 목록 불러오기
    $round_list = $cls_wt->round_list($wt_view['idx'], SITE_SAVE_LANG);

    //웹툰 회차 상세정보 불러오기
	$round_view = $cls_wt->round_view($params['round_idx'], SITE_SAVE_LANG, 'Y');
	if ($round_view == false) fnMsgJson(503, getTransLangMsg("일치하는 회차 데이터가 없습니다."), "");

    //웹툰 댓글 상세정보 불러오기
    $cmt_view = $cls_comment->wt_comment_view($params['idx']);
    if ($cmt_view == false) fnMsgJson(504, getTransLangMsg("일치하는 댓글 데이터가 없습니다."), "");

    //등록자 본인 체크
    if ($MEM_USR['usr_idx'] != $cmt_view['reg_id']) fnMsgJson(505, getTransLangMsg("등록자 본인만 삭제 가능합니다."), "");

    //신고 체크
    if ($cmt_view['open_flag']=='N') fnMsgJson(506, getTransLangMsg("신고로 인해 댓글이 삭제 되었습니다."), "");

    //삭제 체크
    if ($cmt_view['del_flag']=='Y') fnMsgJson(507, getTransLangMsg("이미 삭제된 데이터 입니다."), "");

    //웹툰 댓글 삭제
    $params['upt_ip'] = NOW_IP;
    $params['upt_id'] = $MEM_USR['usr_idx'];
    if (!$cls_comment->wt_comment_delete($params)) fnMsgJson(506, getTransLangMsg("삭제 처리중 문제가 발생했습니다.\n고객센터에 문의주세요."), "");
?>
{"result": 200, "message": "OK", "point_save": "<?=$point_save?>"}