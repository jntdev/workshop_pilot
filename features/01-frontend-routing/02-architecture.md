# 02 - Architecture pages & routing

- `layouts/MainLayout` : header/breadcrumb/nav communs.
- `layouts/ChapterLayout` (optionnel) : titre + actions contextuelles par chapitre.
- Pages :
  - `HomeDashboard` → `/`
  - `ClientsIndex` → `/clients`
  - `AtelierIndex` → `/atelier`
  - `LocationIndex` → `/location`
- Routing : déclarer les routes ci-dessus (React Router, Vue Router, etc.) en prévoyant le lazy loading si pertinent. 
