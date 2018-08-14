$.ajax({  
		  type: "POST",  
		  url: "getHomeDetails.php",
		  data : "mode=mindsparkinnumbers",
		  async: true,
		  success: function(msg){
			  $('#testimonial-list').html(msg);
			  $('#testimonials #overallUsageChart_loading').hide();
		  }  
		});
$.ajax({  
	  type: "POST",  
	  url: "getHomeDetails.php",
	  dataType : "json",
	  async: true,
	  data : "mode=strength-weakness",
	  success: function(msg){
		  $('#strength-weekness .strength-container').html(msg['strength-weekness']);
		  $('#strength-weekness .strength-container').show();
		  $('#strength-weekness #overallUsageChart_loading').hide();
		  
	  }  
	});
$.ajax({  
	  type: "POST",  
	  url: "getHomeDetails.php",
	  data : "mode=class-data",
	  async: true,
	  success: function(msg){
		  $('#class-details .strength-container').html(msg);
		  $('#class-details .strength-container').show();
		  $('#class-details #overallUsageChart_loading').hide();
	  }  
	});
$.ajax({  
	  type: "POST",  
	  url: "getHomeDetails.php",
	  data : "mode=alerts",
	  async: true,
	  success: function(msg){
		 if(msg == ""){
			 $('#alerts').hide();
		}else{
			$('#alerts').html(msg);
			$('#alerts #overallUsageChart_loading').hide();
		}
		  
	  }  
	});