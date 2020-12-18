//'use strict';

$(function() {

    /*
    |--------------------------------------------------------------------------
    | Parallax Elements
    |--------------------------------------------------------------------------
    */

    var scene = document.getElementById('js-parallax');
    var parallax = new Parallax(scene);

    /*
    |--------------------------------------------------------------------------
    | Masked Phone
    |--------------------------------------------------------------------------
    */
    $(".js-masked-phone").mask("+7(999)999-99-99",{placeholder:" "});

    /*
    |--------------------------------------------------------------------------
    | Subscribe Modal
    |--------------------------------------------------------------------------
    */

    var is_shown_modal = false;

    // Call Function in 1000 sec
    let timerId = setTimeout(function(){
        if(is_shown_modal == false){
            $('#modal-subscribe').modal('show');
            is_shown_modal = true;
        }
    }, 14000);

    $(window).scroll(function() {
        if(is_shown_modal == false){
            var oTop = $('#plans').offset().top - window.innerHeight;
            if ($(window).scrollTop() > oTop) {
                $('#modal-subscribe').modal('show');
                is_shown_modal = true;
                clearTimeout(timerId);
            }
        }
    });

    /*
    |--------------------------------------------------------------------------
    | Mobile menu
    |--------------------------------------------------------------------------
    */

    // Click on burger button
    $('.jsBurgerMenu').click(function() {

        // Toggle Open Class
        $(this).toggleClass('-open');

        // Toggle Mobile Menu
        $('.m-menu').toggleClass('-show');

        // Disable Scroll On Body
        if ($(this).hasClass('-open')) {
            $('body').css({"overflow": "hidden"});
        } else {
            $('body').css({"overflow": ""});
        }
    });

    /*
    |--------------------------------------------------------------------------
    | Responsive Iframe Inside Modal
    |--------------------------------------------------------------------------
    */

    function toggle_video_modal() {

        // Click on video thumbnail or link
        $(".jsTriggerVideoModal").on("click", function(e){

            // prevent default behavior for a-tags, button tags, etc.
            e.preventDefault();

            // Grab the video ID from the element clicked
            var id = $(this).attr('data-youtube-id');

            // Autoplay when the modal appears
            // Note: this is intetnionally disabled on most mobile devices
            // If critical on mobile, then some alternate method is needed
            var autoplay = '?autoplay=1';

            // Don't show the 'Related Videos' view when the video ends
            var related_no = '&rel=0';

            // String the ID and param variables together
            var src = '//www.youtube.com/embed/'+id+autoplay+related_no;

            // Pass the YouTube video ID into the iframe template...
            // Set the source on the iframe to match the video ID
            $("#youtube").attr('src', src);

            // Add class to the body to visually reveal the modal
            $("body").addClass("show-video-modal noscroll");

        });

        // Close and Reset the Video Modal
        function close_video_modal() {

            event.preventDefault();

            // re-hide the video modal
            $("body").removeClass("show-video-modal noscroll");

            // reset the source attribute for the iframe template, kills the video
            $("#youtube").attr('src', '');

        }
        // if the 'close' button/element, or the overlay are clicked
        $('body').on('click', '.close-video-modal, .video-modal .overlay', function(event) {

            // call the close and reset function
            close_video_modal();

        });
        // if the ESC key is tapped
        $('body').keyup(function(e) {
            // ESC key maps to keycode `27`
            if (e.keyCode == 27) {

                // call the close and reset function
                close_video_modal();

            }
        });
    }
    toggle_video_modal();

    /*
    |--------------------------------------------------------------------------
    | Smooth Scroll
    |--------------------------------------------------------------------------
    */

    $('.page-scroll').on('click', function(event) {
        if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
            let target = $(this.hash),
                speed = $(this).data("speed") || 800;
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 0
                }, speed);
            }
        }
    });

    /*
    |--------------------------------------------------------------------------
    | Sticky Header
    |--------------------------------------------------------------------------
    */

    var fixedBlocks = document.querySelectorAll('.sticky');

    function positionCalculation(index){
        var posTop = 0;
        for(var i = index; i > -1; i--){
            posTop += fixedBlocks[i].getBoundingClientRect().height;
        }
        return posTop;
    }

    function neighborHeight(i){
        if(i > 0){
            return fixedBlocks[i-1].getBoundingClientRect().top + fixedBlocks[i-1].getBoundingClientRect().height;
        }
        return 0;
    }

    function positionDetermination(){
        for(var i = 0; i < fixedBlocks.length; i++){
            if(fixedBlocks[i].getBoundingClientRect().top + window.pageYOffset  <= window.pageYOffset + neighborHeight(i)){
                if(!fixedBlocks[i].spacer){
                    fixedBlocks[i].spacer = document.createElement('div');
                    fixedBlocks[i].spacer.style.position = 'static';
                    fixedBlocks[i].spacer.style.width = fixedBlocks[i].getBoundingClientRect().width + 'px';
                    fixedBlocks[i].spacer.style.height = fixedBlocks[i].getBoundingClientRect().height + 'px';
                    fixedBlocks[i].spacer.style.display = 'block';
                    fixedBlocks[i].spacer.style.verticalAlign = 'baseline';
                    fixedBlocks[i].spacer.style.float = 'none';

                    fixedBlocks[i].parentNode.insertBefore(fixedBlocks[i].spacer, fixedBlocks[i]);
                }

                fixedBlocks[i].classList.add(fixedBlocks[i].dataset.classFixed);
                if(i > 0){
                    fixedBlocks[i].style.top =  positionCalculation(i-1) + 'px';
                }

                if(fixedBlocks[i].getBoundingClientRect().top <= fixedBlocks[i].spacer.getBoundingClientRect().top){
                    fixedBlocks[i].parentNode.removeChild(fixedBlocks[i].spacer);
                    fixedBlocks[i].classList.remove(fixedBlocks[i].dataset.classFixed);
                    fixedBlocks[i].spacer = null;
                }
            }
        };
    };

    positionDetermination();

    document.addEventListener('scroll', function(){
        positionDetermination();
    });

    /*
    |--------------------------------------------------------------------------
    | Spoiler Text
    |--------------------------------------------------------------------------
    */

    let containerHeight = document.querySelectorAll(".jsSpoilerInner");
    let uncoverLink = document.querySelectorAll(".jsSpoilerMore");

    for(let i = 0; i < containerHeight.length; i++){
        let openData = uncoverLink[i].dataset.open;
        let closeData = uncoverLink[i].dataset.close;
        let curHeight = containerHeight[i].dataset.height;

        uncoverLink[i].innerHTML = openData;
        containerHeight[i].style.maxHeight = curHeight + "px";

        uncoverLink[i].addEventListener("click", function(){
            if(containerHeight[i].classList.contains("-open")){

                containerHeight[i].classList.remove("-open");

                uncoverLink[i].innerHTML = openData;

                containerHeight[i].style.maxHeight = curHeight + "px";

            } else {
                containerHeight[i].removeAttribute("style");

                containerHeight[i].classList.add("-open");

                uncoverLink[i].innerHTML = closeData;

            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Features Slider
    |--------------------------------------------------------------------------
    */

	let featuresSlider = new Swiper('.jsFeaturesSlider', {
		speed: 600,
		mousewheel: false,
		loop: false,
		spaceBetween: 0,
		navigation: {
			nextEl: '.jsFeaturesSliderNext',
			prevEl: '.jsFeaturesSliderPrev',
		},
		slidesPerView: 1,
        pagination: {
            el: '.jsFeaturesPagination',
        },
	});

    /*
    |--------------------------------------------------------------------------
    | Why Desktop Slider
    |--------------------------------------------------------------------------
    */

    let whyDesktopSlider = new Swiper('.jsWhyDesktopSlider', {
        speed: 600,
        mousewheel: false,
        loop: false,
        spaceBetween: 0,
        navigation: {
            nextEl: '.jsWhyDesktopSliderNext',
            prevEl: '.jsWhyDesktopSliderPrev',
        },
        slidesPerView: 1,
        pagination: {
            el: '.jsWhyDesktopPagination',
        },
    });

    /*
    |--------------------------------------------------------------------------
    | Why Slider
    |--------------------------------------------------------------------------
    */

    let whySlider = new Swiper('.jsWhySlider', {
        speed: 600,
        mousewheel: false,
        loop: false,
        spaceBetween: 0,
        navigation: {
            nextEl: '.jsWhySliderNext',
            prevEl: '.jsWhySliderPrev',
        },
        slidesPerView: 1,
        pagination: {
            el: '.jsWhyPagination',
        },
    });

    /*
    |--------------------------------------------------------------------------
    | Light Gallery
    |--------------------------------------------------------------------------
    */

	$('.lg').lightGallery({
		selector: ".lg__item",
	});

    /*
    |--------------------------------------------------------------------------
    | Polyfill object-fit/object-position on <img>: IE9, IE10, IE11, Edge, Safari, ...
    | https://github.com/bfred-it/object-fit-images
    |--------------------------------------------------------------------------
    */

    objectFitImages();
    // if you use jQuery, the code is: $(function () { objectFitImages() });

});
