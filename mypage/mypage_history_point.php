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
	$list = $cls_member->mypage_history_point_list($params, $total_cnt, $total_page);

	$pageNum = "7";
	$pageSubNum = "2";
	$pageSubNum2 = "3";
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
                                <col class="con">
                                <col>
                                <col class="wid3">
                                <col class="date2">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="hide-m"><?=getTransLangMsg("번호")?></th>
                                    <th><?=getTransLangMsg("구분")?></th>
                                    <th><?=getTransLangMsg("Point 내역")?></th>
                                    <th><?=getTransLangMsg("포인트")?></th>
                                    <th><?=getTransLangMsg("날짜")?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?for ($i=0; $i<count($list); $i++) {?>
                                    <tr>
                                        <td class="hide-m"><?=formatNumbers($total_cnt-(($params['page']-1)*$params['list_size'])-$i)?></td>
                                        <td><?=getPointTypeName($list[$i]['point_type'])?></td>
                                        <td class="ta-l"><?=htmlDecode($list[$i]['content'])?></td>
                                        <td><?=formatNumbers($list[$i]['point'],0)?> Point</td>
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