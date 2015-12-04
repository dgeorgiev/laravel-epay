<?php
namespace Dgeorgiev\Epay;

/**
 * Epay API Class
 *
 * Wrapper for Epay.bg API
 *
 * @author Daniel Georgiev <me@dgeorgiev.biz>
 * @version 1.0.0
 * @package dgeorgiev\epay
 */
class Epay
{
    /**
    * @var Array
    */
    private $config;

    /**
    * @var Array
    */
    private $data;

    /**
    * @var hash_hmac
    */
    private $checksum;


    /**
     * Create a new Epay Instance
     */
    public function __construct()
    {

        /**
        * Get user config
        * Check mode and correct it if necessary
        * Assign config to local variable
        **/

        $config = config('epay');

        $mode = $config['mode'];
        if(!in_array($config['mode'], ['stage', 'prod'])){
            $mode = 'stage';
        }

        $this->config = config('epay.'.$mode);

    }

    /**
     * Get form submit url
     * @method getSubmitUrl
     * @return string       form action url
     */
    public function getSubmitUrl()
    {
        return $this->config['submit_url'];
    }

    /**
     * Set invoice data
     * @method setData
     * @param  integer     $invoice         required
     * @param  decimal     $amount          required
     * @param  DateString  $expiration   required   d.m.Y example - 01.08.2020
     * @param  string      $description [description]
     */
    public function setData($invoice, $amount, $expiration, $description = '')
    {

        $data = <<<DATA
MIN={$this->config['client_id']}
INVOICE={$invoice}
AMOUNT={$amount}
EXP_TIME={$expiration}
DESCR={$description}
DATA;

        $this->data = base64_encode($data);

        $this->setChecksum();

    }

    /**
     * Get inputed data encoded with base64
     * @method getData
     * @return string  base64 encoded data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set checksum based on data
     * @method setChecksum
     */
    public function setChecksum()
    {
        $this->checksum = $this->generateChecksumHash();
    }

    /**
     * Generate hash_hmac based string
     * @method generateChecksumHash
     * @param  $data               $data    overwrite data
     * @return string                       return hash_hmac
     */
    private function generateChecksumHash($data = false){
        $customData = ($data) ? $data : $this->data;
        return hash_hmac('sha1', $customData, $this->config['secret']);
    }

    /**
     * Get generated checksum
     * @method getChecksum
     * @return string      hashed data
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Generate html hidden inputs
     * @method generateHiddenInputs
     * @param  string               $successUrl Return after success
     * @param  string               $cancelUrl  Return after cancel
     * @return string                           generated html
     */
    public function generateHiddenInputs($successUrl = false, $cancelUrl = false)
    {

        return '
            <input type="hidden" name="PAGE" value="paylogin">
            <input type="hidden" name="ENCODED" value="'.$this->getData().'">
            <input type="hidden" name="CHECKSUM" value="'.$this->getChecksum().'">
            <input type="hidden" name="URL_OK" value="'.(($successUrl) ? $successUrl : $this->config['success_url']).'">
            <input type="hidden" name="URL_CANCEL" value="'.(($cancelUrl) ? $cancelUrl : $this->config['cancel_url']).'">
        ';

    }

    /**
     * Receive Handler
     * @method receiveNotification
     * @param  array             $requestInputs   Request inputs (post data)
     * @return array                              Response and invoice data
     */
    public function receiveNotification($requestInputs)
    {
        $encoded  = $requestInputs['encoded'];
        $checksum = $requestInputs['checksum'];

        $hmac   = $this->generateChecksumHash($encoded);

        if ($hmac == $checksum) {

            $result = [];

            $result['data'] = base64_decode($encoded);
            $lines = explode("\n", $result['data']);

            $result['items'] = [];

            $response = '';

            foreach ($lines as $line) {
                if (preg_match("/^INVOICE=(\d+):STATUS=(PAID|DENIED|EXPIRED)(:PAY_TIME=(\d+):STAN=(\d+):BCODE=([0-9a-zA-Z]+))?$/", $line, $regs)) {

                    $item = [];
                    $item['invoice'] = $regs[1];
                    $item['status'] = $regs[2];
                    $item['pay_date'] = $regs[4];
                    $item['stan'] = $regs[5];
                    $item['bcode'] = $regs[6];

                    $result['items'][] = $item;

                    switch ($item['status']) {
                        case 'PAID':
                            $response .= "INVOICE=$regs[1]:STATUS=OK\n";
                            break;
                        case 'DENIED':
                            $response .= "INVOICE=$regs[1]:STATUS=ERR\n";
                            break;
                        default:
                            /**
                             * EXPIRED OR OTHER
                             *
                             */
                            $response .= "INVOICE=$regs[1]:STATUS=NO\n";
                            break;
                    }

                }
            }
            $result['response'] = $response;
            return $result;
        }
        else {
            \Log::error("Checksum doesn't match!");
        }
    }

}
