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

  if(!isset($_GET['sort']) && isset($_GET['liga'])) {
    header("Location: statistika.php?liga&sort=DESC");
  } else if (!isset($_GET['sort']) && !isset($_GET['liga'])){
    header("Location: statistika.php?sort=DESC");
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Statistika</title>
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

        echo '<span>Statistika prema:</span><br><a href="statistika.php">Korisniku</a><br><a href="statistika.php?liga">Ligi</a><br><br>';
        echo '<a href="';

        if($_GET['sort'] == "DESC" && isset($_GET['liga'])) {
          echo 'statistika.php?liga&sort=ASC">SORTIRAJ</a><br><br>';
        } else if($_GET['sort'] == "DESC" && !isset($_GET['liga'])){
          echo 'statistika.php?sort=ASC">SORTIRAJ</a><br><br>';
        } else if($_GET['sort'] == "ASC" && isset($_GET['liga'])){
          echo 'statistika.php?liga&sort=DESC">SORTIRAJ</a><br><br>';
        } else if($_GET['sort'] == "ASC" && !isset($_GET['liga'])){
          echo 'statistika.php?sort=DESC">SORTIRAJ</a><br><br>';
        }

        echo '<br><br><span>Vremenski period: </span><br><br>';
        if(isset($_GET['liga'])) {
          if(isset($_GET['sort'])) {
            $s = $_GET['sort'];
            echo '<form id="vrijeme" name="vrijeme" method="POST" action="statistika.php?liga&sort='.$s.'" style="border: 1px solid black; width: fit-content; margin: 0 auto; padding: 10px;">';
          } else {
            echo '<form id="vrijeme" name="vrijeme" method="POST" action="statistika.php?liga&sort=DESC" style="border: 1px solid black; width: fit-content; margin: 0 auto; padding: 10px;">';
          }
        } else {
          if(isset($_GET['sort'])) {
            $s = $_GET['sort'];
            echo '<form id="vrijeme" name="vrijeme" method="POST" action="statistika.php?sort='.$s.'" style="border: 1px solid black; width: fit-content; margin: 0 auto; padding: 10px;">';
          } else {
            echo '<form id="vrijeme" name="vrijeme" method="POST" action="statistika.php?sort=DESC" style="border: 1px solid black; width: fit-content; margin: 0 auto; padding: 10px;">';
          }
        }
          echo '<label for="dvp">Datum i vrijeme pocetka:</label>';
          echo '<input type="text" name="dvp" placeholder="dd.mm.gggg hh:mm:ss"><br><br>';

          echo '<label for="dvz">Datum i vrijeme zavrsetka:</label>';
          echo '<input type="text" name="dvz" placeholder="dd.mm.gggg hh:mm:ss"><br><br>';

          echo '<input name="submit" type="submit" value="Filtriraj"/>';
        echo '</form><br><br>';

        if(isset($_GET['liga'])) {
          $ord = $_GET['sort'];
          if(isset($_POST['submit'])) {
            $d1 = dateformat($_POST['dvp']);
            $d2 = dateformat($_POST['dvz']);

            $sql="SELECT m.liga_id, SUM(CASE WHEN l.status = 'D' THEN 1 ELSE 0 END) AS dobitni, 
            SUM(CASE WHEN l.status='N' THEN 1 ELSE 0 END) AS nedobitni
            FROM listic l, utakmica u, momcad m
            WHERE l.utakmica_id=u.utakmica_id AND m.momcad_id=u.momcad_1
            AND u.datum_vrijeme_zavrsetka BETWEEN '".$d1."' AND '".$d2."' 
            GROUP BY m.liga_id ORDER BY dobitni $ord";
          } else {
            $sql="SELECT m.liga_id, SUM(CASE WHEN l.status = 'D' THEN 1 ELSE 0 END) AS dobitni, 
            SUM(CASE WHEN l.status='N' THEN 1 ELSE 0 END) AS nedobitni
            FROM listic l, utakmica u, momcad m
            WHERE l.utakmica_id=u.utakmica_id AND m.momcad_id=u.momcad_1
            AND u.datum_vrijeme_zavrsetka BETWEEN '2019-01-01 00:00:00' AND '2019-12-31 00:00:00' 
            GROUP BY m.liga_id ORDER BY dobitni $ord";
          }
          
          $rs=izvrsiUpit($bp,$sql);
          
          while(list($id,$dobitni,$nedobitni)=mysqli_fetch_array($rs)) {
            $sql_liga="SELECT naziv FROM liga WHERE liga_id='$id'";
            $rs_liga=izvrsiUpit($bp,$sql_liga);
            list($naziv)=mysqli_fetch_array($rs_liga);
            
            echo '<div style="border: 1px solid black; width: fit-content; margin: 0 auto; padding: 10px; margin-bottom: 20px;">';
              echo '<br><span>Liga: '.$naziv.'</span><br>';
              echo '<br><span>Broj dobivenih: '.$dobitni.'</span><br>';
              echo '<br><span>Broj nedobitnih: '.$nedobitni.'</span><br>';
            echo '</div>';
          }
        } else {
          $ord = $_GET['sort'];

          if(isset($_POST['submit'])) {
            $d1 = dateformat($_POST['dvp']);
            $d2 = dateformat($_POST['dvz']);

            $sql="SELECT l.korisnik_id, SUM(CASE WHEN l.status = 'D' THEN 1 ELSE 0 END) AS dobitni, 
            SUM(CASE WHEN l.status='N' THEN 1 ELSE 0 END) AS nedobitni
            FROM listic l, utakmica u 
            WHERE l.utakmica_id=u.utakmica_id AND u.datum_vrijeme_zavrsetka 
            BETWEEN '".$d1."' AND '".$d2."' 
            GROUP BY l.korisnik_id ORDER BY dobitni $ord";
            $rs=izvrsiUpit($bp,$sql);
          } else {
            $sql="SELECT l.korisnik_id, SUM(CASE WHEN l.status = 'D' THEN 1 ELSE 0 END) AS dobitni, 
            SUM(CASE WHEN l.status='N' THEN 1 ELSE 0 END) AS nedobitni
            FROM listic l, utakmica u 
            WHERE l.utakmica_id=u.utakmica_id AND u.datum_vrijeme_zavrsetka 
            BETWEEN '2019-01-01 00:00:00' AND '2019-12-31 00:00:00' 
            GROUP BY l.korisnik_id ORDER BY dobitni $ord";
            $rs=izvrsiUpit($bp,$sql);
          }
          
          
          while(list($id,$dobitni,$nedobitni)=mysqli_fetch_array($rs)) {
            $sql_korisnik="SELECT korisnicko_ime FROM korisnik WHERE korisnik_id='$id'";
            $rs_korisnik=izvrsiUpit($bp,$sql_korisnik);
            list($korisnicko_ime)=mysqli_fetch_array($rs_korisnik);
            
            echo '<div style="border: 1px solid black; width: fit-content; margin: 0 auto; padding: 10px; margin-bottom: 20px;">';
              echo '<br><span>Korisnik: '.$korisnicko_ime.'</span><br>';
              echo '<br><span>Broj dobivenih: '.$dobitni.'</span><br>';
              echo '<br><span>Broj nedobitnih: '.$nedobitni.'</span><br>';
            echo '</div>';
          }
        }

        function dateformat($date) {
          $sec = strtotime($date);  
          $newdate = date ("Y-m-d H:i:s", $sec);  
          return $newdate;
        }
      ?>

    </div>
  </main>
  <footer>

  </footer>
</body>
</html>