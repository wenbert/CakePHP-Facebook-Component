<?php
App::uses('Component', 'Controller');
App::import('Vendor', 'facebook', array('file' => 'facebook/facebook.php'));

//https://github.com/wenbert/CakePHP-Facebook-Component
class FacebookComponent extends Component {
    private $facebook;
    private $userProfile;
    private $user;
    private $loginUrl;
    private $logoutUrl;

    var $settings = array();

    public function __construct(ComponentCollection $collection, $settings = array()) {
        $this->settings['appId'] = isset($settings['appId']) ? $settings['appId'] : null;
        $this->settings['secret'] = isset($settings['secret']) ? $settings['secret'] : null;
        $this->settings['cookie'] = isset($settings['cookie']) ? $settings['cookie'] : null;
        $this->settings['fileUpload'] = isset($settings['fileUpload']) ? $settings['fileUpload'] : null;
        $this->settings['canvas'] = isset($settings['canvas']) ? $settings['canvas'] : null;
        $this->settings['fbconnect'] = isset($settings['connect']) ? $settings['fbconnect'] : null;
        $this->settings['display'] = isset($settings['display']) ? $settings['display'] : null;
        $this->settings['scope'] = isset($settings['scope']) ? $settings['scope'] : null;

        //This is the URL of the Facebook Page Tab
        //Eg: https://www.facebook.com/pages/Your-Test-Page/1234?id=1234&sk=app_1234567
        $this->settings['redirect_uri'] = isset($settings['redirect_uri']) ? $settings['redirect_uri'] : null;
    }

/**
 * Initialize the component
 * @param $controller Controller Object
 * @return void
 */
    public function initialize(Controller $controller) {
        $config = array();
        $config['appId'] =  $this->settings['appId'];
        $config['secret'] = $this->settings['secret'];
        $config['cookie'] = $this->settings['cookie'];
        $config['fileUpload'] = $this->settings['fileUpload'];

        //make fb object accessible to all actions in this controller
        $this->facebook = new Facebook($config);
        $this->user = $this->facebook->getUser();
        if ($this->user) {
            try {
                // Proceed knowing you have a logged in user who's authenticated.
                $this->userProfile = $this->facebook->api('/me');
            } catch (FacebookApiException $e) {
                error_log($e);
                $this->user = null;
            }
        }

        $this->loginUrl = '';
        $this->logoutUrl = '';
        if ($this->user) {
            $this->logoutUrl = $this->facebook->getLogoutUrl();
        } else {
            $this->loginUrl = $this->facebook->getLoginUrl(
                array(
                    'canvas'    => $this->settings['canvas'],
                    'fbconnect' => $this->settings['fbconnect'],
                    'display'   => $this->settings['display'], 
                    'scope'     => $this->settings['scope'],
                    'redirect_uri' => $this->settings['redirect_uri'],
                )
            );
        }

        $controller->userProfile = $this->userProfile;
        $controller->loginUrl = $this->loginUrl;
        $controller->logoutUrl = $this->logoutUrl;
        $controller->insideFacebook = false;
        if(isset($_REQUEST['signed_request'])) {
            $controller->hasLiked = $this->_hasLiked($_REQUEST['signed_request']);
            $controller->signed_request = $_REQUEST['signed_request'];
            $controller->insideFacebook = true;
        } else {
            $controller->hasLiked = false;
            $controller->signed_request = null;
        }
    }


    /**
     * Checks if a User has Liked the page
     * 
     * @param $signed_request from Facebook $_REQUEST['signed_request']
     * @return boolean - true if user has Linked the page, otherwise false
     * @link https://developers.facebook.com/docs/authentication/signed_request/
     */
    private function _hasLiked($signed_request) {
        $encoded_sig = null;
        $payload = null;
        if($signed_request) {
            list($encoded_sig, $payload) = explode('.', $signed_request, 2);
            $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
            $signed_request = json_decode(base64_decode(strtr($payload, '-_', '+/'), true));    
        } else {
            return false;
        }
        

        // debug($signed_request);
        
        if($signed_request->page->liked) {
            return true;
        } else {
            return false;
        }
    }

    private function _isInsideFacebook($signed_request = null) {
        if($signed_request) {
            return true;
        } else {
            return false;
        }
    }
}
