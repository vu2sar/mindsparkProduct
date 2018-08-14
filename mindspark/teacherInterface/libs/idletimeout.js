var timer=setInterval("auto_logout()",1200000);
function reset_interval()
{
clearInterval(timer);
//second step: implement the timer again
timer=setInterval("auto_logout()",1200000);
}
function auto_logout()
{
//this function will redirect the user to the logout script
window.location="logout.php";
}