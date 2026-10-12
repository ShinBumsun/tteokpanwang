<?php
/* 메인(index.php) 외의 그누보드 기본 화면(로그인 등)에서 쓰는 머리말 */
if (!defined('_GNUBOARD_')) exit;
include_once(G5_PATH.'/head.sub.php');
?>
<style>
body {margin:0; background:#FFF8EF; color:#171717; font-family:"Pretendard", -apple-system, BlinkMacSystemFont, "Apple SD Gothic Neo", sans-serif;}
#tp_head {display:flex; align-items:center; justify-content:space-between; height:72px; padding:0 24px; background:#171717;}
#tp_head .th_logo {display:flex; align-items:center; gap:10px; color:#fff; font-size:24px; font-weight:900; text-decoration:none;}
#tp_head .th_logo i {width:30px; height:23px; background:#FFD447; clip-path:polygon(0 100%, 0 20%, 25% 55%, 50% 0, 75% 55%, 100% 20%, 100% 100%);}
#tp_head .th_home {color:#fff; font-size:14px; opacity:0.75; text-decoration:none;}
#tp_wrap {max-width:960px; margin:0 auto; padding:48px 20px 80px;}
#tp_foot {padding:24px; background:#E92828; color:#171717; font-size:13px; font-weight:700; text-align:center;}
</style>
<!-- Header S -->
<header id="tp_head">
	<a href="<?php echo G5_URL ?>/" class="th_logo" title="Logo_떡판왕 홈"><i aria-hidden="true"></i>떡판왕</a>
	<a href="<?php echo G5_URL ?>/" class="th_home">홈으로</a>
</header>
<!-- Header E -->
<!-- Contents S -->
<main id="tp_wrap">
