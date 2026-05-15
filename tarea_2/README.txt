Gabriel Garcés   rol: 202473555-1
Martin Araya   rol: 202473646-9


- Se agregaron relaciones de composición entre POSTULACION/CRONOGRAMA, POSTULACION/PERSONA_POSTULACION Y 
POSTULACION/DOCUMENTO.
- La barra de busqueda solo muestra las postulaciones que tengan alguna coincidencia con el string ingresado en el nombre de la iniciativa, el nombre de la empresa o el numero de postulacion

Consideraciones:
- Se asume que los datos ingresados tienen sentido, o sea, que no se puede tener 2 personas con el mismo RUT.
- Si se sobreescriben los datos de algún integrante, entonces se cambian sus valores para todas las otras postulaciones
- Asumimos que el rol 2 (evaluador/coordinador) no tiene la función de crear postulaciones.
- Asumimos que uno puede ingresar a la página asumiendo 1 solo rol simultaneamente.

Instrucciones ejecución:
