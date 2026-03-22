# Hotfix — Sélection non prise en compte en édition

## Symptôme
- Lorsqu’on édite une réservation existante, on peut décocher un vélo et en sélectionner un autre dans la grille.  
- La section “Vélos sélectionnés” du formulaire se met bien à jour (car elle lit `selectors.selectedBikes`).  
- Après avoir validé, la réservation conserve pourtant les anciens vélos : l’API reçoit les mêmes `items`/`selection` qu’avant modification.

## Cause
- Le formulaire stocke l’état soumis dans `formData.items` et `formData.selection`.  
- Ces champs sont synchronisés avec la sélection du calendrier via :
  ```ts
  useEffect(() => {
      if (!draft.isActive || draft.editingReservationId) return;
      setFormData((prev) => ({
          ...prev,
          items: selectors.items.length > 0 ? selectors.items : prev.items,
          selection: selectors.selectedBikes,
      }));
  }, [draft.isActive, draft.editingReservationId, selectors.items, selectors.selectedBikes]);
  ```
- En mode édition, `draft.editingReservationId` est défini, donc l’effet s’arrête (`return`) et `formData` n’est plus mis à jour.  
- L’interface continue d’afficher `selectors.selectedBikes`, mais le payload soumis reste celui chargé initialement (`editingReservation`).

## Risques
- L’utilisateur pense avoir remplacé un vélo mais la réservation persiste avec l’ancien.  
- Peut entraîner des conflits de planning ou de logistique (vélo préparé pour le mauvais client).

## Piste de correction
- Réautoriser la synchronisation même quand `editingReservationId` est renseigné (ou fournir un bouton “Appliquer la sélection”).  
- Veiller à ce que `formData.items` / `selection` reflètent toujours l’état du draft avant soumission.
