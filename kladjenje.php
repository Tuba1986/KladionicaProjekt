<?php
	include("baza.php");
	$bp=spojiSeNaBazu();
	if(session_id()=="")session_start();

  if(isset($_SESSION['aktivni_korisnik'])){
		$aktivni_korisnik=$_SESSION['aktivni_korisnik'];
		$aktivni_korisnik_ime=$_SESSION['aktivni_korisnik_ime'];
		$aktivni_korisnik_tip=$_SESSION['aktivni_korisnik_tip'];
		$aktivni_korisnik_id=$_SESSION["aktivni_korisnik_id"];
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Klađenje</title>
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
  <?php
  
  if(isset($_POST['submit'])) {
    $oklada = $_POST['oklada'];
    $utakmica = $_POST['utakmica'];
    $aktivni_korisnik = $_SESSION['aktivni_korisnik_id'];

    $sql_ima = "SELECT * FROM listic WHERE korisnik_id=$aktivni_korisnik AND utakmica_id=$utakmica";
    $rs_ima = izvrsiUpit($bp,$sql_ima);

    if(mysqli_num_rows($rs_ima) > 0) {
      echo "<span>Oklada na ovu utakmicu je već izvršena</span>";
    } else {
      $sql_oklada = "INSERT INTO listic (korisnik_id, utakmica_id, ocekivani_rezultat, status) VALUES ('$aktivni_korisnik', '$utakmica', '$oklada', 'P')";
      $rs_oklada=izvrsiUpit($bp,$sql_oklada);

      echo 'Uspjesna oklada na utakmicu!';
    }           
  }
  
  ?>
  <main>
    <div class="popis-liga">
      <?php
        if(!isset($_SESSION['aktivni_korisnik']) || ($_SESSION['aktivni_korisnik_tip'] != 2 && $_SESSION['aktivni_korisnik_tip'] != 1 && $_SESSION['aktivni_korisnik_tip'] != 0)) {
          header("Location: index.php");
        }
        
        $sql_utakmica="SELECT * FROM utakmica WHERE datum_vrijeme_zavrsetka > NOW()";
        $rs_utakmica=izvrsiUpit($bp,$sql_utakmica);

        while(list($id, $m1, $m2, $d1, $d2, $r1, $r2)=mysqli_fetch_array($rs_utakmica)) {
          $sql_momcad_1 = "SELECT naziv FROM momcad WHERE momcad_id=$m1";
          $sql_momcad_2 = "SELECT naziv FROM momcad WHERE momcad_id=$m2";
          $rs_momcad_1=izvrsiUpit($bp,$sql_momcad_1);
          $rs_momcad_2=izvrsiUpit($bp,$sql_momcad_2);
          list($naziv_m1)=mysqli_fetch_array($rs_momcad_1);
          list($naziv_m2)=mysqli_fetch_array($rs_momcad_2);

          $dp1 = dateformat($d1);
          $dp2 = dateformat($d2);

          echo '<form id="kladjenje'.$id.'" name="kladjenje'.$id.'" method="POST" action="kladjenje.php">';
            echo '<br><br><span>Utakmica: '.$naziv_m1.' - '.$naziv_m2.'</span><br>';
            echo '<span>Rezultat: '.$r1.' - '.$r2.'</span><br>';
            echo '<span>Datum i vrijeme (početak - završetak): '.$dp1.' - '.$dp2.'</span><br><br>';
            echo '<input name="utakmica" type="hidden" value="'.$id.'">';

            echo '<label for="oklada">Odaberite ishod:</label>';
            echo '<select id="oklada" name="oklada">';
              echo '<option value="0">Nerjeseno</option>';
              echo '<option value="1">Pobjeda domacina</option>';
              echo '<option value="2">Pobjeda gosta</option>';
            echo '</select>';
            echo '<input name="submit" type="submit" value="Oklada"/>';
          echo '</form>';
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