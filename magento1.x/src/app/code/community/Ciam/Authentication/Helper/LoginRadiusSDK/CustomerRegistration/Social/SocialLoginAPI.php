<?php

/**
 * @link                : http://www.loginradius.com
 * @category            : CustomerRegistration
 * @package             : SocialLoginAPI
 * @author              : LoginRadius Team
 * @license             : https://opensource.org/licenses/MIT
 */

namespace LoginRadiusSDK\CustomerRegistration\Social;

use LoginRadiusSDK\Utility\Functions;
use LoginRadiusSDK\LoginRadiusException;

/**
 * Class for SocialLoginAPI.
 *
 * This is the main class to communicate with LoginRadius Unified Social API.
 */
class SocialLoginAPI
{

    /**
     *
     * @param type $apikey
     * @param type $apisecret
     * @param type $customize_options
     */
    function __construct($apikey = '', $apisecret = '', $customize_options = array())
    {
        new Functions($apikey, $apisecret, $customize_options);
    }

    /**
     *
     * LoginRadius function - Fetch LoginRadius access token after authentication. It will be valid for the specific duration of time specified in the response.
     * @param string LoginRadius API Secret
     * @param string LoginRadius API token
     *
     * @return mixed string|object LoginRadius access token.
     *
     * try{
     *   $accesstoken = $loginradiusObject->exchangeAccessToken($request_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function exchangeAccessToken($request_token, $fields = '*')
    {
        return $this->apiClientHandler('access_token', false, array("token" => $request_token, "secret" => Functions::getApiSecret(), 'fields' => $fields));
    }

    /**
     * LoginRadius function - To fetch social profile data from the user's social account after authentication. The social profile will be retrieved via oAuth and OpenID protocols. The data is normalized into LoginRadius' standard data format.
     *
     * @param string $access_token LoginRadius access token
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object User profile data.
     *
     * try{
     *   $userProfileData = $loginradiusObject->getUserProfiledata($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getUserProfiledata($access_token, $raw = false, $fields = '*')
    {
        return $this->apiClientHandler('userprofile', $raw, array("access_token" => $access_token, 'fields' => $fields));
    }
    
    /**
     * LoginRadius function - To fetch custom fileds of raas. The social profile will be retrieved via oAuth and OpenID protocols. The data is normalized into LoginRadius' standard data format.
     *
     * @param string $apiKey LoginRadius api key
     * @param string $secret LoginRadius secret key
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object custom fields of raas.
     *
     * try{
     *   $customFields = $loginradiusObject->getUserCustomFields($apiKey, $secret, $raw = false);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getUserCustomFields($apiKey, $secret, $raw = false, $fields = '*') {
        return $this->apiClientHandler('userprofile/fields', $raw, array('apikey' => $apiKey, 'apisecret' => $secret, 'fields' => $fields));
    }
	
    /**
     * LoginRadius function - To get the Albums data from the user's social account. The data will be normalized into LoginRadius' data format.
     *
     * @param string $access_token LoginRadius access token
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object User's albums data.
     *
     * try{
     *   $photoAlbums = $loginradiusObject->getPhotoAlbums($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getPhotoAlbums($access_token, $raw = false, $fields = '*')
    {
        return $this->apiClientHandler('album', $raw, array("access_token" => $access_token, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To fetch photo data from the user's social account. The data will be normalized into LoginRadius' data format.
     *
     * @param string $access_token LoginRadius access token
     * @param string $album_id ID of the album to fetch photos from
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object User's photo data.
     *
     * try{
     *   $photos = $loginradiusObject->getPhotos($access_token, $albumId);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getPhotos($access_token, $album_id, $raw = false, $fields = '*')
    {
        return $this->apiClientHandler("photo", $raw, array("access_token" => $access_token, "albumid" => $album_id, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To fetch check-ins data from the user's social account. The data will be normalized into LoginRadius' data format.
     *
     * @param string $access_token LoginRadius access token
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object User's check-ins.
     *
     * try{
     *   $checkins = $loginradiusObject->getCheckins($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getCheckins($access_token, $raw = false, $fields = '*')
    {
        return $this->apiClientHandler('checkin', $raw, array("access_token" => $access_token, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To fetch user's audio files data from the user's social account. The data will be normalized into LoginRadius' data format.
     *
     * @param string $access_token LoginRadius access token
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object User's audio files data.
     *
     * try{
     *   $audio = $loginradiusObject->getAudio($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getAudio($access_token, $raw = false, $fields = '*')
    {
        return $this->apiClientHandler("audio", $raw, array("access_token" => $access_token, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To fetch user's contacts/friends/connections data from the user's social account. The data will normalized into LoginRadius' data format.
     *
     * @param string $access_token LoginRadius access token
     * @param integer $next_cursor Offset to start fetching contacts from
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object User's contacts/friends/followers.
     *
     * try{
     *   $contacts = $loginradiusObject->getContacts($access_token, $nextCursor);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getContacts($access_token, $next_cursor = '', $raw = false, $fields = '*')
    {
        return $this->apiClientHandler("contact", $raw, array("access_token" => $access_token, "nextcursor" => $next_cursor, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To get mention data from the user's social account. The data will be normalized into LoginRadius' data format.
     *
     * @param string $access_token LoginRadius access token
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object User's twitter mentions.
     *
     * try{
     *   $mentions = $loginradiusObject->getMentions($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getMentions($access_token, $raw = false, $fields = '*')
    {
        return $this->apiClientHandler("mention", $raw, array("access_token" => $access_token, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To fetch information of the people, user is following on Twitter.
     *
     * @param string $access_token LoginRadius access token
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object Information of the people, user is following.
     *
     * try{
     *   $following = $loginradiusObject->getFollowing($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getFollowing($access_token, $raw = false, $fields = '*')
    {
        return $this->apiClientHandler("following", $raw, array("access_token" => $access_token, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To get the event data from the user's social account. The data will be normalized into LoginRadius' data format.
     *
     * @param string $access_token LoginRadius access token
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object User's event data.
     *
     * try{
     *   $events = $loginradiusObject->getEvents($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getEvents($access_token, $raw = false, $fields = '*')
    {
        return $this->apiClientHandler("event", $raw, array("access_token" => $access_token, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To get posted messages from the user's social account. The data will be normalized into LoginRadius' data format.
     *
     * @param string $access_token LoginRadius access token
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object User's posted messages.
     *
     * try{
     *   $posts = $loginradiusObject->getPosts($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getPosts($access_token, $raw = false, $fields = '*')
    {
        return $this->apiClientHandler("post", $raw, array("access_token" => $access_token, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To get the followed company's data in the user's social account. The data will be normalized into LoginRadius' data format.
     *
     * @param string $access_token LoginRadius access token
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object Companies followed by user.
     *
     * try{
     *   $companies = $loginradiusObject->getFollowedCompanies($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getFollowedCompanies($access_token, $raw = false, $fields = '*')
    {
        return $this->apiClientHandler("company", $raw, array("access_token" => $access_token, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To get group data from the user's social account. The data will be normalized into LoginRadius' data format.
     *
     * @param string $access_token LoginRadius access token
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object Group data.
     *
     * try{
     *   $groups = $loginradiusObject->getGroups($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getGroups($access_token, $raw = false, $fields = '*')
    {
        return $this->apiClientHandler("group", $raw, array("access_token" => $access_token, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To get the status messages from the user's social account. The data will be normalized into LoginRadius' data format.
     *
     * @param string $access_token LoginRadius access token
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object Status messages.
     *
     * try{
     *   $status = $loginradiusObject->getStatus($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getStatus($access_token, $raw = false, $fields = '*')
    {
        return $this->apiClientHandler("status", $raw, array("access_token" => $access_token, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To get videos data from the user's social account. The data will be normalized into LoginRadius' data format.
     *
     * @param string $access_token LoginRadius access token
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object Videos data.
     *
     * try{
     *   $videos = $loginradiusObject->getVideos($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getVideos($access_token, $raw = false, $fields = '*')
    {
        return $this->apiClientHandler("video", $raw, array("access_token" => $access_token, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To get likes data from the user's social account. The data will be normalized into LoginRadius' data format.
     *
     * @param string $access_token LoginRadius access token
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object likes data.
     *
     * try{
     *   $likes = $loginradiusObject->getLikes($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getLikes($access_token, $raw = false, $fields = '*')
    { 
        return $this->apiClientHandler("like", $raw, array("access_token" => $access_token, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To get the page data from the user's social account. The data will be normalized into LoginRadius' data format.
     *
     * @param string $access_token LoginRadius access token
     * @param string $page_name Page name
     * @param boolean $raw If true, raw data is fetched
     *
     * @return object Page data.
     *
     * try{
     *   $pages = $loginradiusObject->getPages($access_token, $page_name);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function getPages($access_token, $page_name, $raw = false, $fields = '*')
    {
        return $this->apiClientHandler("page", $raw, array("access_token" => $access_token, "pagename" => $page_name, 'fields' => $fields));
    }

    /**
     * LoginRadius function - To update the status on the user's wall.
     *
     * @param string $access_token LoginRadius access token
     * @param string $title Title for status message (Optional).
     * @param string $url A web link of the status message (Optional).
     * @param string $imageurl An image URL of the status message (Optional).
     * @param string $status The status message text (Required).
     * @param string $caption Caption of the status message (Optional).
     * @param string $description Description of the status message (Optional).
     *
     * @return boolean Returns true if successful, false otherwise.
     *
     * try{
     *  $result = $loginradiusObject->postStatus($access_token, $title, $url, $imageurl, $status, $caption, $description);
     * }
     * catch (LoginRadiusException $e){
     *    $e->getMessage();
     *    $e->getErrorResponse();
     * }
     *
     */
    public function postStatus($access_token, $status, $title = '', $url = '', $imageurl = '', $caption = '', $description = '', $fields = '*')
    {
        $data = array(
            'access_token' => $access_token,
            'title' => $title,
            'url' => $url,
            'imageurl' => $imageurl,
            'status' => $status,
            'caption' => $caption,
            'description' => $description,
            'fields' => $fields
        );
        return $this->apiClientHandler("status", false, $data, array('method' => 'POST', 'post_data' => true));
    }

