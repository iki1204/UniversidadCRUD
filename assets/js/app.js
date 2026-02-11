(function () {
  const table = document.getElementById("itemsTable");
  if (!table) return;

  const tbody = table.querySelector("tbody");
  const totalCell = document.getElementById("totalCell");
  const addBtn = document.getElementById("addRow");
  const products = window.__products || [];

  function money(n) {
    return "$" + Number(n || 0).toFixed(2);
  }

  function rowTemplate(index) {
    const tr = document.createElement("tr");

    const tdProd = document.createElement("td");
    const sel = document.createElement("select");
    sel.className = "form-select";
    sel.name = `items[${index}][PRODUCTO_ID]`;
    sel.required = true;
    sel.innerHTML =
      `<option value="">Seleccione...</option>` +
      products
        .map(
          (p) =>
            `<option value="${p.id}" data-precio="${p.precio}" data-stock="${p.stock}">${p.label}</option>`,
        )
        .join("");
    tdProd.appendChild(sel);

    const tdQty = document.createElement("td");
    const qty = document.createElement("input");
    qty.type = "number";
    qty.min = "1";
    qty.value = "1";
    qty.className = "form-control";
    qty.name = `items[${index}][CANTIDAD]`;
    tdQty.appendChild(qty);

    const tdPrice = document.createElement("td");
    const price = document.createElement("input");
    price.type = "number";
    price.step = "0.01";
    price.min = "0";
    price.value = "0.00";
    price.className = "form-control";
    price.name = `items[${index}][PRECIO]`;
    tdPrice.appendChild(price);

    const tdSub = document.createElement("td");
    tdSub.className = "fw-semibold";
    tdSub.textContent = money(0);

    const tdDel = document.createElement("td");
    tdDel.className = "text-center";
    const btn = document.createElement("button");
    btn.type = "button";
    btn.className = "btn btn-outline-danger btn-sm";
    btn.innerHTML = '<i class="bi bi-x"></i>';
    tdDel.appendChild(btn);

    function syncFromSelect() {
      const opt = sel.options[sel.selectedIndex];
      const p = Number(opt?.dataset?.precio || 0);
      const s = Number(opt?.dataset?.stock || 0);
      if (p > 0) price.value = p.toFixed(2);
      qty.max = s ? String(s) : "";
      recalc();
    }

    function recalc() {
      const q = Number(qty.value || 0);
      const pr = Number(price.value || 0);
      tdSub.textContent = money(q * pr);
      recalcTotal();
    }

    sel.addEventListener("change", syncFromSelect);
    qty.addEventListener("input", recalc);
    price.addEventListener("input", recalc);
    btn.addEventListener("click", () => {
      tr.remove();
      recalcTotal();
    });

    tr.append(tdProd, tdQty, tdPrice, tdSub, tdDel);
    return tr;
  }

  function recalcTotal() {
    let total = 0;
    tbody.querySelectorAll("tr").forEach((tr) => {
      const sub = tr.children[3]?.textContent?.replace("$", "") || "0";
      total += Number(sub);
    });
    if (totalCell) totalCell.textContent = money(total);
  }

  let idx = 0;
  function addRow() {
    tbody.appendChild(rowTemplate(idx++));
  }

  addBtn?.addEventListener("click", addRow);

  addRow();
})();

(function () {
  const inputs = document.querySelectorAll("[data-table-search]");
  if (!inputs.length) return;

  function filterRows(input) {
    const selector = input.getAttribute("data-table-search");
    if (!selector) return;

    const table = document.querySelector(selector);
    const tbody = table?.tBodies?.[0];
    if (!tbody) return;

    const rows = Array.from(tbody.rows);
    const query = input.value.trim().toLowerCase();
    const terms = query ? query.split(/\s+/).filter(Boolean) : [];

    rows.forEach((row) => {
      const text = row.textContent.toLowerCase();
      const matches =
        terms.length === 0 || terms.every((term) => text.includes(term));
      row.style.display = matches ? "" : "none";
    });
  }

  inputs.forEach((input) => {
    const button = input
      .closest(".input-group")
      ?.querySelector("[data-table-search-button]");
    button?.addEventListener("click", () => filterRows(input));
    input.addEventListener("keydown", (event) => {
      if (event.key === "Enter") {
        event.preventDefault();
        filterRows(input);
      }
    });
  });
})();
