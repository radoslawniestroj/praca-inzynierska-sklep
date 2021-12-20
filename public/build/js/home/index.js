$( document ).ready(function(){
    let carousel = $('.carousel');

    carousel.carousel({
        interval: 4000
    })

    $( "#carousel-next" ).click(function() {
        carousel.carousel('next')
    });
    $( "#carousel-prev" ).click(function() {
        carousel.carousel('prev')
    });
});
