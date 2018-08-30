<?php

namespace LoginRadius\SocialProfileData\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();
        $this->createTables($setup);
        $setup->endSetup();
    }

    /**
     * 
     * @param type $tableName
     * @return type
     */
    private function getTablesColamns($tableName) {
        $colamns = array(
            'basic_profile_data' => array('id', 'entity_id', 'sociallogin_id', 'provider', 'first_name', 'middle_name', 'last_name', 'full_name', 'nick_name', 'profile_name', 'birth_date', 'gender', 'prefix', 'suffix', 'country_code', 'country_name', 'thumbnail_image_url', 'image_url', 'local_country', 'profile_country'),
            'emails' => array('id', 'entity_id', 'email_type', 'email'),
            'sports' => array('id', 'entity_id', 'sport_id', 'sport'),
            'addresses' => array('id', 'entity_id', 'type', 'address_line1', 'address_line2', 'city', 'state', 'postal_code', 'region', 'country'),
            'contacts' => array('id', 'entity_id', 'provider', 'name', 'email', 'phone_number', 'social_id', 'profile_url', 'image_url', 'status', 'industry', 'country', 'gender'),
            'extended_location_data' => array('id', 'entity_id', 'main_address', 'hometown', 'state', 'city', 'local_city', 'profile_city', 'profile_url', 'local_language', 'language'),
            'extended_profile_data' => array('id', 'entity_id', 'website', 'favicon', 'industry', 'about', 'timezone', 'verified', 'last_profile_update', 'created', 'relationship_status', 'quote', 'interested_in', 'interests', 'religion', 'political_view', 'https_image_url', 'followers_count', 'friends_count', 'is_geo_enabled', 'total_status_count', 'number_of_recommenders', 'honors', 'associations', 'hirable', 'repository_url', 'age', 'professional_headline', 'provider_access_token', 'provider_token_secret', 'no_of_login'),
            'positions' => array('id', 'entity_id', 'position', 'summary', 'start_date', 'end_date', 'is_current', 'company', 'location'),
            'companies' => array('id', 'entity_id', 'company_name', 'company_type', 'industry'),
            'education' => array('id', 'entity_id', 'school', 'year', 'type', 'notes', 'activities', 'degree', 'field_of_study', 'start_date', 'end_date'),
            'phone_numbers' => array('id', 'entity_id', 'number_type', 'phone_number'),
            'imaccounts' => array('id', 'entity_id', 'account_type', 'account_username'),
            'twitter_mentions' => array('id', 'entity_id', 'mention_id', 'tweet', 'date_time', 'likes', 'place', 'source', 'image_url', 'link_url', 'mentioned_by'),
            'status' => array('id', 'entity_id', 'provider', 'status_id', 'status', 'date_time', 'likes', 'place', 'source', 'image_url', 'link_url'),
            'linkedin_companies' => array('id', 'entity_id', 'company_id', 'company_name'),
            'groups' => array('id', 'entity_id', 'provider', 'group_id', 'group_name'),
            'facebook_posts' => array('id', 'entity_id', 'post_id', 'from_name', 'title', 'start_time', 'update_time', 'message', 'place', 'picture', 'likes', 'shares'),
            'facebook_events' => array('id', 'entity_id', 'event_id', 'event', 'start_time', 'rsvp_status', 'location'),
            'favorites' => array('id', 'entity_id', 'social_id', 'name', 'type'),
            'books' => array('id', 'entity_id', 'book_id', 'category', 'name', 'created_date'),
            'patents' => array('id', 'entity_id', 'patent_id', 'title', 'date'),
            'languages' => array('id', 'entity_id', 'language_id', 'language'),
            'recommendations_received' => array('id', 'entity_id', 'recommendation_id', 'recommendation_type', 'recommendation_text', 'recommender'),
            'volunteer' => array('id', 'entity_id', 'volunteer_id', 'role', 'organization', 'cause'),
            'courses' => array('id', 'entity_id', 'course_id', 'course', 'course_number'),
            'certifications' => array('id', 'entity_id', 'certification_id', 'certification_name', 'authority', 'license_number', 'start_date', 'end_date'),
            'current_status' => array('id', 'entity_id', 'status_id', 'status', 'source', 'created_date'),
            'skills' => array('id', 'entity_id', 'skill_id', 'name'),
            'inspirational_people' => array('id', 'entity_id', 'social_id', 'name'),
            'facebook_likes' => array('id', 'entity_id','likes_id','name','category','created_date','website','description')
            
            
        );
        return isset($colamns[$tableName]) ? $colamns[$tableName] : null;
    }

    /**
     * 
     * @param type $name
     * @return type
     */
    private function getColamnArray($name) {
        $colamn = array(
            'id' => array('id', Table::TYPE_INTEGER, null, array('nullable' => false, 'primary' => true, 'identity'=>true), 'Auto Increment Id'),
            'entity_id' => array('entity_id', Table::TYPE_INTEGER, null, array('nullable' => false, 'primary' => false), 'Customer Id'),
            'sociallogin_id' => array('sociallogin_id', Table::TYPE_TEXT, 1000, array('nullable' => false, 'primary' => false), 'Social Provider Id'),
            'provider' => array('provider', Table::TYPE_TEXT, 20, array('nullable' => true, 'primary' => false), 'Provider Name'),
            'prefix' => array('prefix', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Prefix'),
            'first_name' => array('first_name', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'First Name'),
            'middle_name' => array('middle_name', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Middle Name'),
            'last_name' => array('last_name', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Last Name'),
            'suffix' => array('suffix', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Suffix'),
            'full_name' => array('full_name', Table::TYPE_TEXT, 200, array('nullable' => true, 'primary' => false), 'Full Name'),
            'nick_name' => array('nick_name', Table::TYPE_TEXT, 200, array('nullable' => true, 'primary' => false), 'Nick Name'),
            'profile_name' => array('profile_name', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Profile Name'),
            'birth_date' => array('birth_date', Table::TYPE_TEXT, 20, array('nullable' => true, 'primary' => false), 'Birth Date'),
            'gender' => array('gender', Table::TYPE_TEXT, 6, array('nullable' => true, 'primary' => false), 'Gender'),
            'country_code' => array('country_code', Table::TYPE_TEXT, 10, array('nullable' => true, 'primary' => false), 'Country Code'),
            'country_name' => array('country_name', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Country Name'),
            'thumbnail_image_url' => array('thumbnail_image_url', Table::TYPE_TEXT, 1000, array('nullable' => true, 'primary' => false), 'Thumbnail Image Url'),
            'image_url' => array('image_url', Table::TYPE_TEXT, 1000, array('nullable' => true, 'primary' => false), 'Image Url'),
            'local_country' => array('local_country', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Local Country'),
            'profile_country' => array('profile_country', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Profile Country'),
            'email_type' => array('email_type', Table::TYPE_TEXT, 10, array('nullable' => true, 'primary' => false), 'Email Type'),
            'email' => array('email', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Email'),
            'sport_id' => array('sport_id', Table::TYPE_TEXT, 20, array('nullable' => true, 'primary' => false), 'Sport Id'),
            'sport' => array('sport', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Sport'),
            'type' => array('type', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Type'),
            'address_line1' => array('address_line1', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Address Line1'),
            'address_line2' => array('address_line2', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Address Line2'),
            'city' => array('city', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'City'),
            'state' => array('state', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'State'),
            'postal_code' => array('postal_code', Table::TYPE_TEXT, 20, array('nullable' => true, 'primary' => false), 'Postal Code'),
            'name' => array('name', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Name'),
            'region' => array('region', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Region'),
            'phone_number' => array('phone_number', Table::TYPE_TEXT, 30, array('nullable' => true, 'primary' => false), 'Phone Number'),
            'social_id' => array('social_id', Table::TYPE_TEXT, 1000, array('nullable' => true, 'primary' => false), 'Social Id'),
            'profile_url' => array('profile_url', Table::TYPE_TEXT, 1000, array('nullable' => true, 'primary' => false), 'Profile Url'),
            'status' => array('status', Table::TYPE_TEXT, null, array('nullable' => true, 'primary' => false), 'Status'),
            'industry' => array('industry', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Industry'),
            'country' => array('country', Table::TYPE_TEXT, 20, array('nullable' => true, 'primary' => false), 'Country'),
            'main_address' => array('main_address', Table::TYPE_TEXT, 500, array('nullable' => true, 'primary' => false), 'Main Aaddress'),
            'hometown' => array('hometown', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Hometown'),
            'local_city' => array('local_city', Table::TYPE_TEXT, 500, array('nullable' => true, 'primary' => false), 'Local City'),
            'profile_city' => array('profile_city', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Profile City'),
            'local_language' => array('local_language', Table::TYPE_TEXT, 10, array('nullable' => true, 'primary' => false), 'Local Language'),
            'language' => array('language', Table::TYPE_TEXT, 10, array('nullable' => true, 'primary' => false), 'Language'),
            'website' => array('website', Table::TYPE_TEXT, 1000, array('nullable' => true, 'primary' => false), 'Website'),
            'favicon' => array('favicon', Table::TYPE_TEXT, 1000, array('nullable' => true, 'primary' => false), 'Favicon'),
            'about' => array('about', Table::TYPE_TEXT, null, array('nullable' => true, 'primary' => false), 'about'),
            'timezone' => array('timezone', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Time Zone'),
            'verified' => array('verified', Table::TYPE_TEXT, 10, array('nullable' => true, 'primary' => false), 'Verified'),
            'last_profile_update' => array('last_profile_update', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Last Profile Update'),
            'created' => array('created', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Created'),
            'relationship_status' => array('relationship_status', Table::TYPE_TEXT, 20, array('nullable' => true, 'primary' => false), 'Relationship Status'),
            'quote' => array('quote', Table::TYPE_TEXT, 1000, array('nullable' => true, 'primary' => false), 'Quote'),
            'interested_in' => array('interested_in', Table::TYPE_TEXT, 10, array('nullable' => true, 'primary' => false), 'Interested In'),
            'interests' => array('interests', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Interests'),
            'religion' => array('religion', Table::TYPE_TEXT, 20, array('nullable' => true, 'primary' => false), 'Religion'),
            'political_view' => array('political_view', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Political View'),
            'https_image_url' => array('https_image_url', Table::TYPE_TEXT, 500, array('nullable' => true, 'primary' => false), 'Https Image Url'),
            'followers_count' => array('followers_count', Table::TYPE_TEXT, 11, array('nullable' => true, 'primary' => false), 'Followers Count'),
            'friends_count' => array('friends_count', Table::TYPE_TEXT, 11, array('nullable' => true, 'primary' => false), 'Friends Count'),
            'is_geo_enabled' => array('is_geo_enabled', Table::TYPE_TEXT, 1, array('nullable' => true, 'primary' => false), 'is GEO Enabled'),
            'total_status_count' => array('total_status_count', Table::TYPE_TEXT, 11, array('nullable' => true, 'primary' => false), 'Total Status Count'),
            'number_of_recommenders' => array('number_of_recommenders', Table::TYPE_TEXT, 11, array('nullable' => true, 'primary' => false), 'Number Of Recommenders'),
            'honors' => array('honors', Table::TYPE_TEXT, 1000, array('nullable' => true, 'primary' => false), 'Honors'),
            'associations' => array('associations', Table::TYPE_TEXT, 1000, array('nullable' => true, 'primary' => false), 'Associations'),
            'hirable' => array('hirable', Table::TYPE_TEXT, 1, array('nullable' => true, 'primary' => false), 'Hirable'),
            'repository_url' => array('repository_url', Table::TYPE_TEXT, 1000, array('nullable' => true, 'primary' => false), 'Repository Url'),
            'age' => array('age', Table::TYPE_TEXT, 3, array('nullable' => true, 'primary' => false), 'Age'),
            'no_of_login' => array('no_of_login', Table::TYPE_TEXT, 3, array('nullable' => true, 'primary' => false), 'No Of Login'),
            'professional_headline' => array('professional_headline', Table::TYPE_TEXT, 1000, array('nullable' => true, 'primary' => false), 'Professional Headline'),
            'provider_access_token' => array('provider_access_token', Table::TYPE_TEXT, null, array('nullable' => true, 'primary' => false), 'Provider Access Token'),
            'provider_token_secret' => array('provider_token_secret', Table::TYPE_TEXT, null, array('nullable' => true, 'primary' => false), 'Provider Token Secret'),
            'position' => array('position', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Position'),
            'summary' => array('summary', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Summary'),
            'start_date' => array('start_date', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Start Date'),
            'end_date' => array('end_date', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'End Date'),
            'is_current' => array('is_current', Table::TYPE_TEXT, 1, array('nullable' => true, 'primary' => false), 'Is Current'),
            'company' => array('company', Table::TYPE_TEXT, 11, array('nullable' => true, 'primary' => false), 'Company'),
            'location' => array('location', Table::TYPE_TEXT, 255, array('nullable' => true, 'primary' => false), 'Location'),
            'company_name' => array('company_name', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Company Name'),
            'company_type' => array('company_type', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Company Type'),
            'school' => array('school', Table::TYPE_TEXT, 255, array('nullable' => true, 'primary' => false), 'School'),
            'year' => array('year', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Year'),
            'notes' => array('notes', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Notes'),
            'activities' => array('activities', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Activities'),
            'degree' => array('degree', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Degree'),
            'field_of_study' => array('field_of_study', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Field Of Study'),
            'number_type' => array('number_type', Table::TYPE_TEXT, 20, array('nullable' => true, 'primary' => false), 'Number Type'),
            'account_type' => array('account_type', Table::TYPE_TEXT, 20, array('nullable' => true, 'primary' => false), 'Account Type'),
            'account_username' => array('account_username', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Account Username'),
            'mention_id' => array('mention_id', Table::TYPE_TEXT, 30, array('nullable' => true, 'primary' => false), 'Mention Id'),
            'tweet' => array('tweet', Table::TYPE_TEXT, null, array('nullable' => true, 'primary' => false), 'Tweet'),
            'date_time' => array('date_time', Table::TYPE_TEXT, 30, array('nullable' => true, 'primary' => false), 'Date Time'),
            'likes' => array('likes', Table::TYPE_TEXT, 11, array('nullable' => true, 'primary' => false), 'Likes'),
            'place' => array('place', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Place'),
            'source' => array('source', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Source'),
            'link_url' => array('link_url', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Link Url'),
            'mentioned_by' => array('mentioned_by', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Mentioned By'),
            'status_id' => array('status_id', Table::TYPE_TEXT, 20, array('nullable' => true, 'primary' => false), 'Status Id'),
            'company_id' => array('company_id', Table::TYPE_TEXT, 20, array('nullable' => true, 'primary' => false), 'Company Id'),
            'company_name' => array('company_name', Table::TYPE_TEXT, 200, array('nullable' => true, 'primary' => false), 'Company Name'),
            'group_id' => array('group_id', Table::TYPE_TEXT, 30, array('nullable' => true, 'primary' => false), 'Group Id'),
            'group_name' => array('group_name', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Group Name'),
            'post_id' => array('post_id', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Post Id'),
            'from_name' => array('from_name', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'From Name'),
            'title' => array('title', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Title'),
            'start_time' => array('start_time', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Start Time'),
            'update_time' => array('update_time', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Update Time'),
            'message' => array('message', Table::TYPE_TEXT, null, array('nullable' => true, 'primary' => false), 'Message'),
            'picture' => array('picture', Table::TYPE_TEXT, 1000, array('nullable' => true, 'primary' => false), 'Picture'),
            'shares' => array('shares', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Shares'),
            'event_id' => array('event_id', Table::TYPE_TEXT, 30, array('nullable' => true, 'primary' => false), 'Event Id'),
            'event' => array('event', Table::TYPE_TEXT, 500, array('nullable' => true, 'primary' => false), 'Event'),
            'rsvp_status' => array('rsvp_status', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'RSVP Status'),
            'patent_id' => array('patent_id', Table::TYPE_TEXT, 30, array('nullable' => true, 'primary' => false), 'Patent Id'),
            'date' => array('date', Table::TYPE_TEXT, 30, array('nullable' => true, 'primary' => false), 'Date'),
            'language_id' => array('language_id', Table::TYPE_TEXT, 30, array('nullable' => true, 'primary' => false), 'Language Id'),
            'recommendation_id' => array('recommendation_id', Table::TYPE_TEXT, 30, array('nullable' => true, 'primary' => false), 'Recommendation Id'),
            'recommendation_type' => array('recommendation_type', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Recommendation Type'),
            'recommendation_text' => array('recommendation_text', Table::TYPE_TEXT, null, array('nullable' => true, 'primary' => false), 'Recommendation Text'),
            'recommender' => array('recommender', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Recommender'),
            'volunteer_id' => array('volunteer_id', Table::TYPE_TEXT, 30, array('nullable' => true, 'primary' => false), 'Volunteer Id'),
            'role' => array('role', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Role'),
            'organization' => array('organization', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Organization'),
            'cause' => array('cause', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Cause'),
            'course_id' => array('course_id', Table::TYPE_TEXT, 30, array('nullable' => true, 'primary' => false), 'Course Id'),
            'course' => array('course', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Course'),
            'course_number' => array('course_number', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Course Number'),
            'certification_id' => array('certification_id', Table::TYPE_TEXT, 30, array('nullable' => true, 'primary' => false), 'Certification Id'),
            'certification_name' => array('certification_name', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Certification Name'),
            'authority' => array('authority', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Authority'),
            'license_number' => array('license_number', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'License Number'),
            'created_date' => array('created_date', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Created Date'),
            'skill_id' => array('skill_id', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Skill Id'),
            'likes_id' => array('likes_id', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Likes Id'),
            'category' => array('category', Table::TYPE_TEXT, 100, array('nullable' => true, 'primary' => false), 'Category'),
            'description' => array('description', Table::TYPE_TEXT, null, array('nullable' => true, 'primary' => false), 'Description'),
            
            'book_id' => array('book_id', Table::TYPE_TEXT, 50, array('nullable' => true, 'primary' => false), 'Book Id'),
        );
        return isset($colamn[$name]) ? $colamn[$name] : null;
    }

    /**
     * 
     * @param type $setup
     */
    private function createTables($setup) {
        $tables = array('basic_profile_data' => 'Basic Profile Data',
            'emails' => 'Emails Data',
            'sports' => 'Sports Data',
            'addresses' => 'Address Data',
            'contacts' => 'Contacts Data',
            'extended_location_data' => 'Extended Location Data',
            'extended_profile_data' => 'Extended Profile Data',
            'positions' => 'Positions Data',
            'companies' => 'Companies Data',
            'education' => 'Education Data',
            'phone_numbers' => 'Phone Numbers Data',
            'imaccounts' => 'IMaccounts Data',
            'twitter_mentions' => 'Twitter Mentions Data',
            'status' => 'Status Data',
            'linkedin_companies' => 'LinkedIn Companies Data',
            'groups' => 'Groups Data',
            'facebook_posts' => 'Facebook Posts Data',
            'facebook_events' => 'Facebook Events Data',
            'favorites' => 'Favorites Data',
            'books' => 'Books Data',
            'patents' => 'Patents Data',
            'languages' => 'Languages Data',
            'recommendations_received' => 'Recommendations Received Data',
            'volunteer' => 'Volunteer Data',
            'courses' => 'Courses Data',
            'certifications' => 'Certifications Data',
            'current_status' => 'Current Status Data',
            'skills' => 'Skills Data',
            'inspirational_people' => 'Inspirational People Data',
            'facebook_likes' => 'Facebook Likes Data'
        );
        foreach ($tables as $table => $comment) {
            $this->createTable($setup, $table, $comment);
        }
    }

    /**
     * 
     * @param type $setup
     * @param type $tname
     * @param type $comment
     */
    private function createTable($setup, $tname, $comment) {

        // Get tutorial_simplenews table
        $tableName = $setup->getTable('lr_' . $tname);
        // Check if the table already exists
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $setup->getConnection()->newTable($tableName);
            $columnNames = $this->getTablesColamns($tname);
            $columns = array();
            foreach ($columnNames as $columnName) {
                $columns[] = $this->getColamnArray($columnName);
            }
            foreach ($columns as $column) {
                $table->addColumn(
                        $column[0], $column[1], $column[2], $column[3], $column[4]
                );
            }
            $table->setComment($comment)
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
            $setup->getConnection()->createTable($table);
        }
    }

}
