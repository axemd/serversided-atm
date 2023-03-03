<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>ATM</title>
		<script type="text/javascript">
		var id = -1;
		var code;
		document.addEventListener("DOMContentLoaded", function() {
			var nom = prompt("Quel est votre nom ?");
			var prenom = prompt("Quel est votre prénom ?");
			fetch("http://localhost/server.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded"
				},
				body: new URLSearchParams({
					type: "check",
					nom,
					prenom
				}).toString()
			}).then((response) => response.text()).then((dataStr) => {
				var data = JSON.parse(dataStr);
				document.getElementById("balance").innerHTML = data["balance"].toString();
				id = data["id"];
				if(data["exist"] == false)
				{
					code = prompt("Merci de définir un code afin de pouvoir effectuer des transactions");
					fetch("http://localhost/server.php", {
						method: "POST",
						headers: {
							"Content-Type": "application/x-www-form-urlencoded"
						},
						body: new URLSearchParams({
							type: "registerCode",
							nom,
							prenom,
							code,
							id
						}).toString()
					}).then((response) => response.text()).then((dataStr) => {
						var data = JSON.parse(dataStr);
						if(data["success"] == 1)
						{
							alert("Nous vous remercions pour votre inscription !");
						}
						else
						{
							alert("Une erreur est survenue");
						}
					})
				}
				else
				{
					alert("Bienvenue " + prenom);
				}
			});
		});
		function withdraw() {
			code = prompt("Quel est votre code ?");
			var amount = prompt("Combien voulez-vous retirer ?");
			fetch("http://localhost/server.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded"
				},
				body: new URLSearchParams({
					type: "withdraw",
					amount,
					code,
					id
				})
			}).then((response) => response.text()).then((dataStr) => {
				var data = JSON.parse(dataStr);
				if(data["success"])
				{
					alert("Opération effectuée avec succès");
					document.getElementById("balance").innerHTML = data["balance"].toString();
 				}
				else
				{
					switch(data["message"])
					{
						case "insufficient_balance":
							alert("Balance insuffisante !");
							break;
						case "incorrect_code":
							alert("Code incorrect !");
							break;
						default:
							console.log("Erreur : " + data["message"]);
							break;
					}
				}
			});
		}
		function deposit() {
			code = prompt("Quel est votre code ?");
			var amount = prompt("Combien voulez-vous déposer ?");
			fetch("http://localhost/server.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded"
				},
				body: new URLSearchParams({
					type: "deposit",
					amount,
					code,
					id
				})
			}).then((response) => response.text()).then((dataStr) => {
				var data = JSON.parse(dataStr);
				if(data["success"])
				{
					alert("Opération effectuée avec succès");
					document.getElementById("balance").innerHTML = data["balance"].toString();
 				}
				else
				{
					switch(data["message"])
					{
						case "incorrect_code":
							alert("Code incorrect !");
							break;
						default:
							console.log("Erreur : " + data["message"]);
							break;
					}
				}
			});
		}
		function eraseSession() {
			fetch("http://localhost/server.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded"
				},
				body: new URLSearchParams({
					type: "erase"
				}).toString()
			}).then((response) => response.text()).then((data) => {
				location.reload();
			});
		}
		</script>
	</head>
	<body>
		<center>
			<input onClick="javascript:withdraw();" type="button" value="Retirer" />
			<input onClick="javascript:deposit();" type="button" value="Déposer" />
			<input onClick="javascript:eraseSession();" type="button" value="Erase session" />
			<br />
			<p>Solde en banque : <b><span id="balance"></span>€</b><br />Solde physique : <b>Infini</b></p>
		</center>
	</body>
</html>