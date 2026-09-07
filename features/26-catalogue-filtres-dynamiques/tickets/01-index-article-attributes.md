# Ticket 01 : Migration d'index sur `article_attributes.key`

**Type** : Backend / Migration
**Priorité** : Basse (performance, non bloquant)
**Estimation** : 15min

**Dépend de** : —

## Tâches

- [ ] `php artisan make:migration add_key_index_to_article_attributes_table --no-interaction`
- [ ] `up()` : `$table->index('key');` sur `article_attributes`
- [ ] `down()` : `$table->dropIndex(['key']);`
- [ ] Ne pas toucher à l'index unique existant `(article_id, key)`
- [ ] `php artisan migrate --no-interaction`

## Critères d'acceptation

- `SHOW INDEX FROM article_attributes` liste un index sur `key` seul, en plus de l'unique `(article_id, key)` déjà présent
- `php artisan migrate:rollback --step=1 --no-interaction` puis `php artisan migrate --no-interaction` ne lève aucune erreur
