<?php
namespace Jwpage;

use Goutte\Client;
use Jwpage\GoCard\History;

class GoCard {
    
    protected $cardNumber;
    protected $password;
    protected $loginCrawler;
    protected $client;
    protected $baseUrl;
    
    /**
     * Creates a new GoCard instance.
     */
    public function __construct($cardNumber, $password)
    {
        $this->cardNumber = $cardNumber;
        $this->password   = $password;
        $this->client     = new Client();
        $this->baseUrl    = 'https://gocard.translink.com.au/webtix';
    }

    public function getClient()
    {
        return $this->client;
    }
    
    /**
     * Logs the user in to the Gocard webiste.
     * @return boolean sucessful login
     */
    public function login() 
    {
        $crawler = $this->client->request(
            'POST', 
            $this->baseUrl.'/',
            array(
                'cardNum' => $this->cardNumber,
                'pass'    => $this->password,
                'cardOps' => 'Display'
            )
        );
        $this->loginCrawler = $crawler;
        return count($crawler->filter('.content h2:contains("Sorry, there was a problem")')) === 0;
    }

    /**
     * Get the current GoCard balance.
     * @return string balance in $xx.xx format
     */
    public function getBalance() {
        if (!$this->loginCrawler) {
            $this->login();
        }
        
        $balance = $this->loginCrawler->filter('#balance-table td:nth-of-type(2)')->text();
        return str_replace('$', '', $balance);
    }

    /**
     * Get the GoCard activity history.
     * @param string $period the period of history to retreive. Can be 'last20' or '-7', '-14', '-30' or '-60' days.
     * @return array of arrays, containing 'time' (in ISO format), 'action', 'location' and 'charge'
     */
    public function getHistory($startDate, $endDate) {
        $crawler = $this->client->request(
            'POST',
            $this->baseUrl.'/tickets-and-fares/go-card/online/history',
            array(
                'startDate' => $startDate->format('d/m/Y'),
                'endDate'   => $endDate->format('d/m/Y'),
                'submit'    => 'Search'
            )
        );
        $rows = $crawler->filter('#travel-history tbody tr');

        $entries = array();
        foreach ($rows as $row) {
            if ($row->getAttribute('class') == 'sub-heading') {
                $currentDate = $row->firstChild->textContent;
                continue;
            }

            if (!$row->hasAttribute('class')) {
                $tds = $row->getElementsByTagName('td');
                $start = \DateTime::createFromFormat('d F Y h:i A', $currentDate.' '.$tds->item(0)->textContent);
                $end   = \DateTime::createFromFormat('d F Y h:i A', $currentDate.' '.$tds->item(2)->textContent);

                
                $entry = new History(
                    $start,
                    $tds->item(1)->textContent,
                    $end,
                    $tds->item(3)->textContent,
                    str_replace('$ ', '', $tds->item(4)->textContent)
                );
                $entries[] = $entry;
            }
            // TODO: handle .sub-heading-transacton rows
        }
        return $entries;
    }

    /**
     * Log the user out of the GoCard webiste.
     * @return true assumes successful logout.
     */
    public function logout() {
        $crawler = $this->client->request(
            'GET', 
            '/welcome/welcome.do',
            array(
                'logout' => 'true'
            )
        );
        return count($crawler->filter('input[value="Login"]')) > 0;
    }
}