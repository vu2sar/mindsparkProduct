<?php

##############################################################################################
//Body
##############################################################################################
$html = '<table width="98%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
        <tr>
        
         
        
            <td align="left" style="font-family:verdana;font-size:16px;line-height:1.3em;text-align:left;padding:15px;background: #B5EEF5;">
        <div style="float:left; margin-right:25px;"><img src="'.DOMAIN_URL.'/admin/images/logo_is.png" height="70" width="70" />
            </div>     
            <div><p style="color:#009197;font-size:14px;letter-spacing: 5.5px; line-height: 2;font-weight:bold;">
        
                   ' . MAINTITLE . '
                </p></div>
        
            </td>
        </tr>
        <tr style="background:#E6E6E6;">
            <td valign="top" align="left" style="font-family:verdana;font-size:16px;line-height:1.3em;text-align:left;padding:15px">
                <table  width="100%">
                    <tr style="background:#F5F5F5;border-radius:5px;">
                        <td style="font-family:verdana;font-size:13px;line-height:1.3em;text-align:left;padding:15px;">
                            <h1 style="font-family:verdana;color:#424242;font-size:28px;line-height:normal;letter-spacing:-1px">	
									        					Dear ' . MAINTITLE . ' User.
                            </h1>
                            <p style="color:#ADADAD">
                                Your Password reset request is accepted
                            </p>
                            <p> 
                                <b style="color:#036564;">
                                    In order to reset your password please:
                                </b><br>
                                <a target="_blank" href="' . $link . '" style="color:#666666;">																																									 									Click Here
                                </a> 
                            </p>
                            <hr style="margin-top:30px;border-top-color:#ccc;border-top-width:1px;border-style:solid none none">
                            <p>
                            	Thanks & regards,
								<br>
                                ' . MAINTITLE . ' Team.
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
//$this->email->set_newline("\r\n");
//
//$this->email->from(ADMIN_EMAIL);
//
//$this->email->to($email);
//
//$this->email->subject($subject);
//
//$this->email->message($html);
//
//$this->email->send();

$this->email->set_newline("\r\n");
$config['charset'] = 'utf-8';
$config['wordwrap'] = TRUE;
$config['mailtype'] = 'html';
$this->email->initialize($config);
$this->email->from($from);
$this->email->to($email);
$this->email->subject($subject);
$this->email->message($html);
if ($this->email->send()) 
{
        redirect('forgot_pass/index/1');
}
else 
{
        redirect('forgot_pass/index/0');
}

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