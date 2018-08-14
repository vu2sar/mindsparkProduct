<?php
##############################################################################################
									//Body
##############################################################################################
$html	=	'<table width="98%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
        <tr>
            <td align="left" style="font-family:verdana;font-size:16px;line-height:1.3em;text-align:left;padding:15px;background: #081937;">
                <p style="color:#CDB380;font-size:14px;letter-spacing: 5.5px; line-height: 2;font-weight:bold;">
                    FITNESS AEGIS
                </p>
            </td>
        </tr>
        <tr style="background:#E6E6E6;">
            <td valign="top" align="left" style="font-family:verdana;font-size:16px;line-height:1.3em;text-align:left;padding:15px">
                <table  width="100%">
                    <tr style="background:#FFF;border-radius:5px;">
                        <td style="font-family:verdana;font-size:13px;line-height:1.3em;text-align:left;padding:15px;">
                            <h1 style="font-family:verdana;color:#424242;font-size:28px;line-height:normal;letter-spacing:-1px">			        					Dear User.
                            </h1>
                            <p style="color:#ADADAD">
                                Welcome To fitness Aegis.
                            </p>
                            <p> 
                                <b style="color:#036564;">
                                    In order to  Activate your account please:
                                </b><br>
                                <a target="_blank" href="'.$link.'" style="color:#666666;">																																									 									Click Here
                                </a> 
                            </p>
                            <hr style="margin-top:30px;border-top-color:#ccc;border-top-width:1px;border-style:solid none none">
                            <p>
                            	Thanks & regards,
								<br>
                                Fitness Aegis Team.
                          	</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>';
?>
<?php

##############################################################################################
									//SEND MAIL
##############################################################################################
	$from = 'admin@fitnessaegis.com';
	$to = $email;
	$message = $html;
	$subject = $subject;

	$headers  = "From: $from\r\n"; 
    $headers .= "Content-type: text/html\r\n";
	
	$send_mail = mail($to, $subject, $message, $headers); 
	
	if ($send_mail)
	{
        return 1;
    }
	else
	{
        return 0;
    }

?>