# Ticket 02 : Upload photo article

**Type** : Backend / API
**Priorité** : Haute
**Estimation** : 1h30

**Dépend de** : —

## Tâches

- [ ] Route dans `routes/api.php`, groupe Catalogue — Articles :
  ```php
  Route::post('/articles/upload-photo', [ArticleController::class, 'uploadPhoto']);
  ```
- [ ] `php artisan make:request Api/UploadArticlePhotoRequest --no-interaction` :
  ```php
  public function rules(): array
  {
      return [
          'photo' => ['required', 'image', 'mimes:jpeg,png,webp,heic', 'max:5120'],
      ];
  }
  ```
  Mêmes contraintes que `MobileUploadController::upload()` (feature 19), pour cohérence de règles à travers le projet — voir `../01-modele-donnees.md`.
- [ ] `ArticleController::uploadPhoto(UploadArticlePhotoRequest $request): JsonResponse` :
  ```php
  public function uploadPhoto(UploadArticlePhotoRequest $request): JsonResponse
  {
      $path = $request->file('photo')->store('articles', 'public');

      return response()->json(['image_url' => Storage::url($path)]);
  }
  ```
- [ ] Vérifier que le disque `public` est bien lié (`php artisan storage:link` si pas déjà fait en environnement de développement — ne pas l'exécuter en CI/tests, uniquement noter la nécessité pour le déploiement)
- [ ] Aucune association à un article à cette étape — l'upload retourne une URL indépendante, à intégrer ensuite dans le payload de `POST /api/inventory/articles` (ticket 06) ou de mise à jour d'un article existant

## Critères d'acceptation

- `POST /api/articles/upload-photo` avec un fichier image valide retourne `{ "image_url": "/storage/articles/xxxxx.jpg" }`, fichier réellement présent sur `storage/app/public/articles/`
- Un fichier non-image (ex. PDF) retourne une erreur de validation 422
- Un fichier dépassant 5120 Ko retourne une erreur de validation 422
- Aucune requête sans fichier ne doit planter (422 propre, pas 500)
