# Mantener db-minimal

Este repositorio posee el layout, navegación, paleta, regiones de contenido y
vistas de db-minimal. Core posee descubrimiento, localización, privacidad, SEO,
render y assets compartidos. Blog posee comentarios, moderación, formulario y URL
de envío; `blog/show` incluye su partial canónico. Shop posee precio,
especificaciones, oferta, carrito, checkout y resumen del pedido. Sus partials
poseen formularios, importes, consultas y privacidad; el tema conserva galería y
presentación.

El perfil predeterminado prueba core sin módulos: catálogo propio, independencia
de layouts, navegación en tres composiciones de idioma/mercado, render de secciones,
regiones y selección de assets. `content` verifica Blog y Gallery reales; `popups`
verifica overlays y caché con el propietario desactivado.

Al añadir una vista opcional, conserva su prueba en el consumidor y declara la
dependencia en el perfil correspondiente. Mantén un layout propio y los hooks de
privacidad y overlays. Los logos/fuentes externos requieren el tratamiento real
correspondiente; los recursos predeterminados son locales.

Los procedimientos comunes tienen una sola fuente pública:

- [Desarrollo Docker](https://github.com/dbravoan/dbcms-module-template/blob/main/docs/DEVELOPMENT.md)
- [Pruebas, perfiles y locks](https://github.com/dbravoan/dbcms-module-template/blob/main/docs/TESTING.md)
- [Publicación por tag con SSH local](https://github.com/dbravoan/dbcms-module-template/blob/main/docs/PUBLICATION.md)
- [Arquitectura y contratos](https://github.com/dbravoan/dbcms-core/tree/main/docs)

Tras cambiar dependencias o composición, regenera y revisa sus locks desde Docker.
Conserva el tooling local necesario para contribuir sin repositorios privados;
las actualizaciones de la plantilla se incorporan y prueban en este propietario.

## Geometría en navegador

`tests/Browser/content-region.spec.mjs` conserva las expectativas propias del tema
para 390, 800 y 1480 px. La suite sirve HTML PHP y assets reales con red interceptada.
Genera fixtures con `DBCMS_REGION_FIXTURE_DIR` al ejecutar el `ContentRegionTest`
del perfil base y su build. Después, desde un entorno Node 22 con Chromium:

```bash
npm --prefix tests/Browser ci
DBCMS_REGION_FIXTURE_DIR=/ruta/fixtures \
DBCMS_REGION_ASSET_ROOT="$PWD/.build/app/public" \
npm exec --prefix tests/Browser -- playwright test --config tests/Browser/playwright.config.mjs
```

`DBCMS_BROWSER_CHROME` permite seleccionar Chromium; `DBCMS_BROWSER_RESULTS`
dirige trazas y capturas fuera de Git. La invocación exige ambos directorios y
falla si faltan; no convierte la ausencia del entorno en pruebas omitidas.
