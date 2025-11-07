// 하트 버튼 토글
document.addEventListener("DOMContentLoaded", () => {
  const greenBtn = "img/heart_49e99c.png";
  const grayBtn = "img/heart_6f6c76.png";

  document.querySelectorAll(".likeBtn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const img = btn.querySelector("img");
      const current = img.getAttribute("src");
      img.setAttribute("src", current === grayBtn ? greenBtn : grayBtn);
    });
  });
});

// 요즘 대세(영화/드라마) 버튼 토글 — 섹션별 독립 동작
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".hot-container").forEach((section) => {
    const nav = section.querySelector(".hot-nav-box");
    if (!nav) return;

    const buttons = () => nav.querySelectorAll(".all-btn");

    // 초기 활성화: 해당 섹션에 active가 하나도 없으면 첫 번째에 부여
    if (!nav.querySelector(".all-btn.active") && buttons().length) {
      buttons()[0].classList.add("active");
    }

    // 이벤트 위임: 섹션 내부 버튼끼리만 토글
    nav.addEventListener("click", (e) => {
      const btn = e.target.closest(".all-btn");
      if (!btn || !nav.contains(btn)) return;

      buttons().forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
    });
  });
});

//
const reviewBtn = document.querySelector(".formBtn-box");
const reviewForm = document.querySelector(".review-form");
const prevBtn = document.querySelector(".prev-btn");

reviewForm.classList.remove("open");

reviewBtn.addEventListener("click", (e) => {
  reviewForm.classList.add("open");
});

prevBtn.addEventListener("click", () => {
  reviewForm.classList.remove("open");
});

//------------------아직 작업 전입니다.
// 화면 최상단에서 아래로 스와이프하는 경우 감지
// 기본 새로고침 동작 차단 start
// let touchStartY = 0;

// window.addEventListener(
//   "touchstart",
//   (e) => {
//     touchStartY = e.touches[0].clientY;
//   },
//   { passive: false }
// );

// window.addEventListener(
//   "touchmove",
//   (e) => {
//     const currentY = e.touches[0].clientY;

//     if (window.scrollY === 0 && currentY > touchStartY) {
//       e.preventDefault();
//     }
//   },
//   { passive: false }
// );

// 화면 최상단에서 아래로 스와이프하는 경우 감지
// 기본 새로고침 동작 차단 end

// 하트 버튼 토클 start
// document.addEventListener("DOMContentLoaded", () => {
//   const hearts = document.querySelectorAll(".heart-icon");

//   hearts.forEach((heart) => {
//     heart.addEventListener("click", () => {
//       heart.classList.toggle("active");
//     });
//   });
// });
// 하트 버튼 토클 end

// 하단 메뉴바 start
// document.addEventListener("DOMContentLoaded", () => {
//   const buttons = document.querySelectorAll(".menu-btn");

//   buttons.forEach((btn) => {
//     btn.addEventListener("click", () => {
//       buttons.forEach((b) => b.classList.remove("active"));
//       btn.classList.add("active");
//     });
//   });
// });
// 하단 메뉴바 end

console.log("페이지가 로드되었습니다.");
