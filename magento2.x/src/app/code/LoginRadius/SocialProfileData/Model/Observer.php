<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LoginRadius\SocialProfileData\Model;

use Magento\Framework\Event\ObserverInterface;

class Observer implements ObserverInterface {

    protected $_messageManager;
    protected $_objectManager;
    protected $_socialLoginObject;

    public function __construct(
    \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_messageManager = $messageManager;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        
        $event = $observer->getEvent();
        $accessToken = $event->getToken();
        $entityid = $event->getEntityid();
        $userProfiledata = $event->getProfiledata();
        $activationHelper = $this->_objectManager->get('LoginRadius\Activation\Model\Helper\Data');
        $socialProfileDataHelper = $this->_objectManager->get('LoginRadius\SocialProfileData\Model\Helper\Data');
        $this->_socialLoginObject = new \LoginRadiusSDK\SocialLogin\SocialLoginAPI($activationHelper->siteApiKey(), $activationHelper->siteApiSecret(), array('authentication' => false, 'output_format' => 'json'));
       
        if ($socialProfileDataHelper->basicProfile() == '1') {
            $this->saveBasicProfileData($entityid, $userProfiledata);
            $this->saveEmailsData($entityid, $userProfiledata);
        }
        if ($socialProfileDataHelper->extendedLocation() == '1') {
            $this->saveExtendedLocationData($entityid, $userProfiledata);
        }
        if ($socialProfileDataHelper->extendedProfile() == '1') {
            
            $this->saveExtendedProfileData($entityid, $userProfiledata);
            $this->savePositionComapnyData($entityid, $userProfiledata);
            $this->saveEducationData($entityid, $userProfiledata);
            $this->savePhoneNumbersData($entityid, $userProfiledata);
            $this->saveIMAccountsData($entityid, $userProfiledata);
            $this->saveAddressesData($entityid, $userProfiledata);
            $this->saveSportsData($entityid, $userProfiledata);
            $this->saveInspirationalData($entityid, $userProfiledata);
            $this->saveSkillData($entityid, $userProfiledata);
            $this->saveCurrentStatusSata($entityid, $userProfiledata);
            $this->saveCertificationsData($entityid, $userProfiledata);
            $this->saveCoursesData($entityid, $userProfiledata);
            $this->saveVolunteerData($entityid, $userProfiledata);
            $this->saveRecommendationsReceivedData($entityid, $userProfiledata);
            $this->savelanguagesData($entityid, $userProfiledata);
            $this->savePatentsData($entityid, $userProfiledata);
            $this->saveFavoritesData($entityid, $userProfiledata);
            $this->saveBooksData($entityid, $userProfiledata);
        }
        if ($socialProfileDataHelper->contacts() == '1') {
            $this->saveContactsData($entityid, $userProfiledata->Provider, $accessToken);
        }
        if ($socialProfileDataHelper->facebookEvents() == '1') {
            $this->saveEventsData($entityid, $userProfiledata->Provider, $accessToken);
        }
        if ($socialProfileDataHelper->facebookPosts() == '1') {
            $this->savePostsData($entityid, $userProfiledata->Provider, $accessToken);
        }
        if ($socialProfileDataHelper->followedCompanies() == '1') {
            $this->saveCompaniesData($entityid, $userProfiledata->Provider, $accessToken);
        }
        if ($socialProfileDataHelper->statusMessages() == '1') {
            $this->saveStatusData($entityid, $userProfiledata->Provider, $accessToken);
        }
        if ($socialProfileDataHelper->twitterMentions() == '1') {
            $this->saveMentionsData($entityid, $userProfiledata->Provider, $accessToken);
        }
        if ($socialProfileDataHelper->groups() == '1') {
            $this->saveGroupsData($entityid, $userProfiledata->Provider, $accessToken);
        }
        if ($socialProfileDataHelper->likes() == '1') {
            $this->saveFacebookLikesData($entityid, $userProfiledata->Provider, $accessToken);
        }
    }

