$('a[href*="#"]').click(function () {
  //全てのページ内リンクに適用させたい場合はa[href*="#"]のみでもOK
  var elmHash = $(this).attr("href"); //ページ内リンクのHTMLタグhrefから、リンクされているエリアidの値を取得
  var pos = $(elmHash).offset().top; //idの上部の距離からHeaderの高さを引いた値を取得
  $("body,html").animate({ scrollTop: pos }, 500); //取得した位置にスクロール。500の数値が大きくなるほどゆっくりスクロール
  return false;
});

$(function () {
  // ハンバーガーボタンクリックで実行
  $("#menu-icon").click(function () {
    $(this).toggleClass("active");
    $("#menu-panel").toggleClass("active");
  });

  $(".p-drawer__panel-list li a").click(function () {
    $("#menu-icon").removeClass("active");
    $("#menu-panel").removeClass("active");
  });
  // function
});

const animateFade = (entries, obs) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      // console.log(entry.target);
      entry.target.animate(
        {
          opacity: [0, 1],
          filter: ["blur(.4rem)", "blur(0)"],
          translate: ["0 4rem", 0],
        },
        {
          duration: 1000,
          easing: "ease",
          fill: "forwards",
        }
      );
      obs.unobserve(entry.target);
    }
  });
};

const fadeObserver = new IntersectionObserver(animateFade);
const fadeElements = document.querySelectorAll(".fadein");
fadeElements.forEach((fadeElement) => {
  fadeObserver.observe(fadeElement);
});

// kirinObserver.observe(document.querySelector("#kirin"));
