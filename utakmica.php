
<?php
	include("baza.php");
	$bp=spojiSeNaBazu();
	if(session_id()=="")session_start();

  if(isset($_SESSION['aktivni_korisnik'])){
		$aktivni_korisnik=$_SESSION['aktivni_korisnik'];
		$aktivni_korisnik_ime=$_SESSION['aktivni_korisnik_ime'];
		$aktivni_korisnik_tip=$_SESSION['aktivni_korisnik_tip'];
		$aktivni_korisnik_id=$_SESSION["aktivni_korisnik_id"];
	} else {
    header("Location: prijava.php");
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Utakmica</title>
</head>

<body>
  <header>
    <ul class="navigation">
      <li><a href="utakmice.php">Utakmice</a></li>
    </ul>
  </header>
  <main>
    <div class="popis-liga">
      <?php
        if(!isset($_SESSION['aktivni_korisnik']) || ($_SESSION['aktivni_korisnik_tip'] != 0 && $_SESSION['aktivni_korisnik_tip'] != 1)) {
          header("Location: index.php");
        }

        if(isset($_GET['utakmica'])) {
          $utakmica = $_GET['utakmica'];

          $sql_utakmica="SELECT momcad_1, momcad_2, rezultat_1, rezultat_2 FROM utakmica WHERE utakmica_id=$utakmica";
          $rs_utakmica=izvrsiUpit($bp,$sql_utakmica);
          list($m1, $m2, $r1, $r2)=mysqli_fetch_array($rs_utakmica);

          $sql_momcad_1 = "SELECT naziv FROM momcad WHERE momcad_id=$m1";
          $sql_momcad_2 = "SELECT naziv FROM momcad WHERE momcad_id=$m2";
          $rs_momcad_1=izvrsiUpit($bp,$sql_momcad_1);
          $rs_momcad_2=izvrsiUpit($bp,$sql_momcad_2);
          list($naziv_m1)=mysqli_fetch_array($rs_momcad_1);
          list($naziv_m2)=mysqli_fetch_array($rs_momcad_2);

          echo '<br><br><span>Utakmica: '.$naziv_m1.' - '.$naziv_m2.'</span><br>';

          echo '<br><br><form id="utakmica" name="utakmica" method="POST" action="utakmica.php?utakmica='.$utakmica.'">';
          echo '<input name="utakmica" type="hidden" value="'.$utakmica.'">';
            echo '<input name="rezultat1" type="text" value="'.$r1.'">';
            echo '<input name="rezultat2" type="text" value="'.$r2.'">';

            echo '<input name="submit" type="submit" value="Ažuriraj"/>';
          echo '</form>';

          
          if(isset($_POST['submit'])) {
            $rezultat1 = $_POST['rezultat1'];
            $rezultat2 = $_POST['rezultat2'];
            $utakmica_id = $_POST['utakmica'];
  
            $sql_oklada = "UPDATE utakmica SET rezultat_1=$rezultat1,rezultat_2=$rezultat2 WHERE utakmica_id=$utakmica_id";
            $rs_oklada=izvrsiUpit($bp,$sql_oklada);
  
            $sql_listic = "SELECT * FROM listic WHERE utakmica_id=$utakmica_id";
            $rs_listic=izvrsiUpit($bp,$sql_listic);
  
            while(list($listic, $korisnik, $ut_id, $rezultat, $status)=mysqli_fetch_array($rs_listic)) {
              $konacni = 0;
              $novi = 'N';
              if($rezultat1 == $rezultat2) $konacni = 0;
              else if($rezultat1 > $rezultat2) $konacni = 1;
              else $konacni = 2;
  
              if($konacni == $rezultat) $sql_listic_update = "UPDATE listic SET status='D' WHERE listic_id=$listic";
              else $sql_listic_update = "UPDATE listic SET status='N' WHERE listic_id=$listic";
              
              $rs_listic_update=izvrsiUpit($bp,$sql_listic_update);
            }
  
            header("Location: utakmice.php");
          }
        } else {
          echo "Odaberite ligu i utakmicu <a href='utakmice.php'>ovdje</a> kako bi ste napravili ažuriranje!";
        }
      ?>
    </div>
  </main>
  <footer>

  </footer>
</body>
</html>