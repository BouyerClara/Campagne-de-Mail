<?php
	require 'PHPMailer/PHPMailerAutoload.php';
	require 'config.php';

	try{
		$bdd = new PDO('mysql:host=localhost;dbname=mail;charset=utf8', 'root', '');
	}catch(Exception $e){
		die('Erreur : ' . $e->getMessage());
	}
	if(isset($_GET['id'])){
		$compteur = $_GET['id'];
	}else{
		$compteur=0;
	}

	$req = $bdd->query('SELECT COUNT(*) FROM mail');
	$nombreMailer = $req->fetch();
	$req->closeCursor();
	$reponse = $bdd->query('SELECT * FROM mail LIMIT 5 OFFSET '.$compteur);

	if($compteur==$nombreMailer[0]){
		echo '<br/><h1>Campagne d\'e-mail envoyé </h1>';
		exit();
	}

	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->Host = MAIL_HOST;
	$mail->SMTPAuth = true;
	$mail->Username = MAIL_USERNAME;
	$mail->Password = MAIL_PASSWORD;
	$mail->SMTPSecure = 'tsl';
	$mail->Port = 587;

	while ($mailer = $reponse->fetch()){
		if($mailer['valide']) {
			$mail->setFrom(MAIL_USERNAME, 'Admin');
	    $mail->addAddress($mailer['email']);
	    $mail->addReplyTo(MAIL_USERNAME, 'NoReply');
	    $mail->isHTML(true);
			$mail->Subject = 'Bonjour '.$mailer['prenom'].' '.$mailer['nom'];
			$mail->Body = "<h1>Ceci est un test pour une campagne de pub.</h1>
	                   <p>Merci de ne pas renvoyer d'email à cette adresse.</p>";
			if(!$mail->send()) {
				// Ici le code pour l'erreur avec le bounce mais je n'ai pas réussi à le faire marcher
	      echo "Message non envoyé ! <br />";
	      echo "Erreur de mail: " . $mail->ErrorInfo . "<br />";
		  }else {
		    echo 'Le message à été envoyé.';
		  }
			sleep(1);
		}
		$compteur++;
	}

	$reponse->closeCursor();
	header('Location: http://localhost/mail/index.php?id='.$compteur);
