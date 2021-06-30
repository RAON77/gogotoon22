<!DOCTYPE html>
<html lang="ko">
<head>
<title><?=getTransLangMsg("고고툰")?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<?if($pageSubNum!="1000") { ?><meta id="viewport" name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, target-densitydpi=medium-dpi, shrink-to-fit=no"> <? } ?>
<meta name="format-detection" content="telephone=no" />
<meta http-equiv="Cache-Control" content="no-cache,no-store" />

<meta name="writer" content="">
<meta name="title" content="" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="author" content="" />
<meta name="format-detection" content="telephone=no" />

<meta property="og:title" content=""/>
<meta property="og:type" content=""/>
<meta property="og:url" content=""/>
<meta property="og:description" content=""/>
<meta property="og:image" content="<?=SITE_URL?>/images/common/logo.png"/>


<!-- link -->
<link rel="canonical" href=""/>
<link rel="shortcut icon" href="<?=SITE_URL?>/images/common/favicon.ico">


<link href="/css/common.css" rel="stylesheet">
<link href="/css/mobile.css" rel="stylesheet">
<link href="/css/popup.css" rel="stylesheet">

<!-- script -->
<script src="/js/jquery-1.12.4.min.js"></script>
<script src="/js/jquery-ui.min.js"></script>
<script src="/js/jquery.lazy.min.js"></script>
<script src="/js/common.js"></script>

<script type="text/javascript" src="/module/js/class.helper.js"></script>
<script type="text/javascript" src="/module/js/fn.user.define.js"></script>
<script type="text/javascript">
    var ajaxStatus = false;

	//IE 미지원 체크
	if(/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) {
		window.location = 'microsoft-edge:' + window.location;
		setTimeout(function() {
			window.location = 'https://go.microsoft.com/fwlink/?linkid=2135547';
		}, 0);
	}
</script>

</head>
<body>

<div id="skipNaviWrap">
	<p class="hidden"><?=getTransLangMsg("바로가기 메뉴")?></p>
	<a href="#container"><?=getTransLangMsg("컨텐츠 바로가기")?></a>
	<a href="#footer"><?=getTransLangMsg("하단 메뉴 바로가기")?></a>
</div>

<div id="<?if($pageNum=="10") { ?>wrap_viewer<?} else {?>wrap<? } ?>">
