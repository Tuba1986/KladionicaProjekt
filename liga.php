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
  <title>Liga</title>
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
            $moderator=$_POST['moderator'];
            $naziv=$_POST['naziv'];
            $slika=$_POST['slika'];
            $video=$_POST['video'];
            $opis=$_POST['opis'];
      
            if($id==0){
              $sql="INSERT INTO liga
              (moderator_id,naziv,slika,video,opis)
              VALUES
              ('$moderator','$naziv','$slika','$video','$opis');
              ";
            }
            else{
              $sql="UPDATE liga SET
                moderator_id='$moderator',
                naziv='$naziv',
                slika='$slika',
                video='$video',
                opis='$opis'
                WHERE liga_id='$id'
              ";
            }
            izvrsiUpit($bp,$sql);
            header("Location:lige.php");
          }
        }
        if(isset($_GET['liga'])){
          $id=$_GET['liga'];
          $sql="SELECT * FROM liga WHERE liga_id='$id'";
          $rs=izvrsiUpit($bp,$sql);
          list($liga_id,$moderator_id,$naziv,$slika,$video,$opis)=mysqli_fetch_array($rs);
          $moderator = $moderator_id;
        }
        if(isset($_POST['reset']))header("Location:korisnik.php");
        ?>
        <form method="POST" action="<?php if(isset($_GET['liga']))echo "liga.php?liga=$liga_id";else echo "liga.php";?>">
          <table>
            <tbody style="display: flex; flex-flow: column; align-items: flex-end;">
              <tr>
                <td colspan="2">
                  <input type="hidden" name="novi" value="<?php if(!empty($liga_id))echo $liga_id;else echo 0;?>"/>
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
                    value="<?php if(isset($_GET['liga'])) echo $naziv;?>" size="80"  maxlength="150"
                    placeholder="Naziv lige"
                    required="required"/>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="slika"><strong>Slika:</strong></label>
                </td>
                <td>
                  <input type="text" name="slika" id="slika"
                    value="<?php if(isset($_GET['liga'])) echo $slika;?>" size="80" maxlength="150"
                    placeholder="URL slike"
                    required="required"/>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="video"><strong>Video:</strong></label>
                </td>
                <td>
                  <input type="text" name="video" id="video"
                    value="<?php if(isset($_GET['liga'])) echo $video;?>" size="80" maxlength="150"
                    placeholder="URL videa"
                    required="required"/>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="opis"><strong>Opis:</strong></label>
                </td>
                <td>
                  <input type="text" name="opis" id="opis"
                    value="<?php if(isset($_GET['liga'])) echo $opis;?>" size="80" maxlength="150"
                    placeholder="Opis"
                    required="required"/>
                </td>
              </tr>
              <tr>
                <td><label for="moderator"><strong>Moderator:</strong></label></td>
                <td>
                  <select id="moderator" name="moderator">
                    <?php
                      $sql_moderator = "SELECT * FROM korisnik WHERE tip_korisnika_id=1";
                      $rs_moderator = izvrsiUpit($bp,$sql_moderator);

                      while(list($id, $tip, $korisnicko)=mysqli_fetch_array($rs_moderator)) {
                        echo '<option value='.$id.'';if($moderator==$id)echo " selected='selected'";echo'>'.$korisnicko.'</option>';
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