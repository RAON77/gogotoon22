<?include("../inc/config.php")?>
<?
	if (!chkReferer()) fnMsgGo(500, "잘못된 접근 입니다.", "/", "");

    $params['webtoon_idx'] = chkReqRpl("wt", null, "", "", "INT");

	$cls_wt = new CLS_WEBTOON;

    //웹툰 상세정보 불러오기
    $wt_view = $cls_wt->wt_view($params['webtoon_idx'], SITE_SAVE_LANG, 'Y');
    if ($wt_view == false) fnMsgGo(501, getTransLangMsg("일치하는 웹툰 데이터가 없습니다."), "RELOAD", "");

    //웹툰 공지사상 목록 불러오기
    $list = $cls_wt->notice_list($wt_view['idx'], SITE_SAVE_LANG);
?>
<section class="popup ty2">
    <header class="p_head">
        <h2 class="tit f-gm"><span>NEWS</span></h2>
        <button type="button" class="btn_close b-close"><span>닫기</span></button>
    </header>
    <div class="p_cont">
        <div class="tbl_faq">
            <ul>
                <?for ($i=0; $i<count($list); $i++) {?>
                    <li <?if ($i==0) {?>class="on"<?}?>>
                        <a href="javascript:;" class="tit">
                            <span><?=$list[$i]['title']?></span>
                            <span class="date"><?=formatDates($list[$i]['reg_date'], 'Y.m.d')?></span>
                        </a>
                        <div class="txt" <?if ($i==0) {?>style="display:block"<?}?>><?=textareaDecode($list[$i]['content'])?></div>
                    </li>
                <?}?>
            </ul>
        </div>
    </div>
</section>