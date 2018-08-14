<?php
//error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING);
error_reporting(E_ALL);
set_time_limit(0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
include("header.php");
include("../userInterface/clsUser.php");
//print_r($_REQUEST);
$keys = array_keys($_REQUEST);
foreach ($keys as $key) {
    ${$key} = $_REQUEST[$key];
}
$displayEmailForm = FALSE;
$userID = $_SESSION['userID'];
$schoolCode = isset($_SESSION['schoolCode']) ? $_SESSION['schoolCode'] : "";
$user = new User($userID);
$category = $user->category;
$_SESSION['teacherEmail'] = $user->childEmail;
$getTeacherEmail = FALSE;
if ($_SESSION['teacherEmail'] == '')
    $getTeacherEmail = TRUE;
if (strcasecmp($category, "Teacher") == 0 || strcasecmp($category, "School Admin") == 0) {
    $query = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=" . $schoolCode;
    $r = mysql_query($query);
    $l = mysql_fetch_array($r);
    $schoolName = $l[0];
}
$class = $_REQUEST['cls'];
$section = $_REQUEST['section'];
$temp = "hello";
if ($class != '')
    $displayEmailForm = TRUE;

function formatName($name) {
    $nameArray1 = explode(' ', $name);
    $nameArray2 = array_map('strtolower', $nameArray1);
    $nameArray = array_map('ucfirst', $nameArray2);
    $name = implode(' ', $nameArray);
    return $name;
}

function sendParentEmail($userID, $parentEmail, $emailContent, $bccTeacher) {
    $user = new User($userID);
    if (trim($user->parentEmail) == '' && trim($parentEmail) != '') {
        $user->updateParentEmail($parentEmail);
        $user = new User($userID);
    }
    $tmpName = explode(" ", $user->childName);
//    echo var_dump($tmpName);
    $firstName = formatName($tmpName[0]);
//    echo $firstName;
    $subject = "$firstName's teacher has sent you a personal message through Mindspark.";
    $headers = "From:<" . $_SESSION['teacherEmail'] . ">\r\n";
//	$headers .= "To:".$parentEmail."\r\n";
    if ($bccTeacher)
        $headers .= "Bcc:" . $_SESSION['teacherEmail'] . ",notification@ei-india.com\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $emailContent = str_replace("[childFirstName]", $firstName, $emailContent);
//    echo 'sentto:' . $parentEmail;
//    echo '<br/>';
//    echo 'subject:'.$subject;
//    echo '<br/>';
//    echo $emailContent;
//    echo '<br/>';
    return mail($parentEmail, $subject, $emailContent, $headers);
}

if (strcasecmp($category, "School Admin") == 0) {
    $query = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
				   FROM     adepts_userDetails
				   WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND endDate>=curdate() AND subjects like '%" . SUBJECTNO . "%'
				   GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
} elseif (strcasecmp($category, "Teacher") == 0) {
    $query = "SELECT   class, group_concat(distinct section ORDER BY section)
				  FROM     adepts_teacherClassMapping
				  WHERE    userID = $userID AND subjectno=" . SUBJECTNO . "
				  GROUP BY class ORDER BY class, section";
} elseif (strcasecmp($category, "Home Center Admin") == 0) {
    $query = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
				   FROM     adepts_userDetails
				   WHERE    category='STUDENT' AND subcategory='Home Center' AND schoolCode=$schoolCode AND enabled=1 AND endDate>=curdate() AND subjects like '%" . SUBJECTNO . "%'
				   GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
} else {
    echo "You are not authorised to access this page!";
    exit;
}
$classArray = array();
$sectionArray = array();
$failedDelivery = array();
$emailSentMessage = FALSE;
//echo $query;
$result = mysql_query($query) or die(mysql_error());
while ($line = mysql_fetch_array($result)) {
    array_push($classArray, $line[0]);
    if ($line[1] != '')
        $hasSections = true;
    $sections = explode(",", $line[1]);
    $sectionStr = "";
    for ($i = 0; $i < count($sections); $i++) {
        if ($sections[$i] != "")
            $sectionStr .= "'" . $sections[$i] . "',";
    }
    $sectionStr = substr($sectionStr, 0, -1);
    array_push($sectionArray, $sectionStr);
}
if (isset($_POST['studentName'])) {
    $studentID = $_POST['studentName'];
    $parentEmail = $_POST['parentEmail'];
    $emailContent = nl2br($_POST['emailContent']);
    $emailFooter = "To check [childFirstName]'s reports login into the Mindspark Parent Connect (www.mindspark.in/mindspark/login/).";
    $emailContent .= '<br/><br/>' . $emailFooter;
    $bccTeacher = true;
    $studentID = array_unique($studentID);
//    echo var_dump($studentID);
    for ($i = 0; $i < count($studentID); $i++) {
        if ($studentID[$i] == '')
            continue;
        if (!sendParentEmail($studentID[$i], $parentEmail[$i], $emailContent, $bccTeacher)) {
            array_push($failedDelivery, $parentEmail[$i]);
        }
        $bccTeacher = false;
    }
    $emailSentMessage = true;
    $displayEmailForm = FALSE;
}
if (isset($_POST['teacherEmail'])) {
    $teacherEmail = $_POST['teacherEmail'];
    $userID = $_SESSION['userID'];
    $user = new User($userID);

    if ($teacherEmail != '')
        $user->updateEmail($teacherEmail);
    $_SESSION['teacherEmail'] = $teacherEmail;
    $getTeacherEmail = FALSE;
}
//array_push($failedDelivery, 'ruchit.rami@ei-india.com');
?>

<title>Mail to parents</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/token-input-facebook.css?ver=1" rel="stylesheet" type="text/css">
<link href="css/resetStudentPassword.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="libs/css/jquery-ui.css" />
<link rel="stylesheet" href="/resources/demos/style.css" />
<script>
    $(function() {
        $(".datepicker").datepicker();
    });
    var gradeArray = new Array();
    var sectionArray = new Array();
<?php
for ($i = 0; $i < count($classArray); $i++) {
    echo "gradeArray.push($classArray[$i]);\r\n";
    echo "sectionArray[$i] = new Array($sectionArray[$i]);\r\n";
}
?>
</script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/jquery.tokeninputNew.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<!--<script type="text/javascript" src="libs/placeholders.min.js"></script>-->
<!--<script type="text/javascript" src="libs/jquery.placeholder.js"></script>-->
<style type="text/css">
    .token-input-list-facebook
    {
        width: 330px!important;
        display: -webkit-inline-flex;
        /*display: -webkit-box;       OLD - iOS 6-, Safari 3.1-6 */
        display: -moz-box;         /* OLD - Firefox 19- (buggy but mostly works) */
        display: -ms-flexbox;      /* TWEENER - IE 10 */
        /*display: -webkit-flex;      NEW - Chrome */
        /*display: flex;              NEW, Spec - Opera 12.1, Firefox 20+ */
        /*display: inline-flex;*/
        /*display: inline-block;*/
        /*position: absolute;*/
    }
    .validationError
    {
        border-color:red;
    }
    .parentEmail
    {
        /*display: inline-block;*/
        /*display: inline-flex;*/
        /*position: absolute;*/
        padding: 4px;
        margin: 0px;
        margin-left: 5px;
        width: 180px;
    }
    .placeholder { color: #aaa; }
    input, textarea { color: #000; }
</style>
<script>
    var langType = '<?= $language; ?>';
    $(document).ready(function(e) {
        setSection('<?= $section ?>');
        $('#mailAllParents').change(function() {
            if ($(this).is(":checked")) {
                populateClassStudents();
//                document.forms['frmEmail'].submit();
            }
            else
            {
                clearEmails();
                addMoreEmailFunction();
                addMoreEmailFunction();
                addMoreEmailFunction();
            }
        });
    });
    var selectClass = '<?php echo $class ?>';
    var section = "<?php echo $section ?>";
    var schoolCode = "<?= $schoolCode ?>";
    function addTokenInput(selectorClass)
    {
        $(selectorClass).tokenInput("getautocompleteUsers.php?class=" + selectClass + "&section=" + section + "&schoolCode=" + schoolCode, {
            hintText: "Search by student name",
            theme: "facebook",
            searchingText: "Mindspark is searching...",
            noResultsText: "No similar student name found",
            tokenLimit: 1,
            placeholder: 'Student name',
            onAdd: function(item) {
                $(this).parent().parent().find('.parentEmail').val(item.parentEmail);
                $(this).parent().parent().find('.parentEmail').focus();
                if (item.parentEmail == '' || item.parentEmail == null)
                {
                    $(this).parent().parent().find('.parentEmail').addClass('validationError');
                    $(this).parent().parent().find('.parentEmail').attr('placeholder', 'Parent email id not available');
//                    $('input, textarea').placeholder();
                }
                else
                {
                    $(this).parent().parent().find('.parentEmail').removeClass('validationError');
                    $(this).parent().parent().find('.parentEmail').attr('placeholder', 'Parent\'s email id');
//                    $('input, textarea').placeholder();
                }
            },
            onDelete: function(item) {
                $(this).parent().parent().find('.parentEmail').val('');
                $(this).parent().parent().find('.parentEmail').removeClass('validationError');
                $(this).parent().parent().find('.parentEmail').attr('placeholder', 'Parent\'s email id');
//                $('input, textarea').placeholder();
            }
        });
        $(".studentNameNew:eq(1)").addClass("studentName").removeClass("studentNameNew");
    }
    $(document).ready(function() {
        var rs = "";
        addTokenInput(".studentName");

<?php if (count($failedDelivery) > 0) { ?>
            var alertString = '<?php echo implode('\n', $failedDelivery); ?>';
            alert('The message could not be delivered to the following email ids-\n' + alertString);
<?php } else if ($emailSentMessage) { ?>
            alert('The emails have been sent! You would also receive a copy of the mail sent to one of the parents.');
<?php } ?>
<?php if ($displayEmailForm) { ?>
            $('#frmEmail').show();
<?php } else { ?>
            $('#frmEmail').hide()
<?php } ?>
//        addTokenInput();
    });

    function load() {
//        var fixedSideBarHeight = window.innerHeight;
        var sideBarHeight = window.innerHeight - 95;
        var containerHeight = window.innerHeight - 115;
//        $("#fixedSideBar").css("height", fixedSideBarHeight + "px");
        $("#sideBar").css("height", sideBarHeight + "px");
        /*$("#container").css("height",containerHeight+"px");*/
        $("#features").css("font-size", "1.4em");
        $("#features").css("margin-left", "40px");
        $(".arrow-right-blue").css("margin-left", "10px");
        $(".rectangle-right-blue").css("display", "block");
        $(".arrow-right-blue").css("margin-top", "3px");
        $(".rectangle-right-blue").css("margin-top", "3px");
//        if (document.getElementById('rdStudent').checked == true) {
//            showDiv('student');
//        } else {
//            showDiv('teacher');
//        }
    }
    function populateClassStudents()
    {
        $.get("getautocompleteUsers.php?class=" + selectClass + "&section=" + section + "&schoolCode=" + schoolCode, function(data) {
            $('#tblStudentEmail').find('tbody').find('tr').remove();
            for (i = 0; i<=data.length; i++)
            {
                if(data[i]!==undefined)
                    prepopulateEmail(data[i]);
            }
        }, "json");
    }
    function prepopulateEmail(data)
    {
        $("#tblStudentEmail").find('tbody')
                .append($('<tr>')
                        .append($('<td>')
                                .append($('.studentNameNew:first').clone()
                                        )
                                )
                        .append($('<td>')
                                .append($('.parentEmail:first').clone()
                                        )
                                ).addClass('studentEmail')
                        );
        prepopulateTokenInput(data);
    }
    function prepopulateTokenInput(data)
    {
        var id = data.id;
        var name = data.name;
        $(".studentNameNew:eq(1)").parent().parent().find('.parentEmail').val(data.parentEmail);
        if (data.parentEmail == '' || data.parentEmail == null)
                {
                    $(".studentNameNew:eq(1)").parent().parent().find('.parentEmail').addClass('validationError');
                    $(".studentNameNew:eq(1)").parent().parent().find('.parentEmail').attr('placeholder', 'Parent email id not available');
                }
        $(".studentNameNew:eq(1)").tokenInput("getautocompleteUsers.php?class=" + selectClass + "&section=" + section + "&schoolCode=" + schoolCode, {
            prePopulate: [
                {id: id, name: name}
            ],
            hintText: "Search by student name",
            theme: "facebook",
            searchingText: "Mindspark is searching...",
            noResultsText: "No similar student name found",
            tokenLimit: 1,
            placeholder: 'Student name',
            onAdd: function(item) {
                $(this).parent().parent().find('.parentEmail').val(item.parentEmail);
                $(this).parent().parent().find('.parentEmail').focus();
                if (item.parentEmail == '' || item.parentEmail == null)
                {
                    $(this).parent().parent().find('.parentEmail').addClass('validationError');
                    $(this).parent().parent().find('.parentEmail').attr('placeholder', 'Parent email id not available');
                }
                else
                {
                    $(this).parent().parent().find('.parentEmail').removeClass('validationError');
                    $(this).parent().parent().find('.parentEmail').attr('placeholder', 'Parent\'s email id');
                }
            },
            onDelete: function(item) {
                $(this).parent().parent().find('.parentEmail').val('');
                $(this).parent().parent().find('.parentEmail').removeClass('validationError');
                $(this).parent().parent().find('.parentEmail').attr('placeholder', 'Parent\'s email id');
            }
        });
        $(".studentNameNew:eq(1)").addClass("studentName").removeClass("studentNameNew");
    }
    function removeAllOptions(selectbox)
    {
        var i;
        for (i = selectbox.options.length - 1; i > 0; i--)
        {
            selectbox.remove(i);
        }
    }
    function validate()
    {
        if (document.getElementById('lstClass').value == "")
        {
            alert("Please select a Class!");
            document.getElementById('lstClass').focus();
            return false;
        } else if (document.getElementById('lstSection').value == "" && $(".noSection").is(":visible"))
        {
            alert("Please select a Section!");
            document.getElementById('lstSecton').focus();
            return false;
        }
        else {
			setTryingToUnload();
            document.forms["frmMain"].submit();
        }
    }
    function setSection(sec)
    {
        var cls = document.getElementById('lstClass').value;
        if (document.getElementById('lstSection'))
        {
            var obj = document.getElementById('lstSection');
            removeAllOptions(obj);
            if (cls == "")
            {
                document.getElementById('lstSection').style.display = "inline";
                document.getElementById('lstSection').selectedIndex = 0;
            }
            else
            {
                for (var i = 0; i < gradeArray.length && gradeArray[i] != cls; i++)
                    ;
                if (sectionArray[i].length > 0)
                {
                    $(".noSection").show();
                    for (var j = 0; j < sectionArray[i].length; j++)
                    {
                        OptNew = document.createElement('option');
                        OptNew.text = sectionArray[i][j];
                        OptNew.value = sectionArray[i][j];
                        if (sec == sectionArray[i][j])
                            OptNew.selected = true;
                        obj.options.add(OptNew);
                    }
                }
                else
                {
                    $(".noSection").hide();
                }
            }
        }
    }
    function addMoreEmailFunction()
    {
//        $("#studentEmail").append($("#emailElement").html());
        $("#tblStudentEmail").find('tbody')
                .append($('<tr>')
                        .append($('<td>')
                                .append($('.studentNameNew:first').clone()
                                        )
                                )
                        .append($('<td>')
                                .append($('.parentEmail:first').clone()
                                        )
                                ).addClass('studentEmail')
                        );
        addTokenInput(".studentNameNew:eq(1)");
//        $( "#emailElement" ).html().appendTo( "#studentEmail" );
    }
    function clearEmails()
    {
        $("#tblStudentEmail").find('tbody').html('');
    }
    function validateEmail()
    {
        var validation = 2;
        $('#tblStudentEmail').find('.studentEmail').each(function() {
            if ($(this).find('.studentName').length > 0)
            {
                var name = $(this).find('.studentName').val();
                var email = $(this).find('.parentEmail').val();
                if (name != '' && email == '')
                {
                    $(this).find('.parentEmail').addClass('validationError');
                    $(this).find('.parentEmail').attr('placeholder', 'Parent email id not available');
//                    $('input, textarea').placeholder();
                    validation = 1;
                }
                else if (name != '' && email != '')
                {
                    if (validation != 1)
                        validation = 0;
                }
                if (email != '')
                {
                    if (!echeck(email))
                    {
                        $(this).find('.parentEmail').addClass('validationError');
                        $(this).find('.parentEmail').attr('placeholder', 'Parent\'s email id');
//                        $('input, textarea').placeholder();
                        validation = 3;
                    }
                    else
                    {
                        $(this).find('.parentEmail').removeClass('validationError');
                        $(this).find('.parentEmail').attr('placeholder', 'Parent\'s email id');
//                        $('input, textarea').placeholder();
                    }
                }
            }
        });
//        alert(validation);
        if (validation == 1)
            alert('You have not entered the parent mail id for all the students.');
        if (validation == 2)
            alert('You have to enter at least one child name to send email.');
        if (validation == 3)
            alert('Please enter valid email address.');
        if (validation != 0)
            return false;
        if ($('#emailContent').val() == '')
        {
            alert('Please enter the email content');
            return false;
        }
		setTryingToUnload();
        document.forms['frmEmail'].submit();
    }
    function validateTeacherEmail()
    {
        var teacherEmail = $('#teacherEmail').val();
        if (!echeck(teacherEmail))
        {
            alert('Please enter valid email address.');
            return false;
        }
		setTryingToUnload();
        document.forms['frmTeacherEmail'].submit();
    }
    function echeck(str) {
        var at = "@";
        var dot = ".";
        var lat = str.indexOf(at);
        var lstr = str.length;
        var ldot = str.indexOf(dot);
        if (str.indexOf(at) == -1) {
            //alert("Invalid e-mail");
            return false;
        }
        if (str.indexOf(at) == -1 || str.indexOf(at) == 0 || str.indexOf(at) == lstr) {
            //alert("Invalid e-mail");
            return false;
        }
        if (str.indexOf(dot) == -1 || str.indexOf(dot) == 0 || str.indexOf(dot) == lstr) {
            //alert("Invalid e-mail");
            return false;
        }
        if (str.indexOf(at, (lat + 1)) != -1) {
            //alert("Invalid e-mail");
            return false;
        }
        if (str.substring(lat - 1, lat) == dot || str.substring(lat + 1, lat + 2) == dot) {
            //alert("Invalid e-mail");
            return false;
        }
        if (str.indexOf(dot, (lat + 2)) == -1) {
            //alert("Invalid e-mail");
            return false;
        }
        if (str.indexOf(" ") != -1) {
            //alert("Invalid e-mail");
            return false;
        }
        return true;
    }
    function divScroll()
    {
        var mydiv = $('#studentEmail');
        mydiv.scrollTop(mydiv.prop('scrollHeight'));        
    }
</script>
</head>
<body class="translation" onLoad="load()" onResize="load()">
    <?php include("eiColors.php") ?>
    <div id="fixedSideBar">
        <?php include("fixedSideBar.php") ?>
    </div>
    <div id="topBar">
        <?php include("topBar.php") ?>
    </div>
    <div id="sideBar">
        <?php include("sideBar.php") ?>
    </div>
    <div id="container">
        <div id="innerContainer">

            <div id="containerHead">
                <div id="triangle"> </div>
                <span>Mail parents</span>
                <span style="float:right; font-size:12px;<?php echo ($getTeacherEmail ? 'display:none' : ($displayEmailForm?'':'display:none')) ?>">A copy of this mail will be sent to your email id too.</span>
            </div>            
            <div <?php echo (!$getTeacherEmail ? 'style="display:none"' : '') ?>>
                <form name="frmTeacherEmail" id="frmTeacherEmail" method="post">
                    <br/>
                    Please enter your email address so parents can write back to seek clarification:<input type="text" id="teacherEmail" name="teacherEmail" />
                    <!--<br/>-->
                    <input type="button" class="button" name="submitTeacherEmail" id="submitTeacherEmail" name="Submit" value="Submit" onClick="validateTeacherEmail();" />
                </form>
            </div>
            <div id="divMainForm" <?php echo ($getTeacherEmail ? 'style="display:none"' : '') ?>>
                <form name="frmMain" id="frmMain" method="post">
                    <table id="topicDetails">
                        <td width="5%"><label>Class</label></td>
                        <td width="22%" style="border-right:1px solid #626161">
                            <select name="cls" id="lstClass"  onchange="setSection('');" style="width:65%;">
                                <option value="">Select</option>
                                <?php
                                for ($i = 0; $i < count($classArray); $i++) {
                                    echo "<option value='" . $classArray[$i] . "'";
                                    if ($class == $classArray[$i]) {
                                        echo " selected";
                                    }
                                    echo ">" . $classArray[$i] . "</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <input type="hidden" name="openTab" id="openTab" value="1"/>
                        <td width="7%" class="noSection"><label style="margin-left:20px;" id="lblSection" >Section</label></td>
                        <td width="22%" class="noSection" style="border-right:1px solid #626161" >
                            <select name="section" id="lstSection" style="width:85%;">
                                <option value="">Select</option>
                            </select>
                        </td>
                        <td>
                            <input type="button" class="button" name="btnGenerate" id="generate" value="Go" onClick="validate();">
                        </td>
                    </table>

                </form>
            </div>
            <br/>
            <form method="post" id="frmEmail" style="display:none">
                <div id="emailForm">
                    <div>
                        <div id="emailElement" style="display:none">

                            <div class='studentEmail' style="padding:1px;">
                                <input type="text" id="studentName" class='studentNameNew' name="studentName[]" autocomplete="off" placeholder="Student name" autocomplete="off" width="500px" />

                                <input type="text" id="parentEmail" class='parentEmail' name="parentEmail[]" placeholder="Parent's email id" />
                            </div>
                        </div>
                        To:
                        <div id="studentEmail" style="height:120px; overflow-y:auto; text-align: left; ">
                            <!--<div id="studentEmail" >-->
                            <table id="tblStudentEmail"  >
                                <thead style="text-align:center;" >
                                    <tr>
                                        <th>Student name</th>
                                        <th style="padding-left:7px;">Parent email ID</th>
                                    </tr>
                                </thead>
<!--                                <tbody style="height:120px; overflow-y:auto; display:block; width: 100%">-->
                                <tbody >
                                    <tr class="studentEmail">
                                        <td>
                                            <input type="text" id="studentName" style="width:165px;" class='studentName' name="studentName[]" autocomplete="off" placeholder="Student name" width="500px" />
                                        </td>
                                        <td>
                                            <input type="text" id="parentEmail"  class='parentEmail' name="parentEmail[]" placeholder="Parent's email id" />
                                        </td>
                                    </tr>
                                    <tr class="studentEmail">
                                        <td>
                                            <input type="text" id="studentName" style="width:165px;" class='studentName' name="studentName[]" autocomplete="off" placeholder="Student name" width="500px" />
                                        </td>
                                        <td>
                                            <input type="text" id="parentEmail" class='parentEmail' name="parentEmail[]" placeholder="Parent's email id" />
                                        </td>
                                    </tr>
                                    <tr class="studentEmail">
                                        <td>
                                            <input type="text" id="studentName" class='studentName' name="studentName[]" autocomplete="off" placeholder="Student name" width="500px" />
                                        </td>
                                        <td>
                                            <input type="text" id="parentEmail"  class='parentEmail' name="parentEmail[]" placeholder="Parent's email id" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!--                            <div class='studentEmail' style="padding:1px; ">
                                                            <input type="text" id="studentName" style="width:165px;" class='studentName' name="studentName[]" autocomplete="off" placeholder="Student name" width="500px" />
                                                            <input type="text" id="parentEmail" style="width:165px;" class='parentEmail' name="parentEmail[]" placeholder="Parent's email id" />
                                                        </div>
                                                        <div class='studentEmail' style="padding:1px; ">
                                                            <input type="text" id="studentName" style="width:165px;" class='studentName' name="studentName[]" autocomplete="off" placeholder="Student name" width="500px" />
                                                            <input type="text" id="parentEmail" style="width:165px;" class='parentEmail' name="parentEmail[]" placeholder="Parent's email id" />
                                                        </div>
                                                        <div class='studentEmail' style="padding:1px; ">
                                                            <input type="text" id="studentName" style="width:165px;" class='studentName' name="studentName[]" autocomplete="off" placeholder="Student name" width="500px" />
                                                            <input type="text" id="parentEmail" style="width:165px;" class='parentEmail' name="parentEmail[]" placeholder="Parent's email id" />
                                                        </div>-->
                        </div>
                        <br/>
                        <div onClick="addMoreEmailFunction();divScroll();" style="cursor:pointer;width:70px;">
                            <img src="assets/add.png" alt="add" style="height:16px; width:16px; vertical-align: middle">&nbsp;Add more
                        </div>
                        <br/>
                        <label><input type="checkbox" id="mailAllParents">Mail parents of all students</input></label>
                        <!--<input type="button" class="button" id="addMoreEmail" value="Add" onClick="addMoreEmailFunction();" />-->
                    </div>
                    <div>
                        <textarea name="emailContent" class="Box4" id="emailContent" rows="8" cols="100" placeholder="Add text here"></textarea>
                        <br/>
                        <input type="button" class="button" name="sendEmail" id="sendEmail" name="send" value="SEND" onClick="validateEmail();" />
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php include("footer.php") ?>