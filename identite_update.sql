-- ============================================================
--  identite_update.sql
--  1) Renomme la catégorie "Identité" en "Identité, titres officiels et citoyenneté"
--  2) Ajoute les 5 nouveaux liens dans cette catégorie
-- ============================================================

-- 1) Renommage de la catégorie (id = 1)
UPDATE categories
SET nom = 'Identité, titres officiels et citoyenneté'
WHERE id = 1;

-- 2) Ajout des nouveaux liens (categorie_id = 1)
-- ordre = ordre max actuel + 1, +2, +3... (exécuter les INSERT dans l'ordre, un par un)

INSERT INTO liens (titre, url, description, logo, categorie_id, ordre, actif, mots_cles)
VALUES (
  'Permis de conduire',
  'https://permisdeconduire.ants.gouv.fr/',
  'Permis de conduire',
  NULL,
  1,
  (SELECT ordre FROM (SELECT COALESCE(MAX(ordre), 0) + 1 AS ordre FROM liens) t),
  1,
  'permis conduire points ants'
);

INSERT INTO liens (titre, url, description, logo, categorie_id, ordre, actif, mots_cles)
VALUES (
  'Certificat d''immatriculation / carte grise',
  'https://immatriculation.ants.gouv.fr/',
  'Certificat d''immatriculation / carte grise',
  NULL,
  1,
  (SELECT ordre FROM (SELECT COALESCE(MAX(ordre), 0) + 1 AS ordre FROM liens) t),
  1,
  'carte grise immatriculation vehicule ants'
);

INSERT INTO liens (titre, url, description, logo, categorie_id, ordre, actif, mots_cles)
VALUES (
  'Inscription sur les listes électorales',
  'https://www.service-public.fr/particuliers/vosdroits/R16396',
  'Inscription sur les listes électorales',
  NULL,
  1,
  (SELECT ordre FROM (SELECT COALESCE(MAX(ordre), 0) + 1 AS ordre FROM liens) t),
  1,
  'liste electorale vote elections inscription'
);

INSERT INTO liens (titre, url, description, logo, categorie_id, ordre, actif, mots_cles)
VALUES (
  'Demande d''acte d''état civil',
  'https://www.service-public.fr/particuliers/vosdroits/N359',
  'Demande d''acte d''état civil',
  NULL,
  1,
  (SELECT ordre FROM (SELECT COALESCE(MAX(ordre), 0) + 1 AS ordre FROM liens) t),
  1,
  'acte naissance mariage deces etat civil'
);

INSERT INTO liens (titre, url, description, logo, categorie_id, ordre, actif, mots_cles)
VALUES (
  'Recensement citoyen',
  'https://www.service-public.fr/particuliers/vosdroits/F870',
  'Recensement citoyen',
  NULL,
  1,
  (SELECT ordre FROM (SELECT COALESCE(MAX(ordre), 0) + 1 AS ordre FROM liens) t),
  1,
  'recensement citoyen jdc journee defense'
);
