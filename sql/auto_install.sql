DROP TABLE IF EXISTS `civicrm_team_contact`;
DROP TABLE IF EXISTS `civicrm_team`;

-- /*******************************************************
-- *
-- * civicrm_team
-- *
-- * Basic Team definition
-- *
-- *******************************************************/
CREATE TABLE `civicrm_team` (
     `id`         int unsigned NOT NULL AUTO_INCREMENT        COMMENT 'Unique Team ID',
     `team_name`  varchar(255) NOT NULL                       COMMENT 'Human-redable team name.',
     `domain_id`  int unsigned NULL                           COMMENT 'FK to domain table',
     `created`    timestamp    NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date on which the Team was created',
     `created_id` int unsigned                                COMMENT 'FK to contact table.',
     `is_active`  tinyint      NOT NULL DEFAULT 1             COMMENT 'Is this Team active?',
     `data`       text         NULL                           COMMENT 'Serialised JSON of additional configuration.',
     PRIMARY KEY (`id`),
     CONSTRAINT FK_civicrm_team_domain_id  FOREIGN KEY (`domain_id`)  REFERENCES `civicrm_domain`(`id`)  ON DELETE CASCADE,
     CONSTRAINT FK_civicrm_team_created_id FOREIGN KEY (`created_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE SET NULL  
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- /*******************************************************
-- *
-- * civicrm_team_contact
-- *
-- * Contact Membership in a Team
-- *
-- *******************************************************/
CREATE TABLE `civicrm_team_contact` (
     `id`         int unsigned NOT NULL AUTO_INCREMENT        COMMENT 'Unique Contact ID',
     `team_id`    int unsigned NOT NULL                       COMMENT 'FK to civicrm_team',
     `contact_id` int unsigned NOT NULL                       COMMENT 'FK to Contact',
     `date_added` timestamp    NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date on which the Contact was added to the team',
     `status`     tinyint      NOT NULL                       COMMENT 'Indicates if the contact is currently participating in the Team.',
     PRIMARY KEY (`id`),
     UNIQUE INDEX `UI_team_contact_id` (`team_id`, `contact_id`),
     CONSTRAINT FK_civicrm_team_contact_team_id    FOREIGN KEY (`team_id`)    REFERENCES `civicrm_team`(`id`)    ON DELETE CASCADE,
     CONSTRAINT FK_civicrm_team_contact_contact_id FOREIGN KEY (`contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE  
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- /*******************************************************
-- *
-- * civicrm_team_entity
-- *
-- * Entity Relationship with a Team
-- *
-- *******************************************************/
CREATE TABLE `civicrm_team_entity` (
     `id`            int unsigned NOT NULL AUTO_INCREMENT        COMMENT 'Unique TeamEntity ID',
     `team_id`       int unsigned NOT NULL                       COMMENT 'FK to civicrm_team',
     `entity_id`     int unsigned NOT NULL                       COMMENT 'FK to Entity',
     `entity_name`   varchar(255) NOT NULL                       COMMENT 'Entity name',
     `date_added`    timestamp    NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date on which the Entity was added to the team',
     `date_modified` timestamp    NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date on which record is updated',
     `isactive`      tinyint      NOT NULL DEFAULT 1             COMMENT 'Indicates if the entity is currently attached with the Team.',
     PRIMARY KEY (`id`),
     UNIQUE INDEX `UI_team_entity_id` (`team_id`, `entity_id`, `entity_name`),
     CONSTRAINT FK_civicrm_team_entity_team_id    FOREIGN KEY (`team_id`)    REFERENCES `civicrm_team`(`id`)    ON DELETE CASCADE
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;