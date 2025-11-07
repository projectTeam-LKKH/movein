// 햄버거 루트 연결
$(function () {
  $("#header-slot").load("import/header.html", initHeader); // 로드 완료 후에만 바인딩
});

function initHeader() {
  const root = document.getElementById("header-slot"); // 범위 한정
  root
    .querySelectorAll(".ham-sub")
    .forEach((el) => el.classList.remove("active")); // ✅ 초기 상태 해제
  const hamBtn = root.querySelector(".hambtn");
  const ham = root.querySelector("nav.hamburger");
  const modal = root.querySelector(".modal-bg");
  const logo = root.querySelector("header a img");

  // 햄버거 토글
  hamBtn?.addEventListener("click", () => {
    const open = ham.classList.toggle("show");
    modal?.classList.toggle("active", open);
    document.body.classList.toggle("lock", open);
    logo?.classList.toggle("hide", open);
  });

  // 오버레이 클릭 시 닫기
  modal?.addEventListener("click", () => {
    ham.classList.remove("show");
    modal.classList.remove("active");
    document.body.classList.remove("lock");
    logo?.classList.remove("hide");
  });

  // 서브 메뉴: 전체에서 단 하나만 활성
  root.addEventListener("click", (e) => {
    const sub = e.target.closest(".ham-sub");
    if (!sub) return;

    // 전부 해제
    root
      .querySelectorAll(".ham-sub.active")
      .forEach((el) => el.classList.remove("active"));
    // 클릭한 것만 활성
    sub.classList.add("active");
  });
}

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
const reviewBtn = document.querySelector(".review-input");
const reviewForm = document.querySelector(".review-form");

reviewForm.style.display = "none";

reviewBtn.addEventListener("click", (e) => {
  e.preventDefault();
  reviewForm.style.display =
    reviewForm.style.display === "none" ? "block" : "none";
});

//푸터 루트 연결
$(function () {
  $("#bottom-nav").load("import/bottom-nav.html");
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
