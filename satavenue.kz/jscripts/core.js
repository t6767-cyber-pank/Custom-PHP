var eTop;
var offset=0;
var myMap=null;
var MarkerWidth=43/1.7*1.3;
var MarkerHeight=48/1.7*1.3;
var HeaderHeight=0;
var mysitekey='6LeW1iYUAAAAAIXb2RsieWwiKh4bTetNn9qDoDZP';
var recaptcha1;
var recaptcha2;
var recaptchaProductOrder;
function onloadCallback(){
	if ($('#recaptcha1').length) {
		recaptcha1=grecaptcha.render('recaptcha1',{
			'sitekey':mysitekey
		});
	}
	if ($('#recaptcha2').length) {
		recaptcha2=grecaptcha.render('recaptcha2',{
			'sitekey':mysitekey
		});
	}
	if ($('#recaptchaForm').length) {
		recaptchaProductOrder=grecaptcha.render('recaptchaForm',{
			'sitekey':mysitekey
		});
	}
}
String.prototype.DataToArray=function(){
	var Str=this.split("&");
	var NewArray=[];
	for(var i=0; i<Str.length; i++){
		Str[i]=Str[i].split("=");
		NewArray[Str[i][0]]=Str[i][1];
	}
	return NewArray;
};
String.prototype.AddZero=function(){
	var Str=String(this);
	if(Str.length<2){
		Str="0"+Str;
	}
	return Str;
};
Array.prototype.ArrayToData=function(){
	var NewStr=[];
	for(var key in this){
		if((typeof this[key])=="string"){
			NewStr[NewStr.length]=key+"="+this[key];
		}
	}
	NewStr=NewStr.join("&");
	return NewStr;
};
Array.prototype.inArray=function(needle){
	var length=this.length;
	for(var i=0; i<length; i++){
		if(this[i]==needle) return true;
	}
	return false;
};
function DigitalToPrice(InputPrice){
	var ListingPrice=[];
	var FirstLength;
	var StartPosition;
	var FirstValue;
	var result;
	var Digitals=3;
	var Delim=" ";
	InputPrice=parseInt(InputPrice);
	var Parts;
	if(InputPrice>100){
		Parts=Math.floor(String(InputPrice).length/Digitals);
		FirstLength=String(InputPrice).length-Parts*Digitals;
		for(var i=Parts-1; i>=0; i--){
			StartPosition=FirstLength+i*Digitals;
			ListingPrice.push(String(InputPrice).substr(StartPosition,Digitals));
		}
		FirstValue=String(InputPrice).substr(0,FirstLength);
		if(FirstValue!==""){
			ListingPrice.push(String(InputPrice).substr(0,FirstLength));
		}
		ListingPrice.reverse();
		result=ListingPrice.join(Delim);
		return result;
	}else{
		return InputPrice;
	}
}
function ScrollingEvent(){
	offset=$(window).scrollTop()-eTop;
	if(offset>=HeaderHeight){
		$("body").addClass("fixed");
	}else{
		$("body").removeClass("fixed");
	}
}
function MobileViewControls(){
	if($(window).width()<1200){
		var MainMenuItems=$("menu.main div.items");
		var MainMenuItemsScroll=$("<div class=\"menuScroll\"></div>");
		$(MainMenuItems).before(MainMenuItemsScroll);
		$(MainMenuItemsScroll).append(MainMenuItems);
		$("menu.categories div.item").unbind("focus").focus(function(){
			return false;
		});
		$("menu.categories div.item>a").unbind("click").click(function(){
			if($(this).next().is("ul"))
				return false;
		});
		$("menu.main a.menu").unbind("click").click(function(){
			$("menu.categories").toggleClass("visible");
			return false;
		});
		var SearchItems=$("section.search div.search form input");
		$(SearchItems).each(function(){
			var className=$(this).attr("class");
			var box=$("<div class=\""+className+"\"></div>");
			$(this).before(box);
			$(box).append(this);
		});
		var BasketBlock=$("#basketInHeader");
		$("menu.main div.menuScroll").before(BasketBlock);
		$("section.textPage table").each(function(){
			$(this).replaceWith($("<div class=\"scroll\"></div>").append($(this).clone()));
		});
	}
}
function ResizeEvent(){
	ScrollingEvent();
}
function CloseEventButton(IgnorePopup){
	$("#callBack").unbind("click").click(function(e){
		e.stopPropagation();
	});
	if(IgnorePopup!=="callBack"){
		$("#callBack").removeClass("visible");
	}
}
function PopupBoxCloseButtonAction(){
	$(".popupBox,.popup").each(function(){
		var popup=this;
		$(this).unbind("click").click(function(e){
			e.stopPropagation();
			if($(e.toElement).hasClass("popupBox")){
				$(this).find("a.close").click();
			}
		});
		$(popup).find("a.close").unbind("close").click(function(){
			$(popup).removeClass("visible");
			if($(".popupBox.visible").length||$(".popup.visible").length){
				/*	DO NOTHING	*/
			}else{
				$("body").removeClass("novf");
			}
			return false;
		});
	});
}
function MakeBlockCarousel(rootElement,autoAnimation){
	var areaList=$(rootElement).find("div.list");
	var arenaIWidth;
	var canScroll=true;
	var animationInterval=0;
	clearInterval(animationInterval);
	if(autoAnimation){
		animationInterval=setInterval(function(){
			$(rootElement).find("a.arrow.right").click();
		},7000);
	}
	$(rootElement).find("a.arrow").unbind("click").click(function(){
		arenaIWidth=$(areaList).find(".item").outerWidth();
		var left=parseInt($(areaList).css("left"));
		if(!canScroll){
			return false;
		}
		if($(this).hasClass("right")){
			left=(-1)*arenaIWidth;
			$(areaList).find(".item:first").clone().appendTo(areaList);
			setTimeout(function(){
				$(areaList).find(".item:first").remove();
				$(areaList).addClass("ntr").css("left",left+arenaIWidth);
				$(areaList).removeClass("ntr");
				canScroll=true;
			},200);
		}else if($(this).hasClass("left")){
			left=0;
			$(areaList).removeClass("ntr");
			$(areaList).css("left",(-1)*arenaIWidth);
			$(areaList).find(".item:last").prependTo(areaList);
			setTimeout(function(){
				canScroll=true;
			},200);
		}
		canScroll=false;
		setTimeout(function(){
			$(areaList).addClass("ntr").css("left",left);
		},20);
		AddPhotoClickEvent(rootElement);
		return false;
	});
}
function AddPhotoClickEvent(elem){
	PhotoBoxInit();
}
function TabsInit(){
	$(".tabs").each(function(){
		var tabs=$(this).find(".tabItems a");
		var tabItems=$(this).find(".tabContainer>div");
		$(tabs).each(function(num){
			var tabNum=num;
			$(this).unbind("click").click(function(){
				$(tabs).removeClass("current");
				$(this).addClass("current");
				$(tabItems).removeClass("current");
				$(tabItems[tabNum]).addClass("current");
				return false;
			});
		});
	});
}
function PhotoBoxInit(){
	$(".carousel").each(function(){
		var Carousel=this;
		$(Carousel).find("a.item").unbind("click").click(function(){
			CloseEventButton("Photos");
			var url=$(this).attr("href");
			var img=$("<img src=\""+url+"\" />");
			$("#photoPopup div.photo").html("");
			$("#photoPopup div.photo").append(img);
			$("#photoPopup").parent().addClass("visible");
			return false;
		});
	});
}
function DisqusInit(){
	if($("#disqus_thread").length){
		var canonical=$("link[rel=\"canonical\"]").attr("href");
		window.disqus_config=function(){
			this.page.url=canonical;
			this.page.identifier=canonical;
		};
		(function(){
			var d=document,s=d.createElement('script');
			s.src='//topdocme.disqus.com/embed.js';
			s.setAttribute('data-timestamp',+new Date());
			(d.head||d.body).appendChild(s);
		})();
	}
}
function PlaceholdersInit(){
	$("[placeholder]").unbind("focusin").unbind("focusout").focusin(function(){
		$(this).data("placeholder",$(this).attr("placeholder"));
		$(this).attr("placeholder","");
	}).focusout(function(){
		$(this).attr("placeholder",$(this).data("placeholder"));
	});
}
function BlockSliderInit(){
	$(".blockSlider").each(function(){
		var buttons=$(this).find(".pages a");
		var slides=$(this).find(".container");
		var slide=$(slides).find(".items");
		var width=$(slide).width();
		$(buttons).each(function(num){
			var n=num;
			$(this).unbind("click").click(function(){
				var left=n*width;
				$(slides).css("left",-1*left-29*n);
				$(buttons).removeClass("current");
				$(this).addClass("current");
				return false;
			});
		});
	});
}
function SignInUpInit(){
	if($("section.signInUp,section.signBlock").length){
		var Tabs=$("section.signInUp div.tabs a.tab");
		var TabItems=$("section.signInUp div.tabItems div.tab");
		$(Tabs).unbind("click").click(function(){
			switch($(this).attr("href")){
				case '#signUp':
					yaCounter44499730.reachGoal('checkin');
					ga('send','event','Click','Fill','Checkin');
					break;
				case '#signIn':
					yaCounter44499730.reachGoal('entrance');
					ga('send','event','Click','Fill','Entrance');
					break;
				case '#restore':
					yaCounter44499730.reachGoal('reestablish');
					ga('send','event','Click','Fill','Reestablish');
					break;
			}
			var id=$(this).attr("href").split("#");
			id=id[1];
			$(Tabs).removeClass("current");
			$(this).addClass("current");
			$(TabItems).removeClass("current");
			$(TabItems).filter("#"+id).addClass("current");
			return false;
		});
		var forms=$("section.signInUp form,section.signBlock form");
		$("section.signInUp form,section.signBlock form").unbind("submit").submit(function(){
			var form=$(this);
			if(form.attr('action')=='?signup'){
				yaCounter44499730.reachGoal('checkin2');
				ga('send','event','Click','Fill','Checkin2');
			}
			if(form.attr('action')=='?signin'){
				yaCounter44499730.reachGoal('entrance2');
				ga('send','event','Click','Fill','Entrance2');
			}
			if(form.attr('action')=='?restore'){
				yaCounter44499730.reachGoal('reestablish2');
				ga('send','event','Click','Fill','Reestablish2');
			}
			var data=$(this).serialize();
			var url=$(this).attr("action")+"&ajax";
			$(form).find(".message").remove();
			form[0].reset();
			$.ajax({
				type:"post",
				cache:false,
				data:data,
				url:url,
				success:function(html){
					if(html.substr(0,3)=="OK:"){
						var url=html.substr(3);
						window.location=url;
					}else{
						$(form).prepend(html);
					}
				}
			});
			return false;
		});
	}
}
function CallBackFormInit(){
	$("#callBack form").unbind("submit").submit(function(){
		var form=this;
		var data=$(this).serialize();
		var url=$(this).attr("action")+"&ajax";
		yaCounter44499730.reachGoal('backcall2');
		ga('send','event','Backcall2','Click','Send');
		$(form).find(".message").remove();
		var captcha='';
		var id=$(this).attr('id');
		switch(id){
			case 'frecaptcha1':
				captcha=grecaptcha.getResponse(recaptcha1);
				break;
		}
		if(!captcha.length){
			$('#recaptchaError').text('* Вы не прошли проверку "Я не робот"');
		}else{
			$('#recaptchaError').text('');
		}
		if(captcha.length){
			$.ajax({
				type:"post",
				cache:false,
				data:data,
				url:url,
				success:function(html){
					$(form)[0].reset();
					$(form).prepend(html);
					setTimeout(function(){
						$('#callBack').removeClass('visible');
					},1000);
				}
			});
		}
		return false;
	});
}
function AddFundsFormInit(){
	var page=$("section.addFunds");
	var tabs=$(page).find("div.tabs a.tab");
	var brandItems=$(page).find(".brandTabs .brand");
	var brandLabels=$(page).find("label.brand");
	$(tabs).unbind("click").click(function(){
		var id=$(this).attr("href").split("#");
		var brand=$(this).data("brand");
		id=id[1];
		$(tabs).removeClass("current");
		$(this).addClass("current");
		$("input#brand").attr("value",brand);
		$(brandItems).removeClass("current");
		$(brandItems).filter("."+id).addClass("current");
		$(brandLabels).removeClass("current");
		$(brandLabels).filter("."+id).addClass("current");
		if(typeof($(page).find(tabs).attr('href')) != "undefined"){
			var current = $("div.tabs a.tab.current").attr('href');
			switch(current){
				case '#thuraya':
					$('#phone').inputmask({"mask": "+8(8216711)-99-999"});
					break;
				case '#iridium':
					$('#phone').inputmask({"mask": "+8(816)-9999-9999"});
					break;
				case '#inmarsat':
					$('#phone').inputmask({"mask": "+8(707)-9999-9999"});
					break;
				default:
					$('#phone').inputmask({"mask": "+8(8216711)-99-999"});
			}
			$('#mobile').inputmask({"mask": "+7(999)-999-9999"});
		}
		if($(this).find("span").text()=="Inmarsat"){
			$("div.rightBlock p.hide").addClass("h");
		}else{
			$("div.rightBlock p.hide").removeClass("h");
		}
		return false;
	});
}
function BasketButtonsInit(){
	$(".product[data-id]").each(function(){
		var id=$(this).data("id");
		var button=$(this).find("a.basket");
		$(button).data("value",$(button).text());
		$(this).find("a.basket").unbind("click").click(function(){
			if($(this).attr("href")=="#"){
				$.ajax({
					type:"post",
					cache:false,
					data:"product="+id,
					url:"/basket/?add&ajax",
					success:function(html){
						html=html.split(":SEP:");
						if(html[0]=="OK"){
							$("#basketInHeader a span.amount").html(html[1]);
							$("#basketInHeader a span.total").html(html[2]);
							$(button).text(langpack.InBasket);
							setTimeout(function(){
								$(button).text($(button).data("value"));
							},3000);
						}
						$('#after-buy-modal').modal('show');
					}
				});
				return false;
			}
		});
	});
	$(".productInfo form.order").unbind("submit").submit(function(){
		var form=this;
		var data=$(form).serialize();
		var url=$(form).attr("action")+"&ajax";
		var button=$(form).find("input.submit");
		$(button).data("value",$(button).val());
		$.ajax({
			type:"post",
			cache:false,
			data:data,
			url:url,
			success:function(html){
				$(form)[0].reset();
				html=html.split(":SEP:");
				if(html[0]=="OK"){
					$("#basketInHeader a span.amount").html(html[1]);
					$("#basketInHeader a span.total").html(html[2]);
					$(button).val(langpack.InBasket);
					setTimeout(function(){
						$(button).attr("value",$(button).data("value"));
					},3000);
				}
				$('#after-buy-modal').modal('show');
			}
		});
		return false;
	});
	$(".simServices form.basket").each(function(){
		var form=this;
		var button=$(this).find("input.submit");
		$(button).data("value",$(button).val());
		$(form).unbind("submit").submit(function(){
			var data=$(this).serialize();
			var url=$(this).attr("action")+"&ajax";
			$.ajax({
				type:"post",
				cache:false,
				data:data,
				url:url,
				success:function(html){
					$(form)[0].reset();
					html=html.split(":SEP:");
					if(html[0]=="OK"){
						$("#basketInHeader a span.amount").html(html[1]);
						$("#basketInHeader a span.total").html(html[2]);
						$(button).val(langpack.InBasket);
						setTimeout(function(){
							$(button).attr("value",$(button).data("value"));
						},3000);
						$('#after-buy-modal').modal('show');
					}else{
						alert(html);
					}
				}
			});
			return false;
		});
	});
}
function BasketPageInit(){
	var Basket=$(".basketInfo");
	var BasketForm=$(Basket).find("form.step1");
	$(BasketForm).find(".item").each(function(){
		var line=$(this);
		$(line).find(".delete").unbind("click").click(function(){
			$(line).remove();
			$(BasketForm).change();
			return false;
		});
	});
	$(BasketForm).find(".promo .submit").unbind("click").click(function(){
		$(BasketForm).change();
		return false;
	});
	$(BasketForm).unbind("change").change(function(){
		var data=$(this).serialize();
		var url="/basket/?update";
		$.ajax({
			type:"post",
			cache:false,
			data:data,
			url:url,
			success:function(html){
				html=html.split(":SEP:");
				$(BasketForm).find(".actions").replaceWith(html[0]);
				$(BasketForm).find(".list").html(html[1]);
				$("#basketInHeader a span.amount").html(html[2]);
				$("#basketInHeader a span.total").html(html[3]);
				BasketPageInit();
			}
		});
		return false;
	});
	var Tabs=$(Basket).find(".tabs");
	var TabItems=$(Tabs).next().find(">div.tab");
	$(Tabs).find(".tab").each(function(num){
		var TabNum=num;
		$(this).unbind("click").click(function(){
			$(Tabs).find(".tab").removeClass("current");
			$(this).addClass("current");
			$(TabItems).removeClass("current");
			$(TabItems[TabNum]).addClass("current");
			CheckBasketRequired();
			return false;
		});
	});
	var DeliveryBlocks=$(Basket).find("div.delivery");
	$(DeliveryBlocks).each(function(){
		var DeliveryBlock=this;
		var DeliveryForms=$(DeliveryBlock).next();
		if($(DeliveryForms).is(".deliveryForm")){
			var DeliveryTabs=$(DeliveryBlock).find("input");
			var DeliveryItems=$(DeliveryForms).find(">div");
			var PaymentItems=$(".payMethods input");
			var PaymentLabels=$(".payMethods label");
			/*$(DeliveryTabs).each(function(num){
			 var TabNum=num;
			 $(this).unbind("change").change(function(){
			 var AddressCheckbox=$(DeliveryItems[TabNum]).find("div.address input");
			 $(DeliveryItems).removeClass("current");
			 $(DeliveryItems[TabNum]).addClass("current");

			 CheckBasketRequired();
			 $(AddressCheckbox).unbind("change").change(function(){
			 if($(this).is(":checked")){
			 $(DeliveryItems[TabNum]).find("div.line").addClass("hidden");
			 }else{
			 $(DeliveryItems[TabNum]).find("div.line").removeClass("hidden");
			 }

			 CheckBasketRequired();

			 return false;
			 });
			 $(this).unbind("change").change(function(){
			 if(num==1){
			 $(PaymentItems[0]).addClass("hidden").removeAttr("checked");
			 $(PaymentLabels[0]).addClass("hidden");
			 }else{
			 $(PaymentItems).removeClass("hidden");
			 $(PaymentLabels).removeClass("hidden");
			 }

			 CheckBasketRequired();

			 return false;
			 });
			 });
			 });*/
			$(DeliveryTabs).filter(":checked").change();
		}
	});
	var PayMethodBlocks=$(".payMethods");
	$(PayMethodBlocks).each(function(){
		var PayMethodBlock=this;
		var PayMethodForms=$(PayMethodBlock).next();
		if($(PayMethodForms).is(".payTabs")){
			var PayMethodsTabs=$(PayMethodBlock).find(">input");
			var PayMethodsItems=$(PayMethodForms).find(">div");
			$(PayMethodsTabs).each(function(num){
				var TabNum=num;
				$(this).unbind("change").change(function(){
					$(PayMethodsItems).removeClass("current");
					$(PayMethodsItems[TabNum]).addClass("current");
					CheckBasketRequired();
				});
			});
		}
	});
	var PrepaymentBlocks=$(Basket).find(".payPrepayment");
	var PrepaymentTabs=$(PrepaymentBlocks).find(".payMethods>input");
	var PrepaymentItems=$(PrepaymentBlocks).find(".payTabs>div");
	$(PrepaymentTabs).each(function(num){
		var TabNum=num;
		$(this).unbind("change").change(function(){
			$(PrepaymentItems).removeClass("current");
			$(PrepaymentItems[TabNum]).addClass("current");
			if(num==2){
				$.ajax({
					type:"post",
					cache:false,
					url:"?getPayRequest",
					success:function(data){
						$("input#base64").attr("value",data);
					}
				});
			}else{
				$("input#base64").attr("value","");
			}
			CheckBasketRequired();
		});
	});
	/*var BasketFormStep2=$(Basket).find("form.step2");

	 $(BasketFormStep2).unbind("submit").submit(function(){
	 var form=this;
	 var data=$(this).serialize();
	 var url=$(form).attr("action")+"&ajax";

	 $(form).find(".error").removeClass("error");

	 $.ajax({
	 type:"post",
	 cache:false,
	 data:data,
	 url:url,
	 success: function(html){
	 html=html.split(":SEP:");
	 */
	/*if(html[0]=="ONLINE" || html[0]=="BANK"){
	 console.log(html[1]);
	 window.location=html[1];
	 }else */
	/*if(html[0]=="OK"){
	 window.location=html[1];
	 }else if(html[0]=="ERROR"){
	 html=html[1].split(",");

	 for(var e=0;e<html.length;e++){
	 $(form).find("[name=\""+html[e]+"\"]").addClass("error");
	 }

	 alert(langpack.BasketFieldsError);
	 }
	 }
	 });

	 return false;
	 });*/
	if($(".basketHistory").length){
		var Baskets=$(".basketHistory .table[data-id]");
		$(Baskets).each(function(){
			var Basket=this;
			var Button=$(Basket).find(".more a.more");
			$(Button).unbind("click").click(function(){
				$(Basket).toggleClass("current");
				$(Basket).next().toggleClass("visible");
				return false;
			});
		});
	}
	CheckBasketRequired();
}
function CheckBasketRequired(){
	var Basket=$(".basketInfo");
	var BasketForm=$(Basket).find("form.step2");
	var Items=$(BasketForm).find("[required],[data-required]");
	$(Items).each(function(){
		$(this).data("required",true).attr("data-required",true);
		if($(this).is(":visible")){
			$(this).attr("required",true);
		}else{
			$(this).attr("required",false);
		}
	});
}
function SubscribeFormInit(){
	$("#subscribe form").unbind("submit").submit(function(){
		var form=this;
		var url=$(this).attr("action")+"&ajax";
		var data=$(this).serialize();
		$(form).find(".message").remove();
		yaCounter44499730.reachGoal('subscribe');
		ga('send','event','Subscribe','Click','View');
		$.ajax({
			type:"post",
			cache:false,
			data:data,
			url:url,
			success:function(html){
				$(form)[0].reset();
				$(form).prepend(html);
			}
		});
		return false;
	});
}
/*
 * */
