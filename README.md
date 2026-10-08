# 떡판왕 TTEOKPANWANG 공식 홈페이지

> 떡볶이의 판을 뒤집다. — 선릉역 즉석떡볶이 떡판왕 원페이지 랜딩

## 구조
```
index.html        마크업 (Hero / Brand / Menu / Experience / Gallery / Location / Footer)
css/init.css      리셋
css/layout.css    레이아웃·컴포넌트·반응형 (1600 / 1280 / 1024 / 768 / 425)
js/data.js        ★ 메뉴·가격·매장정보·갤러리 데이터 (여기만 수정)
js/lib.js         렌더링·스크롤 애니메이션·헤더·모바일 메뉴
images/           실제 사진 보관 위치
```

## 콘텐츠 수정
`js/data.js` 에서
- `stores[]` 매장 목록 (이름·주소·지번·영업시간·전화) — 항목 추가 시 카드 자동 생성
- `brand.instagram / youtube` SNS 주소
- `menu[]` 대표 메뉴 카드 (price 숫자, priceNote, from)
- `menuBoard[]` 전체 메뉴판 (카테고리별 이름·가격)
- `menu[].image`, `gallery[].image` 에 `images/pic_menu_chadol.jpg` 처럼 경로 입력 → 플레이스홀더 대신 사진 노출 (lazy loading)

실제 사진이 없는 영역은 '사진 준비중' 플레이스홀더로 표시되며, Hero 의 떡볶이 판은 SVG 일러스트입니다.

## 로컬 실행
```
python3 -m http.server 8000
```
