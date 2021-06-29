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
  <title>Home</title>
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
        $sql="SELECT * FROM liga";
        $rs=izvrsiUpit($bp,$sql);
        
        while(list($id,$mod_id,$naziv,$slika,$video, $opis)=mysqli_fetch_array($rs)) {
          echo '<div class="dropdown">';
            echo '<img src="'.$slika.'" width="120" height="100">';
            echo '<div class="dropdown-content">';
            echo '<img src="'.$slika.'" width="300" height="200">';
            echo '<div class="desc">'.$naziv.'</div>';
            echo '<div class="desc">'.$opis.'</div>';
            echo '<div class="desc"><a href="index.php?liga='.$id.'">Prikaži popis završenih utakmica</a></div>';
            echo '<div class="desc"><iframe width="420" height="315" src="'.$video.'"></iframe></div>';
            echo '</div>';
          echo '</div>';
        }

        if(isset($_GET['liga'])) {
          $liga = $_GET['liga'];

          $sql_utakmica="SELECT momcad_1, momcad_2, datum_vrijeme_pocetka, datum_vrijeme_zavrsetka, rezultat_1, rezultat_2 FROM momcad, utakmica 
          WHERE momcad.momcad_id = utakmica.momcad_1 
          AND momcad.liga_id = $liga AND datum_vrijeme_zavrsetka < NOW()";

          $rs_utakmica=izvrsiUpit($bp,$sql_utakmica);

          while(list($m1, $m2, $d1, $d2, $r1, $r2)=mysqli_fetch_array($rs_utakmica)) {
            $sql_momcad_1 = "SELECT naziv FROM momcad WHERE momcad_id=$m1";
            $sql_momcad_2 = "SELECT naziv FROM momcad WHERE momcad_id=$m2";
            $rs_momcad_1=izvrsiUpit($bp,$sql_momcad_1);
            $rs_momcad_2=izvrsiUpit($bp,$sql_momcad_2);
            list($naziv_m1)=mysqli_fetch_array($rs_momcad_1);
            list($naziv_m2)=mysqli_fetch_array($rs_momcad_2);

            $dp1 = dateformat($d1);
            $dp2 = dateformat($d2);

            echo '<br><br><span>Utakmica: '.$naziv_m1.' - '.$naziv_m2.'</span><br>';
            echo '<span>Rezultat: '.$r1.' - '.$r2.'</span><br>';
            echo '<span>Datum i vrijeme (početak - završetak): '.$dp1.' - '.$dp2.'</span><br><br>';
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