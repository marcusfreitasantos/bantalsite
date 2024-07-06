$(document).ready(function () {
  $("#clients__carousel").slick({
    slidesToShow: 8,
    slidesToScroll: 1,
    dots: false,
    speed: 500,
    autoplay: true,
    nextArrow: '<button type="button" class="slick-next">Next</button>',
    prevArrow: '<button type="button" class="slick-prev">Previous</button>',
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 6,
          infinite: true,
        },
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 3,
          dots: true,
        },
      },
      {
        breakpoint: 300,
        settings: "unslick", // destroys slick
      },
    ],
  });
});
