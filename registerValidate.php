<?php

require "Includes/functions/connect.php";

$emri = $_POST['fname'];
$email = $_POST['email'];
$departamenti = $_POST['departament'];
$id = $_POST['idReg'];
$pass = $_POST['passwordReg'];

$query2 = "SELECT id FROM perdoruesi WHERE id = '$id';";
$result2 = mysqli_query($connect, $query2);
$countID = mysqli_num_rows($result2);

$register = true;

//ne vazhdim do te shikojme vetem rastet kur ka ndodhur ndonje gabim gjate plotesimit te formes te cilen po e validojme (per te dhenat e saj aktuale)
//nese asnjera nga fushat e formes nuk eshte e plotesuar
if(empty($emri) && empty($email) && $departamenti == "Perzgjidh departamentin" && empty($id) && empty($pass)) {
	$error = "Te gjitha fushat duhet te plotesohen!";
	$register = false;
}

//nese te pakten njera nga fushat permban nje vlere perkatese, na nevojitet ta validojme ate vlere
else {
	//emri ka vlere, validoje ate
	if(empty($emri)) {
		$errorEmri = "Fusha e emrit duhet te plotesohet!";
		$register = false;
	}
	
	//nese emri permban edhe karaktere tjera jo-shkronje
	else {
		//nese emri permban edhe karaktere tjera jo-shkronje
		if(!preg_match("/^[a-zA-Z ]+$/", $emri)) {
			$errorEmri = "Emri duhet te permbaje vetem shkronja!";
			$register = false;
		}
	}
	
	//nese fusha e email adreses eshte e zbrazet
	if(empty($email)) {
		$errorEmail = "Fusha e email duhet te plotesohet!";
		$register = false;
	}
	
	//email adresa ka vlere, validoje ate
	else {
		//nese formati i email adreses se shenuar nuk eshte i sakte
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errorEmail = "Formati i email adreses nuk eshte i sakte!";
			$register = false;
		}
	}
	
	//nese ne listen e departamenteve perdoruesi nuk ka zgjedhur asnjerin nga opsionet qe perfaqesojne nje departament ne fakultet
	if($departamenti == "Perzgjidh departamentin") {
		$errorDept = "Fusha e departamentit duhet te plotesohet!";
	}
	
	//nese fusha e id-se eshte e zbrazet
	if(empty($id)) {
		$errorID = "Fusha e ID-se duhet te plotesohet!";
		$register = false;
	}
	
	//nese fusha e id-se eshte e zbrazet
	else {
		
		//nese id permban edhe karaktere tjera jo-numra
		if(!is_numeric($id)) {
			$errorID = "Numri i id-se duhet te permabje vetem vetem numra!";
			$register = false;
		}
		
		//nese id nuk ka saktesisht 9 karaktere
		else if(strlen($id) != 9) {
			$errorID = "Numri i id-se duhet te jete 9 karaktere i gjate!";
			$register = false;
		}
		
		//nese ekziston nje perdorues qe e ka kete id (id paraqet username permes se ciles do te kyqet perdoruesi ne llogarine e tij ne sistem)
		else if($countID != 0) {
			$errorID = "Ky perdorues ekziston!";
			$register = false;
		}
	}
	
	//nese fusha e fjalekalimit eshte e zbrazet
	if(empty($pass)) {
		$errorPass = "Fusha e password-it duhet te plotesohet!";
		$register = false;
	}
	
	//fjalekalimi ka vlere, validoje ate
	else {
		$uppercase = preg_match("@[A-Z]@", $pass);
		$lowercase = preg_match("@[a-z]@", $pass);
		$number = preg_match("@[0-9]@", $pass);
		$symbols = preg_match("@[^\w]@", $pass);
		
		//nese fjalekalimi eshte i dobet
		//nese nuk plotesohet njeri nga kushtet e meposhtem atehere konsiderohet qe fjalekalimi eshte i dobet
		if(!$uppercase || !$lowercase || !$number || !$symbols || strlen($pass) < 8) {
			$errorPass = "Fjalekalimi i dobet!";
			$errorPassTooltip = "Fjalekalimi duhet te permbaje te pakten 8 karaktere dhe duhet te perfshije te pakten nje shkronje te madhe, nje numer dhe nje karakter special!";
			$register = false;
		}
	}
	
	//realizohet insertimi
	if($register == true) {
		
		$dataReg = date("Y-m-d");
		
		$queryDeptID = "SELECT id FROM departamenti WHERE emri = '$departamenti';";
		$resultDeptID = mysqli_query($connect, $queryDeptID);
		$rowDeptID = mysqli_fetch_assoc($resultDeptID);
		$deptID = $rowDeptID['id'];
		
		//tani jemi gati te insertojme perdoruesin e ri ne db
		//ne rastin tone do te bejme nje insertim te dyfishte ne dy tabela te ndryshme

		$querysql = "INSERT INTO perdoruesi (id, emri, email, fjalekalimi, roli)
					 VALUES ('$id', '$emri', '$email', md5('$pass'), '3');";
		
		$querysql .= "INSERT INTO studenti (id, dataregjistrimit, departamenti, semestri)
					  VALUES ('$id', '$dataReg', '$deptID', '1');";
		
		if(mysqli_multi_query($connect, $querysql)) {
			header("Location: login.php");
		}
		else {
			$error = "Ka ndodhur nje gabim ne insertimin.!";;
		}
	}
}

?>