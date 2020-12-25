<script>
	function strip_tags(str,allowed_tags){
		var key='',allowed=false;
		var matches=[];
		var allowed_array=[];
		var allowed_tag='';
		var i=0;
		var k='';
		var html='';
		var replacer=function(search,replace,str){
			return str.split(search).join(replace);
		};
		if(allowed_tags){
			allowed_array=allowed_tags.match(/([a-zA-Z0-9]+)/gi);
		}
		str+='';

		matches=str.match(/(<\/?[\S][^>]*>)/gi);
		for(key in matches){
			if(isNaN(key)){
				continue;
			}

			html=matches[key].toString();
			allowed=false;

			for(k in allowed_array){            // Init
				allowed_tag=allowed_array[k];
				i= -1;

				if(i!=0){
					i=html.toLowerCase().indexOf('<'+allowed_tag+'>');
				}
				if(i!=0){
					i=html.toLowerCase().indexOf('<'+allowed_tag+' ');
				}
				if(i!=0){
					i=html.toLowerCase().indexOf('</'+allowed_tag);
				}

				if(i==0){
					allowed=true;
					break;
				}
			}
			if(!allowed){
				str=replacer(html,"",str);
			}
		}
		return str;
	}
</script>

<script language="javascript" type="text/javascript" src="/admin/editor/tinymce.min.js"></script>
<script language="javascript" type="text/javascript" src="/admin/editor/plugins/moxiecut/plugin.min.js"></script>
<script language="javascript" type="text/javascript">
	tinymce.init({
		mode:"textareas",
		editor_deselector:"mceNoEditor",
		theme:"modern",
		plugins:[
			"advlist autolink lists link image charmap print preview hr anchor pagebreak",
			"searchreplace wordcount visualblocks visualchars code fullscreen",
			"insertdatetime media nonbreaking save table directionality",
			"template paste textcolor visualblocks qrcode addgallery responsivefilemanager moxiecut"
		],
		toolbar1:"undo redo | styleselect formatselect | fontselect fontsizeselect forecolor backcolor | bold italic underline strikethrough subscript superscript outdent indent | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent | link unlink image addgallery | table inserttable tableprops deletetable cell row column | visualblocks visualchars anchor qrcode | template",
		toolbar2:"print preview media | removeformat blockquote hr | charmap | code nonbreaking | responsivefilemanager",
		image_advtab:true,
		theme_advanced_toolbar_location:"top",
		theme_advanced_toolbar_align:"left",
		theme_advanced_path_location:"bottom",
		plugin_insertdate_dateFormat:"%Y-%m-%d",
		plugin_insertdate_timeFormat:"%H:%M:%S",
		content_css:"/css/editor.css",
		style_formats:[
			{title:'Зеленый цвет',inline:'span',classes:'green'},
			{title:'Светло серый',inline:'span',classes:'lightGray'},
			{title:'Темно серый',inline:'span',classes:'darkGray'},
			{title:'Темная ссылка',selector:'a',classes:'darkLink'},
			{title:'Таблица',selector:'table',classes:'table'},
			{title:'Список с точками',selector:'ul',classes:'ul'},
			{title:'Список с номерами',selector:'ol',classes:'ol'},
		],

		paste_remove_spans:true,
		paste_remove_styles:true,
		paste_strip_class_attributes:'all',
		paste_auto_cleanup_on_paste:true,
		paste_preprocess:function(pl,o){
			o.content=strip_tags(o.content,'<p><h1><h2><h3><h4><h5><img><strong><em><u><ul><li><ol><a><div>');
		},
		paste_postprocess:function(pl,o){
		},

		contextmenu:"paste copy cut | undo redo | link image inserttable | cell row column deletetable",

		external_filemanager_path:"/admin/editor/plugins/responsivefilemanager/filemanager/",
		filemanager_title:"Выберите файл",
		filemanager_sort_by:"date",
		filemanager_descending:1,
		external_plugins:{"filemanager":"/admin/editor/plugins/responsivefilemanager/filemanager/plugin.min.js"},

		menubar:false,
		relative_urls:false,
		remove_script_host:true,
		document_base_url:'/',
		toolbar_items_size:'small',
		extended_valid_elements:"hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],script[type|src],div[id|style|class]",
		paste_use_dialog:false,
		table_styles:"Таблица программы=programm",
		theme_advanced_resizing:true,
		nonbreaking_force_tab:true,
		apply_source_formatting:true,
		language:"ru",
		theme_advanced_resize_horizontal:false,
		templates:[
			/*{
			 title:'Наши преимущества',
			 url:'/admin/editor/templates/advantages.html',
			 description:'Вставка шаблока блоков для секции "Наши преимущества"'
			 },*/
		]
	});
</script>