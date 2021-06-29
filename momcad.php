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
  <title>Momčad</title>
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
        $moderator = 0;
        $greska="";
        if(isset($_POST['submit'])){
          foreach ($_POST as $key => $value)if(strlen($value)==0)$greska="Sva polja za unos su obavezna";
          if(empty($greska)){
            $id=$_POST['novi'];
            $liga=$_POST['liga'];
            $naziv=$_POST['naziv'];
            $opis=$_POST['opis'];
      
            if($id==0){
              $sql="INSERT INTO momcad
              (liga_id,naziv,opis)
              VALUES
              ('$liga','$naziv','$opis');
              ";
            }
            else{
              $sql="UPDATE momcad SET
                liga_id='$liga',
                naziv='$naziv',
                opis='$opis'
                WHERE momcad_id='$id'
              ";
            }
            izvrsiUpit($bp,$sql);
            header("Location:momcadi.php");
          }
        }
        if(isset($_GET['momcad'])){
          $id=$_GET['momcad'];
          $sql="SELECT * FROM momcad WHERE momcad_id='$id'";
          $rs=izvrsiUpit($bp,$sql);
          list($momcad_id,$liga_id,$naziv,$opis)=mysqli_fetch_array($rs);
        } else {
          $liga_id = "";
        }
        if(isset($_POST['reset']))header("Location:korisnik.php");
        ?>
        <form method="POST" action="<?php if(isset($_GET['momcad']))echo "momcad.php?liga=$momcad_id";else echo "momcad.php";?>">
          <table>
            <tbody style="display: flex; flex-flow: column; align-items: flex-end;">
              <tr>
                <td colspan="2">
                  <input type="hidden" name="novi" value="<?php if(!empty($momcad_id))echo $momcad_id;else echo 0;?>"/>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="text-align:center;">
                  <label class="greska"><?php if($greska!="")echo $greska; ?></label>
                </td>
              </tr>
              <tr>
                <td class="lijevi">
                  <label for="naziv"><strong>Naziv:</strong></label>
                </td>
                <td>
                  <input type="text" name="naziv" id="naziv"
                    value="<?php if(isset($_GET['momcad'])) echo $naziv;?>" size="80"  maxlength="50"
                    placeholder="Naziv momcadi"
                    required="required"/>
                </td>
              </tr>
              
              <tr>
                <td>
                  <label for="opis"><strong>Opis:</strong></label>
                </td>
                <td>
                  <input type="text" name="opis" id="opis"
                    value="<?php if(isset($_GET['momcad'])) echo $opis;?>" size="80" maxlength="50"
                    placeholder="Opis"
                    required="required"/>
                </td>
              </tr>
              <tr>
                <td><label for="liga"><strong>Liga:</strong></label></td>
                <td>
                  <select id="liga" name="liga">
                    <?php
                      $sql_liga = "SELECT * FROM liga";
                      $rs_liga = izvrsiUpit($bp,$sql_liga);

                      while(list($id, $moderator, $naziv)=mysqli_fetch_array($rs_liga)) {
                        echo '<option value='.$id.'';if($liga_id==$id) echo " selected='selected'";echo'>'.$naziv.'</option>';
                      }
                    ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="text-align:center;">
                  <?php
                    echo '<input type="submit" name="submit" value="Pošalji"/>';
                  ?>
                </td>
              </tr>
            </tbody>
          </table>
        </form>
    </div>
  </main>
  <footer>

  </footer>
</body>
</html>