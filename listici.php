
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
  <title>Listići</title>
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
        if(!isset($_SESSION['aktivni_korisnik'])) {
          header("Location: index.php");
        }

        $aktivni_korisnik = $_SESSION['aktivni_korisnik_id'];

        $sql_listici="SELECT * FROM listic WHERE korisnik_id='$aktivni_korisnik'";
        
        $rs_listici=izvrsiUpit($bp,$sql_listici);

        while(list($listic, $korisnik, $utakmica, $rezultat, $status)=mysqli_fetch_array($rs_listici)) {
          $sql_utakmica = "SELECT momcad_1, momcad_2, datum_vrijeme_pocetka, datum_vrijeme_zavrsetka FROM utakmica WHERE utakmica_id=$utakmica";
          $rs_utakmica=izvrsiUpit($bp,$sql_utakmica);
          list($m1, $m2, $dp, $dz)=mysqli_fetch_array($rs_utakmica);


          $sql_momcad_1 = "SELECT naziv FROM momcad WHERE momcad_id=$m1";
          $sql_momcad_2 = "SELECT naziv FROM momcad WHERE momcad_id=$m2";
          $rs_momcad_1=izvrsiUpit($bp,$sql_momcad_1);
          $rs_momcad_2=izvrsiUpit($bp,$sql_momcad_2);
          list($naziv_m1)=mysqli_fetch_array($rs_momcad_1);
          list($naziv_m2)=mysqli_fetch_array($rs_momcad_2);

          $dpp = dateformat($dp);
          $dzp = dateformat($dz);

            echo '<br><br><span>Utakmica: '.$naziv_m1.' - '.$naziv_m2.'</span><br>';
            echo '<span>Datum i vrijeme početka utakmice: ';
              echo $dpp;
            echo '</span><br>';
            echo '<span>Datum i vrijeme završetka utakmice: ';
              echo $dzp;
            echo '</span><br>';
            echo '<span>Oklada:';
              if($rezultat == 0) echo 'Nerjeseno';
              else if($rezultat == 1) echo 'Pobjeda domacina';
              else echo 'Pobjeda gosta';
            echo '</span><br>';
            echo '<span>Status listica:'; 
              if($status == 'P') echo 'Predan';
              else if($status == 'O') echo 'Nije predan';
              else if($status == 'D') echo 'Dobitan';
              else echo 'Nije dobitan';
            echo '</span><br>';
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