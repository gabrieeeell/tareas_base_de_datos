document.addEventListener("DOMContentLoaded", function() {
    // --- LÓGICA PARA EL EQUIPO DE TRABAJO ---
    const btnAgregarIntegrante = document.getElementById("btn_agregar_integrante");
    const contenedorIntegrantes = document.getElementById("contenedor_integrantes");
    const templateIntegrante = document.getElementById("template_integrante");
    
    // Empezamos en 1 porque ya existe el Integrante 1 (Responsable)
    let contadorIntegrantes = 1; 

    if (btnAgregarIntegrante && contenedorIntegrantes && templateIntegrante) {
        btnAgregarIntegrante.addEventListener("click", function() {
            contadorIntegrantes++;
            
            // Clonamos el contenido del template
            const nuevoIntegrante = templateIntegrante.content.cloneNode(true);
            
            // Actualizamos el título visual (Ej: "Integrante 2", "Integrante 3")
            nuevoIntegrante.querySelector(".titulo-integrante").textContent = "Integrante " + contadorIntegrantes;
            
            // Le damos vida al botón rojo de eliminar (la "X")
            const btnEliminar = nuevoIntegrante.querySelector(".btn-eliminar");
            btnEliminar.addEventListener("click", function(e) {
                // Al hacer clic, borra toda la cajita de ese integrante
                e.target.closest(".integrante-item").remove();
            });
            
            // Inyectamos la copia en la pantalla
            contenedorIntegrantes.appendChild(nuevoIntegrante);
        });
    }

    // --- LÓGICA PARA EL CRONOGRAMA ---
    const btnAgregarEtapa = document.getElementById("btn_agregar_etapa");
    const contenedorCronograma = document.getElementById("contenedor_cronograma");
    const templateEtapa = document.getElementById("template_etapa");
    
    let contadorEtapas = 1;

    if (btnAgregarEtapa && contenedorCronograma && templateEtapa) {
        btnAgregarEtapa.addEventListener("click", function() {
            contadorEtapas++;
            
            // Clonamos el template de la etapa
            const nuevaEtapa = templateEtapa.content.cloneNode(true);
            
            // Actualizamos el título (Ej: "Etapa 2")
            nuevaEtapa.querySelector(".titulo-etapa").textContent = "Etapa " + contadorEtapas;
            
            // Lógica para eliminar esta etapa específica
            const btnEliminarEtapa = nuevaEtapa.querySelector(".btn-eliminar-etapa");
            btnEliminarEtapa.addEventListener("click", function(e) {
                e.target.closest(".etapa-item").remove();
            });
            
            // Inyectamos la nueva etapa al final del contenedor
            contenedorCronograma.appendChild(nuevaEtapa);
        });
    }
});