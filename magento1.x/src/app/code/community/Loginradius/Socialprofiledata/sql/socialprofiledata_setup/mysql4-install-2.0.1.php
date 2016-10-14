<?php
$this->startSetup();
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_basic_profile_data')} (
  `user_id` int(11) NOT NULL,
  `loginradius_id` varchar(1000) NOT NULL,
  `provider` varchar(20) DEFAULT NULL,
  `prefix` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `suffix` varchar(100) DEFAULT NULL,
  `full_name` varchar(200) DEFAULT NULL,
  `nick_name` varchar(200) DEFAULT NULL,
  `profile_name` varchar(100) DEFAULT NULL,
  `birth_date` varchar(20) DEFAULT NULL,
  `gender` varchar(6) DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `country_name` varchar(100) DEFAULT NULL,
  `thumbnail_image_url` varchar(1000) DEFAULT NULL,
  `image_url` varchar(1000) DEFAULT NULL,
  `local_country` varchar(50) DEFAULT NULL,
  `profile_country` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_emails')} (
  `user_id` int(11) NOT NULL,
  `email_type` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_contacts')} (
  `user_id` int(11) NOT NULL,
  `provider` varchar(20) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `social_id` varchar(255) DEFAULT NULL,
  `profile_url` varchar(1000) DEFAULT NULL,
  `image_url` varchar(1000) DEFAULT NULL,
  `status` text DEFAULT NULL,
  `industry` varchar(50) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_extended_location_data')} (
  `user_id` int(11) NOT NULL,
  `main_address` varchar(500) DEFAULT NULL,
  `hometown` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `local_city` varchar(50) DEFAULT NULL,
  `profile_city` varchar(50) DEFAULT NULL,
  `profile_url` varchar(1000) DEFAULT NULL,
  `local_language` varchar(10) DEFAULT NULL,
  `language` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_extended_profile_data')} (
    `user_id` int(11) NOT NULL,
    `website` varchar(50) DEFAULT NULL,
    `favicon` varchar(1000) DEFAULT NULL,
    `industry` varchar(200) DEFAULT NULL,
    `about` text DEFAULT NULL,
    `timezone` varchar(100) DEFAULT NULL,
    `verified` varchar(10) DEFAULT NULL,
    `last_profile_update` varchar(100) DEFAULT NULL,
    `created` varchar(100) DEFAULT NULL,
    `relationship_status` varchar(20) DEFAULT NULL,
    `quote` varchar(1000) DEFAULT NULL,
    `interested_in` varchar(10) DEFAULT NULL,
    `interests` varchar(100) DEFAULT NULL,
    `religion` varchar(20) DEFAULT NULL,
    `political_view` varchar(100) DEFAULT NULL,
    `https_image_url` varchar(500) DEFAULT NULL,
    `followers_count` varchar(11) DEFAULT NULL,
    `friends_count` varchar(11) DEFAULT NULL,
    `is_geo_enabled` varchar(1) DEFAULT NULL,
    `total_status_count` varchar(11) DEFAULT NULL,
    `number_of_recommenders` varchar(11) DEFAULT NULL,
    `honors` varchar(1000) DEFAULT NULL,
    `associations` varchar(1000) DEFAULT NULL,
    `hirable` varchar(1) DEFAULT NULL,
    `repository_url` varchar(1000) DEFAULT NULL,
    `age` varchar(3) DEFAULT NULL,
    `professional_headline` varchar(1000) DEFAULT NULL,
    `provider_access_token` varchar(100) DEFAULT NULL,
    `provider_token_secret` varchar(100) DEFAULT NULL,
    PRIMARY KEY (`user_id`)
);");

