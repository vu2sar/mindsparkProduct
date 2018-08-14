<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
	<div class="row"> 
		<div class="col-sm-6">
		<h3 align="center">Simulation Form</h3>
		<form method="post" action="<?php echo site_url('Language/simulationflow/filter'); ?>">
			<div class="form-group row">
				<div class="col-xs-6">
					<label for="ex1">User ID <span class="text-danger">*</span></label>
					<input class="form-control" required="required" name="userid" id="userid" type="text">
					<div id="useriderror"></div>
				</div>
				<div class="col-xs-6">
					<label for="ex2">Class <span class="text-danger">*</span></label>
					<input class="form-control" required="required" name="childclass" id="childclass" type="text">
				</div>
			</div>

			<div class="form-group row">
				<div class="col-xs-6">
					<label for="ex1">School Code </label>
					<input class="form-control" name="schoolcode" id="schoolcode" type="text">
				</div>
				<div class="col-xs-6">
					<label for="ex2">Passage Percent Correct  <small>(%)</small> <span class="text-danger">*</span></label>
					<input class="form-control" min="1" max="100" name="passagepercent" id="passagepercent" required="required" type="number">
				</div>
			</div>

			<div class="form-group row">
				<div class="col-xs-6">
					<label for="ex1">Passage Remediation </label>
					<input class="form-control" name="passageremediation" id="passageremediation" type="text">
				</div>
				<div class="col-xs-6">
					<label for="ex2">Passage Remediation Accurcy  <small>(%)</small> </label>
					<input class="form-control" min="1" max="100" name="passageremediationacc" id="passageremediationacc" type="number">
				</div>
			</div>

			<div class="form-group row">
				<div class="col-xs-6">
					<label for="ex1">Activate Group Skill </label>
					<input class="form-control"  name="activegroupskill" id="activegroupskill"  type="text">
				</div>
				<div class="col-xs-6">
					<label for="ex2">Simulate Number of Flow </label>
					<input class="form-control" name="numberflow" id="numberflow"   type="text">
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6 col-sm-offset-3">
					<input type="submit" class="btn btn-primary" value="Submit">
				</div>
			</div>
		</form>
		</div>
	</div>
</div>
</body>
</html>


<script>
$(document).ready(function(){

   $("#userid").keyup(function(){
      var userID = $("#userid").val().trim();
      if(userID != ''){
         $.ajax({
            url: '<?php echo site_url('Language/simulationflow/userIDcheck') ?>',
            type: 'post',
            data: {userID:userID},
            success: function(response){
                if(response==0){
                    $("#useriderror").html("<span class='text-danger'>Invalid User Id. Please enter Correct User ID</span>");
                }
                else{
                	alert("hai");
                }
             }
          });
      }
    });
 });
</script>