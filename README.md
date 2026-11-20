# dbCMS Minimal (Nordic Monocrom)

Tema público autónomo para dbCMS, compatible con `dbravoan/dbcms-core:~0.0.1`.
Posee su layout, navegación, paleta y vistas. Se desarrolla y publica desde este
repositorio.

```bash
composer require dbravoan/dbcms-theme-db-minimal:~0.0.1
```

Selecciona `db-minimal` en Apariencia o al crear la aplicación con el instalador
público. El proyecto compila sus recursos mediante Vite; no edites vendor.

El tema funciona con core sin módulos opcionales. Sus vistas para Blog, Gallery
y Shop se usan exclusivamente cuando está instalado y activo el propietario.
Shop es una integración experimental y se prueba en un perfil separado; no es
una dependencia obligatoria del tema. Popups reutiliza overlays y consentimiento
del núcleo y también tiene un perfil de integración propio.

Para personalizar, conserva los overrides en la aplicación o crea un tema propio.
Mantén los hooks comunes de privacidad, SEO, localización, admin bar y regiones
de contenido. Cada layout pertenece a su tema; no toma prestado el de otro.

- [Mantenimiento específico](https://github.com/dbravoan/dbcms-theme-db-minimal/blob/main/docs/MAINTENANCE.md)
- [Desarrollo y pruebas](https://github.com/dbravoan/dbcms-module-template/blob/main/docs/TESTING.md)
- [Publicación](https://github.com/dbravoan/dbcms-module-template/blob/main/docs/PUBLICATION.md)
- [Contratos públicos del core](https://github.com/dbravoan/dbcms-core/tree/main/docs)

Licencia MIT.
