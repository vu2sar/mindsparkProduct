tinyMCEPopup.requireLangPack();

var ExampleDialog = {
	init : function() {
		var f = document.forms[0];

		// Get the selected contents as text and place it in the input
		f.someval.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		f.somearg.value = tinyMCEPopup.getWindowArg('some_custom_arg');
	},

	insert : function() {
		var f = document.forms[0];
		if(f.file.value == "")
		{
			alert("Please select a file !");
		}
		else
		{
			f.submit();
		}
	}
};

tinyMCEPopup.onInit.add(ExampleDialog.init, ExampleDialog);