    private function checkVariable($userProfile, $key, $defaultValue = '') {
        $variable = isset($userProfile->$key) && $userProfile->$key != "unknown" ? $userProfile->$key : $defaultValue;

        if (!is_array($variable) && !is_object($variable)) {
            return trim($variable);
        }

        return implode(", ", (array) $variable);
    }

    private function saveFacebookLikesData($entityid, $provider, $accessToken) {
        if ($provider == 'facebook') {
            try {
                $facebookLikes = $this->_socialLoginObject->getLikes($accessToken);
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                //$this->_eventManager->dispatch('lr_logout_sso', array('exception' => $e));
            }
            
            if (isset($facebookLikes) && !is_string($facebookLikes) && count($facebookLikes) > 0) {
                $this->deleteDataFromTable($entityid, 'facebook_likes');
                foreach ($facebookLikes as $like) {
                    $data = array(
                        'entity_id' => $entityid,
                        'likes_id' => $this->checkVariable($like, 'ID'),
                        'name' => $this->checkVariable($like, 'Name'),
                        'category' => $this->checkVariable($like, 'Category'),
                        'created_date' => $this->checkVariable($like, 'CreatedDate'),
                        'website' => $this->checkVariable($like, 'Website'),
                        'description' => $this->checkVariable($like, 'Description')
                    );
                    $this->saveDataInTable('facebook_likes', $data);
                }
            }
        }
    }

    private function saveGroupsData($entityid, $provider, $accessToken) {
        if (in_array($provider, array('facebook', 'linkedin'))) {
            try {
                $groups = $this->_socialLoginObject->getGroups($accessToken);
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                //$this->_eventManager->dispatch('lr_logout_sso', array('exception' => $e));
            }
            if (isset($groups) && !is_string($groups) && count($groups) > 0) {
                $this->deleteDataFromTable($entityid, 'groups');
                foreach ($groups as $group) {
                    $data = array(
                        'entity_id' => $entityid,
                        'provider' => $provider,
                        'group_id' => $this->checkVariable($group, 'ID'),
                        'group_name' => $this->checkVariable($group, 'Name')
                    );
                    $this->saveDataInTable('groups', $data);
                }
            }
        }
    }

    private function saveMentionsData($entityid, $provider, $accessToken) {
        if ($provider == 'twitter') {
            try {
                $mentions = $this->_socialLoginObject->getMentions($accessToken);
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                //$this->_eventManager->dispatch('lr_logout_sso', array('exception' => $e));
            }
            if (isset($mentions) && !is_string($mentions) && count($mentions) > 0) {
                $this->deleteDataFromTable($entityid, 'twitter_mentions');
                foreach ($mentions as $mention) {
                    $data = array(
                        'entity_id' => $entityid,
                        'mention_id' => $this->checkVariable($mention, 'Id'),
                        'tweet' => $this->checkVariable($mention, 'Text'),
                        'date_time' => $this->checkVariable($mention, 'DateTime'),
                        'likes' => $this->checkVariable($mention, 'Likes'),
                        'place' => $this->checkVariable($mention, 'Place'),
                        'source' => $this->checkVariable($mention, 'Source'),
                        'image_url' => $this->checkVariable($mention, 'ImageUrl'),
                        'link_url' => $this->checkVariable($mention, 'LinkUrl'),
                        'mentioned_by' => $this->checkVariable($mention, 'Name')
                    );
                    $this->saveDataInTable('twitter_mentions', $data);
                }
            }
        }
    }

    private function saveStatusData($entityid, $provider, $accessToken) {
        if (in_array($provider, array('twitter', 'facebook', 'linkedin'))) {
            try {
                $statusReport = $this->_socialLoginObject->getStatus($accessToken);
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                //$this->_eventManager->dispatch('lr_logout_sso', array('exception' => $e));
            }
            if (isset($statusReport) && !is_string($statusReport) && count($statusReport) > 0) {
                $this->deleteDataFromTable($entityid, 'status');
                foreach ($statusReport as $status) {
                    $data = array(
                        'entity_id' => $entityid,
                        'provider' => $provider,
                        'status_id' => $this->checkVariable($status, 'Id'),
                        'status' => $this->checkVariable($status, 'Text'),
                        'date_time' => $this->checkVariable($status, 'DateTime'),
                        'likes' => $this->checkVariable($status, 'Likes'),
                        'place' => $this->checkVariable($status, 'Place'),
                        'source' => $this->checkVariable($status, 'Source'),
                        'image_url' => $this->checkVariable($status, 'ImageUrl'),
                        'link_url' => $this->checkVariable($status, 'LinkUrl')
                    );
                    $this->saveDataInTable('status', $data);
                }
            }
        }
    }

