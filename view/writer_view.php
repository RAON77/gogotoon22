<?include("../inc/config.php")?>
<?
	if (!chkReferer()) fnMsgGo(500, "잘못된 접근 입니다.", "/", "");

    $params['webtoon_idx'] = chkReqRpl("wt", null, "", "", "INT");
    $params['writer_idx']  = chkReqRpl("writer_idx", null, "", "", "INT");

	$cls_wt = new CLS_WEBTOON;
    $cls_cp_writer = new CLS_SETTING_CP_WRITER;

    //웹툰 상세정보 불러오기
    $wt_view = $cls_wt->wt_view($params['webtoon_idx'], SITE_SAVE_LANG, 'Y');
    if ($wt_view == false) fnMsgGo(501, getTransLangMsg("일치하는 웹툰 데이터가 없습니다."), "RELOAD", "");

	//웹툰작가 상세정보 불러오기
	$writer_view = $cls_cp_writer->writer_view($params['writer_idx'], 'Y');
	if ($writer_view == false) fnMsgGo(502, getTransLangMsg("일치하는 작가 데이터가 없습니다."), "RELOAD", "");

    //웹툰작가 참여팍품 목록 불러오기
    $webtoon_list = $cls_cp_writer->writer_webtoon_list($writer_view['idx'], 'Y');
?>
<section class="popup ty2">
    <header class="p_head">
        <h2 class="tit f-gm"><span><?=getTransLangMsg("작가소개")?></span></h2>
        <button type="button" class="btn_close b-close"><span><?=getTransLangMsg("닫기")?></span></button>
    </header>
    <div class="p_cont">
        <div class="area_writer">
            <div class="top">
                <div class="img"><span style="background-image: url('<?=filePathCheck("/upload/writer/".getUpfileName($writer_view['up_file']))?>');"></span></div>
                <div class="txt">
                    <p class="h1"><?=iif(SITE_SAVE_LANG=='KO', $writer_view['nick_name'], $writer_view['nick_name_en'])?></p>
                    <p class="h2"><?=getCpWriterPartName(explode(",", $writer_view['part']))?></p>
                </div>
            </div>
            <div class="list">
                <ul>
                    <?for ($i=0; $i<count($webtoon_list);$i++) {?>
                        <li>
                            <div class="img"><span style="background-image: url('<?=filePathCheck('/upload/webtoon/'. $webtoon_list[$i]['webtoon_idx'] .'/list/'. getUpfileName($webtoon_list[$i]['up_file_1']))?>');"></span></div>
                            <div class="txt">
                                <p class="h1"><?=$webtoon_list[$i]['title']?></p>
                                <div class="h2 t_line">
                                    <p><?=getTransLangMsg("장르")?> : <?=$webtoon_list[$i]['genre_name']?></p>
                                    <p class="c-pink">
                                        <?
                                            //키워드 목록 불러오기
                                            $keyword_list = $cls_wt->wt_keyword_list($webtoon_list[$i]['webtoon_idx'], 'Y', SITE_SAVE_LANG);

                                            for ($k=0; $k<count($keyword_list); $k++) {
                                                echo "<span>#". $keyword_list[$k]['code_name'] ."</span>";
                                            }
                                        ?>
                                    </p>
                                </div>
                                <p class="t1 t-dot"><?=textareaDecode($wt_view['introduce'])?></p>
                                <div class="btn-bot">
                                    <a href="/view/view.php?wt=<?=$webtoon_list[$i]['webtoon_idx']?>" class="btn-pk red rv n"><span>웹툰으로 이동</span></a>
                                </div>
                            </div>
                        </li>
                    <?}?>
                </ul>
            </div>
        </div>
    </div>
</section>