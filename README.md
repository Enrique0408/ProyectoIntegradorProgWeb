# Inventario-RAYDA

## Inicializar la base de datos

Hay un script preparado que crea la base de datos y las tablas necesarias: `backend/setup_database.php`.

Formas de ejecutarlo:

- Desde el navegador (XAMPP):
	- Visita: `http://localhost/Inventario-RAYDA/backend/setup_database.php`

- Desde la línea de comandos (PowerShell / CMD):
	- Ve al directorio del proyecto y ejecuta:

```powershell
php backend\setup_database.php
```

El script intentará extraer las credenciales desde `backend/conexion.php` y crear la base de datos (por defecto `inventario_construccion`) y sus tablas. También incluye datos de ejemplo para `inventario`.

Si necesitas cambiar las credenciales edita `backend/conexion.php` antes de ejecutar el script.

### Script PowerShell `init-db.ps1`

Hay un helper `init-db.ps1` en la raíz del proyecto que automatiza la ejecución de `backend/setup_database.php` desde PowerShell. El script intenta usar `C:\xampp\php\php.exe` si existe, o el `php` disponible en el PATH.

Uso:

```powershell
cd C:\xampp\htdocs\Inventario-RAYDA
.\init-db.ps1
```

El script imprimirá el resultado en la consola.

### Habilitar la extensión `mysqli` (si aparece "Class \"mysqli\" not found")

1. Abre `C:\xampp\php\php.ini` en un editor de texto.
2. Busca la línea que contenga `;extension=mysqli` o `;extension=php_mysqli.dll`.
3. Elimina el `;` al inicio para descomentarla:

```ini
extension=mysqli
```

4. Guarda `php.ini` y reinicia Apache desde el panel de XAMPP.
5. Verifica creando `phpinfo.php` con `<?php phpinfo(); ?>` y abriéndolo en el navegador.

Si la CLI de PHP es distinta a la de Apache, comprueba `php --ini` y `phpinfo()` para ver qué `php.ini` carga cada uno.


Registro, que muestre los datos y guarde la informacion
Agregacion de funcion para guardar productos dentro del catalogo
