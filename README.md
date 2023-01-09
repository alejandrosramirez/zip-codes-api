# ¿Como fue que aborde el problema?

Esta solución fue elaborada con Laravel en la versión más reciente. Dado el archivo de excel anexado en el documento del reto, tuve que resolverlo de la siguiente manera:

1. Se dio de alta el modelo 'ZipCode' y la migración '2023_01_09_152141_create_zip_codes_table' con los que di de alta los mismos campos que los del archivo de excel y no sobreescribir con nuevos nombres.
2. Como el archivo de excel era realmente pesado para importarlo en 1 solo, lo dividi en 4 archivos diferentes por 8 estados cada archivo.
3. Di de alta una ruta en el backend llamada `/zip-codes` que recibe un parametro llamado `?zip_code_file=` y este permite verificar si el archivo que se desea importar es valido con un enum que declare en la carpeta de enums, en caso de no ser correcta, arroja una excepción que indica que el archivo no existe y no se procedera con la importación. A su vez en el metodo del controlador `ZipCodeController@save` anexo comentarios extras que explican la forma en cómo se realizo para la importación.
4. Se hablito la ruta `/api/zip-codes/{zip_code}` para hacer la busqueda de los códigos postales y replicar el json de respuesta del ejemplo del documento.
5. Cómo extra utilice los mutadores/accesores que ofrecen los modelos de eloquent para hacer modificaciones en algunas columnas de mi tabla, de las cuales fueron devolver algunas columnas con mayusculas y sin acentos y usar la propiedad `$casts` para parsear algunos elementos de cadenas a números.
6. Se realizaron pruebas del endpoint para obtener resultados y comparar los tiempos de respuesta tanto local como en un servidor desplegado.

> Observaciones finales de este reto
> A mi parecer fue algo muy retador, más por el tema de trabajar con una base de datos de códigos postales,
> ya que en un inicio pretendía trabajar con una base de datos más elaborada que manejara modelos como
> la entidad federativa, los tipos de asentamiento, municipios y poderlos relacionar entre los códigos postales y asentamientos y manetener un poco más
> centralizada la información, pero por el tema del tiempo y la gran cantidad de datos se planteo esta solución y hacerlo más rápido.