$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_positions')} (
    `user_id` int(11) NOT NULL,
    `position` varchar(100) DEFAULT NULL,
    `summary` varchar(100) DEFAULT NULL,
    `start_date` varchar(50) DEFAULT NULL,
    `end_date` varchar(50) DEFAULT NULL,
    `is_current` varchar(1) DEFAULT NULL,
    `company` varchar(11) DEFAULT NULL,
    `location` varchar(255) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_companies')} (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `company_name` varchar(100) DEFAULT NULL,
    `company_type` varchar(50) DEFAULT NULL,
    `industry` varchar(100) DEFAULT NULL,
    PRIMARY KEY (`id`)
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_education')} (
    `user_id` int(11) NOT NULL,
    `school` varchar(100) DEFAULT NULL,
    `year` varchar(50) DEFAULT NULL,
    `type` varchar(50) DEFAULT NULL,
    `notes` varchar(100) DEFAULT NULL,
    `activities` varchar(100) DEFAULT NULL,
    `degree` varchar(100) DEFAULT NULL,
    `field_of_study` varchar(100) DEFAULT NULL,
    `start_date` varchar(50) DEFAULT NULL,
    `end_date` varchar(50) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_phone_numbers')} (
    `user_id` int(11) NOT NULL,
    `number_type` varchar(20) DEFAULT NULL,
    `phone_number` varchar(20) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_IMaccounts')} (
    `user_id` int(11) NOT NULL,
    `account_type` varchar(20) DEFAULT NULL,
    `account_username` varchar(100) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_addresses')} (
    `user_id` int(11) NOT NULL,
    `type` varchar(20) DEFAULT NULL,
    `address_line1` varchar(100) DEFAULT NULL,
    `address_line2` varchar(100) DEFAULT NULL,
    `city` varchar(100) DEFAULT NULL,
    `state` varchar(100) DEFAULT NULL,
    `postal_code` varchar(20) DEFAULT NULL,
    `region` varchar(100) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_sports')} (
    `user_id` int(11) NOT NULL,
    `sport_id` varchar(20) DEFAULT NULL,
    `sport` varchar(50) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_inspirational_people')} (
    `user_id` int(11) NOT NULL,
    `social_id` varchar(20) DEFAULT NULL,
    `name` varchar(50) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_skills')} (
    `user_id` int(11) NOT NULL,
    `skill_id` varchar(20) DEFAULT NULL,
    `name` varchar(50) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_current_status')} (
    `user_id` int(11) NOT NULL,
    `status_id` varchar(30) DEFAULT NULL,
    `status` text DEFAULT NULL,
    `source` varchar(50) DEFAULT NULL,
    `created_date` varchar(50) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_certifications')} (
    `user_id` int(11) NOT NULL,
    `certification_id` varchar(30) DEFAULT NULL,
    `certification_name` varchar(50) DEFAULT NULL,
    `authority` varchar(50) DEFAULT NULL,
    `license_number` varchar(50) DEFAULT NULL,
    `start_date` varchar(50) DEFAULT NULL,
    `end_date` varchar(50) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_courses')} (
    `user_id` int(11) NOT NULL,
    `course_id` varchar(30) DEFAULT NULL,
    `course` varchar(100) DEFAULT NULL,
    `course_number` varchar(50) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_volunteer')} (
    `user_id` int(11) NOT NULL,
    `volunteer_id` varchar(30) DEFAULT NULL,
    `role` varchar(50) DEFAULT NULL,
    `organization` varchar(50) DEFAULT NULL,
    `cause` varchar(100) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_recommendations_received')} (
    `user_id` int(11) NOT NULL,
    `recommendation_id` varchar(30) DEFAULT NULL,
    `recommendation_type` varchar(100) DEFAULT NULL,
    `recommendation_text` text DEFAULT NULL,
    `recommender` varchar(50) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_languages')} (
    `user_id` int(11) NOT NULL,
    `language_id` varchar(30) DEFAULT NULL,
    `language` varchar(30) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_patents')} (
    `user_id` int(11) NOT NULL,
    `patent_id` varchar(30) DEFAULT NULL,
    `title` varchar(100) DEFAULT NULL,
    `date` varchar(30) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_favorites')} (
    `user_id` int(11) NOT NULL,
    `social_id` varchar(30) DEFAULT NULL,
    `name` varchar(100) DEFAULT NULL,
    `type` varchar(50) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_facebook_events')} (
  `user_id` int(11) NOT NULL,
  `event_id` varchar(30) NOT NULL,
  `event` varchar(500) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `rsvp_status` varchar(50) DEFAULT NULL,
  `location` varchar(50) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_facebook_posts')} (
  `user_id` int(11) NOT NULL,
  `post_id` varchar(50) NOT NULL,
  `from_name` varchar(100) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `start_time` varchar(100) DEFAULT NULL,
  `update_time` varchar(100) DEFAULT NULL,
  `message` text,
  `place` varchar(50) DEFAULT NULL,
  `picture` varchar(1000) DEFAULT NULL,
  `likes` varchar(11) DEFAULT NULL,
  `shares` varchar(11) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_groups')} (
  `user_id` int(11) NOT NULL,
  `provider` varchar(20) NOT NULL,
  `group_id` varchar(30) NOT NULL,
  `group_name` varchar(100) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_linkedin_companies')} (
  `user_id` int(11) NOT NULL,
  `company_id` varchar(20) NOT NULL,
  `company_name` varchar(200) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_status')} (
  `user_id` int(11) NOT NULL,
  `provider` varchar(20) NOT NULL,
  `status_id` varchar(20) NOT NULL,
  `status` text,
  `date_time` varchar(100) DEFAULT NULL,
  `likes` varchar(11) DEFAULT NULL,
  `place` varchar(50) DEFAULT NULL,
  `source` varchar(500) DEFAULT NULL,
  `image_url` varchar(1000) DEFAULT NULL,
  `link_url` varchar(1000) DEFAULT NULL
);");
$this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('lr_twitter_mentions')} (
  `user_id` int(11) NOT NULL,
  `mention_id` varchar(30) NOT NULL,
  `tweet` text DEFAULT NULL,
  `date_time` varchar(30) DEFAULT NULL,
  `likes` varchar(11) DEFAULT NULL,
  `place` varchar(50) DEFAULT NULL,
  `source` varchar(50) DEFAULT NULL,
  `image_url` varchar(1000) DEFAULT NULL,
  `link_url` varchar(1000) DEFAULT NULL,
  `mentioned_by` varchar(100) DEFAULT NULL
);");
$this->endSetup();
