var isEdit=false;
var FormAction="";
var Languages=[];
var LastLanguage;
var SortLanguage;

var CategoryId=0;

var CallBackRefreshURL="?";

Array.prototype.in_array=function(p_val){
	for(var i=0,l=this.length;i<l;i++){
		if(this[i]==p_val){
			return true;
		}
	}
	return false;
};

function responsive_filemanager_callback(field_id){
	var url=jQuery('#'+field_id).val();

	$("tr#coverTr span.hint").removeClass("hidden");
	$("input#Cover").attr("value", url);
	$("input#Upload").attr("value", "Y");
	$("div#coverMaker img").attr("src", url);
	$("div#coverMaker").toggleClass("display");
	$(window).scrollTop(0);
}

function ConfirmFormFields(required_fields){
	var result=true;

	for(j=0;j<document.forms.length;j++){
		form=document.forms[j];
		for(i=0;i<form.elements.length;i++){
			if(required_fields.in_array(form.elements[i].name)){
				if(form.elements[i].value=='' || form.elements[i].value=='undefined' || (form.elements[i].type=='checkbox' && form.elements[i].checked==false)){
					form.elements[i].className='red';
					if(form.elements[i].type=='checkbox')
						form.elements[i].parentNode.parentNode.className='red';
					result=false;
				}else{
					form.elements[i].className='';
					if(form.elements[i].type=='checkbox')
						form.elements[i].parentNode.parentNode.className='';
				}
			}
		}
	}

	if(!result)
		alert('Пожалуйста, заполните все помеченные поля!');

	return result;
}

function inArray(needle, haystack) {
	var length = haystack.length;
	for(var i = 0; i < length; i++) {
		if(haystack[i] == needle) return true;
	}
	return false;
}

function CheckForm(required_fields){
	var result=true;

	for(j=0;j<document.forms.length;j++){
		form=document.forms[j];
		for(i=0;i<form.elements.length;i++){
			if(required_fields.in_array(form.elements[i].name)){
				if(form.elements[i].value=='' || form.elements[i].value=='undefined'){
					form.elements[i].className='red';
					form.elements[i].parentNode.parentNode.className='red';
					result=false;
				}else{
					form.elements[i].className='';
					form.elements[i].parentNode.parentNode.className='';
				}
			}
		}
	}

	if(!result)
		alert('Пожалуйста, заполните все помеченные поля!');

	return result;
}

$.fn.ignore = function(sel){
	return this.clone().find(sel||">*").remove().end();
};

function CheckCMSBlocks(){
	if($("section div.title h1").length){
		var Blocks=$("table.table thead");

		if(Blocks.length>1){
			var ListTitle=$("<div class=\"addnew\"><a href=\"#\" class=\"add\">Раздел</a></div>");
			var List=$("<ul id=\"addType\"></ul>");
			Blocks.each(function(){
				var Block=this;
				var Title=$(this).find("tr:first-child").find("th").ignore("a").text();
				var LI="<li><a href=\"#\">"+Title+"</a></li>";

				$(List).append(LI);
			});
			$(ListTitle).append(List);

			$("section div.title h1").append(ListTitle);

			$(ListTitle).find("li").each(function(num){
				var TitleNum=num;
				$(this).click(function(){
					$('html,body').animate({scrollTop: $(Blocks[TitleNum]).offset().top-$("div.headerBar").height()*2},'fast');
					return false;
				});
			});
		}
	}
}

function ChangeLanguage(LanguagePrefix){
	LastLanguage=LanguagePrefix;

	$("ul#LanguageSettings li").removeClass("active");
	$("ul#LanguageSettings li#"+LanguagePrefix).addClass("active");
	$("div#LanguagesSlider li").removeClass("active");
	$("div#LanguagesSlider li#"+LanguagePrefix).addClass("active");

	for(var i=0;i<Languages.length;i++){
		$("input[type='text'],input[type='file'],div,textarea").filter(function(index){
			if($(this).attr("id")==undefined)
				return false;
			return ($(this).attr("id").substr($(this).attr("id").length-3)=="_"+Languages[i] || $(this).attr("id").substr($(this).attr("id").length-4)=="_"+Languages[i]);
		}).hide();
	}

	$("input[type='text'],input[type='file'],div,textarea").filter(function(index){
		if($(this).attr("id")==undefined)
			return false;
		return ($(this).attr("id").substr($(this).attr("id").length-3)=="_"+LanguagePrefix || $(this).attr("id").substr($(this).attr("id").length-4)=="_"+LanguagePrefix);
	}).fadeIn("fast");
}

