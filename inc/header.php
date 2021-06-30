<header id="header" class="header">
	<div class="inr-c">
		<h1 class="logo"><a href="/"><img src="/images/common/logo.png" alt="<?=getTransLangMsg("고고툰")?>"></a></h1>

		<div class="gnbbox">
			<div class="rgh">
				<a href="javascript:;" class="btn_sch"><span class="i-set i_sch2"><?=getTransLangMsg("검색")?></span></a>
				<div class="hd_sch">
					<div class="in">
						<div class="sch">
							<input type="text" id="top_sch_word" class="inp_txt w100p" maxlength="20" placeholder="<?=getTransLangMsg("작품/작가명을 검색해주세요.")?>">
							<button type="button" class="btn-pk red rv bdrs n f-gm"><span><?=getTransLangMsg("검색")?></span></button>
						</div>
						<div class="hd_lst_sch1 lst_prd1">
							<ul class="search_data"></ul>
							<div class="no_data"><?=getTransLangMsg("작품/작가명을 검색해주세요.")?></div>
						</div>

						<script>
							$(function(){
								$("#top_sch_word").keyup(function(){
									var $target = $(".hd_lst_sch1");
									var this_val = $(this).val();

									if (this_val == "") {
										$target.find(".search_data").empty().hide();
										$target.find(".no_data").show().text("<?=getTransLangMsg("작품/작가명을 검색해주세요.")?>");
									} else {
										$target.find(".search_data").empty().show();
										$target.find(".no_data").hide().text("<?=getTransLangMsg("작품/작가명을 검색해주세요.")?>");

										if (!search_check) {
											search_check = true;
											setTimeout("topSearchList()", 500)
										}
									}
								});

								//헤더 검색부분
								$("#header .rgh .sch .inp_txt").on("focus", function(){
									$("#header .hd_sch").addClass("active");
								});
								$(document).on("click", function(e){
									if($(".hd_sch > .in").has(e.target).length === 0){
										$("#header .hd_sch").removeClass("active");
										$("#header .rgh .sch .inp_txt").val("");

										$("#header .hd_sch").find(".search_data").empty().hide();
										$("#header .hd_sch").find(".no_data").show().text("<?=getTransLangMsg("작품/작가명을 검색해주세요.")?>");
									}
								});

								window.onpageshow = function(event) {
									if ( event.persisted || (window.performance && window.performance.navigation.type == 2)) {
										$("#top_sch_word").val("");
									}
								}
							})

							var search_check = false;
							function topSearchList() {
								var $target = $(".hd_lst_sch1");
								var this_val = encodeURIComponent($("#top_sch_word").val());

								if (this_val == "") {
									$target.find(".search_data").empty().hide();
									$target.find(".no_data").show().text("<?=getTransLangMsg("작품/작가명을 검색해주세요.")?>");
								} else {
									$.getJSON("/top_search_list.php?q="+this_val, function(data){
										if (data.result == 200) {
											$target.find(".search_data").empty().show();
											$target.find(".no_data").hide().text("<?=getTransLangMsg("작품/작가명을 검색해주세요.")?>");

											$.each(data.list, function(i, item){
												var list  = "";
													list += "<li class=\"box\"><a href=\"/view/view.php?wt="+ item.webtoon_idx +"\">";
													list += "	<div class=\"img\"><span><img src=\""+ item.up_file +"\"></span></div>";
													list += "	<div class=\"txt\">";
													list += "		<p class=\"h1\">"+ item.title +"</p>";
													list += "		<p class=\"t1 line\"><span>"+ item.latest_ep +"</span><span>"+ item.genre +"</span></p>";
													list += "	</div>";
													list += "</a></li>";

												$target.find(".search_data").append(list);
											})
										} else {
											$target.find(".search_data").hide().empty();
											$target.find(".no_data").show().text("<?=getTransLangMsg("해당하는 작품/작가가 없습니다.")?>");
										}
									}, "json");
								}

								search_check = false;
							}
						</script>
					</div>
					<div class="popup_dim"></div>
				</div>
				<div class="btn_legs">
					<button type="button" class="btn_leg f-gm"><span class="i-set i_leng"><?=getTransLangMsg("언어선택")?></span></button>
					<?for ($i=0; $i<count($TOP_LANG_LIST); $i++) {?>
						<a href="javascript:;" class="btn_leg f-gm" onclick="$('#action_ifrm').attr('src', '/set_lang.php?lang=<?=$TOP_LANG_LIST[$i]['code']?>')"><span><?=$TOP_LANG_LIST[$i]['short_code']?></span></a>
					<?}?>
				</div>
				<button type="button" class="btn_menu" onclick="openLayerPopup('popMenu');"><span><?=getTransLangMsg("로그인")?></span></button>
			</div>

			<nav id="gnb" class="gnb">
				<ul class="menu">
					<li class="g1 <?if($pageNum=="1"){ ?>on<? } ?>"><a href="/serial/list.php"><?=getTransLangMsg("연재")?><!-- <span class="i-set i_new">N</span> --></a></li>
					<li class="g2 <?if($pageNum=="2"){ ?>on<? } ?>"><a href="/rank/list.php"><?=getTransLangMsg("랭킹")?></a></li>
					<li class="g3 <?if($pageNum=="3"){ ?>on<? } ?>"><a href="/complete/list.php"><?=getTransLangMsg("완결")?></a></li>
					<li class="g4 <?if($pageNum=="4"){ ?>on<? } ?>"><a href="/free/list.php"><?=getTransLangMsg("기다리면 무료")?></a></li>

					<?if ($MEM_USR['usr_gubun'] == '20') {?>
						<li class="g5 hide-m"><a href="/translation/translatable_list.php"><?=getTransLangMsg("번역회원")?></a></li>
					<?}?>
				</ul>
			</nav>
		</div>
	</div>
</header><!-- //header -->