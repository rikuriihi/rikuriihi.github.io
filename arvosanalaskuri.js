function kerroArvosana(event) {
  event.preventDefault(); // estää sivun latautumisen uudelleen

  const vastaus = document.getElementById("arvosana-vastaus");
  const syote = document.getElementById("pisteet").value;
  const pisteet = Number(syote);

  if (syote === "" || Number.isNaN(pisteet) || pisteet < 0 || pisteet > 30) {
    vastaus.textContent = "Anna pisteet väliltä 0–30.";
    return;
  }

  if (pisteet >= 27) {
    vastaus.textContent = "Arvosana on kiitettävä eli 5";
  } else if (pisteet >= 24) {
    vastaus.textContent = "Arvosana on erinomainen eli 4";
  } else if (pisteet >= 21) {
    vastaus.textContent = "Arvosana on hyvä eli 3";
  } else if (pisteet >= 18) {
    vastaus.textContent = "Arvosana on tyydyttävä eli 2";
  } else if (pisteet >= 15) {
    vastaus.textContent = "Arvosana on heikko eli 1";
  } else {
    vastaus.textContent = "Arvosana on hylätty eli 0";
  }
}

document.getElementById("arvosana-lomake").addEventListener("submit", kerroArvosana);