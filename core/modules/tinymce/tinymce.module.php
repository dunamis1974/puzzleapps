<?
global $_TINYMCEINIT;

//ibrowser,
$_TINYMCEINIT = "
<script language=\"javascript\" type=\"text/javascript\" src=\"./admin/tiny_mce/tiny_mce.js\"></script>
<script language=\"javascript\" type=\"text/javascript\">
	tinyMCE.init({
		// General options
		entity_encoding : \"raw\",
		mode : \"textareas\",
		editor_selector : \"mceEditor\",
		theme : \"advanced\",
                skin : \"o2k7\",
		plugins : \"ibrowser,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template\",

		theme_advanced_buttons1 : \"save,newdocument,|,bold,italic,underline,strikethrough,|,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect,|,fullscreen,|,help,\",
		theme_advanced_buttons2 : \"cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,ibrowser,cleanup,code,|,insertdate,inserttime,preview,|,forecolor,backcolor,|,ltr,rtl,\",
		theme_advanced_buttons3 : \"tablecontrols,|,hr,removeformat,visualaid,|,charmap,emotions,media,advhr\",
		theme_advanced_toolbar_location : \"top\",
		theme_advanced_toolbar_align : \"left\",
		theme_advanced_statusbar_location : \"bottom\",
		theme_advanced_resizing : true,
		file_browser_callback : \"ajaxfilemanager\",
		content_css : \"css/content.css\",

		paste_auto_cleanup_on_paste : true,
		paste_convert_headers_to_strong : false,
		paste_strip_class_attributes : \"all\"
	});

	
        function ajaxfilemanager(field_name, url, type, win) {
            var ajaxfilemanagerurl = \"../../../../admin/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php\";
            switch (type) {
                case \"image\":
                    break;
                case \"media\":
                    break;
                case \"flash\": 
                    break;
                case \"file\":
                    break;
                default:
                    return false;
            }
            tinyMCE.activeEditor.windowManager.open({
                url: \"../../../../admin/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php\",
                width: 782,
                height: 440,
                inline : \"yes\",
                close_previous : \"no\"
            },{
                window : win,
                input : field_name
            });
            
        }
	
    function toggleEditor(id) {
    	if (!tinyMCE.get(id))
    		tinyMCE.execCommand('mceAddControl', false, id);
    	else
	    	tinyMCE.execCommand('mceRemoveControl', false, id);
    }
</script>
";

?>