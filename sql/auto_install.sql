DROP TABLE IF EXISTS `civicrm_team`;
-- /*******************************************************
-- *
-- * civicrm_team
-- *
-- * Basic Team definition
-- *
-- *******************************************************/
CREATE TABLE `civicrm_team` (
     `id`         int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Team ID',
     `team_name`  varchar(255)                         COMMENT 'Human-redable team name.',
     `domain_id`  int unsigned NULL                    COMMENT 'FK to domain table',
     `created`    timestamp    NULL                    COMMENT 'Date on which the Team was created',
     `created_id` int unsigned                         COMMENT 'FK to contact table.',
     `is_active`  tinyint                              COMMENT 'Is this Team active?',
     `data`       text         NULL                    COMMENT 'Serialised JSON of additional configuration.',
     PRIMARY KEY (`id`),
     CONSTRAINT FK_civicrm_team_domain_id  FOREIGN KEY (`domain_id`)  REFERENCES `civicrm_domain`(`id`)  ON DELETE CASCADE,
     CONSTRAINT FK_civicrm_team_created_id FOREIGN KEY (`created_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE SET NULL  
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

DROP TABLE IF EXISTS `civicrm_team_contact`;
-- /*******************************************************
-- *
-- * civicrm_team_contact
-- *
-- * Contact Membership in a Team
-- *
-- *******************************************************/
CREATE TABLE `civicrm_team_contact` (
     `id`         int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Contact ID',
     `team_id`    int unsigned NOT NULL                COMMENT 'FK to civicrm_team',
     `contact_id` int unsigned NOT NULL                COMMENT 'FK to Contact',
     `date_added` timestamp    NULL                    COMMENT 'Date on which the Contact was added to the team',
     `status`     tinyint      NOT NULL                COMMENT 'Indicates if the contact is currently participating in the Team.',
     PRIMARY KEY (`id`) ,
     CONSTRAINT FK_civicrm_team_contact_team_id    FOREIGN KEY (`team_id`)    REFERENCES `civicrm_team`(`id`)    ON DELETE CASCADE,
     CONSTRAINT FK_civicrm_team_contact_contact_id FOREIGN KEY (`contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE CASCADE  
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