function MakeLanguagesChecked(){
	if($("div#LanguagesSlider").length){
		$("div#LanguagesSlider li").each(function(){
			if(!inArray($(this).attr("id"),Languages))
				Languages[Languages.length]=$(this).attr("id");

			$(this).click(function(){
				ChangeLanguage($(this).attr("id"));
			});
		});

		ChangeLanguage(LastLanguage);
	}
}

function MakeContactBlocksClicked(){
	var Blocks=$("div.contactBlock");

	$("#ContactBlocks div.addnew ul#addType li a").unbind("click").click(function(){
		var Type=$(this).data("type");
		$.ajax({
			type:"POST",
			cache:false,
			data:"",
			url:"?NewContact&Type="+Type,
			success: function(html){
				$("#ContactBlocks td."+Type).append(html);

				MakeContactBlocksClicked();
			}
		});

		return false;
	});

	$(Blocks).each(function(){
		var Block=this;
		$(this).find("a.button.add").unbind("click").click(function(){
			var Type=$(this).parents("td").data("type");
			$.ajax({
				type:"POST",
				cache:false,
				data:"",
				url:"?NewContact&Type="+Type,
				success: function(html){
					$("#ContactBlocks td."+Type).append(html);

					MakeContactBlocksClicked();
				}
			});

			return false;
		});
		$(this).find("a.button.remove").unbind("click").click(function(){
			$(Block).remove();

			return false;
		});
	});
}

function MakeScheduleBlocksClicked(){
	var Blocks=$("div.schedule");

	$("#ScheduleBlocks div.addnew ul#addType li a").unbind("click").click(function(){
		var Type=$(this).data("type");
		$.ajax({
			type:"POST",
			cache:false,
			data:"",
			url:"?NewSchedule&Type="+Type,
			success: function(html){
				$("#ScheduleBlocks td").append(html);

				MakeScheduleBlocksClicked();
			}
		});

		return false;
	});

	$(Blocks).each(function(){
		var Block=this;
		$(this).find("a.button.add").unbind("click").click(function(){
			var Type=$(this).parents("div.schedule").data("type");
			$.ajax({
				type:"POST",
				cache:false,
				data:"",
				url:"?NewSchedule&Type="+Type,
				success: function(html){
					$("#ScheduleBlocks td").append(html);

					MakeScheduleBlocksClicked();
				}
			});
			return false;
		});
		$(this).find("a.button.remove").unbind("click").click(function(){
			$(Block).remove();

			return false;
		});
	});

	$(Blocks).each(function(){
		var Block=this;

		$(Block).find("input[id^='Company']").autocomplete({
			delay:0,
			minLength:3,
			source: "?getCompanies&"+ ($(Block).find("input[id^='Company']").val()),
			select: function(event, ui){
				$(Block).find("input[id^='Company']").val(ui.item.label);
				$(Block).find("input[id^='CompanyId']").val(ui.item.id);

				return false;
			}
		}).autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append("<div>" + item.label + "</div><div class=\"city\">"+item.city+"</div>")
				.appendTo( ul );
		};

		$(Block).find("input[id^='StartTime']").datepicker({'dateFormat':'dd/mm/yy'});
		$(Block).find("input[id^='FinishTime']").datepicker({'dateFormat':'dd/mm/yy'});
	});
}

function MakeTableHeadExpanded(){
	$("table.headBlocks").each(function(){
		var table=this;
		$(table).addClass("collapseHead");

		$(table).find("thead th").click(function(){
			$(this).parents("thead").toggleClass("visible");

			return false;
		});
	});
}

