CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_category_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `content` text,
  `active` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `comments` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

CREATE TABLE `contents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `text` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

CREATE TABLE `contest_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `group_organizer` tinyint(1) DEFAULT NULL,
  `large_group` tinyint(1) DEFAULT NULL,
  `comments` text,
  `agree_to_terms` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `signuptype` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

CREATE TABLE `group_outings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email_type` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_type` varchar(255) DEFAULT NULL,
  `area_code` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `address_type` varchar(255) DEFAULT NULL,
  `street_1` varchar(255) DEFAULT NULL,
  `street_2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `country_address` varchar(255) DEFAULT NULL,
  `event_length` varchar(255) DEFAULT NULL,
  `number_of_attendees` varchar(255) DEFAULT NULL,
  `held_event_before` tinyint(1) DEFAULT NULL,
  `decision_maker` tinyint(1) DEFAULT NULL,
  `contact_method` varchar(255) DEFAULT NULL,
  `referred_by` varchar(255) DEFAULT NULL,
  `comments` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

CREATE TABLE `holes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(11) DEFAULT NULL,
  `par` int(11) DEFAULT NULL,
  `tournament_tees` int(11) DEFAULT NULL,
  `palmer_tees` int(11) DEFAULT NULL,
  `white_tees` int(11) DEFAULT NULL,
  `forward_tees` int(11) DEFAULT NULL,
  `handicap_mens` int(11) DEFAULT NULL,
  `handicap_womens` int(11) DEFAULT NULL,
  `description` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `image_file_name` varchar(255) DEFAULT NULL,
  `image_content_type` varchar(255) DEFAULT NULL,
  `image_file_size` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

CREATE TABLE `lesson_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `province_state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `lesson` tinyint(1) DEFAULT NULL,
  `preferred_day` varchar(255) DEFAULT NULL,
  `preferred_time` varchar(255) DEFAULT NULL,
  `clinic_number` varchar(255) DEFAULT NULL,
  `golf_school_number` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

CREATE TABLE `passholders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(40) DEFAULT NULL,
  `name` varchar(100) DEFAULT '',
  `email` varchar(100) DEFAULT NULL,
  `crypted_password` varchar(40) DEFAULT NULL,
  `salt` varchar(40) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `remember_token` varchar(40) DEFAULT NULL,
  `remember_token_expires_at` datetime DEFAULT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `activated_at` datetime DEFAULT NULL,
  `state` varchar(255) DEFAULT 'passive',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_passholders_on_login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `schema_migrations` (
  `version` varchar(255) NOT NULL,
  UNIQUE KEY `unique_schema_migrations` (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sign_ups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `signuptype` varchar(255) DEFAULT NULL,
  `handicap` varchar(255) DEFAULT NULL,
  `timeperyear` varchar(255) DEFAULT NULL,
  `company_event` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `promotions` text,
  `income` varchar(255) DEFAULT NULL,
  `other_courses` text,
  `number_of_lessons` varchar(255) DEFAULT NULL,
  `times_per_month` varchar(255) DEFAULT NULL,
  `how_did_you_hear` varchar(255) DEFAULT NULL,
  `website_rating` varchar(255) DEFAULT NULL,
  `influencing_factor` varchar(255) DEFAULT NULL,
  `influencing_factor_2` varchar(255) DEFAULT NULL,
  `influencing_factor_3` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(40) DEFAULT NULL,
  `name` varchar(100) DEFAULT '',
  `email` varchar(100) DEFAULT NULL,
  `crypted_password` varchar(40) DEFAULT NULL,
  `salt` varchar(40) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `remember_token` varchar(40) DEFAULT NULL,
  `remember_token_expires_at` datetime DEFAULT NULL,
  `encrypted_password` varchar(128) DEFAULT NULL,
  `token` varchar(128) DEFAULT NULL,
  `token_expires_at` datetime DEFAULT NULL,
  `email_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_users_on_login` (`login`),
  KEY `index_users_on_id_and_token` (`id`,`token`),
  KEY `index_users_on_email` (`email`),
  KEY `index_users_on_token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

CREATE TABLE `wedding_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `email_type` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address_type` varchar(255) DEFAULT NULL,
  `street_1` varchar(255) DEFAULT NULL,
  `street_2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `phone_type` varchar(255) DEFAULT NULL,
  `area_code` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `country_phone` varchar(255) DEFAULT NULL,
  `requested_date` date DEFAULT NULL,
  `alternate_date` date DEFAULT NULL,
  `contact_time` varchar(255) DEFAULT NULL,
  `comments` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

INSERT INTO schema_migrations (version) VALUES ('20090118041549');

INSERT INTO schema_migrations (version) VALUES ('20090118042950');

INSERT INTO schema_migrations (version) VALUES ('20090118043819');

INSERT INTO schema_migrations (version) VALUES ('20090118050122');

INSERT INTO schema_migrations (version) VALUES ('20090118054805');

INSERT INTO schema_migrations (version) VALUES ('20090118061118');

INSERT INTO schema_migrations (version) VALUES ('20090118061142');

INSERT INTO schema_migrations (version) VALUES ('20090118171223');

INSERT INTO schema_migrations (version) VALUES ('20090118172705');

INSERT INTO schema_migrations (version) VALUES ('20090126063932');

INSERT INTO schema_migrations (version) VALUES ('20090208015750');

INSERT INTO schema_migrations (version) VALUES ('20090208045036');

INSERT INTO schema_migrations (version) VALUES ('20090217042631');

INSERT INTO schema_migrations (version) VALUES ('20090221091339');

INSERT INTO schema_migrations (version) VALUES ('20090228205421');

INSERT INTO schema_migrations (version) VALUES ('20090301065611');

INSERT INTO schema_migrations (version) VALUES ('20090301201109');

INSERT INTO schema_migrations (version) VALUES ('20090301202034');

INSERT INTO schema_migrations (version) VALUES ('20090301202559');

INSERT INTO schema_migrations (version) VALUES ('20090302044413');

INSERT INTO schema_migrations (version) VALUES ('20090302065239');

INSERT INTO schema_migrations (version) VALUES ('20090307183133');

INSERT INTO schema_migrations (version) VALUES ('20090308185710');

INSERT INTO schema_migrations (version) VALUES ('20090326033411');

INSERT INTO schema_migrations (version) VALUES ('20090328005537');

INSERT INTO schema_migrations (version) VALUES ('20090329000441');

INSERT INTO schema_migrations (version) VALUES ('20090329004747');

INSERT INTO schema_migrations (version) VALUES ('20090329012326');

INSERT INTO schema_migrations (version) VALUES ('20090330010027');

INSERT INTO schema_migrations (version) VALUES ('20090405170752');

INSERT INTO schema_migrations (version) VALUES ('20090412015026');

INSERT INTO schema_migrations (version) VALUES ('20090429011033');