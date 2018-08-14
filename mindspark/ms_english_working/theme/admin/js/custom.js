/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function(){

    var l = window.location;
    var base_url = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1];
    
    //alert(base_url);
    $('input[type="file"]').change(function(){
        var filename=$.trim($(this).val()).split("/").pop();
        filename=filename.split("\\").pop();
        $(this).parent().parent().next('.name-space').html(filename);
        $('.progress').show();
    });
    
    if ($("#newline").length > 0){
        $("#newline").dynamicForm("#add", "#remove", {limit:500, formPrefix:"", duration: 2});
    }
    
    if ($('#frmEmpDeclaration').length > 0){
          
        var bar = $('.progress-bar');
        var percent = $('.sr-only');
        var status = $('#status');

        $('#frmEmpDeclaration').ajaxForm({
            beforeSubmit: validateUpload,
            beforeSend: function() {
                var percentVal = '0%';
                status.empty();
                bar.width(percentVal);
                bar.attr('aria-valuenow', percentVal);
                percent.html(percentVal);
                $('#submitall').attr('disabled', 'disabled');
            },
            uploadProgress: function(event, position, total, percentComplete) {
                var percentVal = percentComplete + '%';
                bar.width(percentVal);
                bar.attr('aria-valuenow', percentVal);
                percent.html(percentVal);
                status.html(percentVal + ' Complete');
                //console.log(percentVal, position, total);
            },
            success: function() {
                var percentVal = '100%';
                bar.width(percentVal);
                bar.attr('aria-valuenow', percentVal);
                percent.html(percentVal);
                status.html(percentVal + ' Complete');
                //console.log(percentVal);
                
            },
            complete: function(xhr) {
                
                $("#result").show("slow");
                $('#result strong').html(xhr.responseText);
                setTimeout(function(){ $('#result').hide("slow") }, 5000);
                
                $('#frmEmpDeclaration').resetForm();
                $('#submitall').removeAttr('disabled');
                $('.name-space').each(function(){
                    $(this).html('');
                });
                bar.width('0%');
                bar.attr('aria-valuenow', '0%');
                $('.progress').hide();
                status.html('');
                $('#frmEmpDeclaration').children('.border-row').each(function(index){//alert(index);return false;
                    if((index === 0) && ($(this).attr('id') === 'newline')){
                        $(this).children().find('#remove:visible').hide();
                        $(this).children().find('#add:hidden').show();
                        if ($('.border-row').length > 1){
                            $(this).dynamicForm("#add", "#remove", {limit:500, formPrefix:"", duration: 2});
                        }
                    }else {
                        $(this).remove();
                    }
                });

               window.location.href = '../tax_declaration/emp_investment_details';
            }
        }); 
        return false;
        //});
    }
    	 
});

function validateEdit(){
	//alert('**');
	var msg = '';
	
	if($('#appr_class').val() == ''){
        msg +='You must select the approved class.\n';
    }
    if($('#appr_amount').val()=='' || isNaN($('#appr_amount').val())){
        msg +='You must enter the valid approved amount.\n';
    }
    
  	if(msg ==''){
    	
    	return true;	
	}
    else{
    	alert(msg);
		return false;
	}

}

function validateUpload(){
    var count = 0;
    $('input[name="uploaded_filename[]"]').each(function(){
        if ($.trim($(this).val()) === '') {
            ++count;
        }
    });
    if(count > 0){

        /*
         * Display an Error if Document is not uploaded for any of Investment Declaration Record
         */
        $('html, body').animate({scrollTop: 0}, '');
        $("#resulterr").show("slow");
        $('#resulterr strong').html('You must select a file to upload, no blank attachment field allowed before submission.');
        setTimeout(function() {
            $('#resulterr strong').html('');
            $("#resulterr").hide("slow");
        }, 5000);
        return false;
    }
    count = 0;
    $('select[name="investment_type_id[]"]').each(function(){
        if ($.trim($(this).val()) === '') {
            ++count;
        }
    });
    if(count > 0){

        /*
         * Display an Error if Investment Type not specified for any of Investment Declaration Record
         */
        $('html, body').animate({scrollTop: 0}, '');
        $("#resulterr").show("slow");
        $('#resulterr strong').html('You must select Type of Investment, no blank selection field allowed before submission.');
        setTimeout(function() {
            $('#resulterr strong').html('');
            $("#resulterr").hide("slow");
        }, 5000);
        return false;
    }
    count = 0;
    $('input[name="investment_amount[]"]').each(function(){
        if (($.trim($(this).val()) === '') || isNaN($.trim($(this).val()))) {
            ++count;
        }
    });
    if(count > 0){

        /*
         * Display an Error if Investment Amount not specified for any of Investment Declaration Record
         */
        $('html, body').animate({scrollTop: 0}, '');
        $("#resulterr").show("slow");
        $('#resulterr strong').html('You must Enter the amount in valid numbers.');
        setTimeout(function() {
            $('#resulterr strong').html('');
            $("#resulterr").hide("slow");
        }, 5000);
        return false;
    }

     count = 0;
    $('input[name="description[]"]').each(function(){
        var valtest = /^[a-zA-Z0-9 _-]+/;
       /*  alert(valtest.test($.trim($(this).val())));
            return false;*/
        if (!valtest.test($.trim($(this).val()))) {
           
         
            ++count;
        }
    });
    if(count > 0){

        /*
         * Display an Error if Investment Amount not specified for any of Investment Declaration Record
         */
        $('html, body').animate({scrollTop: 0}, '');
        $("#resulterr").show("slow");
        $('#resulterr strong').html('Description should not contains special characters (i.e. #, $, %, !) or should not be blank.');
        setTimeout(function() {
            $('#resulterr strong').html('');
            $("#resulterr").hide("slow");
        }, 5000);
        return false;
    }
}

function openEdit(recordId){
    $("#popupcontainerdiv").show("slow");
    $('#popupcontainerdiv alert_title').html('Edit Employee Declaration');
    return false;
}


// Catch the form submit and upload the files
function uploadFiles(event)
{
    
    var l = window.location;
    var base_url = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1];
    
    event.stopPropagation(); // Stop stuff happening
    event.preventDefault(); // Totally stop stuff happening

    // START A LOADING SPINNER HERE

    // Create a formdata object and add the files
	var data = new FormData();
	$.each(files, function(key, value)
	{
            data.append(key, value);
	});
    
    $.ajax({
        url: base_url+'/tax_module/tax_declaration/save_emp_investment_details/',
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function(data, textStatus, jqXHR)
        {
        	if(typeof data.error === 'undefined')
        	{
        		// Success so call function to process the form
        		submitForm(event, data);
        	}
        	else
        	{
        		// Handle errors here
        		console.log('ERRORS: ' + data.error);
        	}
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
        	// Handle errors here
        	console.log('ERRORS: ' + textStatus);
        	// STOP LOADING SPINNER
        }
    });
}

function popUpClosed() {
    window.location.reload();
}
    