# dbravoan/dbcms-theme-db-minimal 1.0.0

## Qué hace

`dbravoan/dbcms-theme-db-minimal` es un tema público (`dbcms-theme`). Identifier: `db-minimal`. Estética minimalista escandinava con líneas limpias, máxima legibilidad, paleta monocromática y generoso espacio negativo.

## Requisitos y dependencias

- `dbravoan/dbcms-core` `^1.0`

## Instalación y activación

Añade el paquete Composer de tipo `dbcms-theme`. Selección del tema público activo:

1. Entorno / config: clave `CMS_THEME` → `config('cms.theme')`.
2. Ajuste persistido del panel: `cms.active_theme` (`ThemeManagerService::ACTIVE_THEME_SETTING`), con prioridad sobre la config si el tema está instalado.
3. Panel de temas del núcleo (`ThemesGetController` / activación vía `ThemeManagerService::setActiveTheme`).

El tema debe estar instalado en el proyecto; si no, la activación falla con validación.

## Configuración

Claves por nombre (núcleo, no valores):

- `CMS_THEME` / `cms.theme`
- ajuste `cms.active_theme`

Manifest del tema: `theme.json` (name, identifier, description, author). Tokens de filtros UI en `filter.json` si existe.

## Rutas

No aplica.

## Comandos Artisan

No aplica.

## Permisos

No aplica.

## Privacidad y datos

No aplica de forma propia. Las vistas deben incluir la UI de privacidad del núcleo en layouts completos personalizados (contrato de extensiones).

## Plantillas y assets

- `layouts/app.blade.php`
- `pages/show.blade.php`
- `partials/header.blade.php`
- `partials/footer.blade.php`
- `blog/index.blade.php`
- `blog/show.blade.php`
- `blog/category.blade.php`
- `gallery/index.blade.php`
- `gallery/show.blade.php`
- `shop/index.blade.php`
- `shop/show.blade.php`
- `shop/cart.blade.php`
- `shop/checkout.blade.php`
- `shop/order_success.blade.php`
- `shop/blocks/products.blade.php`
- `theme.css`
- `filter.json`
- `theme.json`

## Enlaces

- [Extensiones](../../../docs/EXTENSIONS.md)
- [Núcleo y extensiones](../../../specs/CORE_AND_EXTENSIONS.md)
- [README del núcleo](../../../packages/core/README.md)
- [README del monorepo](../../../README.md)