function MakeSpecialtiesDeleteClicked(){
	$("table.table.specialties tbody a.button.remove").unbind("click").click(function(e){
		if(confirm('Вы действительно хотите удалить данную услугу?')){
			$(this).closest("tr").remove();
		}

		return false;
	});
}

function MakeSpecialtiesAutocomplete(){
	$.widget("custom.catcomplete", $.ui.autocomplete, {
		_create: function() {
			this._super();
			this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
		},
		_renderMenu: function(ul, items) {
			var self = this,
				currentCategory = "";
			$.each(items, function(index, item) {
				if (item.category != currentCategory) {
					ul.append("<li class='ui-autocomplete-category'>" + item.category + "</li>");
					currentCategory = item.category;
				}
				self._renderItemData(ul, item);
			});
		}
	});

	$("table.table.specialties thead input#service").catcomplete({
		delay:0,
		minLength:5,
		source: "?getSpecialties"+ $("table.table.specialties thead input#service").val(),
		select: function(event, ui){
			$.ajax({
				type:"GET",
				cache:false,
				data:"",
				url:"?addSpecialty&id="+ui.item.id,
				success:function(html){
					if(html!=="ERROR"){
						$("section table.table.specialties tbody").append(html);
						$("section table.table.specialties tbody tr:last-child").find("input").focus();

						MakeSpecialtiesDeleteClicked();
						MakeCalendars();
					}
				}
			});
		}
	});

	$("#serviceStr").catcomplete({
		delay:0,
		minLength:5,
		source: "?getSpecialties"+ $("#serviceStr").val(),
		select: function(event, ui){
			$("input#serviceId").val(ui.item.id);
			$("input#serviceStr").val(ui.item.value);

			return false;
		}
	});

	MakeSpecialtiesDeleteClicked();
}

function MakeBranchesDeleteClicked(){
	$("section table.table.branches tbody a.button.remove").unbind("click").click(function(){
		if(confirm('Вы действительно хотите удалить данный филиал?')){
			$(this).closest("tr").remove();
		}

		return false;
	});
}

function MakeBranchesAutocomplete(){
	$("table.table.branches thead input#company").autocomplete({
		delay:0,
		minLength:3,
		source: "?getCompanies"+ $("table.table.branches thead input#company").val(),
		select: function(event, ui){
			if($("body").find("input#Branch"+ui.item.id).length==0){
				$.ajax({
					type:"GET",
					cache:false,
					data:"",
					url:"?addBranch&id="+ui.item.id,
					success:function(html){
						if(html!=="ERROR"){
							$("section table.table.branches tbody").append(html);

							MakeBranchesDeleteClicked();
						}
					}
				});
			}

			return false;
		}
	}).autocomplete( "instance" )._renderItem = function( ul, item ) {
		return $( "<li>" )
			.append("<div>" + item.label + "</div><div class=\"city\">"+item.city+"</div>")
			.appendTo( ul );
	};

	MakeBranchesDeleteClicked();
}

function MakeDoctorsDeleteClicked(){
	$("section table.table.doctors tbody a.button.remove").unbind("click").click(function(){
		if(confirm('Вы действительно хотите удалить данного врача?')){
			$(this).closest("div").remove();
		}

		return false;
	});
}

function MakeDoctorsAutocomplete(){
	$("table.table.doctors thead input#doctor").autocomplete({
		delay:0,
		minLength:3,
		source: "?getDoctors"+ $("table.table.doctors thead input#doctor").val(),
		select: function(event, ui){
			if($("body").find("input#Doctor"+ui.item.id).length==0){
				$.ajax({
					type:"GET",
					cache:false,
					data:"",
					url:"?addDoctor&id="+ui.item.id,
					success:function(html){
						if(html!=="ERROR"){
							$("section table.table.doctors tbody td").append(html);

							MakeDoctorsDeleteClicked();
						}
					}
				});
			}

			return false;
		}
	}).autocomplete( "instance" )._renderItem = function( ul, item ) {
		return $( "<li>" )
			.append("<div>" + item.label + "</div><div class=\"city\">"+item.city+"</div>")
			.appendTo( ul );
	};

	MakeDoctorsDeleteClicked();
}

