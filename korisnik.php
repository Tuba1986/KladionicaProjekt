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
  <title>Korisnici</title>
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
      <?php if(!isset($_SESSION['aktivni_korisnik'])) echo '<li><a href="login.php">Login</a></li>'; ?>
      <?php if(isset($_SESSION['aktivni_korisnik'])) echo '<li><a href="login.php?logout">Log out</a></li>'; ?>
      <li><a href="o_autoru.html">Autor</a></li>
    </ul>
  </header>
  <main>
    <div class="popis-liga">
      <?php
        if(!isset($_SESSION['aktivni_korisnik']) || $_SESSION['aktivni_korisnik_tip'] != 0) header("Location: index.php");

        $greska="";
        if(isset($_POST['submit'])){
          foreach ($_POST as $key => $value)if(strlen($value)==0)$greska="Sva polja za unos su obavezna";
          if(empty($greska)){
            $id=$_POST['novi'];
            $tip=$_POST['tip'];
            $kor_ime=$_POST['kor_ime'];
            $lozinka=$_POST['lozinka'];
            $ime=$_POST['ime'];
            $prezime=$_POST['prezime'];
            $email=$_POST['email'];
            $slika=$_POST['slika'];
      
            if($id==0){
              $sql="INSERT INTO korisnik
              (tip_korisnika_id,korisnicko_ime,lozinka,ime,prezime,email,slika)
              VALUES
              ($tip,'$kor_ime','$lozinka','$ime','$prezime','$email','$slika');
              ";
            }
            else{
              $sql="UPDATE korisnik SET
                tip_korisnika_id='$tip',
                korisnicko_ime='$kor_ime',
                ime='$ime',
                prezime='$prezime',
                lozinka='$lozinka',
                email='$email',
                slika='$slika'
                WHERE korisnik_id='$id'
              ";
            }
            izvrsiUpit($bp,$sql);
            header("Location:korisnici.php");
          }
        }
        if(isset($_GET['korisnik'])){
          $id=$_GET['korisnik'];
          if($_SESSION["aktivni_korisnik_tip"]==2)$id=$_SESSION["aktivni_korisnik_id"];
          $sql="SELECT * FROM korisnik WHERE korisnik_id='$id'";
          $rs=izvrsiUpit($bp,$sql);
          list($id,$tip,$kor_ime,$lozinka,$ime,$prezime,$email,$slika)=mysqli_fetch_array($rs);
        }
        else{
          $id="";
          $tip=2;
          $kor_ime="";
          $lozinka="";
          $ime="";
          $prezime="";
          $email="";
          $slika="";
        }
        if(isset($_POST['reset']))header("Location:korisnik.php");
        ?>
        <form method="POST" action="<?php if(isset($_GET['korisnik']))echo "korisnik.php?korisnik=$id";else echo "korisnik.php";?>">
          <table>
            <caption>
              <?php
                if(isset($id)&&$_SESSION["aktivni_korisnik_id"]==$id)echo "Uredi moje podatke";
                else if(!empty($id))echo "Uredi korisnika";
                else echo "Dodaj korisnika";
              ?>
            </caption>
            <tbody style="display: flex; flex-flow: column; align-items: flex-end;">
              <tr>
                <td colspan="2">
                  <input type="hidden" name="novi" value="<?php if(!empty($id))echo $id;else echo 0;?>"/>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="text-align:center;">
                  <label class="greska"><?php if($greska!="")echo $greska; ?></label>
                </td>
              </tr>
              <tr>
                <td class="lijevi">
                  <label for="kor_ime"><strong>Korisničko ime:</strong></label>
                </td>
                <td>
                  <input type="text" name="kor_ime" id="kor_ime"
                    <?php
                      if($id!==0 && $tip==0)echo "readonly='readonly'";
                    ?>
                    value="<?php if(!isset($_POST['kor_ime']))echo $kor_ime; else echo $_POST['kor_ime'];?>" size="120" maxlength="50"
                    placeholder="Korisničko ime ne smije sadržavati praznine, treba uključiti minimalno 10 znakova i započeti malim početnim slovom"
                    required="required"/>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="ime"><strong>Ime:</strong></label>
                </td>
                <td>
                  <input type="text" name="ime" id="ime" value="<?php if(!isset($_POST['ime']))echo $ime; else echo $_POST['ime'];?>"
                    size="120" minlength="1" maxlength="50" placeholder="Ime treba započeti velikim početnim slovom" required="required"/>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="prezime"><strong>Prezime:</strong></label>
                </td>
                <td>
                  <input type="text" name="prezime" id="prezime" value="<?php if(!isset($_POST['prezime']))echo $prezime; else echo $_POST['prezime'];?>"
                    size="120" minlength="1" maxlength="50" placeholder="Prezime treba započeti velikim početnim slovom" required="required"/>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="lozinka"><strong>Lozinka:</strong></label>
                </td>
                <td>
                  <input <?php if(!empty($lozinka))echo "type='text'"; else echo "type='password'";?>
                    name="lozinka" id="lozinka" value="<?php if(!isset($_POST['lozinka']))echo $lozinka; else echo $_POST['lozinka'];?>"
                    size="120" minlength="8" maxlength="50"
                    placeholder="Lozinka treba sadržati minimalno 8 znakova uključujući jedno veliko i jedno malo slovo, jedan broj i jedan posebni znak"
                    required="required"/>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="email"><strong>E-mail:</strong></label>
                </td>
                <td>
                  <input type="email" name="email" id="email" value="<?php if(!isset($_POST['email']))echo $email; else echo $_POST['email'];?>"
                    size="120" minlength="5" maxlength="50" placeholder="Ispravan oblik elektroničke pošte je nesto@nesto.nesto" required="required"/>
                </td>
              </tr>
              <?php
                if($_SESSION['aktivni_korisnik_tip']==0){
              ?>
              <tr>
                <td><label for="tip"><strong>Tip korisnika:</strong></label></td>
                <td>
                  <select id="tip" name="tip">
                    <?php
                      if(isset($_POST['tip'])){
                        echo '<option value="0"';if($_POST['tip']==0)echo " selected='selected'";echo'>Administrator</option>';
                        echo '<option value="1"';if($_POST['tip']==1)echo " selected='selected'";echo'>Moderator</option>';
                        echo '<option value="2"';if($_POST['tip']==2)echo " selected='selected'";echo'>Korisnik</option>';
                      }
                      else{
                        echo '<option value="0"';if($tip==0)echo " selected='selected'";echo'>Administrator</option>';
                        echo '<option value="1"';if($tip==1)echo " selected='selected'";echo'>Moderator</option>';
                        echo '<option value="2"';if($tip==2)echo " selected='selected'";echo'>Korisnik</option>';
                      }
                    ?>
                  </select>
                </td>
              </tr>
              <?php
                }
              ?>
              <tr>
                <td>
                  <label for="slika"><strong>Slika:</strong></label>
                </td>
                <td>
                <?php
                  $dir=scandir("korisnici");
                  echo '<select id="slika" name="slika">';
                  foreach($dir as $key => $value){
                    if($key<2)continue;
                    else if(strcmp((isset($_POST['slika'])?$_POST['slika']:$slika),"korisnici/".$value)==0){
                      echo '<option value="'."korisnici/".$value.'"';
                      echo ' selected="selected">'."korisnici/".$value;
                      echo '</option>';
                    }
                    else{
                      echo '<option value="'."korisnici/".$value.'">';
                      echo "korisnici/".$value;
                      echo '</option>';
                    }
                  }
                  echo '</select>';
                ?>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="text-align:center;">
                  <?php
                    if(isset($id)&&$_SESSION["aktivni_korisnik_id"]==$id||!empty($id))echo '<input type="submit" name="submit" value="Pošalji"/>';
                    else echo '<input type="submit" name="reset" value="Izbriši"/><input type="submit" name="submit" value="Pošalji"/>';
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