<? include "../inc/config.php" ?>
<?
	setSession("RETURN_URL", NOW_URL);
	if (!isUser()) fnMsgGo(500, "", "/member/login.php?return_flag=Y", "");

	$params['page']       = chkReqRpl("page", 1, "", "", "INT");
	$params['list_size']  = 10;
	$params['block_size'] = 10;
	$params['sch_usr']    = $MEM_USR['usr_idx'];
	$page_params          = setPageParamsValue($params, "page,list_size,block_size");

	$cls_member = new CLS_MEMBER;

	//사용자 쿠폰함 내역 목록 불러오기
	$list = $cls_member->mypage_history_free_ticket_list($params, $total_cnt, $total_page);

	$pageNum = "7";
	$pageSubNum = "2";
	$pageSubNum2 = "4";
?>
<? include "../inc/top.php" ?>
<? include "../inc/header.php" ?>

<div id="container" class="container mypage">
	<div class="contents">
		<div class="inr-c">
			<? include "top.php" ?>

			<div class="my_cont">
				<? include "__tab_history.php" ?>

				<div class="inner">
                    <div class="tbl_basic">
                        <table class="list">
                            <caption><?=getTransLangMsg("G캐시 내역 목록")?></caption>
                            <colgroup>
                                <col class="num hide-m">
                                <col class="wid3">
                                <col>
                                <col class="wid3">
                                <col class="date2">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="hide-m"><?=getTransLangMsg("번호")?></th>
                                    <th><?=getTransLangMsg("구분")?></th>
                                    <th><?=getTransLangMsg("웹툰명")?></th>
                                    <th><?=getTransLangMsg("상태")?></th>
                                    <th><?=getTransLangMsg("등록일")?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?for ($i=0; $i<count($list); $i++) {?>
                                    <tr>
                                        <td class="hide-m"><?=formatNumbers($total_cnt-(($params['page']-1)*$params['list_size'])-$i)?></td>
                                        <td><?=getWtPurchaseTypeName($list[$i]['types'])?></td>
                                        <td class="ta-l"><?=$list[$i]['title']?></td>
                                        <td>
                                            <?
                                                if ($list[$i]['remaining_time'] > 0) {
                                                    echo getTransLangMsg("기간만료");
                                                } else {
                                                    $remaining_time = abs($list[$i]['remaining_time']);

                                                    if ($remaining_time <= 60) {
                                                        $remaining_time = getTransLangMsg("1분 미만 남음");
                                                    } else {
                                                        $result = "";
                                                        $day    = timeToKor($remaining_time, "D");
                                                        $hour   = timeToKor($remaining_time, "G");
                                                        $minute = timeToKor($remaining_time, "I");
                                                        if ($day > 0) $result .= iif($day>1, "{{days}}일 ", "{{day}}일 ");
                                                        if ($hour > 0) $result .= iif($hour>1, "{{hours}}시간 ", "{{hour}}시간 ");
                                                        if ($day == 0 && $minute > 0) {
                                                            $result .= iif($minute>1, "{{minutes}}분 ", "{{minute}}분 ");
                                                        }

                                                        $remaining_time = getTransLangMsg($result ."남음");
                                                        $remaining_time = str_replace("{{days}}", $day, $remaining_time);
                                                        $remaining_time = str_replace("{{day}}", $day, $remaining_time);
                                                        $remaining_time = str_replace("{{hours}}", $hour, $remaining_time);
                                                        $remaining_time = str_replace("{{hour}}", $hour, $remaining_time);
                                                        $remaining_time = str_replace("{{minutes}}", $minute, $remaining_time);
                                                        $remaining_time = str_replace("{{minute}}", $minute, $remaining_time);
                                                    }

                                                    echo $remaining_time;
                                                }
                                            ?>
                                        </td>
                                        <td><?=formatDates($list[$i]['reg_date'], "Y.m.d")?></td>
                                    </tr>
								<?}?>
								<?if (count($list) == 0) {?>
									<tr>
										<td colspan="5"><?=getTransLangMsg("등록된 데이터가 없습니다.")?></td>
									</tr>
								<?}?>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagenation">
                        <? frontPaging($total_page, $params['block_size'], $params['page'], $page_params, "") ?>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div><!--//container -->

<? include "../inc/footer.php" ?>
<? include "../inc/bottom.php" ?>