var 		fAutoslide=true;

$(document).ready(function(){
	function getDecimal(num){
		return num-Math.floor(num);
	}
	function initProCarousel(){
		$('.pro-car-out').each(function(){
			var carP=$(this);
			var car=carP.find('.pro-carousel');
			var carouselType=+car.attr('data-type');
			var slideBox=car.find('.slide-box');
			var outerW=carP.outerWidth();
			var slide=car.find('.slide');
			var slideL=slide.length;
			var left=0;
			var slideBoxW=0;
			var pages=0;
			var slideOnPage=0;
			var slideRW=+car.find('.slide').outerWidth();
			var positionCar=0;
			var activePag=0;
			switch(carouselType){
				case 1:
					$(car).find('.slide').css({'margin-right': 0, 'margin-left': 0});
					carP.css('max-width',$(document).outerWidth());
					//забиваем ширину слайда
					$(car).find('.slide-box').css({position:'relative',float:'left',width:slideL*outerW});
					$(car).find('.slide').css({float:'left',width:outerW});
					outerW=$(car).outerWidth();
					$(car).find('.slide-box').css({position:'relative',float:'left',width:slideL*outerW});
					$(car).find('.slide').css({float:'left',width:outerW});
					$(car).find('.slide-box').css('left',0);
					slideBoxW=$(slideBox).outerWidth();
					pages=$(car).find('.slide').length;
					//кнопки
					positionCar=0;
					$(carP).find('div.car-btn.left').unbind('click').on('click',function(){
						left=Math.floor(positionCar);
						if((left+outerW)<=0){
							positionCar=positionCar+outerW;
							$(slideBox).animate({'left':left+outerW});
							$(carP).find('.pag li.active').removeClass('active').prev().addClass('active');
						}else{
							positionCar=-$(slideBox).outerWidth()+outerW;
							$(slideBox).animate({'left':-$(slideBox).outerWidth()+outerW});
							$(carP).find('.pag li.active').removeClass('active').siblings('li:last-child').addClass('active');
						}
					});
					$(carP).find('div.car-btn.right').unbind('click').on('click',function(){
						left=Math.floor(positionCar);
						if(((left-outerW)>= -($(slideBox).outerWidth()-outerW))){
							positionCar=positionCar-outerW;
							$(slideBox).animate({'left':left-outerW});
							$(carP).find('.pag li.active').removeClass('active').next().addClass('active');
						}else{
							positionCar=0;
							$(slideBox).css('left',0);
							$(carP).find('.pag li.active').removeClass('active').siblings('li:first-child').addClass('active');
						}
					});

					positionCar=0;
					//пагинация
					if($(carP).find('.pag').length){
						$(carP).find('.pag').html('');
						var stepSize = 0;
						$(carP).find('ul.pag li').remove();
						for (var i = 0; i < pages; i++) {
							$(carP).find('.pag').append('<li data-id="' + i + '"></li>');
						}
						$(carP).find('ul.pag li:first-child').addClass('active');
						$(carP).find('ul.pag li').each(function () {
							$(this).attr('data-page', stepSize++).unbind('click').on('click', function () {
								$(carP).find('ul.pag li').removeClass('active');
								$(this).addClass('active');
								var pagePag = $(this).attr('data-page');
								positionCar = -pagePag * outerW;
								$(this).parents('.pro-car-out').find('.slide-box').animate({'left':positionCar + 'px'});
							});
						});
						if (pages == 1) {
							$(carP).find('.pag').css('display', 'none');
							$(carP).find('.car-btn').css('display', 'none');
						}
						if(fAutoslide){
							var fHover=false;
							carP.hover(function(){
								fHover=true;
							},function(){
								fHover=false;
							});
							var pagL=$(carP).find('ul.pag li').length;

							setInterval(function(){
								if(!fHover){
									var cur = $(carP).find('ul.pag li.active').index();
									cur++;
									if(cur>pagL-1){
										cur=0;
									}
									$(carP).find('ul.pag li').eq(cur).click();
								}
							},5000);
							fAutoslide=false;
						}

					}

					break;
				case 2:
					carP.css('max-width',$(document).outerWidth());
					$(slide).attr('style','');
					var slideWM = $(slide).outerWidth(true); /*ширина блока с маржином справа*/
					var slideW = $(carP).find('.slide:first-child').outerWidth(); /*реальная ширина блока*/
					/*var margin = slideWM-slideW;*/
					/*проверяем нужно ли показываться кнопки и формировать прокрутку*/
					if((outerW/(slideWM*slideL))<1){
						$(car).find('.slide').css({'margin-right': 0,'margin-left': 0}); /*обнуление отступов*/
						/*да*/
						var sum=0;
						for (var i=0;i<slideL;i++){
							sum+=slideWM;
							if(sum<outerW){
								slideOnPage+=1;
							}
						}
						var freeMargin=Math.round(((outerW-(slideOnPage*slideW))/(slideOnPage-1)));
						$(car).find('.slide').css("margin-right",freeMargin+'px');
						slideWM = $(slide).outerWidth(true); /*ширина блока с маржином справа*/

						$(car).find('.slide-box').css({width: (slideL * slideWM + 5)*2});
						/*кнопки*/
						var isAnimated=false;
						$(carP).find('div.car-btn').unbind('click').on('click',function(e){
							if(isAnimated) return false;
							isAnimated = true;
							if($(this).hasClass('right')){
								var clone = $(carP).find('.slide:last-child').clone(true);
								$(carP).find('.slide:last-child').remove();
								$(carP).find('.slide-box').css({'left':-slideWM}).prepend(clone);
								$(carP).find('.slide-box').animate({'left':0},300,function(){
									isAnimated=false;
								});
							}else{
								var clone2 = $(carP).find('.slide:first-child').clone(true);
								$(carP).find('.slide-box').animate({'left':-slideWM},300,function(){
									$(carP).find('.slide:first-child').remove();
									$(carP).find('.slide-box').css({'left':0}).append(clone2);
									isAnimated=false;
								});
							}
						});
						var intervalId = setInterval(function(){
							$(carP).find('.car-btn.right').click();
						},9000);
						$(carP).hover(
							function(){
                                clearInterval(intervalId);},
							function(){
                                intervalId = setInterval(function(){
                                $(carP).find('.car-btn.right').click();
							},9000);});
					}else{
						/*нет*/
						$(carP).find('.pag').css('display', 'none');
						$(carP).find('.car-btn').css('display', 'none');
					}
			}
		});
	}
	function proCarouselStart(){
		if($('.pro-carousel').length){
			$('.pro-carousel').each(function(){
				var carStart=$(this);
				if(carStart.next().hasClass('pag')){
					carStart.add(carStart.next()).wrapAll("<div class='pro-car-out'> </div>");
				}else{
					carStart.wrap("<div class='pro-car-out'> </div>");
				}
				carStart.children().wrapAll("<div class='slide-box'> </div>");
				/*
				 1 - конпки
				 2- пагинация
				 3- все
				 */
				if(carStart.attr('data-controls')==1){
					carStart.parent().append('<div class="car-btn left">&nbsp;</div><div class="car-btn right">&nbsp;</div>');
				}else{
					if(carStart.attr('data-controls')==2){
						carStart.parent().append('<ul class="pag">&nbsp;</ul>');
					}else{
						if(carStart.attr('data-controls')==3){
							carStart.parent().append('<div class="car-btn left">&nbsp;</div><div class="car-btn right">&nbsp;</div><ul class="pag">&nbsp;</ul>');
						}
					}
				}

			});
			initProCarousel();
		}
	}

	if($('.pro-carousel').length){
		proCarouselStart();
		$(window).resize(function(){
			initProCarousel();
		});
	}
});
