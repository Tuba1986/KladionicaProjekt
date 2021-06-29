<?php
	include("baza.php");
	$bp=spojiSeNaBazu();
	if(session_id()=="")session_start();

?>

<?php
	if(isset($_GET['logout'])){
		unset($_SESSION["aktivni_korisnik"]);
		unset($_SESSION['aktivni_korisnik_ime']);
		unset($_SESSION["aktivni_korisnik_tip"]);
		unset($_SESSION["aktivni_korisnik_id"]);
		session_destroy();
		header("Location:index.php");
	}


	$greska= "";
	if(isset($_POST['submit'])){
		$kor_ime=mysqli_real_escape_string($bp,$_POST['korisnicko_ime']);
		$lozinka=mysqli_real_escape_string($bp,$_POST['lozinka']);

		if(!empty($kor_ime)&&!empty($lozinka)){
			$sql="SELECT korisnik_id,tip_korisnika_id,ime,prezime FROM korisnik WHERE korisnicko_ime='$kor_ime' AND lozinka='$lozinka'";
			$rs=izvrsiUpit($bp,$sql);
			if(mysqli_num_rows($rs)==0) echo $greska="Ne postoji korisnik s navedenim korisničkim imenom i lozinkom";
			else{
				list($id,$tip,$ime,$prezime)=mysqli_fetch_array($rs);
				session_start();
				$_SESSION['aktivni_korisnik']=$kor_ime;
				$_SESSION['aktivni_korisnik_ime']=$ime." ".$prezime;
				$_SESSION["aktivni_korisnik_id"]=$id;
				$_SESSION['aktivni_korisnik_tip']=$tip;
				header("Location:index.php");
			}
		}
		else $greska = "Molim unesite korisničko ime i lozinku";
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Login</title>
</head>

<body>
  <header>
    <ul class="navigation">
			<li><a href="index.php">Home</a></li>
			<li><a href="login.php">Login</a></li>
      <li><a href="o_autoru.html">Autor</a></li>
    </ul>
  </header>
  <main>
		<div class="login-wrapper">
			<form id="prijava" name="prijava" method="POST" action="login.php" onsubmit="return validacija();">
				<table>
					<caption>Prijava u sustav</caption>
					<tbody>
						<tr>
								<td colspan="2" style="text-align:center;">
									<label class="greska"><?php if($greska!="")echo $greska; ?></label>
								</td>
						</tr>
						<tr>
							<td class="lijevi">
								<label for="korisnicko_ime"><strong>Korisničko ime:</strong></label>
							</td>
							<td>
								<input name="korisnicko_ime" id="korisnicko_ime" type="text" size="40"/>
							</td>
						</tr>
						<tr>
							<td>
								<label for="lozinka"><strong>Lozinka:</strong></label>
							</td>
							<td>
								<input name="lozinka"	id="lozinka" type="password" size="40"/>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="text-align:center;">
								<input name="submit" type="submit" value="Prijavi se"/>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
  </main>
  <footer></footer>
</body>
</html>

<?php
	zatvoriVezuNaBazu($bp);
?>
