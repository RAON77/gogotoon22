<? include "../inc/config.php" ?>
<?
	setSession("RETURN_URL", NOW_URL);
	if (!isUser()) fnMsgGo(500, "", "/member/login.php?return_flag=Y", "");

	$params['page']       = chkReqRpl("page", 1, "", "", "INT");
	$params['list_size']  = 10;
	$params['block_size'] = 10;
	$params['sch_usr']    = $MEM_USR['usr_idx'];
	$page_params          = setPageParamsValue($params, "page,list_size,block_size");

	$cls_coupon = new CLS_SETTING_COUPON;

	//사용자 쿠폰함 내역 목록 불러오기
	$list = $cls_coupon->mypage_coupon_list($params, $total_cnt, $total_page);

	$pageNum = "7";
	$pageSubNum = "3";
?>
<? include "../inc/top.php" ?>
<? include "../inc/header.php" ?>

<div id="container" class="container mypage">
	<div class="contents">
		<div class="inr-c">
			<? include "top.php" ?>

			<div class="my_cont">
				<div class="tab ty4">
					<ul>
						<li class="on"><a href="mypage_coupon_list.php"><span><?=getTransLangMsg("쿠폰 내역")?></span></a></li>
						<li><a href="mypage_coupon_write.php"><span><?=getTransLangMsg("쿠폰 등록")?></span></a></li>
					</ul>
				</div>
				<div class="inner">
					<div class="tbl_basic">
						<table class="list">
							<caption><?=getTransLangMsg("쿠폰 목록")?></caption>
							<colgroup>
								<col class="num">
								<col class="wid3">
								<col>
								<col class="wid3">
								<col class="date3">
								<col class="con">
							</colgroup>
							<thead>
								<tr>
									<th><?=getTransLangMsg("번호")?></th>
									<th><?=getTransLangMsg("쿠폰번호")?></th>
									<th><?=getTransLangMsg("쿠폰명")?></th>
									<th><?=getTransLangMsg("지급 포인트")?></th>
									<th><?=getTransLangMsg("사용일")?></th>
									<th><?=getTransLangMsg("유효기간")?></th>
								</tr>
							</thead>
							<tbody>
								<?for ($i=0; $i<count($list);$i++) {?>
									<tr>
										<td><?=formatNumbers($total_cnt-(($params['page']-1)*$params['list_size'])-$i)?></td>
										<td><?=$list[$i]['coupon_num']?></td>
										<td class="ta-l"><?=$list[$i]['title']?></td>
										<td><?=formatNumbers($list[$i]['point'])?></td>
										<td><?=formatDates($list[$i]['use_dt'], "Y.m.d H:i")?></td>
										<td><?=formatDates($list[$i]['sdate'], "Y.m.d")?> ~ <?=formatDates($list[$i]['edate'], "Y.m.d")?></td>
									</tr>
								<?}?>

								<?if (count($list) == 0) {?>
									<tr>
										<td colspan="6"><?=getTransLangMsg("등록된 데이터가 없습니다.")?></td>
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