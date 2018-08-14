var xmlHttp;

function ajaxValidation(email, async)
{
//    if(!echeck(email))
//    {
//        setEmailCheckMessage(0);
//        return;
//    }
    xmlHttp = GetXmlHttpObject();
    if (xmlHttp == null)
    {
        //alert ("Your browser does not support AJAX!");
        return;
    }

    var url = "ajaxValidation.php";
    url = url + "?email=" + email;
    xmlHttp.onreadystatechange = function() {
        stateChanged();
    };
    xmlHttp.open("GET", url, async);
    xmlHttp.send(null);
//    if (mode == 3)
//    {
//        if (xmlHttp.status === 200) {
//            return xmlHttp.responseText;
//        }
//    }
}

function stateChanged()
{
    if (xmlHttp.readyState == 4)
    {
        setEmailCheckMessage(xmlHttp.responseText);
    }
}

function setEmailCheckMessage($msg)
{
    if ($msg == 1)
    {
        document.getElementById("errMsg").style.display = "inline";
        document.getElementById("errMsg2").style.display = "none";
        document.getElementById("existsEmail").value = 1;
    }
    else if($msg==2)
    {
        document.getElementById("errMsg").style.display = "none";
        document.getElementById("errMsg2").style.display = "inline";
        document.getElementById("existsEmail").value = 1;
    }
    else
    {
        document.getElementById("errMsg").style.display = "none";
        document.getElementById("errMsg2").style.display = "none";
        document.getElementById("existsEmail").value = 0;
    }
}

function trim(str) {
    // Strip leading and trailing white-space
    return str.replace(/^\s*|\s*$/g, "");
}

function GetXmlHttpObject()
{
    var xmlHttp = null;
    try
    {
        // Firefox, Opera 8.0+, Safari
        xmlHttp = new XMLHttpRequest();
    }
    catch (e)
    {
        // Internet Explorer
        try
        {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e)
        {
            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    return xmlHttp;
}