    private function saveCompaniesData($entityid, $provider, $accessToken) {
        if (in_array($provider, array('facebook', 'linkedin'))) {
            try {
                $linkedInCompanies = $this->_socialLoginObject->getFollowedCompanies($accessToken);
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                //$this->_eventManager->dispatch('lr_logout_sso', array('exception' => $e));
            }

            if (isset($linkedInCompanies) && !is_string($linkedInCompanies) && count($linkedInCompanies) > 0) {
                $this->deleteDataFromTable($entityid, 'linkedin_companies');
                foreach ($linkedInCompanies as $company) {
                    $data = array(
                        'entity_id' => $entityid,
                        'company_id' => $this->checkVariable($company, 'ID'),
                        'company_name' => $this->checkVariable($company, 'Name')
                    );
                    $this->saveDataInTable('linkedin_companies', $data);
                }
            }
        }
    }

    private function savePostsData($entityid, $provider, $accessToken) {
        if ($provider == 'facebook') {
            try {
                $posts = $this->_socialLoginObject->getPosts($accessToken);
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                //$this->_eventManager->dispatch('lr_logout_sso', array('exception' => $e));
            }
            if (isset($posts) && !is_string($posts) && count($posts) > 0) {
                $this->deleteDataFromTable($entityid, 'facebook_posts');
                foreach ($posts as $post) {
                    $data = array(
                        'entity_id' => $entityid,
                        'post_id' => $this->checkVariable($post, 'ID'),
                        'from_name' => $this->checkVariable($post, 'Name'),
                        'title' => $this->checkVariable($post, 'Title'),
                        'start_time' => $this->checkVariable($post, 'StartTime'),
                        'update_time' => $this->checkVariable($post, 'UpdateTime'),
                        'message' => $this->checkVariable($post, 'Message'),
                        'place' => $this->checkVariable($post, 'Place'),
                        'picture' => $this->checkVariable($post, 'Picture'),
                        'likes' => $this->checkVariable($post, 'Likes'),
                        'shares' => $this->checkVariable($post, 'Share')
                    );
                    $this->saveDataInTable('facebook_posts', $data);
                }
            }
        }
    }

    private function saveEventsData($entityid, $provider, $accessToken) {
        if ($provider == 'facebook') {
            try {
                $events = $this->_socialLoginObject->getEvents($accessToken);
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                //$this->_eventManager->dispatch('lr_logout_sso', array('exception' => $e));
            }

            if (isset($events) && !is_string($events) && count($events) > 0) {
                $this->deleteDataFromTable($entityid, 'facebook_events');
                foreach ($events as $event) {
                    $data = array(
                        'entity_id' => $entityid,
                        'event_id' => $this->checkVariable($event, 'ID'),
                        'event' => $this->checkVariable($event, 'Name'),
                        'start_time' => $this->checkVariable($event, 'StartTime'),
                        'location' => $this->checkVariable($event, 'Location'),
                        'rsvp_status' => $this->checkVariable($event, 'RsvpStatus')
                    );
                    $this->saveDataInTable('facebook_events', $data);
                }
            }
        }
    }

    private function saveContactsData($entityid, $provider, $accessToken) {
        if (in_array($provider, array('twitter', 'facebook', 'linkedin', 'google', 'yahoo'))) {
            $this->getContactsByNextcursor($entityid, $provider, $accessToken);
        }
    }

