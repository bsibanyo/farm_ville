<?php
	// Include required phpmailer files
	// Define name spaces
	// Create instance of phpmailer
	// Set amiler to use smtp
	// define smtp host
	// enable smtp
	
	require 'inc/PHPMailer.php';
	require 'inc/SMTP.php';
	require 'inc/Exception.php';

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	function sendResetPassword($email, $remember_token) {
		$mail = new PHPMailer();

		$mail->isSMTP();
		
		$mail->Host = "smtp.gmail.com";
		$mail->SMTPAuth = "true";
		$mail->SMTPSecure = "tls";
		$mail->Port = "587";

		$mail->Username = "matchamanagment@gmail.com";
		$mail->Password = "9876543210khulu";

		$mail->isHTML(true);
		$mail->Subject = "Reset Password";
		$mail->setFrom("matchamanagment@gmail.com");
		$mail->Body = '
		<div class="container-fluid">
    
			<div class="row">
			<div class="col-lg-12">
				<h3 class="text-center">Forgot your password?</h3>
				<hr>
				<p>
					That\'s okay, it happens! Click on the button below to reset your password
				</p>
				<a class="btn btn-primary btn-flat" href="'.base_url.'reset_password.php?remember_token='.$remember_token.'" role="button">Link</a>
			</div>
			</div>
		</div>
	';
		$mail->addAddress($email);
		if ( $mail->Send() ) {
			$resp['status'] = 'success';;
		}else {
			$resp['status'] = 'failed';
			$resp['_error'] ='Unable to send mail. Please make sure you used the correct Password';
		}
		$mail->smtpClose();
	}
?>