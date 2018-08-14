// JavaScript Document
//var resultNamesOfStudents = ['kalpesh', 'aditya'];
$(function() 
            {   
			
			// onChangeSubmit(1);
			 loadClassDetails();
			 initKudosFilter();
			 
			 $("[title~='close']").click(function(){ alert(1); console.log("TEST");  }); //$('#category-select').trigger("reset");
			 
                    
              var to = $( "#userid" ),
              message = $( "#txtMessage" ),
              allFields = $( [] ).add( to ).add( message ),
              tips = $( ".validateTips" );    
            
            function split( val ) {
                return val.split( /,\s*/ );
            }
            function extractLast( term ) {
                return split( term ).pop();
            }
 
            $('#userid').inputosaurus({
                width : '245px',
                autoCompleteSource : '../userInterface/src/kudos/names_ajax.php', //resultNamesOfStudents, [json array]
                activateFinalResult : true,
				cacheLength: 0,
                change : function(ev){
					//this.autoCompleteSource = resultNamesOfStudents;
                    $('#useridval').val(ev.target.value);
					
				}
            });
			
			$('.ui-autocomplete-input').bind('keypress', onlyAlphabets);
 
            function updateTips( t ) {
              tips
                .text( t )
                .addClass( "ui-state-highlight" );
              setTimeout(function() {
                tips.removeClass( "ui-state-highlight", 1500 );
              }, 500 );
            }
 
            function checkLength( o, n, min, max ) {
              if ( o.val().length > max || o.val().length <= min ) {
               // o.addClass( "ui-state-error" );
                alert( "Length of " + n + " must be between " +
                  min + " and " + max + "." );
                return false;
              } else {
                return true;
              }
            }
 				
            function checkRegexp( o, regexp, n ) {
              if ( !( regexp.test( o.val() ) ) ) {
                o.addClass( "ui-state-error" );
                updateTips( n );
                return false;
              } else {
                return true;
              }
            }
			
             
            $( "#btnSendKudo" ) 
            .button()
             .click(function() 
             {
                 var bValid = true;
                  allFields.removeClass( "ui-state-error" );
 
                 var userid = $( '#useridval' ).val();
                 var useridAutoComp = $( '#userid' ).val();
                 var msg = $( '#txtMessage' ).val();
                 msg = $.trim(msg);
                 msg = msg.replace(/\s{2,}/g, ' ');
                 $( '#txtMessage' ).val(msg);
                 var self = '<?php echo $userName ?>';
                 /*var category = <?= $category ?>;
				 //console.log("Category is - "+category); */
                  if(userid.length == 0)
                  {
					  	alert('Please fill in the person you wish to send a Kudos to after a category from the dropdown menu!');
                        bValid = false;
                        return false;
                  }
                  /*else if(userid.length > 30)
                  {
                        alert('Length of To should be less than 30');
                        bValid = false;
                        return false;
                  }*/
                  else if(self == userid)
                  {
                        alert('You cannot send a kudos to yourself!');
                        bValid = false;
                        return false;   
                  }
				  
				   $.ajax({
					 type: "GET",
					 url: "../userInterface/src/kudos/names_ajax.php",
					 dataType: "json",
					 async:false,
				 	 success: function(data){
					    var j=0;
						//console.log(data+"IN AJAX ");
						
						var res = userid.split(",");
						
						for(i=0;i<res.length;i++){ if(jQuery.inArray(res[i], data)==-1){j=99;} }
						
						if(j==99)
						  {	  	
							  //console.log(userid+" "+data);
							  alert('Please select the correct name from the dropdown!');
							  bValid = false;
							  return false;
						  }
						
						  //do your stuff with the JSON data
				 	}
				});
                  
                  if(msg.length == 0)
                  {
                        alert('Please fill in the message for the Kudos!');
                        bValid = false;
                        return false;
                  }
                  /*else if(msg.length < 25)
                  {
                        alert('Message length is too short \n It should atleast be 25 characters');
                        bValid = false;
                        return false;
                  }*/
                  else if(msg.length > 300)
                  {
                        alert('Length of the Message should be less than 300');
                        bValid = false;
                        return false;
                  }
 
                  if ( bValid ) 
                  {
                    if(confirm("Do you wish to send the kudos?"))
                    {
						categoryDropdown=$("#category-select-dropdown option:selected").val();
						//alert(categoryDropdown);
						/*if(categoryDropdown=='student')
						{
						
						var str = $( "#useridval" ).val();
						var split1 = str.split('-');
						
						var nameToSend = split1[0];
						var toClass=split1[1].charAt(1);
						
						}*/
						
						var str = $( "#useridval" ).val();
						str1 = str.replace(/Mr./g ," " );
						strFinal= str1.replace(/Ms./g ," " );									
						var nameToSend = strFinal;							
						
						
                        $( "#hdnAction" ).val('sendKudo');
                        $( "#hdnTo" ).val(nameToSend);
						$( "#hdnMessage" ).val($( "#txtMessage" ).val());
                        $( "#hdnCategoryDropdown" ).val(categoryDropdown );
						
						if(categoryDropdown=='student')
						{var toClass=$("#class-select-dropdown option:selected").val(); $( "#hdnToClass" ).val(toClass);
						 var toSection=$("#section-select-dropdown option:selected").val(); $( "#hdnToSection" ).val(toSection);	
						}
						$( "#formmain" ).submit();
		
						//alert($( "#hdnCategoryDropdown" ).val() );															
                    }                                   
                  }
              });
 			
			$( "#sendKudo" ).dialog({
              dialogClass: "sendKudoDialog",
              resizable: false,
              draggable: false,
              autoOpen: false,
              title: "Send a Kudos!", 
              height: 450,
              width: 300,
              modal: true,
              
             /* show: {
                effect: "blind",
                duration: 1000
              },
              
              hide: {
                effect: "clip",
                duration: 1000
              },*/
              
                /*$('btnThankYou').disabled = true;
                $('btnGoodWork').disabled = true;
                $('btnImpressive').disabled = true;
                $('btnExceptional').disabled = true;*/
              
              
              
              close: function() {
                //$( "#userid" ).val('').removeClass( "ui-state-error" );
				//$( "#useridval" ).val('');
                //$( "#txtMessage" ).val('');
				//$( ".inputosaurus-container" ).val('');
				//$('#category-select')[0].reset();
                //$("#section-select-dropdown").hide();
				//$( "input" ).val('');
                allFields.val( "" ).removeClass( "ui-state-error" );                
              }         
            });
 
            $( "#btnThankYou" )
              .button()
              .click(function() {
                $( "#hdnType" ).val('Thank You');
                var srcImg = "images/thankyou.png";
                $( '#typeImage' ).attr("src", srcImg);
				$( "#sendKudo" ).dialog("open");
              });
              $( "#btnGoodWork" )
              .button()
              .click(function() {
                $( "#hdnType" ).val('Good Work');
                var srcImg = "images/goodwork.png";
                $( '#typeImage' ).attr("src", srcImg);
                $( "#sendKudo" ).dialog( "open" );
              });
              $( "#btnImpressive" )
              .button()
              .click(function() {
                $( "#hdnType" ).val('Impressive');
                var srcImg = "images/impressive.png";
                $( '#typeImage' ).attr("src", srcImg);
                $( "#sendKudo" ).dialog( "open" );
              });
              $( "#btnExceptional" )
              .button()
              .click(function() {
                $( "#hdnType" ).val('Exceptional');
                var srcImg = "images/exceptional.png";
                $( '#typeImage' ).attr("src", srcImg);
                $( "#sendKudo" ).dialog( "open" );
              });
            
			});
			
			function showCertificateModal(kudos_id)
			{
				
				
				$.ajax({
				type:'POST',
				url: "../userInterface/src/kudos/kudosModalAjax.php",
				data: {kudos_id:kudos_id},
				success: function(data)
				{
					
					$('#certificateModal').html(data);
				
				} });	
										
				$.fn.colorbox({'href':'#certificateModal','inline':true,'open':true,'escKey':true, 'height':690, 'width':1050});
			}	
			
			function onlyAlphabets(e, t) {
				try {
					if (window.event) {
						var charCode = window.event.keyCode;
					}
					else if (e) {
						var charCode = e.which;
					}
					else { return true; }
					if ((charCode > 64 && charCode < 91) || (charCode > 96 && charCode < 123) || (charCode==37)|| (charCode==39)|| (charCode==8))
						return true;
					else
						e.preventDefault();
				}
				catch (err) {
					alert(err.Description);
				}
			}

			
			