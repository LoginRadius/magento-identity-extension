<?php

namespace LoginRadius\SocialProfileData\Controller\Adminhtml\Index;

class Index extends \Magento\Customer\Controller\Adminhtml\Index {

    public function execute() {
        $customerId = $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();        
        $block = $resultLayout->getLayout()->getBlock('social.profile.data');

        $block->setBasicProfileData($this->getSocialProfileData($customerId, 'basic_profile_data'))->setUseAjax(true);
        $block->setEmailsData($this->getSocialProfileData($customerId, 'emails'))->setUseAjax(true);
        $block->setSportsData($this->getSocialProfileData($customerId, 'sports'))->setUseAjax(true);
        $block->setAddressesData($this->getSocialProfileData($customerId, 'addresses'))->setUseAjax(true);
        $block->setContactsData($this->getSocialProfileData($customerId, 'contacts'))->setUseAjax(true);
        $block->setExtendedLocationData($this->getSocialProfileData($customerId, 'extended_location_data'))->setUseAjax(true);
        $block->setExtendedProfileData($this->getSocialProfileData($customerId, 'extended_profile_data'))->setUseAjax(true);
        $block->setPositionsData($this->getSocialProfileData($customerId, 'positions'))->setUseAjax(true);
        $block->setCompaniesData($this->getSocialProfileData($customerId, 'companies'))->setUseAjax(true);
        $block->setEducationData($this->getSocialProfileData($customerId, 'education'))->setUseAjax(true);
        $block->setPhoneNumbersData($this->getSocialProfileData($customerId, 'phone_numbers'))->setUseAjax(true);
        $block->setimaccountsData($this->getSocialProfileData($customerId, 'imaccounts'))->setUseAjax(true);
        $block->setTwitterMentionsData($this->getSocialProfileData($customerId, 'twitter_mentions'))->setUseAjax(true);
        $block->setStatusData($this->getSocialProfileData($customerId, 'status'))->setUseAjax(true);
        $block->setLinkedinCompaniesData($this->getSocialProfileData($customerId, 'linkedin_companies'))->setUseAjax(true);
        $block->setGroupsData($this->getSocialProfileData($customerId, 'groups'))->setUseAjax(true);
        $block->setFacebookPostsData($this->getSocialProfileData($customerId, 'facebook_posts'))->setUseAjax(true);
        $block->setFacebookLikessData($this->getSocialProfileData($customerId, 'facebook_likes'))->setUseAjax(true);
        $block->setFacebookEventsData($this->getSocialProfileData($customerId, 'facebook_events'))->setUseAjax(true);
        $block->setFavoritesData($this->getSocialProfileData($customerId, 'favorites'))->setUseAjax(true);
        $block->setPatentsData($this->getSocialProfileData($customerId, 'patents'))->setUseAjax(true);
        $block->setLanguagesData($this->getSocialProfileData($customerId, 'languages'))->setUseAjax(true);
        $block->setRecommendationsReceivedData($this->getSocialProfileData($customerId, 'recommendations_received'))->setUseAjax(true);
        $block->setVolunteerData($this->getSocialProfileData($customerId, 'volunteer'))->setUseAjax(true);
        $block->setCoursesData($this->getSocialProfileData($customerId, 'courses'))->setUseAjax(true);
        $block->setCertificationsData($this->getSocialProfileData($customerId, 'certifications'))->setUseAjax(true);
        $block->setCurrentStatusData($this->getSocialProfileData($customerId, 'current_status'))->setUseAjax(true);
        $block->setSkillsData($this->getSocialProfileData($customerId, 'skills'))->setUseAjax(true);
        $block->setInspirationalPeopleData($this->getSocialProfileData($customerId, 'inspirational_people'))->setUseAjax(true);
      
        return $resultLayout;
    }

    public function getSocialProfileData($customerId, $tableName) {
        /*$colamn = 'id';
        if($tableName != 'companies'){
            $colamn = 'user_'.$colamn;
        }*/
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $ruleTable = $resource->getTableName('lr_' . $tableName);
        $connection = $resource->getConnection();
        $select = $connection->select()->from(['r' => $ruleTable])->where('entity_id=?', $customerId);
        return $connection->fetchAll($select);
    }

}
