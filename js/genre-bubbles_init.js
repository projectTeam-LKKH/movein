
// [A] 페이지 진입 시 버블 초기화
window.addEventListener("DOMContentLoaded", () => {
  const app = window.genreBubbleApp?.init("genre-bubble-container");
  if (!app) return;

  // PHP → JS
  const favoriteGenres = <?php echo json_encode($favorite_genres); ?>;
  const isLoggedIn = <?php echo $nickname ? 'true' : 'false'; ?>;

  // 모든 버블에 공통 적용할 그라데이션 옵션
  const GRAD_OPT = { gradient: { inner: "#504399", outer: "#8670FF" } };

  const allGenres = [
    { name: "애니", color: "#8670FF" },
    { name: "드라마", color: "#8670FF" },
    { name: "액션", color: "#8670FF" },
    { name: "SF", color: "#8670FF" },
    { name: "코미디", color: "#8670FF" },
    { name: "판타지", color: "#8670FF" },
    { name: "스릴러", color: "#8670FF" },
    { name: "로맨스", color: "#8670FF" },
  ];

  if (!isLoggedIn) {
    // ✅ 비로그인도 전부 그라데이션
    allGenres.forEach((g) => app.createGenreBubble(g.name, g.color, 40, GRAD_OPT));
  } else {
    const base = 40, max = 90, step = 5;

    allGenres.forEach((g) => {
      const idx = favoriteGenres.indexOf(g.name);
      if (idx !== -1) {
        const size = Math.max(base, max - idx * step);

        // ✅ 로그인도 전부 그라데이션 (+ 1순위만 볼드 유지)
        const opts = (idx === 0)
          ? { ...GRAD_OPT, fontWeight: 700 } // 1순위 강조(굵기만)
          : GRAD_OPT;

        app.createGenreBubble(g.name, g.color, size, opts);
      } else {
        app.createGenreBubble(g.name, g.color, base, GRAD_OPT);
      }
    });
  }
});
