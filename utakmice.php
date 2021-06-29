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
  <title>Utakmice</title>
</head>

<body>
  <header>
    <ul class="navigation">
      <li><a href="index.php">Home</a></li>
      <?php if(isset($_SESSION['aktivni_korisnik']) && ($_SESSION['aktivni_korisnik_tip'] == 2 || $_SESSION['aktivni_korisnik_tip'] == 1 || $_SESSION['aktivni_korisnik_tip'] == 0)) echo '<li><a href="listici.php">Listici</a></li>'; ?>
      <?php if(isset($_SESSION['aktivni_korisnik']) && ($_SESSION['aktivni_korisnik_tip'] == 2 || $_SESSION['aktivni_korisnik_tip'] == 1 || $_SESSION['aktivni_korisnik_tip'] == 0)) echo '<li><a href="kladjenje.php">Kladjenje</a></li>'; ?>
      <?php if(isset($_SESSION['aktivni_korisnik']) && ($_SESSION['aktivni_korisnik_tip'] == 1 || $_SESSION['aktivni_korisnik_tip'] == 0)) echo '<li><a href="utakmice.php">Utakmice</a></li>'; ?>
      <?php if(isset($_SESSION['aktivni_korisnik']) && ($_SESSION['aktivni_korisnik_tip'] == 1 || $_SESSION['aktivni_korisnik_tip'] == 0)) echo '<li><a href="statistika.php">Statistika</a></li>'; ?>
      <?php if(isset($_SESSION['aktivni_korisnik']) && $_SESSION['aktivni_korisnik_tip'] == 0) echo '<li><a href="korisnici.php">Korisnici</a></li>';?>
      <?php if(isset($_SESSION['aktivni_korisnik']) && $_SESSION['aktivni_korisnik_tip'] == 0) echo '<li><a href="momcadi.php">Momčadi</a></li>';?>
      <?php if(isset($_SESSION['aktivni_korisnik']) && $_SESSION['aktivni_korisnik_tip'] == 0) echo '<li><a href="lige.php">Lige</a></li>';?>
      <?php if(!isset($_SESSION['aktivni_korisnik'])) echo '<li><a href="login.php">Login</a></li>'; ?>
      <?php if(isset($_SESSION['aktivni_korisnik'])) echo '<li><a href="login.php?logout">Log out</a></li>'; ?>
      <li><a href="o_autoru.html">Autor</a></li>
    </ul>
  </header>
  <main>
    <div class="popis-liga">
      <?php
        if(!isset($_SESSION['aktivni_korisnik']) || ($_SESSION['aktivni_korisnik_tip'] != 0 && $_SESSION['aktivni_korisnik_tip'] != 1)) {
          header("Location: index.php");
        }

        $aktivni_korisnik = $_SESSION['aktivni_korisnik_id'];

        if($_SESSION['aktivni_korisnik_tip'] == 0) $sql_liga="SELECT * FROM liga";
        else $sql_liga="SELECT * FROM liga WHERE moderator_id='$aktivni_korisnik'";


        $rs_liga=izvrsiUpit($bp,$sql_liga);

        echo '<br><br><span>Odabir lige:</span><br>';

        while(list($liga, $moderator, $naziv)=mysqli_fetch_array($rs_liga)) {
          echo '<br><br><a href="utakmice.php?liga='.$liga.'">'.$naziv.'</a><br>';
        }

        if(isset($_GET['liga'])) {
          $liga = $_GET['liga'];

          echo '<br><br><span>Nova utakmica: </span><br><br>';
          echo '<form id="utakmica" name="utakmica" method="POST" action="utakmice.php?liga='.$liga.'" style="border: 1px solid black; width: fit-content; margin: 0 auto; padding: 10px;">';
            $sql_momcad1="SELECT * FROM momcad WHERE liga_id='$liga'";
            $rs_momcad1=izvrsiUpit($bp,$sql_momcad1);
            $sql_momcad2="SELECT * FROM momcad WHERE liga_id='$liga'";
            $rs_momcad2=izvrsiUpit($bp,$sql_momcad2);

            echo '<br><label for="momcad1">Odaberite momcad 1:</label>';
            echo '<select id="momcad1" name="momcad1">';
              while(list($id1, $liga1, $naziv1)=mysqli_fetch_array($rs_momcad1)) {
                echo '<option value="'.$id1.'">'.$naziv1.'</option>';
              }
            echo '</select><br><br>';


            echo '<label for="momcad2">Odaberite momcad 2:</label>';
            echo '<select id="momcad2" name="momcad2">';
              while(list($id2, $liga2, $naziv2)=mysqli_fetch_array($rs_momcad2)) {
                echo '<option value="'.$id2.'">'.$naziv2.'</option>';
              }
            echo '</select><br><br>';

            echo '<label for="dvp">Datum i vrijeme pocetka:</label>';
            echo '<input type="text" name="dvp" required placeholder="dd.mm.gggg hh:mm:ss"><br><br>';

            echo '<label for="opis">Opis:</label>';
            echo '<input type="text" name="opis" placeholder="Unesite opis"><br><br>';

            
            echo '<input name="submit" type="submit" value="Kreiraj"/>';
          echo '</form>';


          if(isset($_POST['submit'])) {
            $momcad1 = $_POST['momcad1'];
            $momcad2 = $_POST['momcad2'];
            $dvp = $_POST['dvp'];
            $opis = $_POST['opis'];
            $novi1 = strtotime($dvp);

            $novi1_p = date("Y-m-d H:i:s", $novi1);

            $dvz = date('Y-m-d H:i:s',strtotime('+90 minutes',strtotime($novi1_p)));

            if($momcad1 != $momcad2) {
              $sql_utakmica_unos = "INSERT INTO utakmica (momcad_1, momcad_2, datum_vrijeme_pocetka, datum_vrijeme_zavrsetka, rezultat_1, rezultat_2, opis) 
              VALUES ('$momcad1', '$momcad2', '$novi1_p', '$dvz', '-1', '-1', '$opis')";
              $rs_utakmica_unos=izvrsiUpit($bp,$sql_utakmica_unos);

              echo 'Uspjesno kreirana utakmica';
            } else {
              echo 'Momčadi koje se odabiru ne smiju biti iste';
            }
          }

          $sql_utakmica = "SELECT utakmica_id, momcad_1, momcad_2, datum_vrijeme_pocetka, datum_vrijeme_zavrsetka, rezultat_1, rezultat_2 FROM momcad, utakmica 
          WHERE momcad.momcad_id = utakmica.momcad_1 
          AND momcad.liga_id = $liga
          AND datum_vrijeme_zavrsetka < NOW()";
          $rs_utakmica=izvrsiUpit($bp,$sql_utakmica);

          while(list($utakmica, $m1, $m2, $d1, $d2, $r1, $r2)=mysqli_fetch_array($rs_utakmica)) {
            $sql_momcad_1 = "SELECT naziv FROM momcad WHERE momcad_id=$m1";
            $sql_momcad_2 = "SELECT naziv FROM momcad WHERE momcad_id=$m2";
            $rs_momcad_1=izvrsiUpit($bp,$sql_momcad_1);
            $rs_momcad_2=izvrsiUpit($bp,$sql_momcad_2);
            list($naziv_m1)=mysqli_fetch_array($rs_momcad_1);
            list($naziv_m2)=mysqli_fetch_array($rs_momcad_2);
            
            $d1 = dateformat($d1);
            $d2 = dateformat($d2);

            echo '<br><br><span>Utakmica: '.$naziv_m1.' - '.$naziv_m2.'</span><br>';
            echo '<span>Rezultat: '.$r1.' - '.$r2.'</span><br>';
            echo '<span>Datum i vrijeme (početak - završetak): '.$d1.' - '.$d2.'</span><br><br>';
            echo '<a href="utakmica.php?utakmica='.$utakmica.'">Ažuriranje rezultata</a><br><br>';
          }
        }

        function dateformat($date) {
          $sec = strtotime($date);  
          $newdate = date ("d.m.Y H:i:s", $sec);  
          return $newdate;
        }
      ?>
    </div>
  </main>
  <footer>

  </footer>
</body>
</html>