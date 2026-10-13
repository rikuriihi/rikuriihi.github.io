<?php
/**
 * laheta_palaute.php
 * Vastaanottaa lomakkeen tiedot (POST) ja lähettää ne sähköpostiisi.
 */

// ====== ASETUKSET ======
$vastaanottaja = "rikurii03@gmail.com";      // <-- vaihda oma sähköpostiosoitteesi
$lahettaja     = "https://rikuriihimaki.eu/contact.html";  // <-- osoite sivustosi omasta domainista (parantaa perillemenoa)
$otsikko       = "Uusi palaute portfoliosivustolta";
$kiitossivu    = "https://rikuriihimaki.eu/kiitos.html";         // <-- sivu, jolle palataan lähetyksen jälkeen
// =======================

// Hyväksytään vain POST-pyynnöt
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Virheellinen pyyntö.");
}

// Honeypot-kenttä roskapostibotteja vastaan (lomakkeessa piilotettu kenttä "nettisivu")
if (!empty($_POST["nettisivu"])) {
    exit("Kiitos!"); // Botti: ei lähetetä mitään
}

// Siivotaan syötteet
function puhdista($arvo) {
    return trim(strip_tags($arvo ?? ""));
}

$etunimi  = puhdista($_POST["etunimi"] ?? "");
$sukunimi = puhdista($_POST["sukunimi"] ?? "");
$email    = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
$viesti   = puhdista($_POST["viesti"] ?? ""); // vapaaehtoinen

// Tarkistukset
$virheet = [];
if ($etunimi === "" || mb_strlen($etunimi) > 50) {
    $virheet[] = "Anna etunimi (enintään 50 merkkiä).";
}
if ($sukunimi === "" || mb_strlen($sukunimi) > 50) {
    $virheet[] = "Anna sukunimi (enintään 50 merkkiä).";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $virheet[] = "Anna kelvollinen sähköpostiosoite.";
}
if (mb_strlen($viesti) > 5000) {
    $virheet[] = "Viesti on liian pitkä (enintään 5000 merkkiä).";
}

// Estetään header injection (rivinvaihdot nimessä ja sähköpostissa)
if (preg_match("/[\r\n]/", $etunimi . $sukunimi) || preg_match("/[\r\n]/", $email)) {
    $virheet[] = "Virheellinen syöte.";
}

if (!empty($virheet)) {
    http_response_code(400);
    echo "<h2>Viestiä ei lähetetty:</h2><ul>";
    foreach ($virheet as $v) {
        echo "<li>" . htmlspecialchars($v, ENT_QUOTES, "UTF-8") . "</li>";
    }
    echo "</ul><p><a href='javascript:history.back()'>Takaisin</a></p>";
    exit;
}

// Rakennetaan sähköposti
$sisalto  = "Nimi: $etunimi $sukunimi\n";
$sisalto .= "Sähköposti: $email\n";
$sisalto .= "Lähetetty: " . date("d.m.Y H:i") . "\n\n";
$sisalto .= "Viesti:\n" . ($viesti !== "" ? $viesti : "(ei viestiä)") . "\n";

$headers  = "From: $lahettaja\r\n";
$headers .= "Reply-To: $email\r\n";   // Vastaa-painike menee suoraan palautteen antajalle
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Otsikko UTF-8-koodattuna, jotta ä ja ö toimivat
$koodattuOtsikko = "=?UTF-8?B?" . base64_encode($otsikko) . "?=";

if (mail($vastaanottaja, $koodattuOtsikko, $sisalto, $headers)) {
    header("Location: " . $kiitossivu . "?status=ok");
    exit;
} else {
    http_response_code(500);
    echo "Viestin lähetys epäonnistui. Yritä myöhemmin uudelleen.";
}