function MakeCalendars(){
	if($("input.calendar").length){
		$("input.calendar").each(function(){
			$(this).datepicker({'dateFormat':'dd/mm/yy'});
		});
	}
	if($("input#DateTime,input#FinishTime,input.datetimepicker").length){
		if($("input#DateTime,input#FinishTime,input.datetimepicker").hasClass("datetimepicker"))
			$("input#DateTime,input#FinishTime,input.datetimepicker").datetimepicker({'dateFormat':'dd/mm/yy'});
		else
			$("input#DateTime,input#FinishTime,input.datetimepicker").datepicker({'dateFormat':'dd/mm/yy'});
	}
	if($("input#DateTime,input#StartTime,input.datetimepicker").length){
		if($("input#DateTime,input#StartTime,input.datetimepicker").hasClass("datetimepicker"))
			$("input#DateTime,input#StartTime,input.datetimepicker").datetimepicker({'dateFormat':'dd/mm/yy'});
		else
			$("input#DateTime,input#StartTime,input.datetimepicker").datepicker({'dateFormat':'dd/mm/yy'});
	}
	if($("input#Time").length){
		$("input#Time").timepicker();
	}
}

function MakeCompanyStrAutocomplete(){
	if($("input#CompanyId").length && $("input#CompanyStr").length){
		$("input#CompanyStr").autocomplete({
			delay:0,
			minLength:3,
			source:"?getCompanies&"+$("input#CompanyStr").val(),
			select:function(event,ui){
				$("input#CompanyId").val(ui.item.id);
				$("input#CompanyStr").val(ui.item.value);

				return false;
			}
		}).autocomplete("instance")._renderItem=function(ul,item){
			return $("<li>")
				.append("<div>"+item.label+"</div><div class=\"city\">"+item.city+"</div>")
				.appendTo(ul);
		};
	}
}

function RequestFormActions(){
	$("#scheduleBlock a.callBackBtn").unbind("click").click(function(){
		var href=$(this).attr("href");

		$.ajax({
			type:"get",
			cache:false,
			data:"",
			url:href+"&ajax",
			success: function(){
				var LeftCurrent=$("#doctorList.current div.company.current,#companyList.current div.company.current");
				if($(LeftCurrent).length==0){
					location.reload();
				}else
					$(LeftCurrent).click();
			}
		});

		return false;
	});

	$("#scheduleBlock").find("form").unbind("submit").submit(function(){
		var data=$(this).serialize();
		var action=$(this).attr("action");

		$.ajax({
			type:"POST",
			cache:false,
			data:data,
			url:action+"&ajax",
			success: function(result){
				if(result=="ERROR"){
					alert('Во время операции произошла ошибка. Пожалуйста обновите страницу или повторите запрос.');
				}else if(result=="TIME"){
					alert('Данное время уже занято. Пожалуйста, выберите другое время и повторите попытку.');
				}else if(result=="OK"){
					var LeftCurrent=$("#doctorList.current div.company.current,#companyList.current div.company.current");
					if($(LeftCurrent).length==0){
						location.reload();
					}else
						$(LeftCurrent).click();
				}
			}
		});

		return false;
	})/*.unbind("change").change(function(){
		var data=$(this).serialize();
		var action=$(this).attr("action");

		$.ajax({
			type:"POST",
			cache:false,
			data:data,
			url:action+"&ajax&update",
			success: function(result){
				if(result=="ERROR"){
					alert('Во время операции произошла ошибка. Пожалуйста обновите страницу или повторите запрос.');
				}else if(result=="TIME"){
					alert('Данное время уже занято. Пожалуйста, выберите другое время и повторите попытку.');
				}else if(result=="OK"){
					var LeftCurrent=$("#doctorList div.company.current");
					if($(LeftCurrent).length==0){
						location.reload();
					}else
						$(LeftCurrent).click();
				}
			}
		});

		return false;
	})*/;

	$("tbody[data-id]").each(function(){
		$(this).find("td.confirm .calendar").unbind("change").change(function(){
			var form=$(this).closest("form");
			var data=$(form).serialize();
			var action=$(form).attr("action");
			$.ajax({
				type:"POST",
				cache:false,
				data:data,
				url:action+"&getTimes",
				success: function(html){
					if(html=="SAME"){
						//
					}else{
						$(form).find("select").html(html);
					}
				}
			});
			return false;
		});
		$(this).unbind("click").click(function(){
			var type=$(this).data("type");
			var doctorId=0;
			var companyId=$(this).data("company-id");
			if(type=="DOCTOR"){
				doctorId=$(this).data("doctor-id");
				GetDoctorInfo(doctorId,companyId);
			}else if(type=="SHARE" || type=="COMPANY"){
				GetCompanyInfo(companyId);
			}
		});
	});
}

