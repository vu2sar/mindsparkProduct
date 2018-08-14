/* 
 * To check the Payroll Data from payroll API folder
 * This will help to check the Net payable salary and based on that Tax Calculation possible
 * @params - UserID, Current Month, financial Year
 */



// Catch the form submit and upload the files
function fetchPayrollData(userID, currentMonth, finYear)
{
    var l = window.location;
    var base_url = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1];
    var formData = new Array();
    var payrollData;
    
    formData.push({name: "userID", value: userID});
    formData.push({name: "month", value: currentMonth});
    formData.push({name: "year", value: finYear});
    
    $.post(base_url+'/payroll/api/render_gross_income.php', formData, function(data){
        if(data != ""){
            // If we received Data from Payroll API then success will be called
          //console.log(data);
          payrollData = jQuery.parseJSON(data);
          return payrollData;
        } else {
          // If we ERROR Data from Payroll API then Failure will be called
          //console.log('ERRORS: ' + data);
          payrollData = jQuery.parseJSON(data);
          return payrollData;
        }
    });
    
    
}