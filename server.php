<?php
session_start();
if($_SESSION["clients"]) {
	$clients = $_SESSION["clients"];
}
else
{
	$clients = [];
}
$data = [];
$data["type"] = $_POST["type"];

switch($_POST["type"])
{
	case "check":
		$data["exist"] = false;
		$data["balance"] = 500;
		
		for($i = 0; $i < count($clients); $i++)
		{
			if($clients[$i]["nom"] == $_POST["nom"] && $clients[$i]["prenom"] == $_POST["prenom"])
			{
				// Le client existe déjà
				$data["exist"] = true;
				$data["balance"] = $clients[$i]["balance"];
				$data["id"] = $i; // Numéro du client
			}
		}
		if(!$data["exist"]) // Inscription
		{
			$clients[] = array(
				"nom" => $_POST["nom"],
				"prenom" => $_POST["prenom"],
				"balance" => 500,
				"code" => 0
			);
			$data["id"] = count($clients)-1;
		}
		break;
	case "withdraw": // Retirer
		$data["success"] = 0;
		//echo $_POST["code"] . " et " . $clients[$_POST["id"]]["code"];
		if($_POST["code"] == $clients[$_POST["id"]]["code"])
		{
			if($clients[$_POST["id"]]["balance"] > $_POST["amount"])
			{
				$clients[$_POST["id"]]["balance"] -= $_POST["amount"];
				$data["success"] = 1;
				$data["message"] = "successful";
			}
			else
			{
				
				$data["message"] = "insufficient_balance";
			}
			$data["balance"] = $clients[$_POST["id"]]["balance"];
		}
		else
		{
			$data["message"] = "incorrect_code";
		}
		break;
	case "deposit":
		$data["success"] = 0;
		if($_POST["code"] == $clients[$_POST["id"]]["code"])
		{
			$clients[$_POST["id"]]["balance"] += $_POST["amount"];
			$data["balance"] = $clients[$_POST["id"]]["balance"];
			$data["success"] = 1;
			$data["message"] = "successful";
		}
		else
		{
			$data["message"] = "incorrect_code";
		}
		break;
	case "registerCode":
		if($clients[$_POST["id"]]["code"] == 0) // 1re modification, mesure de sécurité
		{
			$clients[$_POST["id"]]["code"] = $_POST["code"];
			$data["success"] = 1;
		}
		else
		{
			$data["success"] = 0;
		}
		break;
	case "erase":
		$_SESSION["clients"] = [];
		break;
	default:
		break;
}
echo json_encode($data);
if($_POST["type"] != "erase")
{
	$_SESSION["clients"] = $clients;
}
?>