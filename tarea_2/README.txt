Gabriel Garcés   rol: 202473555-1
Martin Araya   rol: 202473646-9


- Se agregaron relaciones de composición entre POSTULACION/CRONOGRAMA, POSTULACION/PERSONA_POSTULACION Y 
POSTULACION/DOCUMENTO.
- La barra de busqueda solo muestra las postulaciones que tengan alguna coincidencia con el string ingresado en el nombre de la iniciativa,
el nombre de la empresa o el numero de postulacion.

Consideraciones:
- Se asume que los datos ingresados tienen sentido, o sea, que no se puede tener 2 personas con el mismo RUT.
- Si se sobreescriben los datos de algún integrante, entonces se cambian sus valores para todas las otras postulaciones.
- Asumimos que el rol 2 (evaluador/coordinador) no tiene la función de crear postulaciones.
- Asumimos que uno puede ingresar a la página asumiendo 1 solo rol simultaneamente.
- Asumimos que no se puede repetir el correo y el teléfono entre 2 personas.
- Asumimos que se respetará que rut tiene acceso a que rol, siendo esté único por rut.
- No se le puede poner rol Responsable a otra persona.

Instrucciones ejecución:
1. Descargar los archivos presentes en la tarea.
2. Utilizar el script para generar la base de datos.
3. Una vez generada, se debe iniciar un server local mediante xampp, activando apache y mysql.
4. Posteriormente se debe ingresar a un navegador web y acceder al localhost donde están los archivos .php y .js.
5. Asegurarse que los archivos se encuentran en sus respectivas carpetas.
6. Acceder a la página según el rol que se desea probar.
7. En la base de datos hay información acerca de quienes son responsables y quienes son coordinadores, se deben usar sus respectivos rut para acceder segun rol.