    private function getContactsByNextcursor($entityid, $provider, $accessToken, $nextCursor = '') {
        try {
            $contacts = $this->_socialLoginObject->getContacts($accessToken, $nextCursor);
        } catch (\LoginRadiusSDK\LoginRadiusException $e) {
            //$this->_eventManager->dispatch('lr_logout_sso', array('exception' => $e));
        }
        if (isset($contacts) && isset($contacts->Data) && !is_string($contacts->Data) && count($contacts->Data) > 0) {
            if ($nextCursor == '') {
                $this->deleteDataFromTable($entityid, 'contacts');
            }
            foreach ($contacts->Data as $contact) {
                $data = array(
                    'entity_id' => $entityid,
                    'provider' => $provider,
                    'social_id' => $this->checkVariable($contact, 'ID'),
                    'name' => $this->checkVariable($contact, 'Name'),
                    'email' => $this->checkVariable($contact, 'EmailID'),
                    'profile_url' => $this->checkVariable($contact, 'ProfileUrl'),
                    'image_url' => $this->checkVariable($contact, 'ImageUrl'),
                    'status' => $this->checkVariable($contact, 'Status'),
                    'industry' => $this->checkVariable($contact, 'Industry'),
                    'country' => $this->checkVariable($contact, 'Country'),
                    'gender' => $this->checkVariable($contact, 'Gender'),
                    'phone_number' => $this->checkVariable($contact, 'PhoneNumber')
                );
                $this->saveDataInTable('contacts', $data);
            }
            if (isset($contacts->NextCursor) && !empty($contacts->NextCursor)) {
                $this->getContactsByNextcursor($entityid, $provider, $accessToken, $contacts->NextCursor);
            }
        }
    }

    private function savelanguagesData($entityid, $userProfiledata) {
        if (isset($userProfiledata->Languages) && count($userProfiledata->Languages) > 0) {
            $this->deleteDataFromTable($entityid, 'languages');
            foreach ($userProfiledata->Languages as $languages) {
                $data = array(
                    'entity_id' => $entityid,
                    'language_id' => $this->checkVariable($languages, 'Id'),
                    'language' => $this->checkVariable($languages, 'Name')
                );
                $this->saveDataInTable('languages', $data);
            }
        }
    }

    private function savePatentsData($entityid, $userProfiledata) {
        if (isset($userProfiledata->Patents) && count($userProfiledata->Patents) > 0) {
            $this->deleteDataFromTable($entityid, 'patents');
            foreach ($userProfiledata->Patents as $patents) {
                $data = array(
                    'entity_id' => $entityid,
                    'patent_id' => $this->checkVariable($patents, 'Id'),
                    'title' => $this->checkVariable($patents, 'Title'),
                    'date' => $this->checkVariable($patents, 'Date')
                );
                $this->saveDataInTable('patents', $data);
            }
        }
    }

    private function saveFavoritesData($entityid, $userProfiledata) {
        if (isset($userProfiledata->FavoriteThings) && count($userProfiledata->FavoriteThings) > 0) {
            $this->deleteDataFromTable($entityid, 'favorites');
            foreach ($userProfiledata->FavoriteThings as $favoriteThings) {
                $data = array(
                    'entity_id' => $entityid,
                    'social_id' => $this->checkVariable($favoriteThings, 'Id'),
                    'name' => $this->checkVariable($favoriteThings, 'Name'),
                    'type' => $this->checkVariable($favoriteThings, 'Type')
                );
                $this->saveDataInTable('favorites', $data);
            }
        }
    }
    private function saveBooksData($entityid, $userProfiledata) {
        if (isset($userProfiledata->Books) && count($userProfiledata->Books) > 0) {
            $this->deleteDataFromTable($entityid, 'books');
            foreach ($userProfiledata->Books as $books) {
                $data = array(
                    'entity_id' => $entityid,
                    'book_id' => $this->checkVariable($books, 'Id'),
                    'category' => $this->checkVariable($books, 'Category'),
                    'name' => $this->checkVariable($books, 'Name'),
                    'created_date' => $this->checkVariable($books, 'CreatedDate')
                );
                $this->saveDataInTable('books', $data);
            }
        }
    }

