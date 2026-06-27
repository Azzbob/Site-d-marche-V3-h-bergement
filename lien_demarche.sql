-- ============================================================
--  liens_demarches — Fichier SQL consolidé pour InfinityFree
--  (Sans CREATE DATABASE / USE — la base existe déjà)
-- ============================================================

-- ------------------------------------------------------------
-- TABLE : utilisateurs (avec colonnes OAuth)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom`           VARCHAR(100)     NOT NULL,
  `prenom`        VARCHAR(100)     NOT NULL,
  `email`         VARCHAR(255)     NOT NULL UNIQUE,
  `mot_de_passe`  VARCHAR(255)     NOT NULL DEFAULT '',
  `google_id`     VARCHAR(255)     NULL DEFAULT NULL,
  `facebook_id`   VARCHAR(255)     NULL DEFAULT NULL,
  `apple_id`      VARCHAR(255)     NULL DEFAULT NULL,
  `created_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_google_id`   (`google_id`),
  UNIQUE KEY `uniq_facebook_id` (`facebook_id`),
  UNIQUE KEY `uniq_apple_id`    (`apple_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE : categories
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id`    INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom`   VARCHAR(100)     NOT NULL,
  `icone` VARCHAR(255)         NULL,
  `ordre` INT(11)          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE : liens (avec mots_cles)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `liens` (
  `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `categorie_id`  INT(11) UNSIGNED     NULL,
  `titre`         VARCHAR(255)     NOT NULL,
  `url`           VARCHAR(500)     NOT NULL,
  `description`   TEXT                 NULL,
  `logo`          VARCHAR(255)         NULL,
  `mots_cles`     TEXT                 NULL,
  `mis_en_avant`  TINYINT(1)       NOT NULL DEFAULT 0,
  `ordre`         INT(11)          NOT NULL DEFAULT 0,
  `actif`         TINYINT(1)       NOT NULL DEFAULT 1,
  `created_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_liens_titre_url` (`titre`, `url`),
  KEY `fk_categorie` (`categorie_id`),
  CONSTRAINT `fk_lien_categorie`
    FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE : favoris
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `favoris` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT(11) UNSIGNED NOT NULL,
  `lien_id`    INT(11) UNSIGNED NOT NULL,
  `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favori` (`user_id`, `lien_id`),
  KEY `fk_favori_user` (`user_id`),
  KEY `fk_favori_lien` (`lien_id`),
  CONSTRAINT `fk_favori_user` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_favori_lien` FOREIGN KEY (`lien_id`) REFERENCES `liens` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE : password_resets
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT(11) UNSIGNED NOT NULL,
  `token_hash` VARCHAR(255)     NOT NULL,
  `expires_at` DATETIME         NOT NULL,
  `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_password_reset_user` (`user_id`),
  CONSTRAINT `fk_password_reset_utilisateur`
    FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE : remember_tokens
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT(11) UNSIGNED NOT NULL,
  `token_hash`  VARCHAR(255)     NOT NULL,
  `expires_at`  DATETIME         NOT NULL,
  `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_token_hash` (`token_hash`),
  CONSTRAINT `fk_remember_user`
    FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE : login_attempts
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`        VARCHAR(255)     NOT NULL,
  `ip_address`   VARCHAR(45)      NOT NULL,
  `attempted_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email_time` (`email`, `attempted_at`),
  KEY `idx_ip_time` (`ip_address`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  CATÉGORIES
-- ============================================================
INSERT INTO `categories` (`nom`, `icone`, `ordre`) VALUES
('Identité',          'img/logos/identite.png',          1),
('Social & Santé',    'img/logos/social_sante.png',       2),
('Travail & Retraite','img/logos/travail_retraite.png',   3),
('Logement',          'img/logos/logement.png',           4),
('Finances',          'img/logos/finances.png',           5),
('Droits & Services', 'img/logos/droits_services.png',    6);

-- ============================================================
--  LIENS  (avec mots_cles détaillés)
-- ============================================================

-- Identité
INSERT INTO `liens` (`categorie_id`, `titre`, `url`, `description`, `logo`, `mots_cles`, `mis_en_avant`, `ordre`) VALUES
(1, 'Carte d\'Identité',
   'https://www.service-public.gouv.fr/particuliers/vosdroits/N358',
   'CNI-Passeport - Accueil - Passeport - France Titres (ANTS).',
   'img/logos/france_titres.png',
   'carte identite CNI document officiel ANTS france titres passeport renouvellement demande identite etat civil justificatif',
   0, 1),

(1, 'Passeport',
   'https://www.service-public.gouv.fr/particuliers/vosdroits/N360',
   'Toutes les démarches pour obtenir ou renouveler votre passeport.',
   'img/logos/france_titres.png',
   'passeport renouvellement obtenir voyage international ANTS france titres document biometrique demande identite',
   0, 2),

(1, 'Changement de prénom',
   'https://www.service-public.gouv.fr/particuliers/vosdroits/F885',
   'Démarches pour changer de prénom à l\'état civil.',
   'img/logos/france_titres.png',
   'changement prenom etat civil mairie modification identite acte naissance registre civil demarche administrative',
   0, 3),

(1, 'Déclaration de naissance',
   'https://www.service-public.gouv.fr/particuliers/vosdroits/F961',
   'Déclarez la naissance de votre enfant en ligne.',
   'img/logos/france_titres.png',
   'declaration naissance bebe enfant naitre mairie acte naissance etat civil nouveau-ne neonatal demarche',
   0, 4);

-- Social & Santé
INSERT INTO `liens` (`categorie_id`, `titre`, `url`, `description`, `logo`, `mots_cles`, `mis_en_avant`, `ordre`) VALUES
(2, 'Complémentaire Santé Solidaire',
   'https://www.service-public.gouv.fr/particuliers/vosdroits/F10027',
   'Complémentaire santé solidaire (ex-CMU-C).',
   'img/logos/france_titres.png',
   'complementaire sante solidaire CSS CMU couverture maladie universelle mutuelle gratuite remboursement soins aide sante precaire',
   0, 1),

(2, 'Aide au Handicap',
   'https://www.service-public.gouv.fr/particuliers/vosdroits/N12230',
   'Allocations (AAH, AEEH, ...) et aides pour les personnes handicapées.',
   'img/logos/france_titres.png',
   'aide handicap AAH AEEH allocation adulte handicape enfant invalidite MDPH reconnaissance incapacite prestation compensation',
   0, 2),

(2, 'Compte Assurance Maladie',
   'https://www.ameli.fr/',
   'Remboursements, carte vitale, médecin traitant et arrêts maladie.',
   'img/logos/ameli.png',
   'ameli assurance maladie remboursement carte vitale medecin traitant arret maladie secu securite sociale cpam indemnites journalieres',
   0, 3),

(2, 'Allocation aux adultes handicapés (AAH)',
   'https://www.service-public.gouv.fr/particuliers/vosdroits/F12242',
   'Demandez ou gérez votre allocation AAH.',
   'img/logos/france_titres.png',
   'AAH allocation adulte handicape demande gestion handicap MDPH revenu invalidite aide financiere prestation sociale',
   0, 4);

-- Travail & Retraite
INSERT INTO `liens` (`categorie_id`, `titre`, `url`, `description`, `logo`, `mots_cles`, `mis_en_avant`, `ordre`) VALUES
(3, 'Trouver une alternance',
   'https://labonnealternance.apprentissage.beta.gouv.fr/',
   'La bonne alternance : trouvez votre alternance, formation et emploi.',
   'img/logos/alternance.png',
   'alternance apprentissage contrat formation emploi bonne alternance jeune entreprise CFA offre stage professionnel',
   0, 1),

(3, 'Trouver un travail',
   'https://www.francetravail.fr/accueil/',
   'Inscription, actualisation, offres d\'emploi et allocations chômage.',
   'img/logos/france_travail.png',
   'france travail pole emploi chomage inscription actualisation offre emploi ARE allocation retour emploi recherche job recrutement',
   1, 2),

(3, 'Compte Assurance Retraite',
   'https://www.info-retraite.fr/portail-services/login',
   'Consultez vos droits à la retraite et effectuez vos démarches.',
   'img/logos/retraite.png',
   'retraite assurance retraite droits pension trimestres carriere cnav liquidation depart simulation releve points',
   0, 3),

(3, 'Mon Compte Formation',
   'https://www.moncompteformation.gouv.fr/espace-prive/html/#/',
   'Gérez votre compte personnel de formation (CPF).',
   'img/logos/cpf.png',
   'CPF compte personnel formation formation professionnelle financement bilan competences reconversion credits euros apprentissage',
   0, 4),

(3, 'Urssaf',
   'https://www.urssaf.fr/accueil.html',
   'Déclarez et payez vos cotisations sociales.',
   'img/logos/urssaf.png',
   'urssaf cotisations sociales autoentrepreneur micro-entrepreneur independant declaration paiement charges patronales salariales CESU',
   1, 5);

-- Logement
INSERT INTO `liens` (`categorie_id`, `titre`, `url`, `description`, `logo`, `mots_cles`, `mis_en_avant`, `ordre`) VALUES
(4, 'Demande d\'aide au logement',
   'https://www.caf.fr/',
   'Page d\'accueil allocataires CAF – Caisse d\'Allocations Familiales.',
   'img/logos/caf.png',
   'CAF aide logement APL ALS ALF allocation logement caisse allocations familiales locataire loyer subvention',
   0, 1),

(4, 'Demande de logement social',
   'https://www.demande-logement-social.gouv.fr/',
   'Faites votre demande de logement social en ligne.',
   'img/logos/logement_social.png',
   'logement social HLM demande numero unique dossier hlm logement abordable location parc social bailleur attributions',
   0, 2),

(4, 'Changement d\'adresse',
   'https://www.service-public.gouv.fr/particuliers/vosdroits/R11193',
   'Déclarez votre changement d\'adresse en ligne.',
   'img/logos/service_public.png',
   'changement adresse demenagement signalement organismes impots secu caf poste transfert courrier nouveau domicile',
   0, 3),

(4, 'Signalement d\'un logement indigne',
   'https://signal-logement.beta.gouv.fr/',
   'Signalez un logement insalubre ou indécent.',
   'img/logos/signal_logement.png',
   'logement indigne insalubre indecent signalement taudis moisissures danger locataire proprietaire bailleur mauvaises conditions habitat',
   0, 4);

-- Finances
INSERT INTO `liens` (`categorie_id`, `titre`, `url`, `description`, `logo`, `mots_cles`, `mis_en_avant`, `ordre`) VALUES
(5, 'Déclaration de revenus',
   'https://www.impots.gouv.fr/accueil',
   'Déclarez vos revenus et consultez votre espace fiscal personnel.',
   'img/logos/impots.png',
   'impots declaration revenus fisc fiscal espace personnel impot sur le revenu IR remboursement trop percu avis imposition',
   1, 1),

(5, 'Demande de prime d\'activité',
   'https://www.caf.fr/',
   'Demandez votre prime d\'activité via la CAF.',
   'img/logos/caf.png',
   'prime activite CAF travailleur salarie modeste complement revenus aide financiere trimestre declaration ressources',
   0, 2),

(5, 'Simuler ses aides sociales',
   'https://beta.gouv.fr/startups/mes-aides.html',
   'Mes Aides — simulez toutes vos aides sociales.',
   'img/logos/beta_gouv.png',
   'simulation aides sociales mes aides RSA prime activite APL aide logement allocations droits prestations eligibilite',
   0, 3),

(5, 'Chèque énergie',
   'https://chequeenergie.gouv.fr/',
   'Accédez à votre chèque énergie pour payer vos factures.',
   'img/logos/cheque_energie.png',
   'cheque energie facture electricite gaz aide chauffage precarite energetique fournisseur paiement reduction',
   0, 4);

-- Droits & Services
INSERT INTO `liens` (`categorie_id`, `titre`, `url`, `description`, `logo`, `mots_cles`, `mis_en_avant`, `ordre`) VALUES
(6, 'Demande d\'extrait de casier judiciaire',
   'https://casier-judiciaire.justice.gouv.fr/',
   'Demandez votre extrait de casier judiciaire (bulletin n°3).',
   'img/logos/justice.png',
   'casier judiciaire extrait bulletin B3 justice demande emploi casier vierge antecedents judiciaires',
   0, 1),

(6, 'Signaler une fraude ou une arnaque',
   'https://signal.conso.gouv.fr/fr',
   'SignalConso, un service public pour les consommateurs.',
   'img/logos/signal_conso.png',
   'signalement fraude arnaque escroquerie signal conso consommateur litige plainte protection droits signalconso DGCCRF',
   0, 2),

(6, 'Saisir le Défenseur des droits',
   'https://www.defenseurdesdroits.fr/',
   'Contactez le Défenseur des droits pour faire valoir vos droits.',
   'img/logos/defenseur_droits.png',
   'defenseur droits reclamation discrimination litige service public plainte saisine recours mediation institution',
   0, 3),

(6, 'Accès à toutes les démarches administratives',
   'https://www.service-public.gouv.fr/',
   'Le portail officiel de l\'administration française.',
   'img/logos/service_public.png',
   'service public administration francaise demarches officielles portail gouvernement formulaires procedures etat',
   0, 4);