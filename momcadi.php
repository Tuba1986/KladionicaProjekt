
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
  <title>Momčadi</title>
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
        $sql="SELECT COUNT(*) FROM momcad";
        $rs=izvrsiUpit($bp,$sql);
        $red=mysqli_fetch_array($rs);
        $broj_redaka=$red[0];
        $vel_str = 5;
        $broj_stranica=ceil($broj_redaka/$vel_str);

        $sql="SELECT * FROM momcad ORDER BY momcad_id LIMIT ".$vel_str;

        if(isset($_GET['stranica'])){
          $sql=$sql." OFFSET ".(($_GET['stranica']-1)*$vel_str);
          $aktivna=$_GET['stranica'];
        }
        else $aktivna = 1;

        $rs=izvrsiUpit($bp,$sql);
        echo "<table style='margin: 0 auto;'>";
        echo "<caption style='margin-bottom: 20px;'>Popis momčadi sustava</caption>";
        echo "<thead><tr>
          <th>Naziv</th>
          <th>Opis</th>
          <th></th>";
        echo "</tr></thead>";
        echo "<tbody>";
        while(list($momcad_id,$liga_id,$naziv,$opis)=mysqli_fetch_array($rs)){
          echo "<tr>
            <td>$naziv</td>
            <td>$opis</td>";
            echo "<td><a class='link' href='momcad.php?momcad=$momcad_id'>UREDI</a></td>";
            echo "<td></td>";
          echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";

        echo '<div id="paginacija">';
          if($aktivna!=1){
            $prethodna=$aktivna-1;
            echo "<a style='margin-right: 5px;' class='link' href=\"momcadi.php?stranica=".$prethodna."\">&lt;</a>";
          }
          for($i=1;$i<=$broj_stranica;$i++){
            echo "<a style='margin-right: 5px;' class='link";
            if($aktivna==$i)echo " aktivna";
            echo "' href=\"momcadi.php?stranica=".$i."\">$i</a>";
          }
        if($aktivna<$broj_stranica){
          $sljedeca=$aktivna+1;
          echo "<a style='margin-right: 5px;' class='link' href=\"momcadi.php?stranica=".$sljedeca."\">&gt;</a>";
        }
        echo "<br/>";
          echo '<a style="margin-right: 5px;" class="link" href="momcad.php">DODAJ MOMČAD</a>';
        echo '</div>'; 
      ?>

    </div>
  </main>
  <footer>

  </footer>
</body>
</html>