    private function saveRecommendationsReceivedData($entityid, $userProfiledata) {
        if (isset($userProfiledata->RecommendationsReceived) && count($userProfiledata->RecommendationsReceived) > 0) {
            $this->deleteDataFromTable($entityid, 'recommendations_received');
            foreach ($userProfiledata->RecommendationsReceived as $recommendationsReceived) {
                $data = array(
                    'entity_id' => $entityid,
                    'recommendation_id' => $this->checkVariable($recommendationsReceived, 'Id'),
                    'recommendation_type' => $this->checkVariable($recommendationsReceived, 'RecommendationType'),
                    'recommendation_text' => $this->checkVariable($recommendationsReceived, 'RecommendationText'),
                    'recommender' => $this->checkVariable($recommendationsReceived, 'Recommender')
                );
                $this->saveDataInTable('recommendations_received', $data);
            }
        }
    }

    private function saveVolunteerData($entityid, $userProfiledata) {
        if (isset($userProfiledata->Volunteer) && count($userProfiledata->Volunteer) > 0) {
            $this->deleteDataFromTable($entityid, 'volunteer');
            foreach ($userProfiledata->Volunteer as $volunteer) {
                $data = array(
                    'entity_id' => $entityid,
                    'volunteer_id' => $this->checkVariable($volunteer, 'Id'),
                    'role' => $this->checkVariable($volunteer, 'Role'),
                    'organization' => $this->checkVariable($volunteer, 'Organization'),
                    'cause' => $this->checkVariable($volunteer, 'Cause')
                );
                $this->saveDataInTable('volunteer', $data);
            }
        }
    }

    private function saveCoursesData($entityid, $userProfiledata) {
        if (isset($userProfiledata->Courses) && count($userProfiledata->Courses) > 0) {
            $this->deleteDataFromTable($entityid, 'courses');
            foreach ($userProfiledata->Courses as $course) {
                $data = array(
                    'entity_id' => $entityid,
                    'course_id' => $this->checkVariable($course, 'Id'),
                    'course' => $this->checkVariable($course, 'Name'),
                    'course_number' => $this->checkVariable($course, 'Number')
                );
                $this->saveDataInTable('courses', $data);
            }
        }
    }

    private function saveCertificationsData($entityid, $userProfiledata) {
        if (isset($userProfiledata->Certifications) && count($userProfiledata->Certifications) > 0) {
            $this->deleteDataFromTable($entityid, 'certifications');
            foreach ($userProfiledata->Certifications as $certifications) {
                $data = array(
                    'entity_id' => $entityid,
                    'certification_id' => $this->checkVariable($certifications, 'Id'),
                    'certification_name' => $this->checkVariable($certifications, 'Name'),
                    'authority' => $this->checkVariable($certifications, 'Authority'),
                    'license_number' => $this->checkVariable($certifications, 'Number'),
                    'start_date' => $this->checkVariable($certifications, 'StartDate'),
                    'end_date' => $this->checkVariable($certifications, 'EndDate')
                );
                $this->saveDataInTable('certifications', $data);
            }
        }
    }

    private function saveCurrentStatusSata($entityid, $userProfiledata) {
        if (isset($userProfiledata->CurrentStatus) && count($userProfiledata->CurrentStatus) > 0) {
            $this->deleteDataFromTable($entityid, 'current_status');
            foreach ($userProfiledata->CurrentStatus as $currentStatus) {
                $data = array(
                    'entity_id' => $entityid,
                    'status_id' => $this->checkVariable($currentStatus, 'Id'),
                    'status' => $this->checkVariable($currentStatus, 'Text'),
                    'source' => $this->checkVariable($currentStatus, 'Source'),
                    'created_date' => $this->checkVariable($currentStatus, 'CreatedDate')
                );
                $this->saveDataInTable('current_status', $data);
            }
        }
    }

    private function saveSkillData($entityid, $userProfiledata) {
        if (isset($userProfiledata->Skills) && count($userProfiledata->Skills) > 0) {
            $this->deleteDataFromTable($entityid, 'skills');
            foreach ($userProfiledata->Skills as $skills) {
                $data = array(
                    'entity_id' => $entityid,
                    'skill_id' => $this->checkVariable($skills, 'Id'),
                    'name' => $this->checkVariable($skills, 'Name')
                );
                $this->saveDataInTable('skills', $data);
            }
        }
    }

