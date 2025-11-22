document.addEventListener("DOMContentLoaded", async () => {
  const tablaLista = document.getElementById("tabla-lista");
  let lista = JSON.parse(localStorage.getItem("lista")) || [];

  // Obtener los productos del backend (con manejo de errores)
  let productos = [];
  try {
    const res = await fetch("../../backend/obtener_inventario.php");
    productos = await res.json();
    // Normalizar ids de productos a string para comparar de forma fiable
    productos = productos.map(p => ({ ...p, id: String(p.id) }));
  } catch (err) {
    console.error("Error al obtener productos:", err);
    tablaLista.innerHTML = `<tr><td colspan=\"3\">Error al cargar productos.</td></tr>`;
    return;
  }

  // Normalizar la lista guardada: aceptar tanto 'cantidad' como 'cantidadSeleccionada'
  lista = lista.map(item => {
    const rawId = item.id ?? item.productoId ?? item.productId;
    const cantidad = Number(item.cantidad ?? item.cantidadSeleccionada ?? item.cantidadSeleccionada) || 1;
    return { id: String(rawId), cantidad };
  });

  if (lista.length === 0) {
    tablaLista.innerHTML = `<tr><td colspan="3">No hay productos en la lista.</td></tr>`;
    return;
  }

  renderLista();

  function renderLista() {
    tablaLista.innerHTML = ""; // limpiar tabla

    lista.forEach((item, index) => {
      const prod = productos.find((p) => p.id === String(item.id));
      if (prod) {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${prod.nombre}</td>
          <td class="cantidad-col">
            <button class="btn-control menos" data-index="${index}">−</button>
            <input type="number" class="input-cantidad" min="1" value="${item.cantidad}" data-index="${index}">
            <button class="btn-control mas" data-index="${index}">+</button>
          </td>
        `;
        tablaLista.appendChild(tr);
      }
    });

    // eventos
    document.querySelectorAll(".mas").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const index = Number(e.currentTarget.dataset.index);
        lista[index].cantidad = Number(lista[index].cantidad) + 1;
        guardarLista();
        renderLista();
      });
    });

    document.querySelectorAll(".menos").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const index = Number(e.currentTarget.dataset.index);
        lista[index].cantidad = Math.max(1, Number(lista[index].cantidad) - 1);
        guardarLista();
        renderLista();
      });
    });

    document.querySelectorAll(".input-cantidad").forEach((input) => {
      input.addEventListener("change", (e) => {
        const index = Number(e.currentTarget.dataset.index);
        const valor = parseInt(e.currentTarget.value);
        lista[index].cantidad = valor > 0 ? valor : 1;
        guardarLista();
        // Re-render to keep UI consistent
        renderLista();
      });
    });
  }

  function guardarLista() {
    localStorage.setItem("lista", JSON.stringify(lista));
  }

  document.getElementById("btn-guardar").addEventListener("click", guardarCambios);
  document.getElementById("btn-limpiar").addEventListener("click", limpiarLista);

  async function guardarCambios() {
    const tipo = document.getElementById("tipo-movimiento").value;

    if (lista.length === 0) {
      alert("No hay productos en la lista.");
      return;
    }

    // Enviar lista normalizada (id como número si backend lo espera)
    const payloadLista = lista.map(i => ({ id: Number(i.id), cantidad: Number(i.cantidad) }));

    const res = await fetch("../../backend/actualizar_inventario.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ tipo, lista: payloadLista }),
    });

    const data = await res.json();
    if (data.status === "ok") {
      alert("Inventario actualizado correctamente ✅");
      localStorage.removeItem("lista");
      location.href = "index.html";
    } else {
      alert("Error al actualizar inventario ❌");
    }
  }

  function limpiarLista() {
    localStorage.removeItem("lista");
    alert("Lista vaciada.");
    renderLista();
  }
});