    /**
     * LoginRadius function - Post messages to the user's contacts. After using the Contact API, you can send messages to the retrieved contacts.
     *
     * @param string $access_token LoginRadius access token
     * @param string $to Social ID of the receiver
     * @param string $subject Subject of the message
     * @param string $message Message
     *
     * @return bool True on success, false otherwise
     *
     * try{
     *  $result = $loginradiusObject->sendMessage($access_token, $to, $subject, $message);
     * }
     * catch (LoginRadiusException $e){
     *    $e->getMessage();
     *    $e->getErrorResponse();
     * }
     */
    public function sendMessage($access_token, $to, $subject, $message, $fields = '*')
    {
        $data = array(
            'access_token' => $access_token,
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'fields' => $fields
        );
        return $this->apiClientHandler("message", false, $data, array('method' => 'POST', 'post_data' => true));
    }
    
    /**
     * LoginRadius function - This API is used to validate access_token, check it is valid, expired or active.
     *
     * @param string $access_token LoginRadius access token
     *
     * @return object User profile data.
     *
     * try{
     *   $userProfileData = $loginradiusObject->tokenValidate
	 * ($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function tokenValidate($access_token, $fields = '*')
    {
        return $this->apiClientHandler('access_token/Validate', false, array("key" => Functions::getApiKey(),"secret" => Functions::getApiSecret(),"access_token" => $access_token, 'fields' => $fields));
    }
	
	    /**
     * LoginRadius function - This API is used to invalidate access token, means expiring token. After this API call passed access_token no longer be active and will not accepted by LoginRadius APIs.
     *
     * @param string $access_token LoginRadius access token
     *
     * @return object User profile data.
     *
     * try{
     *   $userProfileData = $loginradiusObject->tokenInvalidate($access_token);
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function tokenInvalidate($access_token, $fields = '*')
    {
        return $this->apiClientHandler('access_token/invalidate', false, array("key" => Functions::getApiKey(),"secret" => Functions::getApiSecret(),"access_token" => $access_token, 'fields' => $fields));
    }


      /**
     * LoginRadius function - This API is used to validate API key and Secret.
     *
     * @return object.
     *
     * try{
     *   $userProfileData = $loginradiusObject->validateKeyandSecret();
     * }
     * catch (LoginRadiusException $e){
     *   $e->getMessage();
     *   $e->getErrorResponse();
     * }
     */
    public function validateKeyandSecret($fields = '*')
    {
         return $this->apiClientHandler('app/validate', false, array("apikey" => rawurlencode(Functions::getApiKey()),"apisecret" => rawurlencode(Functions::getApiSecret()), 'fields' => $fields));
    }

    /**
     * Social API heandler
     *
     * @param type $path
     * @param type $raw_data
     * @param type $query_array
     * @param type $options
     * @return type
     */
    private function apiClientHandler($path, $raw_data, $query_array = array(), $options = array())
    {
        $raw = $raw_data ? '/raw' : '';
        return Functions::apiClient("/api/v2/" . $path . $raw, $query_array, $options);
    }

}