    private function saveInspirationalData($entityid, $userProfiledata) {
        if (isset($userProfiledata->InspirationalPeople) && count($userProfiledata->InspirationalPeople) > 0) {
            $this->deleteDataFromTable($entityid, 'inspirational_people');
            foreach ($userProfiledata->InspirationalPeople as $inspirationalPeople) {
                $data = array(
                    'entity_id' => $entityid,
                    'social_id' => $this->checkVariable($inspirationalPeople, 'Id'),
                    'name' => $this->checkVariable($inspirationalPeople, 'Name')
                );
                $this->saveDataInTable('inspirational_people', $data);
            }
        }
    }

    private function saveSportsData($entityid, $userProfiledata) {
        if (isset($userProfiledata->Sports) && count($userProfiledata->Sports) > 0) {
            $this->deleteDataFromTable($entityid, 'sports');
            foreach ($userProfiledata->Sports as $sports) {
                $data = array(
                    'entity_id' => $entityid,
                    'sport_id' => $this->checkVariable($sports, 'Id'),
                    'sport' => $this->checkVariable($sports, 'Name')
                );
                $this->saveDataInTable('sports', $data);
            }
        }
    }

    private function saveAddressesData($entityid, $userProfiledata) {
        
        if (isset($userProfiledata->Addresses) && count($userProfiledata->Addresses) > 0) {
            
            $this->deleteDataFromTable($entityid, 'addresses');
            foreach ($userProfiledata->Addresses as $addresses) {
                $data = array(
                    'entity_id' => $entityid,
                    'type' => $this->checkVariable($addresses, 'Type'),
                    'address_line1' => $this->checkVariable($addresses, 'Address1'),
                    'address_line2' => $this->checkVariable($addresses, 'Address2'),
                    'city' => $this->checkVariable($addresses, 'City'),
                    'state' => $this->checkVariable($addresses, 'State'),
                    'postal_code' => $this->checkVariable($addresses, 'PostalCode'),
                    'region' => $this->checkVariable($addresses, 'Region'),
                    'country' => $this->checkVariable($addresses, 'Country')
                );
                $this->saveDataInTable('addresses', $data);
            }
        }
    }

    private function saveIMAccountsData($entityid, $userProfiledata) {
        if (isset($userProfiledata->IMAccounts) && count($userProfiledata->IMAccounts) > 0) {
            $this->deleteDataFromTable($entityid, 'imaccounts');
            foreach ($userProfiledata->IMAccounts as $imAccounts) {
                $data = array(
                    'entity_id' => $entityid,
                    'number_type' => $this->checkVariable($imAccounts, 'AccountType'),
                    'phone_number' => $this->checkVariable($imAccounts, 'AccountName')
                );
                $this->saveDataInTable('imaccounts', $data);
            }
        }
    }

    private function savePhoneNumbersData($entityid, $userProfiledata) {
        if (isset($userProfiledata->PhoneNumbers) && count($userProfiledata->PhoneNumbers) > 0) {
            $this->deleteDataFromTable($entityid, 'phone_numbers');
            foreach ($userProfiledata->PhoneNumbers as $phoneNumber) {
                $data = array(
                    'entity_id' => $entityid,
                    'number_type' => $this->checkVariable($phoneNumber, 'PhoneType'),
                    'phone_number' => $this->checkVariable($phoneNumber, 'PhoneNumber')
                );
                $this->saveDataInTable('phone_numbers', $data);
            }
        }
    }

    private function saveEducationData($entityid, $userProfiledata) {
        if (isset($userProfiledata->Educations) && count($userProfiledata->Educations) > 0) {
            $this->deleteDataFromTable($entityid, 'education');
            foreach ($userProfiledata->Educations as $education) {
                $data = array(
                    'entity_id' => $entityid,
                    'school' => $this->checkVariable($education, 'School'),
                    'year' => $this->checkVariable($education, 'year'),
                    'type' => $this->checkVariable($education, 'type'),
                    'notes' => $this->checkVariable($education, 'notes'),
                    'activities' => $this->checkVariable($education, 'activities'),
                    'degree' => $this->checkVariable($education, 'degree'),
                    'field_of_study' => $this->checkVariable($education, 'fieldofstudy'),
                    'start_date' => $this->checkVariable($education, 'StartDate'),
                    'end_date' => $this->checkVariable($education, 'EndDate')
                );
                $this->saveDataInTable('education', $data);
            }
        }
    }

