$(function(){
	var D = window.TPW_DATA || {};
	var S = D.store || {};
	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var TBD = '추후 공지';

	function esc(v){
		return $('<div>').text(v == null ? '' : v).html();
	}

	// 매장 정보 바인딩 S
	$('[data-store]').each(function(){
		var key = $(this).data('store');
		var val = S[key];
		if (key === 'tel' && val) {
			$(this).html('<a href="tel:' + esc(val.replace(/[^0-9+]/g, '')) + '">' + esc(val) + '</a>');
		} else if (val) {
			$(this).text(val);
		} else {
			$(this).addClass('is_tbd').text(TBD);
		}
	});
	$('#footer [data-store].is_tbd').remove();
	$('.js_naver').attr('href', S.naverMap);
	$('.js_kakao').attr('href', S.kakaoMap);
	$('.js_year').text(new Date().getFullYear());
	// 매장 정보 바인딩 E

	// SNS 렌더 S
	var sns = [['instagram', 'INSTAGRAM'], ['youtube', 'YOUTUBE']];
	$.each(sns, function(i, s){
		var url = S[s[0]];
		$('#snsList').append(url
			? '<li><a href="' + esc(url) + '" target="_blank" rel="noopener" class="font" title="SNS_' + s[1] + ' 새창">' + s[1] + '</a></li>'
			: '<li><span class="font is_tbd" title="' + s[1] + ' 계정 준비중">' + s[1] + '</span></li>');
	});
	// SNS 렌더 E

	// 메뉴 렌더 S
	var menuHtml = '';
	var menuLen = (D.menu || []).length;
	$.each(D.menu || [], function(i, m){
		// 첫 카드는 2행 차지(t1), 남는 칸이 생기면 마지막 카드를 가로형(t2)으로
		var cls = i === 0 ? ' t1' : (i === menuLen - 1 && (menuLen - 1) % 2 === 1 ? ' t2' : '');
		var price = m.price ? Number(m.price).toLocaleString('ko-KR') + '원' : '가격 ' + TBD;
		var pic = m.image
			? '<img src="' + esc(m.image) + '" alt="' + esc(m.name) + '" loading="lazy" decoding="async">'
			: '<div class="cmnp_ph" role="img" aria-label="' + esc(m.name) + ' 사진 준비중"><b class="font">' + esc(m.en) + '</b><span class="font">PHOTO COMING SOON</span></div>';
		menuHtml += '<li class="cmn_card' + cls + '">'
			+ '<div class="cmn_pic">' + pic + '<em class="cmnp_label font">' + esc(m.label) + '</em></div>'
			+ '<div class="cmn_body">'
			+ '<span class="cmnb_en font">' + esc(m.en) + '</span>'
			+ '<strong class="cmnb_name font2">' + esc(m.name) + '</strong>'
			+ '<p class="cmnb_desc txt normal">' + esc(m.desc) + '</p>'
			+ '<span class="cmnb_price' + (m.price ? '' : ' is_tbd') + '">' + price + '</span>'
			+ '</div></li>';
	});
	$('#menuList').html(menuHtml);
	// 메뉴 렌더 E

	// 갤러리 렌더 S
	var galHtml = '';
	$.each(D.gallery || [], function(i, g){
		var inner = g.image
			? '<img src="' + esc(g.image) + '" alt="' + esc(g.caption) + '" loading="lazy" decoding="async">'
			: '<div class="cglp_ph" role="img" aria-label="' + esc(g.caption) + ' 사진 준비중"><b class="font">' + esc(g.en) + '</b><span>사진 준비중</span></div>';
		galHtml += '<li class="cgl_item ' + esc(g.type) + '"><figure>' + inner + '<figcaption class="cgli_cap">' + esc(g.caption) + '</figcaption></figure></li>';
	});
	$('#galleryList').html(galHtml);
	// 갤러리 렌더 E

	// 스크롤 등장 애니메이션 S
	var $targets = $('.section, .step');
	if (reduce || !('IntersectionObserver' in window)) {
		$targets.addClass('on');
	} else {
		var io = new IntersectionObserver(function(entries){
			entries.forEach(function(en){
				if (en.isIntersecting) {
					$(en.target).addClass('on');
					io.unobserve(en.target);
				}
			});
		}, {threshold:0.15, rootMargin:'0px 0px -8% 0px'});
		$targets.each(function(){ io.observe(this); });
	}
	$('#section01').addClass('on');
	// 스크롤 등장 애니메이션 E

	// 헤더 / 현재 섹션 표시 S
	var $win = $(window);
	var $header = $('#header');
	var $links = $('.hnl_link');
	var ticking = false;
	function onScroll(){
		var st = $win.scrollTop();
		$header.toggleClass('scroll', st > 40);
		$('body').toggleClass('dock_on', st > $win.height() * 0.6);
		var cur = '';
		$('.section').each(function(){
			if (this.getBoundingClientRect().top < $win.height() * 0.4) cur = this.id;
		});
		$links.removeClass('on').filter('[href="#' + cur + '"]').addClass('on');
		ticking = false;
	}
	$win.on('scroll', function(){
		if (!ticking) { ticking = true; window.requestAnimationFrame(onScroll); }
	});
	onScroll();
	// 헤더 / 현재 섹션 표시 E

	// 모바일 메뉴 S
	var $menuBtn = $('.h_menubtn');
	function closeMenu(){
		$header.removeClass('open');
		$menuBtn.attr({'aria-expanded':'false', 'aria-label':'메뉴 열기'});
	}
	$menuBtn.on('click', function(){
		var open = !$header.hasClass('open');
		$header.toggleClass('open', open);
		$menuBtn.attr({'aria-expanded':String(open), 'aria-label':open ? '메뉴 닫기' : '메뉴 열기'});
	});
	$('.hml_link').on('click', closeMenu);
	$(document).on('keydown', function(e){ if (e.key === 'Escape') closeMenu(); });
	// 모바일 메뉴 E

	// 앵커 스크롤 S
	$('a[href^="#section"]').on('click', function(e){
		var $t = $($(this).attr('href'));
		if (!$t.length) return;
		e.preventDefault();
		var top = $t.offset().top - ($t.is('#section01') ? 0 : $header.outerHeight() - 1);
		$('html, body').stop().animate({scrollTop:top}, reduce ? 0 : 700);
	});
	// 앵커 스크롤 E

	// 히어로 패럴랙스 S
	if (!reduce) {
		var $pan = $('.ch_visual');
		$win.on('scroll', function(){
			var st = $win.scrollTop();
			if (st < $win.height()) $pan.css('transform', 'translate3d(0,' + (st * 0.25) + 'px,0)');
		});
	}
	// 히어로 패럴랙스 E
});
