<?php

//te dhenat nga forma e kycjes
$usernameID = $_POST['usernameLogin'];
$pass = $_POST['passLogin'];

//konektimi me DB
require "Includes/functions/connect.php";

$login = true;

//validimi i te dhenave hyrese
if(empty($usernameID) && empty($pass)) {
	$error = "Te gjitha te dhenat duhet te plotesohen!";
	$login = false;
}
else {
	//username ID
	//nese e zbrazte
	if(empty($usernameID)) {
		$errorUser = "Fusha e username-it duhet te plotesohet!";
		$login = false;
	}
	else {
		//kontrolloje nese perdoruesi ekziston
		$query1 = "SELECT * FROM perdoruesi WHERE id = '$usernameID';";
		$result1 = mysqli_query($connect, $query1);
		$count1 = mysqli_num_rows($result1);
		
		if($count1 == 0) {
			$errorUser = "Ky perdorues nuk ekziston!";
			$login = false;
		}
	}
	
	//password
	//nese e zbrazte
	if(empty($pass)) {
		$errorPass = "Fusha e fjalekalimit duhet te plotesohet!";
		$login = false;
	}
	else {
		$query2 = "SELECT fjalekalimi FROM perdoruesi WHERE id = '$usernameID';";
		$result2 = mysqli_query($connect, $query2);
		$row2 = mysqli_fetch_assoc($result2);
		$passDB = $row2['fjalekalimi'];
		$passEnc = md5($pass);
		
		if($passDB != $passEnc) {
			$errorPass = "Fjalekalimi nuk eshte i sakte!";
			$login = false;
		}
	}
	
	if($login == true) {
		//perdoruesi kycet ne sistem varesisht prej rolit te tij
		$query3 = "SELECT roli FROM perdoruesi WHERE id = '$usernameID';";
		$result3 = mysqli_query($connect, $query3);
		$row3 = mysqli_fetch_assoc($result3);
		$roli = $row3['roli'];
		
		$_SESSION['usernameID'] = $usernameID;
		$_SESSION['roli'] = $roli;
		
		//ridrejtoje ne faqen e cila hapet pas kycjes
		header("Location: home.php");
	}
}

?>