    private function savePositionComapnyData($entityid, $userProfiledata) {
        if (isset($userProfiledata->Positions) && count($userProfiledata->Positions) > 0) {
            $this->deleteDataFromTable($entityid, 'positions');
            $this->deleteDataFromTable($entityid, 'companies');
            foreach ($userProfiledata->Positions as $position) {
                $companyId = '';
                if (isset($position->Comapny)) {
                    $companyData = array(
                        'entity_id' => $entityid,
                        'company_name' => $this->checkVariable($position->Comapny, 'Name'),
                        'company_type' => $this->checkVariable($position->Comapny, 'Type'),
                        'industry' => $this->checkVariable($position->Comapny, 'Industry')
                    );
                    $companyId = $this->saveDataInTable('companies', $companyData);
                }
                $data = array(
                    'entity_id' => $entityid,
                    'position' => $this->checkVariable($position, 'Position'),
                    'summary' => $this->checkVariable($position, 'Summary'),
                    'start_date' => $this->checkVariable($position, 'StartDate'),
                    'end_date' => $this->checkVariable($position, 'EndDate'),
                    'is_current' => $this->checkVariable($position, 'IsCurrent'),
                    'company' => $companyId,
                    'location' => $this->checkVariable($position, 'Location')
                );
                $this->saveDataInTable('positions', $data);
            }
        }
    }

    private function saveExtendedProfileData($entityid, $userProfiledata) {
        $data = array(
            'entity_id' => $entityid,
            'website' => $this->checkVariable($userProfiledata, 'Website'),
            'favicon' => $this->checkVariable($userProfiledata, 'Favicon'),
            'industry' => $this->checkVariable($userProfiledata, 'Industry'),
            'about' => $this->checkVariable($userProfiledata, 'About'),
            'timezone' => $this->checkVariable($userProfiledata, 'TimeZone'),
            'verified' => $this->checkVariable($userProfiledata, 'Verified'),
            'last_profile_update' => $this->checkVariable($userProfiledata, 'UpdatedTime'),
            'created' => $this->checkVariable($userProfiledata, 'Created'),
            'relationship_status' => $this->checkVariable($userProfiledata, 'RelationshipStatus'),
            'quote' => $this->checkVariable($userProfiledata, 'Quote'),
            'interested_in' => $this->checkVariable($userProfiledata, 'InterestedIn'),
            'interests' => $this->checkVariable($userProfiledata, 'Interests'),
            'religion' => $this->checkVariable($userProfiledata, 'Religion'),
            'political_view' => $this->checkVariable($userProfiledata, 'Political'),
            'https_image_url' => $this->checkVariable($userProfiledata, 'HttpsImageUrl'),
            'followers_count' => $this->checkVariable($userProfiledata, 'FollowersCount'),
            'friends_count' => $this->checkVariable($userProfiledata, 'FriendsCount'),
            'is_geo_enabled' => $this->checkVariable($userProfiledata, 'IsGeoEnabled'),
            'total_status_count' => $this->checkVariable($userProfiledata, 'TotalStatusesCount'),
            'number_of_recommenders' => $this->checkVariable($userProfiledata, 'NumRecommenders'),
            'honors' => $this->checkVariable($userProfiledata, 'Honors'),
            'associations' => $this->checkVariable($userProfiledata, 'Associations'),
            'hirable' => $this->checkVariable($userProfiledata, 'Hireable'),
            'repository_url' => $this->checkVariable($userProfiledata, 'RepositoryUrl'),
            'age' => $this->checkVariable($userProfiledata, 'Age'),
            'professional_headline' => $this->checkVariable($userProfiledata, 'ProfessionalHeadline'),
            'provider_access_token' => $this->checkVariable($userProfiledata->ProviderAccessCredential, 'AccessToken'),
            'provider_token_secret' => $this->checkVariable($userProfiledata->ProviderAccessCredential, 'TokenSecret'),
            'no_of_login' => $this->checkVariable($userProfiledata, 'NoOfLogins')
        );
        $this->deleteDataFromTable($entityid, 'extended_profile_data');
        $this->saveDataInTable('extended_profile_data', $data);
    }

