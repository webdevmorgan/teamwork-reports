<?php
class QuickbooksAPI {

    private $oauth_consumer_key;
    private $oauth_consumer_secret;
    private $quickbooks_oauth_url;
    private $db_quickbooks;
    private $the_username;
    private $the_tenant;
    private $encryption_key;
    private $quickbooks_success_url;
    private $IntuitAnywhere;
    private $quickbooks_CompanyInfo;

    function __construct(){
        $this->oauth_consumer_key = Config::get('quickbook.oauth_consumer_key');
        $this->oauth_consumer_secret = Config::get('quickbook.oauth_consumer_secret');
        $this->quickbooks_oauth_url = Config::get('quickbook.quickbooks_oauth_url');
        $this->the_username = Config::get('quickbook.the_username');
        $this->the_tenant =  Config::get('quickbook.the_tenant');
        $this->db_quickbooks = Config::get('quickbook.db_quickbooks');
        $this->encryption_key = Config::get('quickbook.encryption_key');
    }

    private function execute() {
        if (!QuickBooks_Utilities::initialized($this->db_quickbooks))
        {
            QuickBooks_Utilities::initialize($this->db_quickbooks);
        }

        $this->IntuitAnywhere = new QuickBooks_IPP_IntuitAnywhere($this->db_quickbooks, $this->encryption_key, $this->oauth_consumer_key, $this->oauth_consumer_secret, $this->quickbooks_oauth_url, $this->quickbooks_success_url);

// Are they connected to QuickBooks right now?
        if ($this->IntuitAnywhere->check($this->the_username, $this->the_tenant) and
            $this->IntuitAnywhere->test($this->the_username, $this->the_tenant))
        {
            // Yes, they are
            $quickbooks_is_connected = true;

            // Set up the IPP instance
            $IPP = new QuickBooks_IPP($this->db_quickbooks);

            // Get our OAuth credentials from the database
            $creds = $this->IntuitAnywhere->load($this->the_username, $this->the_tenant);

            // Tell the framework to load some data from the OAuth store
            $IPP->authMode(
                QuickBooks_IPP::AUTHMODE_OAUTH,
                $this->the_username,
                $creds);

            $realm = $creds['qb_realm'];

            // Load the OAuth information from the database
            $Context = $IPP->context();

            // Get some company info
            $CompanyInfoService = new QuickBooks_IPP_Service_CompanyInfo();
            $this->quickbooks_CompanyInfo = $CompanyInfoService->get($Context, $realm);
        }
        else
        {
            // No, they are not
            $quickbooks_is_connected = false;
        }

        return $quickbooks_is_connected;
    }

    public function checkConnection() {
        $conn = $this->execute();
        return $conn;
    }

    private function oauth() {
        // Try to handle the OAuth request
        if ($this->IntuitAnywhere->handle($this->the_username, $this->the_tenant))
        {
            ; // The user has been connected, and will be redirected to $that_url automatically.
        }
        else
        {
            // If this happens, something went wrong with the OAuth handshake
            die('Oh no, something bad happened: ' . $this->IntuitAnywhere->errorNumber() . ': ' . $this->IntuitAnywhere->errorMessage());
        }
    }

    public function checkOAuth() {
        $this->execute();
        $this->oauth();
    }

    public function getCompanyInfo() {
        return $this->quickbooks_CompanyInfo;
    }

    public function getIntuit() {
        return $this->IntuitAnywhere;
    }

}