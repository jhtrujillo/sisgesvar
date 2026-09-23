const origen_parcela = "MY2024-La_Aurora-2A-37";
const parts = origen_parcela.split("-");
if (parts.length >= 5) {
  console.log("IF: " + parts.slice(0, 4).join("-") + " AND " + parts.slice(4).join("-"));
} else {
  console.log("ELSE: " + origen_parcela);
}
