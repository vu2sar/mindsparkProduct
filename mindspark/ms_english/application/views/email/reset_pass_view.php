<?php
//mprd($email);
##############################################################################################
									//MAIL BODY
##############################################################################################
$html = '<table width="98%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
        <tr>
            <td align="left" style="font-family:verdana;font-size:16px;line-height:1.3em;text-align:left;padding:15px;background: #0041A6;">
                <p style="color:#CDB380;font-size:14px;letter-spacing: 5.5px; line-height: 2;font-weight:bold;">
                    '.MAINTITLE.'
                </p>
            </td>
        </tr>
        <tr style="background:#E6E6E6;">
            <td valign="top" align="left" style="font-family:verdana;font-size:16px;line-height:1.3em;text-align:left;padding:15px">
                <table  width="100%">
                    <tr style="background:#F5F5F5;border-radius:5px;">
                        <td style="font-family:verdana;font-size:13px;line-height:1.3em;text-align:left;padding:15px;">
                            <h1 style="font-family:verdana;color:#424242;font-size:28px;line-height:normal;letter-spacing:-1px">			        					
							Dear '.MAINTITLE.' User.
                            </h1>
                            <p style="color:#ADADAD">
                                Your Password Is Reset SuccessFully. 
                            </p>
                            <p> 
                                <b style="color:#036564;">
                                    Your New Password Is:
                                </b><br>
                                '.$password.'
                            </p>
                             
                            <hr style="margin-top:30px;border-top-color:#ccc;border-top-width:1px;border-style:solid none none">
                            <p>
                            	Thanks & regards,
								<br>
                                '.MAINTITLE.' Team.
                          	</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>';

##############################################################################################
									//SEND MAIL
##############################################################################################


$from = ADMIN_EMAIL;
$to = $email;
$message = $html;
$subject = $subject;

$headers = "From: $from\r\n";
$headers .= "Content-type: text/html\r\n";

$send_mail = mail($to, $subject, $message, $headers);

if ($send_mail) {
   
} else {
   
}
	 
?>