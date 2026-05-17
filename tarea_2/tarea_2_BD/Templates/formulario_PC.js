document.addEventListener("DOMContentLoaded", function() {
    const btnAgregarIntegrante = document.getElementById("btn_agregar_integrante");
    const contenedorIntegrantes = document.getElementById("contenedor_integrantes");
    const templateIntegrante = document.getElementById("template_integrante");
    

    if (btnAgregarIntegrante && contenedorIntegrantes && templateIntegrante) {
        contenedorIntegrantes.addEventListener("click", function(e) {
            if (e.target.classList.contains("btn-eliminar")) {
                e.target.closest(".integrante-item").remove();
            }
        });

        btnAgregarIntegrante.addEventListener("click", function() {

            const nuevoIntegrante = templateIntegrante.content.cloneNode(true);
                nuevoIntegrante.querySelector(".titulo-integrante").textContent = "Integrante";     
            contenedorIntegrantes.appendChild(nuevoIntegrante);
        });
    }


    const btnAgregarEtapa = document.getElementById("btn_agregar_etapa");
    const contenedorCronograma = document.getElementById("contenedor_cronograma");
    const templateEtapa = document.getElementById("template_etapa");

    if (btnAgregarEtapa && contenedorCronograma && templateEtapa) {
        

        contenedorCronograma.addEventListener("click", function(e) {
            if (e.target.classList.contains("btn-eliminar-etapa")) {
                e.target.closest(".etapa-item").remove();
            }
        });
        btnAgregarEtapa.addEventListener("click", function() {
            const nuevaEtapa = templateEtapa.content.cloneNode(true);
            const tituloEtapa = nuevaEtapa.querySelector(".titulo-etapa");
            if (tituloEtapa) {
                tituloEtapa.textContent = "Etapa";
            }
            contenedorCronograma.appendChild(nuevaEtapa);
        });
    }
});