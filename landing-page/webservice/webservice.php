<?php
	require("acess.php");

	if (isset($_POST) ){

		$_POST = json_decode($_POST["data"], true);

		$lead = array('nome'=> $_POST["nome"], 
					'data_nascimento'=> $_POST["data_nascimento"], 
					'email'=> $_POST["email"], 
					'telefone'=> $_POST["telefone"], 
					'regiao'=> $_POST["regiao"], 
					'unidade'=> $_POST["unidade"]
				);

		$score = 10;

		//Região 
		if ($lead["regiao"] == "Sul"){
			$score -=2;
		} else if ($lead["regiao"] == "Sudeste" && $lead["unidade"] != "São Paulo"){
			$score -=1;
		}else if ($lead["regiao"] == "Centro-Oeste"){
			$score -=3;
		}else if ($lead["regiao"] == "Nordeste"){
			$score -=4;
		}else if ($lead["regiao"] == "Norte"){
			$score -=5;
		}

		//Idade
		$birthDate = new DateTime(date( "d-m-Y", strtotime(str_replace('/', '-',$lead["data_nascimento"]))));
		$nowConsidererDate = new DateTime(date( "d-m-Y", strtotime(str_replace('/', '-',"01/11/2016"))));
		//$nowConsidererDate = new DateTime(date( "m-d-Y", strtotime(str_replace('/', '-',"01/11/2016"))));

		$age = ($birthDate->diff($nowConsidererDate));
		if ($age->y < 18){
			$score -= 5;
		}else if($age->y < 40){
			$score -= 0;
		}else if($age->y < 100){
			$score -= 3;
		}else{
			$score -= 5;
		}

		$success_json = array('score' => $score, 'birthday' => $birthDate->format('Y-m-d'));
		echo json_encode($success_json);

		//Insert to data base;

		if (isset($conn)){	
			try {
				$sql = "INSERT INTO leads_get (lead_id, lead_nome, lead_email, lead_telefone, lead_regiao, lead_unidade, lead_data_nascimento, lead_score, lead_timestamp) VALUES (null, :name, :email, :telefone, :regiao, :unidade, :data_nascimento, :score, null)";
				$stmt = $conn->prepare($sql);
				$stmt->bindParam(':name', $lead["nome"], PDO::PARAM_STR);
				$stmt->bindParam(':email', $lead["email"], PDO::PARAM_STR);
				$stmt->bindParam(':telefone', $lead["telefone"], PDO::PARAM_STR);
				$stmt->bindParam(':regiao', $lead["regiao"], PDO::PARAM_STR);
				$stmt->bindParam(':unidade', $lead["unidade"], PDO::PARAM_STR);
				$stmt->bindParam(':data_nascimento', $lead["data_nascimento"], PDO::PARAM_STR);
				$stmt->bindParam(':score', $score, PDO::PARAM_INT);
				$stmt->execute();
				//return $conn->lastInsertId();
			} catch (PDOException $e) {	
				echo "error".$e->errorInfo[1];
			}

			$conn = null;
		}
	}
?>