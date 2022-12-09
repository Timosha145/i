<?php
require_once('connect.php');
//require_once('ab_login.php');

global $yhendus;

session_start();

if (isset($_SESSION['tuvastamine'])) {
    exit();
}

if (!empty($_POST['login']) && !empty($_POST['pass'])) {
//eemaldame kasutaja sisestusest kahtlase pahna
    $login = htmlspecialchars(trim($_POST['login']));
    $pass = htmlspecialchars(trim($_POST['pass']));
//SIIA UUS KONTROLL
    $sool = 'taiestisuvalinetekst';
    $kryp = crypt($pass, $sool);
//kontrollime kas andmebaasis on selline kasutaja ja parool
//$paring = "SELECT * FROM kasutajad WHERE kasutaja='$login' AND parool='$kryp'";
//$valjund = mysqli_query($yhendus, $paring);


    if (isset($_REQUEST['loginA'])) {
        $kask = $yhendus->prepare("SELECT kasutaja, onAdmin, koduleht FROM kasutajad WHERE kasutaja=? AND parool=?");
        $kask->bind_param("ss", $login, $kryp);
        $kask->bind_result($nimi, $onAdmin, $koduleht);
        $kask->execute();

        if ($kask->fetch()) {
            //kui on, siis loome sessiooni ja suuname
            //if (mysqli_num_rows($valjund)==1) {
            $_SESSION['tuvastamine'] = 'misiganes';
            $_SESSION['kasutaja'] = $nimi;
            $_SESSION['onAdmin'] = $onAdmin;

            if (isset($koduleht) && $onAdmin == 1) {

            } else {

            }
        } else {
            echo '<script>alert("Midagi läks valesti!")</script>';
        }
    }
    else if (isset($_REQUEST['register'])) {
        $kask = $yhendus->prepare("INSERT INTO kasutajad (kasutaja, parool) values(?,?)");
        $pass = crypt($pass, $sool);
        $kask->bind_param("ss", $login, $pass);
        $kask->execute();

        echo '<script>alert("Registreeritud!!")</script>';
    }
}

    if (isset($_REQUEST['lisamisVorm']) && !empty($_REQUEST['linn'])) {
        $paring = $yhendus->prepare("INSERT INTO linnad(linnaNimi, rahvastik, linnaPeaId) VALUES (?,?,?)");
        $paring->bind_param('sii', $_REQUEST['linn'], $_REQUEST['rahvastik'], $_REQUEST['linnapea']);
        $paring->execute();
    }

    if (isset($_REQUEST['kustuta'])) {
        $paring = $yhendus->prepare("DELETE FROM linnad WHERE id=?");
        $paring->bind_param('i', $_REQUEST['kustuta']);
        $paring->execute();
    }

    $paring=$yhendus->prepare("SELECT Id, linnaNimi, rahvastik, linnapea.linnaPea FROM linnad, linnapea where linnad.linnaPeaId=linnapea.linnaPeaId");
    $paring->bind_result($id, $linnaNimi, $rahvastik, $linnaPea);
    $paring->execute();


?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Linnad</title>
    <link rel="stylesheet" type="text/css" href="loomadLinkidegaStylw.css">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form Modal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    <script>
        function toggleModal() {
            document.getElementById("overlay").classList.toggle('active');
        }
    </script>
</head>
<body>
<header>
    <h1>Eesti Linnad</h1>
    <a style='font-size: 20pt; color: #A52A2A' href='https://github.com/Timosha145/linnad.git'>Github</a>
    <div class="btnSec">
        <button type="button" onclick="toggleModal()">Sisse logida</button>
    </div>
    <br>
</header>



<section id="overlay">
    <aside>
        <h2>Login</h2>
        <form action="" method="post">
            <a onclick="toggleModal()" class="close">
                <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:none;stroke:#a70eff;stroke-linecap:round;stroke-linejoin:round;stroke-width:2px;}</style></defs><title/><g id="cross"><line class="cls-1" x1="7" x2="25" y1="7" y2="25"/><line class="cls-1" x1="7" x2="25" y1="25" y2="7"/></g></svg>
            </a>
            <h4>Kasutaja</h4>
            <input type="text" name="login" />
            <h4>Salasõna</h4>
            <input type="password" name="pass"/>
            <button type="submit" name="loginA">Login</button>
            <button type="submit" name="register">Registreeri</button>
        </form>
    </aside>
</section>

<div id="menu">
    <h3>Linnade nimed</h3>
    <?php
    
    //näitab loomade loetelu tabelist linnad
    echo "<ul>";
    while($paring->fetch())
    {
        echo "<li><a href='?id=$id'>$linnaNimi</a></li>"; //htmlspecialchars - käsk nurksulgudes mis ei loetakse

    }
    echo "</ul>";
    echo "<a href='?jah'>Lisa linn</a>";


    ?>
</div>
<div id="line"></div>
<div id="info">
    <?php
    if (isset($_REQUEST['id']))
    {
        $paring=$yhendus->prepare("SELECT linnaNimi, rahvastik, linnapea.linnaPea FROM linnad, linnapea WHERE id=? && linnapea.linnaPeaId=linnad.linnaPeaId ");
        $paring->bind_param('i', $_REQUEST['id']);
        //küsimärki asemel aadressiribalt tuleb id
        $paring->bind_result( $linnaNimi, $rahvastik, $linnaPea);
        $paring->execute();

        //$paring=$yhendus->prepare("SELECT linnaPea FROM linnapea WHERE linnaPeaId=?");
        //$paring->bind_param('i', $linnaPeaId);
        //$paring->bind_result($linnaPea);
        //$paring->execute();


        if($paring->fetch())
        {
            echo "<div id='infoSisu'>"."<strong>Linn: </strong>".htmlspecialchars($linnaNimi)."<br>";
            echo "<strong>Rahvastik: </strong>".htmlspecialchars($rahvastik)." inimest<br>";
            echo "<strong>Linnapea: </strong>".htmlspecialchars($linnaPea)."<br>";
            echo "<a  style='font-size: 16pt; color: #A52A2A' href='?kustuta=".$_REQUEST['id']."'><strong>Kustuta</strong></a>";
            echo "</div>";
        }
    }

    else if (isset($_REQUEST['jah']))
    {
    ?>
        <h2>Uue linna lisamine</h2>

    <form name="uusLinn" method="post" action="?">
        <input type="hidden" name="lisamisVorm">
        <input type="text" name="linn" placeholder="Linn">
        <br>
        <label for="rahvastik">Rahvastik: </label>
        <input type="number" name="rahvastik" value="0" min="0">
        <br>
        <label for="Linnapea">Linnapea: </label>
        <input type="number" name="linnapea" value="1" min="1" max="2">
        <input type="submit" value="OK">
    </form>

    <?php
    }
    else
    {
        echo "<h3>Siia tuleb linnade informatsioon</h3>";
    }
    ?>

</div>


</body>
<?php
$yhendus->close();
?>
</html>
