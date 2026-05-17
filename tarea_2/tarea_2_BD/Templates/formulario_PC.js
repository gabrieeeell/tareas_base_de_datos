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

            
            // Clonamos el contenido del template
            const nuevoIntegrante = templateIntegrante.content.cloneNode(true);
            
            // Actualizamos el título visual
            // Actualizamos el título visual de forma fija
                nuevoIntegrante.querySelector(".titulo-integrante").textContent = "Integrante";     
            
            // Inyectamos la copia en la pantalla (la delegación de arriba manejará su botón X)
            contenedorIntegrantes.appendChild(nuevoIntegrante);
        });
    }

    // ==========================================
    // --- LÓGICA PARA EL CRONOGRAMA ---
    // ==========================================
    const btnAgregarEtapa = document.getElementById("btn_agregar_etapa");
    const contenedorCronograma = document.getElementById("contenedor_cronograma");
    const templateEtapa = document.getElementById("template_etapa");

    if (btnAgregarEtapa && contenedorCronograma && templateEtapa) {
        
        // DELEGACIÓN DE EVENTOS: Escucha clics en las etapas cargadas y nuevas
        contenedorCronograma.addEventListener("click", function(e) {
            // Si el elemento cliqueado tiene la clase 'btn-eliminar-etapa' (la X del cronograma)
            if (e.target.classList.contains("btn-eliminar-etapa")) {
                // Borra la cajita de la etapa completa de la pantalla
                e.target.closest(".etapa-item").remove();
            }
        });

        btnAgregarEtapa.addEventListener("click", function() {
            // Clonamos el template de la etapa
            const nuevaEtapa = templateEtapa.content.cloneNode(true);
            
            // Actualizamos el título de forma estática
            const tituloEtapa = nuevaEtapa.querySelector(".titulo-etapa");
            if (tituloEtapa) {
                tituloEtapa.textContent = "Etapa";
            }
            
            // Inyectamos la nueva etapa al final del contenedor
            contenedorCronograma.appendChild(nuevaEtapa);
        });
    }
});