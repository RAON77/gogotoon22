<?include("../inc/config.php")?>
<?
	if (!chkReferer()) fnMsgGo(500, getTransLangMsg("잘못된 접근 입니다."), "/", "");

	$params['webtoon_idx'] = chkReqRpl("wt", null, "", "", "INT");
	$params['round_idx']   = chkReqRpl("ep", null, "", "", "INT");
    $params['cmt_idx']     = chkReqRpl("cmt_idx", null, "", "", "INT");
    $params['parent_idx']  = chkReqRpl("parent_idx", null, "", "", "INT");

	$cls_wt = new CLS_WEBTOON;
	$cls_comment = new CLS_COMMENT;

    //웹툰 상세정보 불러오기
	$wt_view = $cls_wt->wt_view($params['webtoon_idx'], SITE_SAVE_LANG, 'Y');
	if ($wt_view == false) fnMsgGo(502, getTransLangMsg("일치하는 웹툰 데이터가 없습니다."), "/", "");

    //웹툰 회차 상세정보 불러오기
	$round_view = $cls_wt->round_view($params['round_idx'], SITE_SAVE_LANG, 'Y');
	if ($round_view == false) fnMsgGo(503, getTransLangMsg("일치하는 회차 데이터가 없습니다."), "/", "");

    //웹툰 댓글 상세정보 불러오기
    $cmt_view = $cls_comment->wt_comment_view($params['cmt_idx']);
    if ($cmt_view == false) fnMsgGo(504, getTransLangMsg("일치하는 댓글 데이터가 없습니다."), "/", "");

    //카테고리 불러오기
    $categor_list = getCommentReportCategoryList(SITE_SAVE_LANG);
?>

<section class="popup">
    <header class="p_head">
        <h2 class="tit"><span><?=getTransLangMsg("신고하기")?></span></h2>
        <button type="button" class="btn_close b-close"><span><?=getTransLangMsg("닫기")?></span></button>
    </header>
    <div class="p_cont">
        <div class="box">
            <p><?=getTransLangMsg("불법적인 내용, 서비스 이용목적에 부합하지 않는 댓글은 신고하실 수 있으며, 허위신고인 경우 서비스 이용에 제한을 받을 수 있습니다.")?></p>
        </div>
        <div class="list">
            <?for ($i=0; $i<count($categor_list); $i++) {?>
                <label class="inp_radio">
                    <input type="radio" name="report_cate" value="<?=$categor_list[$i]['code']?>"><span><?=$categor_list[$i]['name']?></span>
                </label>
            <?}?>
        </div>
    </div>
    <div class="p_botm">
        <button type="button" class="btn-pk n gray rv b-close"><span>취소</span></button>
        <button type="button" class="btn-pk n red rv" onclick="reportSaveGo('<?=$params['cmt_idx']?>', '<?=$params['parent_idx']?>')"><span>신고하기</span></button>
    </div>
</section>