    private function saveEmailsData($entityid, $userProfiledata) {
        if (isset($userProfiledata->Email) && count($userProfiledata->Email) > 0) {
            $this->deleteDataFromTable($entityid, 'emails');
            foreach ($userProfiledata->Email as $email) {
                $data = array(
                    'entity_id' => $entityid,
                    'email_type' => $this->checkVariable($email, 'Type'),
                    'email' => $this->checkVariable($email, 'Value')
                );
                $this->saveDataInTable('emails', $data);
            }
        }
    }

    private function saveBasicProfileData($entityid, $userProfiledata) {
        $data = array(
            'entity_id' => $entityid,
            'sociallogin_id' => $this->checkVariable($userProfiledata, 'ID'),
            'provider' => $this->checkVariable($userProfiledata, 'Provider'),
            'first_name' => $this->checkVariable($userProfiledata, 'FirstName'),
            'middle_name' => $this->checkVariable($userProfiledata, 'MiddleName'),
            'last_name' => $this->checkVariable($userProfiledata, 'LastName'),
            'full_name' => $this->checkVariable($userProfiledata, 'FullName'),
            'nick_name' => $this->checkVariable($userProfiledata, 'NickName'),
            'profile_name' => $this->checkVariable($userProfiledata, 'ProfileName'),
            'birth_date' => $this->checkVariable($userProfiledata, 'BirthDate'),
            'gender' => $this->checkVariable($userProfiledata, 'Gender'),
            'prefix' => $this->checkVariable($userProfiledata, 'Prefix'),
            'suffix' => $this->checkVariable($userProfiledata, 'Suffix'),
            'country_code' => $this->checkVariable($userProfiledata->Country, 'Code'),
            'country_name' => $this->checkVariable($userProfiledata->Country, 'Name'),
            'thumbnail_image_url' => $this->checkVariable($userProfiledata, 'ThumbnailImageUrl'),
            'image_url' => $this->checkVariable($userProfiledata, 'ImageUrl'),
            'local_country' => $this->checkVariable($userProfiledata, 'LocalCountry'),
            'profile_country' => $this->checkVariable($userProfiledata, 'ProfileCountry')
        );
        $this->deleteDataFromTable($entityid, 'basic_profile_data');
        $this->saveDataInTable('basic_profile_data', $data);
    }

    private function saveExtendedLocationData($entityid, $userProfiledata) {
        $data = array(
            'entity_id' => $entityid,
            'main_address' => $this->checkVariable($userProfiledata, 'MainAddress'),
            'hometown' => $this->checkVariable($userProfiledata, 'HomeTown'),
            'state' => $this->checkVariable($userProfiledata, 'State'),
            'city' => $this->checkVariable($userProfiledata, 'City'),
            'local_city' => $this->checkVariable($userProfiledata, 'LocalCity'),
            'profile_city' => $this->checkVariable($userProfiledata, 'ProfileCity'),
            'profile_url' => $this->checkVariable($userProfiledata, 'ProfileUrl'),
            'local_language' => $this->checkVariable($userProfiledata, 'LocalLanguage'),
            'language' => $this->checkVariable($userProfiledata, 'Language')
        );
        $this->deleteDataFromTable($entityid, 'extended_location_data');
        $this->saveDataInTable('extended_location_data', $data);
    }

    private function saveDataInTable($tableName, $data) {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $changelogName = $resource->getTableName('lr_' . $tableName);
        $connection = $resource->getConnection();
        $connection->insert($changelogName, $data);
        return $connection->lastInsertId();
    }

    private function deleteDataFromTable($entityid, $tableName) {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $changelogName = $resource->getTableName('lr_' . $tableName);
        $connection = $resource->getConnection();
        $connection->delete($changelogName, array('entity_id=' . $entityid));
    }

}
