<?php
require_once 'libs/simple_html_dom.php';

class GoCard {
    
    private $_cookie_file = 'cookiefile';
    private $_card_num;
    private $_password; 
    private $_results;
    
    /**
     * Creates a new GoCard instance.
     */
    public function __construct($cardnum, $password) {
        $this->_card_num = $cardnum;
        $this->_passord = $password;
    }
    
    /**
     * Logs the user in to the Gocard webiste.
     * @return boolean sucessful login
     */
    public function login() {
        $data = array(
            'cardNum' => $this->_card_num,
            'pass' => $this->_password,
            'cardOps' => 'Display'
        );
        $ch = $this->_prepare_curl("https://www.seqits.com.au/webtix/welcome/welcome.do", $data); 
        $result = $this->_exec_curl($ch, 'login');
        return strpos($result, 'Unable to retrieve') === false;
    }

    /**
     * Get the current GoCard balance.
     * @return string balance in $xx.xx format
     */
    public function get_balance() {
        if(!isset($this->_results['login'])) {
            $this->login();
        }
        
        $dom = str_get_html($this->_results['login']);
        $balance = $dom->find('._results_table td', 1);
        if($balance) {
            return $balance->plaintext;
        }
        $dom->clear();
        return false;
    }

    /**
     * Get the GoCard activity history.
     * @param string $period the period of history to retreive. Can be 'last20' or '-7', '-14', '-30' or '-60' days.
     * @return array of arrays, containing 'time' (in ISO format), 'action', 'location' and 'charge'
     */
    public function get_history($period = 'last20') {
        $data = array(
            'condition_1' => $period,
            'Refresh' => 'Refresh',
        );  
        $ch = $this->_prepare_curl("https://www.seqits.com.au/webtix/cardinfo/history.do", $data); 
        $history = $this->_exec_curl($ch, 'history');

        $dom = str_get_html($history);
        $_results = $dom->find('.results_table tr');
        array_shift($_results); // The first row is the header.
        $histories = array();
        $tz = date_default_timezone_get();
        date_default_timezone_set('Australia/Brisbane');
        foreach($_results as $row) {
            $tds = $row->find('td');
            $time = date('c', strtotime($tds[0]->plaintext));
            $charge = (float)str_replace('&nbsp;', '', trim($tds[3]->plaintext));
            $hist = array(
                'time' => $time,
                'action' => $tds[1]->plaintext,
                'location' => $tds[2]->plaintext,
                'charge' => $charge,
            );
            $histories[] = $hist;
        }
        date_default_timezone_set($tz);
        $dom->clear();
        return $histories;
    }

    /**
     * Log the user out of the GoCard webiste.
     * @return true assumes successful logout.
     */
    public function logout() {
        $ch = $this->_prepare_curl("https://www.seqits.com.au/webtix/welcome/welcome.do?logout=true");
        $logout = $this->_exec_curl($ch);
        $this->_results = array();
        // *Assumes* success.
        return true;
    }

    /**
     * Prepares a cURL object for use with the GoCard website.
     * @param string $url the url to request
     * @param array $postdata array of POST data to send
     * @return cURL
     */
    private function _prepare_curl($url, $postdata = array()) {
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEFILE => $this->_cookie_file,
            CURLOPT_COOKIEJAR => $this->_cookie_file,
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        if(!empty($postdata)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
        }
        return $ch;
    }
    
    /**
     * Exeute a cURL request, and optionally store the result for debugging purposes.
     * @param cURL $ch the cURL object to exec.
     * @param string $tag a tag used for identifying a stored result.
     * return string|boolean the response from the cURL handle, false if cURL err.
     */
    private function _exec_curl($ch, $tag = null) {
        $result = curl_exec($ch);
        if($result && !curl_errno($ch)) {
            if($tag) {
                $this->_results[$tag] = $result;
            }
            curl_close($ch);
            return $result;
        }
        curl_close($ch);
        return false;
    }
}