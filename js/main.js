// 메뉴 나오고 안 나오고
document.addEventListener("DOMContentLoaded", () => {
  const hamBtn = document.querySelector(".hambtn");
  const hamMenu = document.querySelector("nav.hamburger");
  const locked = document.body;
  const logoImg = document.querySelector("#header a img");
  const modal = document.querySelector(".modal-bg");

  hamBtn.addEventListener("click", () => {
    hamMenu.classList.toggle("show");

    if (hamMenu.classList.contains("show")) {
      //만약 햄메뉴가 보이면
      modal.classList.add("active");
      locked.classList.add("lock");
      logoImg.classList.add("hide");
    } else {
      //햄메뉴가 안보이면
      modal.classList.remove("active");
      locked.classList.remove("lock");
      logoImg.classList.remove("hide");
    }
  });
});

//헤더 버튼
document.addEventListener("DOMContentLoaded", () => {
  const subMenus = document.querySelectorAll(".ham-sub-box li");

  subMenus.forEach((sub) => {
    sub.addEventListener("click", () => {
      subMenus.forEach((item) => {
        item.classList.remove("active");

        sub.classList.add("active");
      });
    });
  });
});

//------------------아직 작업 전입니다.
// 화면 최상단에서 아래로 스와이프하는 경우 감지
// 기본 새로고침 동작 차단 start
let touchStartY = 0;

window.addEventListener(
  "touchstart",
  (e) => {
    touchStartY = e.touches[0].clientY;
  },
  { passive: false }
);

window.addEventListener(
  "touchmove",
  (e) => {
    const currentY = e.touches[0].clientY;

    if (window.scrollY === 0 && currentY > touchStartY) {
      e.preventDefault();
    }
  },
  { passive: false }
);

// 화면 최상단에서 아래로 스와이프하는 경우 감지
// 기본 새로고침 동작 차단 end

// 하트 버튼 토클 start
document.addEventListener("DOMContentLoaded", () => {
  const hearts = document.querySelectorAll(".heart-icon");

  hearts.forEach((heart) => {
    heart.addEventListener("click", () => {
      heart.classList.toggle("active");
    });
  });
});
// 하트 버튼 토클 end

// 하단 메뉴바 start
document.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll(".menu-btn");

  buttons.forEach((btn) => {
    btn.addEventListener("click", () => {
      buttons.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
    });
  });
});
// 하단 메뉴바 end

console.log("페이지가 로드되었습니다.");