function GetDoctorInfo(DoctorId,CompanyId){
	$.ajax({
		type:"POST",
		cache:false,
		data:"",
		url:"?getDoctorInfo&Id="+DoctorId+"&CompanyId="+CompanyId,
		success: function(html){
			if(html!=="ERROR"){
				html=html.split(":SEP:");
				$("#contactBlock").html(html[0]);
			}
		}
	});
}
function GetCompanyInfo(CompanyId){
	$.ajax({
		type:"POST",
		cache:false,
		data:"",
		url:"?getCompanyInfo&Id="+CompanyId,
		success: function(html){
			if(html!=="ERROR"){
				html=html.split(":SEP:");
				$("#contactBlock").html(html[0]);
			}
		}
	});
}

$(document).ready(function(){
	MakeLanguagesChecked();

	$("[delquestion]").each(function(){
		if($(this).is("a")){
			$(this).click(function(){
				if(confirm('Вы действительно хотите удалить '+$(this).attr("delquestion")+'?')){
					$(this).attr("href",$(this).attr("href")+"&DelAccess");

					return true;
				}else{
					return false;
				}
			});
		}else if($(this).is("form")){
			$(this).submit(function(){
				if(confirm('Вы действительно хотите удалить '+$(this).attr("delquestion")+'?')){
					$(this).attr("action",$(this).attr("action")+"&DelAccess");

					return true;
				}else{
					return false;
				}
			});
		}
	});

	if($("table.TopMenu").length){
		$("table.TopMenu a.showShop").click(function(){
			$("table.TopMenu td.shop").show();
			$("table.TopMenu td.content").hide();
			$("table.TopMenu a.showPage").show();

			$(this).hide();

			return false;
		});
		$("table.TopMenu a.showPage").click(function(){
			$("table.TopMenu td.shop").hide();
			$("table.TopMenu td.content").show();
			$("table.TopMenu a.showShop").show();

			$(this).hide();

			return false;
		});

		$("table.TopMenu a.showShop").click();
	}

	if($("form#GroupChecked").length){
		$("form#GroupChecked input.checkbox-slider").each(function(){
			$(this).change(function(){
				var Id=$(this).parent().parent().attr("fid");
				var data=$("form#GroupChecked").serialize();

				$.ajax({
					type:"POST",
					cache:false,
					data:data,
					url:"?ChangeStatus&Id="+Id,
					success: function(xml){
						//
					}
				});
			});
		});
		$("form#GroupChecked input#ChkAll").change(function(){
			$("form#GroupChecked input.checkbox").prop("checked",$(this).is(":checked"));

			return false;
		});

		$("form#GroupChecked button#filter").click(function(){
			var form=$(this).parent().parent().parent().parent();
			var action=$("<input type=\"hidden\" name=\"Filter\" />");
			$(form).append(action);

			$(form).attr("action","?Filter");
			$(form).attr("method","get");

			$(form).unbind("submit").submit();

			return false;
		});

		$("form#GroupChecked button#recovery").click(function(){
			var form=$(this).parent().parent().parent().parent();
			var action=$("<input type=\"hidden\" name=\"Recovery\" />");
			$(form).append(action);

			$(form).attr("action","?Recovery");

			$(form).unbind("submit").submit();

			return false;
		});

		$("form#GroupChecked").submit(function(){
			var count=0;

			count=$("form#GroupChecked input.checkbox:checked").length;

			return count>0;
		});
	}

	MakeCalendars();

	if($("div#formTitle").length){
		$(window).scroll(function(){
			if($(window).scrollTop()>0){
				$("div#formTitle").addClass("fixed");
			}else{
				$("div#formTitle").removeClass("fixed");
			}
		});
	}

	if($("tr#PhotoChoosen").length){
		$("tr#PhotoChoosen input#Photos").change(function(){
			if($("tr#PhotoChoosen select#PhotoList").length){
				$("tr#PhotoChoosen select#PhotoList").html("");

				for(var i=0;i<this.files.length;i++){
					var option=$("<option value=\""+this.files[i].name+"\">"+this.files[i].name+"</option>");

					$("tr#PhotoChoosen select#PhotoList").append(option);
				}
			}else if($("tr#PhotoChoosen span#PhotoName")){
				$("tr#PhotoChoosen span#PhotoName").html(this.files[0].name);
			}

			return false;
		});
	}

	if($("ul#Photos.PhotoList").length){
		$("ul#Photos.PhotoList").sortable({
			placeholder: "ui-state-highlight",
			update:function(event,ui){
				$("ul#Photos.PhotoList li").each(function(num){
					var Id=$(this).attr("photoid");
					$(this).find("input#PhotoPos"+Id).attr("value",num+1);
				});
			}
		});
		$("ul#Photos.PhotoList").disableSelection();

		$("ul#Photos.PhotoList li").each(function(){
			var li=$(this);
			$(this).find("a").click(function(){
				var Id=$(li).attr("photoid");
				var StatusEl=$(li).find("input#PhotoStatus"+Id);
				var Status=$(StatusEl).attr("value");

				$(StatusEl).attr("value",Status=="Y"?"N":"Y");
				$(li).toggleClass("remove");

				return false;
			});
		});
	}

	if($("ul#Licenses.PhotoList").length){
		$("ul#Licenses.PhotoList").sortable({
			placeholder: "ui-state-highlight",
			update:function(event,ui){
				$("ul#Licenses.PhotoList li").each(function(num){
					var Id=$(this).data("id");
					$(this).find("input#LicensePos"+Id).attr("value",num+1);
				});
			}
		});
		$("ul#Licenses.PhotoList").disableSelection();

		$("ul#Licenses.PhotoList li").each(function(){
			var li=$(this);
			$(this).find("a").click(function(){
				var Id=$(li).data("id");
				var StatusEl=$(li).find("input#LicenseStatus"+Id);
				var Status=$(StatusEl).attr("value");

				$(StatusEl).attr("value",Status=="Y"?"N":"Y");
				$(li).toggleClass("remove");

				return false;
			});
		});
	}

	if($("a#delCover").length){
		$("a#delCover").click(function(){
			var actualStatus=$("input#DelStatus").attr("value");

			if(actualStatus=="Y"){
				$("input#DelStatus").attr("value","N");
				$(this).removeClass("remove");
			}else{
				$("input#DelStatus").attr("value","Y");
				$(this).addClass("remove");
			}

			return false;
		});
	}

	if($("form.Currencies#GroupChecked").length){
		$("form.Currencies#GroupChecked input.checkbox-slider[name^='Default']").change(function(){
			if($(this).is(":checked")){
				$("form.Currencies#GroupChecked input.checkbox-slider[name^='Default']").prop("checked",false);
				$(this).prop("checked",true);
			}

			return false;
		});
	}

	if($("div#coverMaker").length){
		$("a#coverButton,div#coverMaker a.close,input#CoverDone").click(function(){
			$("div#coverMaker").toggleClass("display");
			if($("div#coverMaker").hasClass("display"))
				$("input#size").val($("div#coverMaker img").width() + "x" + $("div#coverMaker img").height());

			return false;
		});

		$("input#size").val($("div#coverMaker img").width()+"x"+ $("div#coverMaker img").height());

		$("div.block").each(function () {
			$(this).resizable({
				containment: "div#CoverCropper div.container",
				grid:1,
				aspectRatio: $(this).width() / $(this).height(),
				stop: function (event, ui) {
					$("input#size" + $(this).attr("block")).val(ui.size.width + "x" + ui.size.height);
					$(this).find("button").removeClass();
					$("input#background" + $(this).attr("block")).val("n");

					$("div#coverMaker div#CoverCropper div.list a.block.b"+ $(this).attr("block")).removeClass("active");

					$("input#size").val($("div#coverMaker img").width() + "x" + $("div#coverMaker img").height());
				}
			}).draggable({
				containment: "div#CoverCropper div.container",
				stop: function(event,ui){
					$("input#position" + $(this).attr("block")).val(ui.position.left+"x"+ui.position.top);
					$(this).find("button").removeClass();
					$("input#background" + $(this).attr("block")).val("n");

					$("div#coverMaker div#CoverCropper div.list a.block.b" + $(this).attr("block")).removeClass("active");

					$("input#size").val($("div#coverMaker img").width() + "x" + $("div#coverMaker img").height());
				}
			});
		});

		$("div#coverMaker div#CoverCropper div.list a").each(function(num){
			$(this).click(function(){
				var hr=this;

				$("input#size").val($("div#coverMaker img").width() + "x" + $("div#coverMaker img").height());

				$("div#coverMaker div#CoverCropper div.container div.block").removeClass("display");
				$("div#coverMaker div#CoverCropper div.container div.block.b" + $(this).attr("block")).addClass("display");
				$("div#coverMaker div#CoverCropper div.container div.block.b" + $(this).attr("block")).find("button").click(function(){
					$("input#background"+$(hr).attr("block")).val("y");
					$(this).addClass("active");
					$("div#coverMaker div#CoverCropper div.list a.block.b" + $(hr).attr("block")).addClass("active");

					$("input#size").val($("div#coverMaker img").width() + "x" + $("div#coverMaker img").height());

					return false;
				});

				return false;
			});
		});

		$(window).scroll(function(){
			if ($("div#coverMaker").hasClass("display"))
				$("input#size").val($("div#coverMaker img").width() + "x" + $("div#coverMaker img").height());
		});
	}

	if($("select#Categories").length || $("select#Regions").length){
		var OldSize=[];
		OldSize["Categories"]= $("select#Categories").attr("size");
		OldSize["Regions"]= $("select#Regions").attr("size");

		$("select#Categories,select#Regions").focus(function(){
			$(this).attr("size", OldSize[$(this).attr("id")]*3);
		}).focusout(function(){
			$(this).attr("size", OldSize[$(this).attr("id")]);
		}).mouseenter(function(){
			$(this).attr("size", OldSize[$(this).attr("id")]*3);
		}).mouseleave(function(){
			$(this).attr("size", OldSize[$(this).attr("id")]);
		});
	}


	function OnMessage(e){
		var event = e.originalEvent;
		if(event.data.sender === 'responsivefilemanager'){
			if(event.data.field_id){
				var fieldID=event.data.field_id;
				var url=event.data.url;
				alert(url);
				$('#'+fieldID).val(url).trigger('change');
				$.fancybox.close();

				$(window).off('message', OnMessage);
			}
		}
	}

	$('.iframe-btn').on('click',function(){
		$(window).on('message', OnMessage);
	});

	$('.iframe-btn').fancybox({
		width:'90%',
		height:'90%',
		minHeight:'90%',
		minWidth:'90%',
		openEffect  : 'none',
		closeEffect : 'none',
		nextEffect  : 'none',
		prevEffect  : 'none',
		'type':'iframe'
	});

	//$("input#Cover").change(function(){alert('fff');}).on("change",function(){alert('aaa')});

	$("a#CoverChoose").click(function(){
		moxman.browse({
			sort_by: "size",
			oninsert: function (args) {
				$("tr#coverTr span.hint").removeClass("hidden");
				$("input#Cover").attr("value", args.files[0].url);
				$("input#Upload").attr("value", "Y");
				$("div#coverMaker img").attr("src", args.files[0].url);
				$("div#coverMaker").toggleClass("display");
				$(window).scrollTop(0);
			}
		});

		return false;
	});

	var DoctorCompanies=$("#doctorList div.doctorBlock div.company");
	$(DoctorCompanies).click(function(){
		var DoctorBlock=$(this).closest("div.doctorBlock");
		var Company=this;
		var Id=$(this).closest("div.doctorBlock").data("id");
		var CompanyId=$(this).data("id");

		$.ajax({
			type:"POST",
			cache:false,
			data:"",
			url:"?getDoctorInfo&Id="+Id+"&CompanyId="+CompanyId,
			success: function(html){
				if(html!=="ERROR"){
					CallBackRefreshURL="?getDoctorInfo&Id="+Id+"&CompanyId="+CompanyId;

					$(DoctorCompanies).closest("div.doctorBlock").removeClass("current");
					$(DoctorCompanies).removeClass("current");
					$(Company).addClass("current");
					$(Company).closest("div.doctorBlock").addClass("current");
					html=html.split(":SEP:");
					$("#contactBlock").html(html[0]);
					$("#scheduleBlock").html(html[1]);

					MakeCalendars();
					RequestFormActions();
				}
			}
		});

		return false;
	});
	
	var Companies=$("#companyList div.companyBlock");
	$(Companies).click(function(){
		var Company=this;
		var Id=$(this).data("id");

		$.ajax({
			type:"POST",
			cache:false,
			data:"",
			url:"?getCompanyInfo&Id="+Id,
			success: function(html){
				if(html!=="ERROR"){
					CallBackRefreshURL="?getCompanyInfo&Id="+Id;

					$(Companies).removeClass("current");
					$(Company).addClass("current");
					html=html.split(":SEP:");
					$("#contactBlock").html(html[0]);
					$("#scheduleBlock").html(html[1]);

					MakeCalendars();
					RequestFormActions();
				}
			}
		});

		return false;
	});

	if($(".table.callcenter").length){
		$(".table.callcenter input[name=\"type\"]").change(function(){
			if($(this).val()=="company"){
				$("#doctorList").removeClass("current");
				$("#companyList").addClass("current");
			}else if($(this).val()=="doctor"){
				$("#companyList").removeClass("current");
				$("#doctorList").addClass("current");
			}
		});

		var callCenterItems=setInterval(function(){
			$.ajax({
				type:"POST",
				cache:false,
				data:"",
				url:CallBackRefreshURL+"&refresh",
				success: function(html){
					if(html!=="ERROR"){
						var Ids=[];
						var body=$("<div></div>");
						$(body).append(html);
						var PlayMelody=false;
						var Appointments=$(body).find("tbody[data-id]");
						$(Appointments).each(function(){
							var Status=$(this).data("status");
							var Id=$(this).data("id");
							var Appointment=this;
							var ActualAppointment=$("#scheduleBlock").find("tbody[data-id='"+Id+"']");
							if(ActualAppointment.length>0){
								if(Status!==$(ActualAppointment).data("status")){
									$(ActualAppointment).remove();
									$("#scheduleBlock thead#confirmTable").after(Appointment);
									PlayMelody=true;
								}
							}else{
								if(Status=="WAIT"){
									$("#scheduleBlock thead#waitTable").after(Appointment);
									PlayMelody=true;
								}
							}
							Ids[Ids.length]=$(Appointment).data("id");
						});
						Appointments=$("#scheduleBlock").find("tbody[data-id]");
						$(Appointments).each(function(){
							if(Ids.in_array($(this).data("id"))==false)
								$(this).remove();
						});

						RequestFormActions();

						if(PlayMelody){
							var audioElement = document.createElement('audio');
							audioElement.setAttribute('src', '/admin/i/callcenter.mp3');
							audioElement.setAttribute('autoplay', 'autoplay');
							$.get();

							audioElement.addEventListener("load",function(){
								audioElement.play();
							}, true);
							audioElement.addEventListener("ended",function(){
								$(audioElement).remove();
							})
						}
					}
				}
			});

		},3000);
	}

	RequestFormActions();

	MakeContactBlocksClicked();
	MakeScheduleBlocksClicked();

	MakeSpecialtiesAutocomplete();
	MakeBranchesAutocomplete();
	MakeDoctorsAutocomplete();

	MakeCompanyStrAutocomplete();

	CheckCMSBlocks();

	MakeTableHeadExpanded();
});