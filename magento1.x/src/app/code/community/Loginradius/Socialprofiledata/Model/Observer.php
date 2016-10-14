<?php

class Loginradius_Socialprofiledata_Model_Observer {

    public function getSocialProfileCheckboxes() {
        return Mage::getStoreConfig('socialprofiledata/profiledataoption/profiledata');
    }

    public function lr_get_profile_data_after_login($observer) {
        $event = $observer->getEvent();
        $entityid = $event->getEntityid();
        $profiledata = $event->getProfiledata();
        $update = $event->getUpdate();
        
        require_once Mage::getModuleDir('', 'Loginradius_Sociallogin') . DS . 'Helper' . DS . 'SDKClient.php';
        global $apiClient_class;
        $apiClient_class = 'Loginradius_Sociallogin_Helper_SDKClient';
        $activationBlockObj = Mage::getBlockSingleton('activation/activation');
        $loginradiusSDK = new LoginRadiusSDK\SocialLogin\SocialLoginAPI($activationBlockObj->apiKey(), $activationBlockObj->apiSecret(), array('output_format' => 'json'));
        $socialloginData = Mage::helper('sociallogin/Data');
        $socialProfileCheckboxes = explode(',', $this->getSocialProfileCheckboxes());
        if (!is_array($socialProfileCheckboxes) || !isset($profiledata->ID)) {
            return;
        }
        // update basic profile data if option is selected
        if (in_array('basic', $socialProfileCheckboxes)) {
            $data = array();
            $data['user_id'] = $entityid;
            $data['loginradius_id'] = isset($profiledata->ID) ? $profiledata->ID : '';
            $data['provider'] = isset($profiledata->Provider) ? $profiledata->Provider : '';
            $data['prefix'] = isset($profiledata->Prefix) ? $profiledata->Prefix : '';
            $data['first_name'] = isset($profiledata->FirstName) ? $profiledata->FirstName : '';
            $data['middle_name'] = isset($profiledata->MiddleName) ? $profiledata->MiddleName : '';
            $data['last_name'] = isset($profiledata->LastName) ? $profiledata->LastName : '';
            $data['suffix'] = isset($profiledata->Suffix) ? $profiledata->Suffix : '';
            $data['full_name'] = isset($profiledata->FullName) ? $profiledata->FullName : '';
            $data['nick_name'] = isset($profiledata->NickName) ? $profiledata->NickName : '';
            $data['profile_name'] = isset($profiledata->ProfileName) ? $profiledata->ProfileName : '';
            $data['birth_date'] = isset($profiledata->BirthDate) ? $profiledata->BirthDate : '';
            $data['gender'] = isset($profiledata->Gender) ? $profiledata->Gender : '';
            $data['country_code'] = isset($profiledata->Country->Code) ? $profiledata->Country->Code : '';
            $data['country_name'] = isset($profiledata->Country->Name) ? $profiledata->Country->Name : '';
            $data['thumbnail_image_url'] = isset($profiledata->ThumbnailImageUrl) ? $profiledata->ThumbnailImageUrl : '';
            $data['image_url'] = isset($profiledata->ImageUrl) ? $profiledata->ImageUrl : '';
            $data['local_country'] = isset($profiledata->LocalCountry) ? $profiledata->LocalCountry : '';
            $data['profile_country'] = isset($profiledata->ProfileCountry) ? $profiledata->ProfileCountry : '';
            if ($update) {
                $this->deleteFromLoginRadiusTable("basic_profile_data", array('user_id = ?' => $entityid));
            }
            $socialloginData->SocialLoginInsert("lr_basic_profile_data", $data);

            // update emails
            if (count($profiledata->Email) > 0) {
                if ($update) {
                    // delete old emails
                    $this->deleteFromLoginRadiusTable("emails", array('user_id = ?' => $entityid));
                }
                foreach ($profiledata->Email as $lrEmail) {
                    $data = array();
                    $data['user_id'] = $entityid;
                    $data['email_type'] = isset($lrEmail->Type) ? $lrEmail->Type : '';
                    $data['email'] = isset($lrEmail->Value) ? $lrEmail->Value : '';
                    $socialloginData->SocialLoginInsert('lr_emails', $data);
                }
            }
        }
        // update extended location data if option is selected
        if (in_array('ex_location', $socialProfileCheckboxes)) {
            $data = array();
            $data['user_id'] = $entityid;
            $data['main_address'] = $data['state'] = $data['city'] = '';
            if (isset($profiledata->Addresses) && is_array($profiledata->Addresses) && count($profiledata->Addresses) > 0) {
                $data['main_address'] = (isset($profiledata->Addresses[0]->Address1) ? $profiledata->Addresses[0]->Address1.' ' : '');
                $data['main_address'] .= (isset($profiledata->Addresses[0]->Address2) ? $profiledata->Addresses[0]->Address2 : '');
                $data['state'] = isset($profiledata->Addresses[0]->State) ? $profiledata->Addresses[0]->State : $profiledata->State;
                $data['city'] = isset($profiledata->Addresses[0]->City) ? $profiledata->Addresses[0]->City : $profiledata->City;
            }
            $data['hometown'] = isset($profiledata->HomeTown) ? $profiledata->HomeTown : '';
            $data['local_city'] = isset($profiledata->LocalCity) ? $profiledata->LocalCity : '';
            $data['profile_city'] = isset($profiledata->ProfileCity) ? $profiledata->ProfileCity : '';
            $data['profile_url'] = isset($profiledata->ProfileUrl) ? $profiledata->ProfileUrl : '';
            $data['local_language'] = isset($profiledata->LocalLanguage) ? $profiledata->LocalLanguage : '';
            $data['language'] = isset($profiledata->Language) ? $profiledata->Language : '';
            if ($update) {
                $this->deleteFromLoginRadiusTable("extended_location_data", array('user_id = ?' => $entityid));
            }
            $socialloginData->SocialLoginInsert("lr_extended_location_data", $data);
        }
        // update extended profile data if option is selected
        if (in_array('ex_profile', $socialProfileCheckboxes)) {
            
            $data = array();
            $data['user_id'] = $entityid;
            $data['total_logins'] = isset($profiledata->NoOfLogins) ? $profiledata->NoOfLogins : '';
            $data['website'] = isset($profiledata->Website) ? $profiledata->Website : '';
            $data['favicon'] = isset($profiledata->Favicon) ? $profiledata->Favicon : '';
            $data['industry'] = isset($profiledata->Industry) ? $profiledata->Industry : '';
            $data['about'] = isset($profiledata->About) ? $profiledata->About : '';
            $data['timezone'] = isset($profiledata->TimeZone) ? $profiledata->TimeZone : '';
            $data['verified'] = isset($profiledata->Verified) ? $profiledata->Verified : '';
            $data['last_profile_update'] = isset($profiledata->UpdatedTime) ? $profiledata->UpdatedTime : '';
            $data['created'] = isset($profiledata->Created) ? $profiledata->Created : '';
            $data['relationship_status'] = isset($profiledata->RelationshipStatus) ? $profiledata->RelationshipStatus : '';
            $data['quote'] = isset($profiledata->Quote) ? $profiledata->Quote : '';
            $data['interested_in'] = is_array($profiledata->InterestedIn) ? implode(', ', $profiledata->InterestedIn) : $profiledata->InterestedIn;
            $data['interests'] = isset($profiledata->Interests) ? $profiledata->Interests : '';
            $data['religion'] = isset($profiledata->Religion) ? $profiledata->Religion : '';
            $data['political_view'] = isset($profiledata->Political) ? $profiledata->Political : '';
            $data['https_image_url'] = isset($profiledata->HttpsImageUrl) ? $profiledata->HttpsImageUrl : '';
            $data['followers_count'] = isset($profiledata->FollowersCount) ? $profiledata->FollowersCount : '';
            $data['friends_count'] = isset($profiledata->FriendsCount) ? $profiledata->FriendsCount : '';
            $data['is_geo_enabled'] = isset($profiledata->IsGeoEnabled) ? $profiledata->IsGeoEnabled : '';
            $data['total_status_count'] = isset($profiledata->TotalStatusesCount) ? $profiledata->TotalStatusesCount : '';
            $data['number_of_recommenders'] = isset($profiledata->NumRecommenders) ? $profiledata->NumRecommenders : '';
            $data['honors'] = isset($profiledata->Honors) ? $profiledata->Honors : '';
            $data['associations'] = isset($profiledata->Associations) ? $profiledata->Associations : '';
            $data['hirable'] = isset($profiledata->Hireable) ? $profiledata->Hireable : '';
            $data['repository_url'] = isset($profiledata->RepositoryUrl) ? $profiledata->RepositoryUrl : '';
            $data['age'] = isset($profiledata->Age) ? $profiledata->Age : '';
            $data['professional_headline'] = isset($profiledata->ProfessionalHeadline) ? $profiledata->ProfessionalHeadline : '';
            $data['provider_access_token'] = isset($profiledata->ProviderAccessCredential->AccessToken) ? $profiledata->ProviderAccessCredential->AccessToken : '';
            $data['provider_token_secret'] = isset($profiledata->ProviderAccessCredential->TokenSecret) ? $profiledata->ProviderAccessCredential->TokenSecret : '';
            if ($update) {
                $this->deleteFromLoginRadiusTable("extended_profile_data", array('user_id = ?' => $entityid));
            }
            $socialloginData->SocialLoginInsert("lr_extended_profile_data", $data);
            // positions
            if (is_array($profiledata->Positions) && count($profiledata->Positions) > 0) {
                $companyResult = $socialloginData->loginRadiusRead('lr_positions', 'get company ids', array($entityid), true);
                $companyIdsArray = $companyResult->fetchAll();
                $companyIds = array();
                foreach ($companyIdsArray as $arr) {
                    $companyIds[] = $arr['company'];
                }
                // delete the companies matching the ids
                $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_write');
                try {
                    $loginRadiusConn->query('delete from ' . $socialloginData->getMazeTable("lr_companies") . ' where id in (' . implode(',', $companyIds) . ')');
                } catch (Exception $e) {
                    
                }
                if ($update) {
                    $this->deleteFromLoginRadiusTable('positions', array('user_id = ?' => $entityid));
                }
                foreach ($profiledata->Positions as $lrPosition) {
                    // companies
                    if (isset($lrPosition->Company)) {
                        $temp = array();
                        $temp['id'] = null;
                        $temp['company_name'] = isset($lrPosition->Company->Name) ? $lrPosition->Company->Name : '';
                        $temp['company_type'] = isset($lrPosition->Company->Type) ? $lrPosition->Company->Type : '';
                        $temp['industry'] = isset($lrPosition->Company->Industry) ? $lrPosition->Company->Industry : '';
                        $tempId = $socialloginData->SocialLoginInsert('lr_companies', $temp);
                    }
                    // positions
                    $data = array();
                    $data['user_id'] = $entityid;
                    $data['position'] = isset($lrPosition->Position) ? $lrPosition->Position : '';
                    $data['summary'] = isset($lrPosition->Summary) ? $lrPosition->Summary : '';
                    $data['start_date'] = isset($lrPosition->StartDate) ? $lrPosition->StartDate : '';
                    $data['end_date'] = isset($lrPosition->EndDate) ? $lrPosition->EndDate : '';
                    $data['is_current'] = isset($lrPosition->IsCurrent) ? $lrPosition->IsCurrent : '';
                    $data['company'] = isset($tempId) ? $tempId : null;
                    $data['location'] = isset($lrPosition->Location) ? $lrPosition->Location : '';
                    $socialloginData->SocialLoginInsert('lr_positions', $data);
                }
            }
            // education
            if (is_array($profiledata->Educations) && count($profiledata->Educations) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('education', array('user_id = ?' => $entityid));
                }
                $keysArray = array("School", "year", "type", "notes", "activities", "degree", "fieldofstudy", "StartDate", "EndDate");
                $this->socialLoginInsertArray($entityid, 'education', $keysArray, $profiledata->Educations);
            }
            // phone numbers
            if (is_array($profiledata->PhoneNumbers) && count($profiledata->PhoneNumbers) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('phone_numbers', array('user_id = ?' => $entityid));
                }
                $keysArray = array("PhoneType", "PhoneNumber");
                $this->socialLoginInsertArray($entityid, 'phone_numbers', $keysArray, $profiledata->PhoneNumbers);
            }
            // IM Accounts
            if (is_array($profiledata->IMAccounts) && count($profiledata->IMAccounts) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('IMaccounts', array('user_id = ?' => $entityid));
                }
                $keysArray = array("AccountType", "AccountName");
                $this->socialLoginInsertArray($entityid, 'IMaccounts', $keysArray, $profiledata->IMAccounts);
            }
            // Addresses
            if (is_array($profiledata->Addresses) && count($profiledata->Addresses) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('addresses', array('user_id = ?' => $entityid));
                }
                $keysArray = array("Type", "Address1", "Address2", "City", "State", "PostalCode", "Region");
                $this->socialLoginInsertArray($entityid, 'addresses', $keysArray, $profiledata->Addresses);
            }
            // Sports
            if (is_array($profiledata->Sports) && count($profiledata->Sports) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('sports', array('user_id = ?' => $entityid));
                }
                $keysArray = array("Id", "Name");
                $this->socialLoginInsertArray($entityid, 'sports', $keysArray, $profiledata->Sports);
            }
            // Inspirational People
            if (is_array($profiledata->InspirationalPeople) && count($profiledata->InspirationalPeople) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('inspirational_people', array('user_id = ?' => $entityid));
                }
                $keysArray = array("Id", "Name");
                $this->socialLoginInsertArray($entityid, 'inspirational_people', $keysArray, $profiledata->InspirationalPeople);
            }
            // Skills
            if (is_array($profiledata->Skills) && count($profiledata->Skills) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('skills', array('user_id = ?' => $entityid));
                }
                $keysArray = array("Id", "Name");
                $this->socialLoginInsertArray($entityid, 'skills', $keysArray, $profiledata->Skills);
            }
            // Current Status
            if (is_array($profiledata->CurrentStatus) && count($profiledata->CurrentStatus) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('current_status', array('user_id = ?' => $entityid));
                }

                $keysArray = array("Id", "Text", "Source", "CreatedDate");
                $this->socialLoginInsertArray($entityid, 'current_status', $keysArray, $profiledata->CurrentStatus);
            }
            // Certifications
            if (is_array($profiledata->Certifications) && count($profiledata->Certifications) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('certifications', array('user_id = ?' => $entityid));
                }
                $keysArray = array("Id", "Name", "Authority", "Number", "StartDate", "EndDate");
                $this->socialLoginInsertArray($entityid, 'courses', $keysArray, $profiledata->Certifications);
            }
            // Courses
            if (is_array($profiledata->Courses) && count($profiledata->Courses) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('courses', array('user_id = ?' => $entityid));
                }
                $keysArray = array("Id", "Name", "Number");
                $this->socialLoginInsertArray($entityid, 'courses', $keysArray, $profiledata->Courses);
            }
            // Volunteer
            if (is_array($profiledata->Volunteer) && count($profiledata->Volunteer) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('volunteer', array('user_id = ?' => $entityid));
                }
                $keysArray = array("Id", "Role", "Organization", "Cause");
                $this->socialLoginInsertArray($entityid, 'volunteer', $keysArray, $profiledata->Volunteer);
            }
            // Recommendations received
            if (is_array($profiledata->RecommendationsReceived) && count($profiledata->RecommendationsReceived) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('recommendations_received', array('user_id = ?' => $entityid));
                }
                $keysArray = array("Id", "RecommendationType", "RecommendationText", "Recommender");
                $this->socialLoginInsertArray($entityid, 'recommendations_received', $keysArray, $profiledata->RecommendationsReceived);
            }
            // Languages
            if (is_array($profiledata->Languages) && count($profiledata->Languages) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('languages', array('user_id = ?' => $entityid));
                }
                $keysArray = array("Id", "Name");
                $this->socialLoginInsertArray($entityid, 'languages', $keysArray, $profiledata->Languages);
            }
            // Patents
            if (is_array($profiledata->Patents) && count($profiledata->Patents) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('patents', array('user_id = ?' => $entityid));
                }
                $keysArray = array('Id', 'Title', 'Date');
                $this->socialLoginInsertArray($entityid, 'patents', $keysArray, $profiledata->Patents);
            }
            // FavoriteThings
            if (isset($profiledata->FavoriteThings) && is_array($profiledata->FavoriteThings) && count($profiledata->FavoriteThings) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('favorites', array('user_id = ?' => $entityid));
                }
                $keysArray = array('Id', 'Name', 'Type');
                $this->socialLoginInsertArray($entityid, 'favorites', $keysArray, $profiledata->FavoriteThings);
            }
        }
        // insert contacts if option is selected
        if (in_array($profiledata->Provider, array('twitter', 'facebook', 'linkedin', 'google', 'yahoo')) && in_array('contacts', $socialProfileCheckboxes)) {
            try {
                $contacts = $loginradiusSDK->getContacts($profiledata->accesstoken);
            } catch (LoginRadiusSDK\LoginRadiusException $e) {
                
            }
        
            if (isset($contacts->Data) && is_array($contacts->Data) && count($contacts->Data) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('contacts', array('user_id = ?' => $entityid));
                }
                foreach ($contacts->Data as $contact) {
                    // collect social IDs of the contacts
                    if ($profiledata->Provider == 'yahoo' || $profiledata->Provider == 'google') {
                        $this->loginRadiusContactIds[] = $contact->EmailID;
                    } else {
                        $this->loginRadiusContactIds[] = $contact->ID;
                    }
                }
                $keysArray = array("Provider", "Name", "EmailID", "PhoneNumber", "ID", "ProfileUrl", "ImageUrl", "Status", "Industry", "Country", "Gender");
                $this->socialLoginInsertArray($entityid, 'contacts', $keysArray, $contacts->Data, $provider = $profiledata->Provider);
            }
        }
        // insert facebook events if option is selected
        if ($profiledata->Provider == 'facebook' && in_array('events', $socialProfileCheckboxes)) {
            try {
                $events = $loginradiusSDK->getEvents($profiledata->accesstoken);
            } catch (LoginRadiusSDK\LoginRadiusException $e) {
                
            }
            
            if (is_array($events) && count($events) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('facebook_events', array('user_id = ?' => $entityid));
                }
                $keysArray = array("ID", "Name", "StartTime", "RsvpStatus", "Location");
                $this->socialLoginInsertArray($entityid, 'facebook_events', $keysArray, $events);
            }
        }
        // insert facebook events if option is selected
        if ($profiledata->Provider == 'facebook' && in_array('likes', $socialProfileCheckboxes)) {
            try {
                $likes = $loginradiusSDK->getLikes($profiledata->accesstoken);
            } catch (LoginRadiusSDK\LoginRadiusException $e) {
                
            }
            
            if (is_array($likes) && count($likes) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('facebook_likes', array('user_id = ?' => $entityid));
                }
                $keysArray = array("ID", "Name", "Category", "DateTime", "Website", "Description");
                $this->socialLoginInsertArray($entityid, 'facebook_likes', $keysArray, $likes);
            }
        }
        // insert posts if option is selected
        if ($profiledata->Provider == 'facebook' && in_array('posts', $socialProfileCheckboxes)) {
            try {
                $posts = $loginradiusSDK->getPosts($profiledata->accesstoken);
            } catch (LoginRadiusSDK\LoginRadiusException $e) {
                
            }
            if (is_array($posts) && count($posts) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('facebook_posts', array('user_id = ?' => $entityid));
                }
                $keysArray = array("ID", "Name", "Title", "StartTime", "UpdateTime", "Message", "Place", "Picture", "Likes", "Share");
                $this->socialLoginInsertArray($entityid, 'facebook_posts', $keysArray, $posts);
            }
        }
        
        // insert LinkedIn Companies if option is selected
        if (in_array($profiledata->Provider, array('facebook', 'linkedin')) && in_array('linkedin_companies', $socialProfileCheckboxes)) {
            try {
                $linkedInCompanies = $loginradiusSDK->getFollowedCompanies($profiledata->accesstoken);
            } catch (LoginRadiusSDK\LoginRadiusException $e) {
                
            }            
            if (isset($linkedInCompanies) && is_array($linkedInCompanies) && count($linkedInCompanies) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('linkedin_companies', array('user_id = ?' => $entityid));
                }
                $keysArray = array("ID", "Name");
                $this->socialLoginInsertArray($entityid, 'linkedin_companies', $keysArray, $linkedInCompanies);
            }
        }
        // insert status if option is selected
        if (in_array($profiledata->Provider, array('twitter', 'facebook', 'linkedin')) && in_array('status', $socialProfileCheckboxes)) {
            try {
                $status = $loginradiusSDK->getStatus($profiledata->accesstoken);
            } catch (LoginRadiusSDK\LoginRadiusException $e) {
                
            }
            
            if (isset($status) && is_array($status) && count($status) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('status', array('user_id = ?' => $entityid));
                }
                $keysArray = array('Id', 'Text', 'DateTime', 'Likes', 'Place', 'Source', 'ImageUrl', 'LinkUrl');
                $this->socialLoginInsertArray($entityid, 'status', $keysArray, $status, $profiledata->Provider);
            }
        }
        // insert mentions if option is selected
        if ($profiledata->Provider == 'twitter' && in_array('mentions', $socialProfileCheckboxes)) {
            try {
                $mentions = $loginradiusSDK->getMentions($profiledata->accesstoken);
            } catch (LoginRadiusSDK\LoginRadiusException $e) {
                
            }
            if (isset($mentions) && is_array($mentions) && count($mentions) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('twitter_mentions', array('user_id = ?' => $entityid));
                }
                $keysArray = array('Id', 'Text', 'DateTime', 'Likes', 'Place', 'Source', 'ImageUrl', 'LinkUrl', 'Name');
                $this->socialLoginInsertArray($entityid, 'twitter_mentions', $keysArray, $mentions);
            }
        }
        // insert groups if option is selected
        if (in_array($profiledata->Provider, array('facebook', 'linkedin')) && in_array('groups', $socialProfileCheckboxes)) {
            try {
                $groups = $loginradiusSDK->getGroups($profiledata->accesstoken);
            } catch (LoginRadiusSDK\LoginRadiusException $e) {
                
            }
            if (isset($groups) && is_array($groups) && count($groups) > 0) {
                if ($update) {
                    $this->deleteFromLoginRadiusTable('groups', array('user_id = ?' => $entityid));
                }
                $keysArray = array('ID', 'Name');
                $this->socialLoginInsertArray($entityid, 'groups', $keysArray, $groups, $profiledata->Provider);
            }
        }

        return;
    }

    public function deleteFromLoginRadiusTable($table, $condition) {
        $loginRadiusConn = Mage::getSingleton('core/resource')->getConnection('core_write');
        try {
            // delete query magento way
            $loginRadiusConn->delete(
                    Mage::getSingleton('core/resource')->getTableName('lr_' . $table), $condition
            );
        } catch (Exception $e) {
            
        }
    }

    public function socialLoginInsertArray($userId, $tableName, $keysArray, $bulkData, $provider = '') {
        $socialloginData = Mage::helper('sociallogin/Data');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->beginTransaction();
        $lrTable = 'lr_' . $tableName;
        $sociallogin = $socialloginData->getMazeTable($lrTable);

        $query = "INSERT INTO " . $sociallogin . " VALUES ";

        foreach ($bulkData as $rowData) {
            $query .= "(" . $userId;
            $data = $this->manageArray($rowData, $keysArray);
            if (!empty($provider)) {
                $data['Provider'] = $provider;
            }
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $value = '';
                }

                $query .= ', "' . $value . '"';
            }

            $query .= "),";
        }
        $query = substr($query, 0, -1);

        try {
            $connection->query($query);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $connection->commit();
    }

    public function manageArray($profileObject, $keysArray) {
        $data = array();
        foreach ($keysArray as $key) {
            $data[$key] = isset($profileObject->$key) ? $profileObject->$key : '';
            $data[$key] = str_replace('"', "", $data[$key]);
        }

        return $data;
    }

}