$(document).ready(function(){
	PopupBoxCloseButtonAction();
	PhotoBoxInit();
	TabsInit();
	DisqusInit();
	MobileViewControls();
	PlaceholdersInit();
	BlockSliderInit();
	BasketButtonsInit();
	BasketPageInit();
	toogleSearch();
	toogleNav();
	var PriceSlider=$("div.slider.price div.controls");
	if($(PriceSlider).length){
		var PriceBlock=$(PriceSlider).parent();
		var handle=$(PriceSlider).find(".handler");
		PriceSlider.slider({
			range:true,
			min:$(PriceSlider).data("min"),
			max:$(PriceSlider).data("max"),
			step:1,
			values:[$(PriceSlider).data("current-min"),$(PriceSlider).data("current-max")],
			slide:function(event,ui){
				$(PriceBlock).find("input#from").val(ui.values[0]);
				$(PriceBlock).find("input#to").val(ui.values[1]);
				$(handle).find("span").text(DigitalToPrice(ui.value));
				$(handle).css("left",$(ui.handle).position().left-$(handle).width()/2-4);
			},
			change:function(event,ui){
				$(PriceBlock).find("input#from").val(ui.values[0]);
				$(PriceBlock).find("input#to").val(ui.values[1]);
				$(handle).find("span").text(DigitalToPrice(ui.value));
				$(handle).css("left",$(ui.handle).position().left-$(handle).width()/2-4);
				$(PriceBlock).closest("form").submit();
			}
		});
		$(PriceSlider).find(".ui-slider-handle").focusin(function(){
			$(handle).addClass("visible");
		}).focusout(function(){
			$(handle).removeClass("visible");
		});
		var PriceControls=$(PriceBlock).find("input");
		$(PriceControls).change(function(){
			var PriceFrom=$(PriceControls).filter("#from").val();
			var PriceTo=$(PriceControls).filter("#to").val();
			$(PriceSlider).slider("option",{values:[PriceFrom,PriceTo]});
			return false;
		});
	}
	if($(".carousel").length){
		$(".carousel").each(function(){
			MakeBlockCarousel(this,false);
		});
	}
	if($("#listPanel").length){
		$("#listPanel form").change(function(){
			var form=$(this);
			var data=$(this).serialize();
			$.ajax({
				type:"get",
				cache:false,
				data:data,
				url:"?",
				success:function(html){
					form[0].reset();
					window.location=window.location;
				}
			});
		});
	}
	if($(".productList")){
		$(".productList .product").each(function(){
			var product=$(this);
			var photos=$(product).find(".list a");
			var bigPhoto=$(product).find(".cover img");
			$(photos).unbind("click").click(function(){
				$(bigPhoto).attr("src",$(this).attr("href"));
				return false;
			});
		});
	}
	if($(".productInfo").length){
		var ProductBlocks=$(".productInfo");
		var PhotoBlock=$(ProductBlocks).find(".photos");
		var Photos=$(PhotoBlock).find(".list a");
		var PhotoCover=$(PhotoBlock).find(".cover img");
		$(Photos).unbind("click").click(function(){
			$(PhotoCover).attr("src",$(this).attr("href"));
			return false;
		});
		var Tabs=$(ProductBlocks).find(".tabs a");
		var TabBlocks=$(ProductBlocks).find(".tabBlocks>.tab");
		$(Tabs).each(function(num){
			var TabNum=num;
			$(this).unbind("click").click(function(){
				$(TabBlocks).removeClass("current");
				$(TabBlocks[TabNum]).addClass("current");
				$(Tabs).removeClass("current");
				$(Tabs[TabNum]).addClass("current");
				return false;
			});
		});
		var OrderBlocks=$(ProductBlocks).find("form.order");
		var Options=$(OrderBlocks).find("input[name=\"optionId\"]");
		var Total=$(OrderBlocks).find(".total span.value");
		$(Options).unbind("change").change(function(){
			var TotalSum=DigitalToPrice($(this).data("price"));
			$(Total).text(TotalSum);
		});
		if($(OrderBlocks).hasClass("request")){
			$(OrderBlocks).unbind("submit").submit(function(){
				var form=$(this);
				var data=$(this).serialize();
				var url=$(this).attr("action")+"&ajax";
				$(OrderBlocks).find("div.form").find("div.message").remove();
				$.ajax({
					type:"post",
					cache:false,
					data:data,
					url:url,
					success:function(html){
						form[0].reset();
						$(OrderBlocks).find("div.form").prepend(html);
					}
				});
				return false;
			});
		}
	}
	if($("section.contactPage,div.postpaid").length){
		$("section.contactPage,div.postpaid").find("form").unbind("submit").submit(function(){
			var data=$(this).serialize();
			var url=$(this).attr("action")+"&ajax";
			var form=this;
			var captcha='';
			var id=$(this).attr('id');
			switch(id){
				case 'frecaptcha2':
					captcha=grecaptcha.getResponse(recaptcha2);
					break;
			}
			if(!captcha.length){
				$('#recaptchaError2').text('* Вы не прошли проверку "Я не робот"');
			}else{
				$('#recaptchaError2').text('');
			}
			if(captcha.length){
				$(form).find("div.message").remove();
				$.ajax({
					type:"post",
					cache:false,
					data:data,
					url:url,
					success:function(html){
						$(form)[0].reset();
						$(form).prepend(html);
					}
				});
			}
			return false;
		});
	}
	if($("section.simServices").length){
		var MainTabs=$("section.simServices .payTabs input");
		var MainTabItems=$("section.simServices>.tabItems>div.tab");
		var PayTypeTabs=$("section.simServices .tab.prepayment .typeItems input");
		var PayTypeItemsLeft=$("section.simServices .tab.prepayment .leftBlock .tabItems>.tab");
		var PayTypeItemsRight=$("section.simServices .tab.prepayment .rightBlock>.tab");
		$(MainTabs).unbind("change").change(function(){
			$(MainTabItems).removeClass("current");
			$(MainTabItems).filter("."+$(this).val()).addClass("current");
			return false;
		});
		$(PayTypeTabs).unbind("change").change(function(){
			$(PayTypeItemsLeft).removeClass("current");
			$(PayTypeItemsRight).removeClass("current");
			$(PayTypeItemsLeft).filter(".tab"+$(this).val()).addClass("current");
			$(PayTypeItemsRight).filter(".tab"+$(this).val()).addClass("current");
			return false;
		});
		var empty = $('.rightBlock').find('div').find('dl').length;
		if (empty <= 0) {
			$('.rightBlock').remove();
			$('.tabItems').find('.line').remove();
			$('.tabItems').find('.prepayment').addClass('no-before');
			$('.submit').find('input').remove();
		}
	}
	if($("#map").length){
		var map=$('div#map');
		$(map).html('');
		var GPS=langpack.GPS.split(",");
		ymaps.ready(function(){
			var myMap=new ymaps.Map('map',{
				center:[GPS[0],GPS[1]],
				zoom:16
			});
			myMap.controls.add('mapTools');
			myMap.controls.add('zoomControl');
			var myPlacemark=new ymaps.Placemark([GPS[0],GPS[1]],{
				hintContent:langpack.MapLabel
			},{
				iconImageHref:'/images/marker.png',
				iconImageSize:[30,45],
				iconImageOffset:[(-1)*Math.round(30/2),(-1)*45]
			});
			myMap.geoObjects.add(myPlacemark);
		});
	}
	SignInUpInit();
	AddFundsFormInit();
	SubscribeFormInit();
	$("#goTop").click(function(){
		$("html,body").animate({scrollTop:0},"slow");
		return false;
	});
	$("a.callback").unbind("click").click(function(){
		$("#callBack").toggleClass("visible");
		yaCounter44499730.reachGoal('backcall');
		ga('send','event','Backcall','Click','Send');
		return false;
	});
	CallBackFormInit();
	HeaderHeight=$("menu.main").height()-$("menu.main nav.mainMenu").height();
	eTop=0;
	$(window).scroll(function(){
		ScrollingEvent();
	}).resize(function(){
		ResizeEvent();
	});
	ScrollingEvent();
	ResizeEvent();
	$('menu.main.body .funds a').unbind('click').click(function(){
		yaCounter44499730.reachGoal('topupthebalance');
		ga('send','event','Replenish','Click','Balance');
	});
	$('section.addFunds.body form').submit(function(){
		yaCounter44499730.reachGoal('send');
		ga('send','event','Replenish','Fill','Send');
	});
	$('section.search form').submit(function(){
		yaCounter44499730.reachGoal('tofind');
		ga('send','event','Tofind','Click','View');
	});
	$('footer .social a.fb').unbind('click').click(function(){
		yaCounter44499730.reachGoal('facebook');
		ga('send','event','Facebook','Click','View');
	});
	$('footer .social a.tw').unbind('click').click(function(){
		yaCounter44499730.reachGoal('twitter');
		ga('send','event','Twitter','Click','View');
	});
	$('footer .social a.in').unbind('click').click(function(){
		yaCounter44499730.reachGoal('instagram');
		ga('send','event','Instagram','Click','View');
	});
	$('footer .social a.yt').unbind('click').click(function(){
		yaCounter44499730.reachGoal('youtube');
		ga('send','event','Youtube','Click','View');
	});
	$('.show-banner-gift').click(function(){
		$('.banner-gift-close').addClass('show');
		ga('send','event','Banner','Click','View');
		yaCounter44499730.reachGoal('discountbanner');
		if($(this).hasClass('show-banner-btn-1')){
			ga('send','event','Discount1','Click','View');
			yaCounter44499730.reachGoal('discountbanner1');
		}
		if($(this).hasClass('show-banner-btn-2')){
			ga('send','event','Discount2','Click','View');
			yaCounter44499730.reachGoal('discountbanner2');
		}
		if($(this).hasClass('show-banner-btn-3')){
			ga('send','event','Discount3','Click','View');
			yaCounter44499730.reachGoal('discountbanner3');
		}
		return false;
	});
	$('.banner-gift-close .close-it,.banner-gift-close .close-layer').unbind('click').click(function(){
		$('.banner-gift-close').removeClass('show');
		return false;
	});
	$('#showed-banner').click(function(){
		ga('send','event','Transition','Click','View');
		yaCounter44499730.reachGoal('click');
	});

	$('.categoryInfo .filter input').on('change', function () {
		$(this).closest('form').submit();
	});

	$('.basket-open-link').click(function(e) {
		e.preventDefault();
		if (parseInt($('.basket .amount').text()) != 0) {
			window.location.href = $(this).attr('href');
		} else	{
			$('.basket-popover').show();
			setTimeout(function() {
				$('.basket-popover').hide();
			}, 3000);
		}
	});

	$('.text-pattern').keyup(function() {
		var regex = /^[A-Za-zА-Яа-яЁё]+$/;
		if (regex.test(this.value) !== true) {
			this.value = this.value.replace(/[^A-Za-zА-Яа-яЁё\-]+/, '');
		}
	});

	$('.phone-pattern').inputmask({"mask": "+7 (999)-999-9999"});
	$('#deliveryCourier, #deliveryExpress, #deliveryLegalCourier, #deliveryLegalExpress').on('click', function() {
		$('.deliveryForm input').each(function(e){
			$(this).attr('required', 'required');
		});
		$('.deliveryForm').show();
	});	

	$('#deliveryPickup, #deliveryLegalPickup').on('click', function() {
		$('.deliveryForm input').each(function(e){
			$(this).removeAttr('required');
		});
		$('.deliveryForm').hide();
	});

	$('.addFunds .brandTabs input[name=package]').change(function() {
		$('#fundsFullForm').show();
		$('.formBlock .rightBlock').show();
		$('.formBlock .submit').show();
	});

	$('.productInfo #amount').change(function() {
		console.log('test');
		productPrice = $('#originalPrice').find('.value').data("price");
		$('.absolutlyTotal').find('.value').text(numberWithSpaces(productPrice * $(this).val()));
	});

	$('#getPrice').click(function () {
		$('#getPriceForm').show();
		$('#getPrice').hide();
	});

	$('.recaptchaForm').unbind('submit').submit(function(e) {
		e.preventDefault();
		var form=this;
		var captcha='';

		captcha=grecaptcha.getResponse(recaptchaProductOrder);
		console.log(captcha);

		if(!captcha.length){
			$('#recaptchaErrorForm').html('* Вы не прошли проверку<br> "Я не робот"');
		}else{
			$('#recaptchaErrorForm').html('');
		}

		if(captcha.length){
			form.submit();
		}
	});
});

function numberWithSpaces(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}


function toogleSearch(){
	$('.js-search__icon').click(function(){

		$(this).parent('.js-search').stop().toggleClass('search--active');
		$(this).stop().toggleClass('search__icon--close');
	})
}


function toogleNav(){

	$('.js-burger').click(function(){

		$(this).stop().toggleClass('burger--active');

		$('.js-page-nav').stop().fadeToggle('page-nav--active');
	})
}

$(function() {
	$('.simServices .radio').on('change', function() {
		if (this.value == '55') {
			$('#simproduct-custom-input').show();
		} else if(this.value == '56') {
			$('#simproduct-custom-input-2').show();
		} else {
			$('#simproduct-custom-input').hide();
			$('#simproduct-custom-input-2').hide();
		}
	});

	$('.addFunds .radio').on('change', function() {
		console.log('test');
		if (this.value == '28') {
			$('#simproduct-custom-input').show();
		} else {
			$('#simproduct-custom-input').hide();
		}
	})
});