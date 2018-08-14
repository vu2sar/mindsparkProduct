$(function() 
            {   
                    
              var to = $( "#userid" ),
              message = $( "#txtMessage" ),
              allFields = $( [] ).add( to ).add( message ),
              tips = $( ".validateTips" );    
            
            function split( val ) {
                return val.split( /,\s*/ );
            }
            function extractLast( term ) {
                console.log(term);
				return split( term ).pop();
				
            }
			
 
            $('#userid').inputosaurus({
                width : '245px',
                autoCompleteSource : 'names_ajax.php',
                activateFinalResult : true,
                change : function(ev){
                    $('#useridval').val(ev.target.value);
                }
            });
 
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
                  
                  if(userid.length == 0)
                  {
                        alert('Please fill in the person you wish to send a Kudos!');
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
                        alert('You cannot send a kudos to yourself');
                        bValid = false;
                        return false;   
                  }
                  
                  if(msg.length == 0)
                  {
                        alert('Please fill in the message for the Kudo');
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
                        alert('Length of Message should be less than 300');
                        bValid = false;
                        return false;
                  }
 
                  if ( bValid ) 
                  {
                     if(confirm("Do you wish to send the kudos?"))
                    {
                        var str = $( "#useridval" ).val();
						
						str1 = str.replace(/Mr./g ," " );
						strFinal= str1.replace(/Ms./g ," " );
																								
						var nameToSend = strFinal;
						var childClass = $("#childClass").val();
												
						//alert("STR IS - "+str);+//"Val to Send is-"+split1[1]);
						//alert("STR FINAL IS - "+strFinal);
						//alert("Hidden category dropdown is - "+$("#category-select-dropdown option:selected").val());
											
						//var nameToSend = split2[1];
						
						$( "#hdnAction" ).val('sendKudo');
                        $( "#hdnTo" ).val(nameToSend);
						$( "#hdnToClass" ).val(childClass);
						$( "#hdnMessage" ).val($( "#txtMessage" ).val());
                        $( "#hdnCategoryDropdown").val($("#category-select-dropdown option:selected").val());
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
                $( "#userid" ).val('');
                $( "#txtMessage" ).val('');
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
                $( "#sendKudo" ).dialog( "open" );
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
				//console.log(kudos_id);
				
				$.ajax({
				type:'POST',
				url: "kudosModalAjax.php",
				data: {kudos_id:kudos_id},
				success: function(data)
				{
					//console.log("In showCertificateModal");
					$('#certificateModal').html(data);
				
				} });	
										
				$.fn.colorbox({'href':'#certificateModal','inline':true,'open':true,'escKey':true, 'height':690, 'width':1050});
			}