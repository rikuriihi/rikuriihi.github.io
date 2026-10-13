function naytaKertotaulu(event) {
  event.preventDefault();

  const vastaus = document.getElementById("kertotaulu-vastaus");
  const luku = Number(document.getElementById("luku").value);
  const rivilla = Number(document.getElementById("rivilla").value) || 1;

  vastaus.innerHTML = "";
  vastaus.style.display = "grid";
  vastaus.style.gap = "4px 16px";
  vastaus.style.gridTemplateColumns = "repeat(" + rivilla + ", auto)";

  for (let i = 1; i <= 20; i++) {
    const solu = document.createElement("span");
    solu.textContent = luku * i;
    vastaus.appendChild(solu);
  }
}

document.getElementById("kertotaulu-lomake").addEventListener("submit", naytaKertotaulu);
