<div class="my_top">
	<div class="col">
		<p><strong class="c-red"><?=formatNumbers($user_view['total_point'])?></strong>Point</p>
		<p><strong class="c-red"><?=formatNumbers($user_view['total_gcash'])?></strong><?=getTransLangMsg("G캐시")?> <a href="mypage_charge.php" class="btn-pk n red rv"><span><?=getTransLangMsg("충전하기")?></span></a></p>
	</div>
	<div class="col">
		<div class="d-ib ta-r">
			<p>
				<?if (!chkBlank($user_view['sns_gubun'])) {?>
					<span class="i-aft i_<?=$user_view['sns_gubun']?>">
				<?}?>
				<?=$user_view['usr_email']?></span>
			</p>
			<button type="button" class="btn-pk s white bdrs" onclick="location.replace('/member/logout.php?tp=btn')"><span><?=getTransLangMsg("로그아웃")?></span></button>
		</div>
	</div>
	<div class="col">
		<div class="d-ib ta-l"><a href="mypage_message.php">
			<p><span class="s"><?=getTransLangMsg("메세지함")?></span></p>
			<p><strong class="c-red"><?=formatNumbers($cls_member->message_total_count($user_view['usr_idx']))?></strong><?=getTransLangMsg("건")?></p>
		</a></div>
	</div>
</div>


<?if($pageSubNum != "5") {?>
	<div class="tab ty3 mb0">
		<ul>
			<li <?if($pageSubNum=="1") {?>class="on"<?}?>><a href="mypage_library_collection.php"><span><?=getTransLangMsg("내 서재")?></span></a></li>
			<li <?if($pageSubNum=="2") {?>class="on"<?}?>><a href="mypage_history_payment.php"><span><?=getTransLangMsg("충전내역")?></span></a></li>
			<li <?if($pageSubNum=="3") {?>class="on"<?}?>><a href="mypage_coupon_list.php"><span><?=getTransLangMsg("쿠폰함")?></span></a></li>
			<li <?if($pageSubNum=="4") {?>class="on"<?}?>><a href="mypage_info.php"><span><?=getTransLangMsg("정보수정")?></span></a></li>
		</ul>
	</div>
<?}?>