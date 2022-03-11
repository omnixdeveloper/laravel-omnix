<?php
namespace omnixdeveloper\LaravelOmnix\Lib;
use kornrunner\Ethereum\Transaction;
use kornrunner\Ethereum\Token;
use kornrunner\Ethereum\Address as Account;

class Ethereum extends JsonRPC
{
    private function ether_request($method, $params=array())
    {
        try
        {
            $ret = $this->request($method, $params);
            return $ret->result;
        }
        catch(RPCException $e)
        {
            throw $e;
        }
    }

    private function decode_hex($input)
    {
        if(substr($input, 0, 2) == '0x')
            $input = substr($input, 2);

        if(preg_match('/[a-f0-9]+/', $input))
            return hexdec($input);

        return $input;
    }

    // Omnixcoin sdk

    function generate_account()
    {
        $account = new Account();
        return $account;
    }

    function get_account($private)
    {
        $account = new Account($private);
        return $account;
    }




    function omnix_version()
    {
        return $this->ether_request('web3_clientVersion');
    }

    function net_version()
    {
        return $this->ether_request(__FUNCTION__);
    }

    function net_listening()
    {
        return $this->ether_request(__FUNCTION__);
    }

    function peer_count()
    {
        return $this->ether_request('net_peerCount');
    }

    function protocol_version()
    {
        return $this->ether_request('eth_protocolVersion');
    }

    function coinbase()
    {
        return $this->ether_request('eth_coinbase');
    }

    function hashrate()
    {
        return $this->ether_request('eth_hashrate');
    }

    function gasprice()
    {
      return $this->ether_request('eth_gasPrice');
    }

    function list_accounts()
    {
      return $this->ether_request('eth_accounts');
    }

    function block_height($decode_hex=FALSE)
    {
      $block = $this->ether_request('eth_blockNumber');

      if($decode_hex)
      $block = $this->decode_hex($block);

      return $block;
    }

    function get_balance($address, $decode_hex=FALSE,$block='latest')
    {
      $balance = $this->ether_request('eth_getBalance', array($address, $block));

      if($decode_hex)
      $balance = $this->decode_hex($balance);

      return $balance;
    }

    function get_token_balance($address,$contractAddress,$decode_hex=FALSE,$block='latest')
    {
      $data = "0x70a08231000000000000000000000000".str_replace('0x','',$address);
      $balance = $this->eth_call(array("from" => null,"to" => $contractAddress,"data" =>$data));
      if($decode_hex)$balance = $this->decode_hex($balance);
      return $balance;
    }

    function toWei($value, $decimal)
    {
        $dividend = (string)$value;
        $divisor = (string)'1'. str_repeat('0', $decimal);
        return intval(bcmul($value, $divisor, $decimal));
    }

    function send_coin($key,$to,$amount)
    {
      $account  = new Account($key);
      $address  = $account->get();
      $nonce    = $this->transaction_count('0x'.$address);
      $gasPrice = $this->gasprice();
      $gasLimit = '5208';
      $wei = $this->toWei($amount,18);
      $transaction = new Transaction($nonce, $gasPrice, $gasLimit, $to, '0x'.dechex($wei), '');
      $raw = $transaction->getRaw($key);
      $txid    = $this->broadcast_transaction('0x'.$raw);
      return $txid;
    }


    function send_token($key,$to,$amount,$contractAddress)
    {
      $account  = new Account($key);
      $address  = $account->get();
      $gasPrice = $this->gasprice();
      $gasLimit = '130B0';
      $token = new Token;
      $data = $token->getTransferData($to, dechex($amount));
      $nonce    = $this->transaction_count('0x'.$address);


      $transaction = new Transaction($nonce, $gasPrice, $gasLimit, $contractAddress, '', $data);
      $raw = $transaction->getRaw($key);
      $txid    = $this->broadcast_transaction('0x'.$raw);
      return $txid;
    }



    function get_storage_at($address, $at, $block='latest')
    {
        return $this->ether_request('eth_getStorageAt', array($address, $at, $block));
    }

    function transaction_count($address, $block='latest', $decode_hex=FALSE)
    {
        $count = $this->ether_request('eth_getTransactionCount', array($address, $block));

        if($decode_hex)
            $count = $this->decode_hex($count);

        return $count;
    }

    function transaction_count_by_hash($tx_hash)
    {
        return $this->ether_request('eth_getBlockTransactionCountByHash', array($tx_hash));
    }

    function transaction_count_by_number($tx='latest')
    {
        return $this->ether_request('eth_getBlockTransactionCountByNumber', array($tx));
    }

    function broadcast_transaction($tx)
    {
        return $this->ether_request('eth_sendRawTransaction', array($tx));
    }

    function uncle_count_by_hash($block_hash)
    {
        return $this->ether_request('eth_getUncleCountByBlockHash', array($block_hash));
    }

    function uncle_count_by_number($block='latest')
    {
        return $this->ether_request('eth_getUncleCountByBlockNumber', array($block));
    }

    function get_code($address, $block='latest')
    {
        return $this->ether_request('eth_getCode', array($address, $block));
    }

    function block_by_hash($hash, $full_tx=TRUE)
    {
        return $this->ether_request('eth_getBlockByHash', array($hash, $full_tx));
    }

    function block_by_number($block='latest', $full_tx=TRUE)
    {
        return $this->ether_request('eth_getBlockByNumber', array($block, $full_tx));
    }

    function transaction_by_hash($hash)
    {
        return $this->ether_request('eth_getTransactionByHash', array($hash));
    }

    function transaction_by_hash_and_index($hash, $index)
    {
        return $this->ether_request('eth_getTransactionByBlockHashAndIndex', array($hash, $index));
    }

    function transaction_by_number_and_index($block, $index)
    {
        return $this->ether_request('eth_getTransactionByBlockNumberAndIndex', array($block, $index));
    }

    function transaction_receipt($tx_hash)
    {
        return $this->ether_request('eth_getTransactionReceipt', array($tx_hash));
    }

    function uncle_by_hash_and_index($hash, $index)
    {
        return $this->ether_request('eth_getUncleByBlockHashAndIndex', array($hash, $index));
    }

    function eth_getUncleByBlockNumberAndIndex($block, $index)
    {
        return $this->ether_request(__FUNCTION__, array($block, $index));
    }

    function eth_getCompilers()
    {
        return $this->ether_request(__FUNCTION__);
    }

    function eth_compileSolidity($code)
    {
        return $this->ether_request(__FUNCTION__, array($code));
    }

    function eth_compileLLL($code)
    {
        return $this->ether_request(__FUNCTION__, array($code));
    }

    function eth_compileSerpent($code)
    {
        return $this->ether_request(__FUNCTION__, array($code));
    }

    function eth_newFilter($filter, $decode_hex=FALSE)
    {
        if(!is_a($filter, EthereumFilter::class))
        {
            throw new ErrorException('Expected a Filter object');
        }
        else
        {
            $id = $this->ether_request(__FUNCTION__, $filter->toArray());

            if($decode_hex)
                $id = $this->decode_hex($id);

            return $id;
        }
    }

    function new_block_filter($decode_hex=FALSE)
    {
        $id = $this->ether_request('eth_newBlockFilter');

        if($decode_hex)
            $id = $this->decode_hex($id);

        return $id;
    }

    function pending_transaction_filter($decode_hex=FALSE)
    {
        $id = $this->ether_request('eth_newPendingTransactionFilter');

        if($decode_hex)
            $id = $this->decode_hex($id);

        return $id;
    }

    function eth_uninstallFilter($id)
    {
        return $this->ether_request(__FUNCTION__, array($id));
    }

    function eth_getFilterChanges($id)
    {
        return $this->ether_request(__FUNCTION__, array($id));
    }

    function eth_getFilterLogs($id)
    {
        return $this->ether_request(__FUNCTION__, array($id));
    }

    function eth_getLogs($filter)
    {
        if(!is_a($filter, EthereumFilter::class))
        {
            throw new ErrorException('Expected a Filter object');
        }
        else
        {
            return $this->ether_request(__FUNCTION__, $filter->toArray());
        }
    }

    function eth_getWork()
    {
        return $this->ether_request(__FUNCTION__);
    }

    function eth_submitWork($nonce, $pow_hash, $mix_digest)
    {
        return $this->ether_request(__FUNCTION__, array($nonce, $pow_hash, $mix_digest));
    }

    function db_putString($db, $key, $value)
    {
        return $this->ether_request(__FUNCTION__, array($db, $key, $value));
    }

    function db_getString($db, $key)
    {
        return $this->ether_request(__FUNCTION__, array($db, $key));
    }

    function db_putHex($db, $key, $value)
    {
        return $this->ether_request(__FUNCTION__, array($db, $key, $value));
    }

    function db_getHex($db, $key)
    {
        return $this->ether_request(__FUNCTION__, array($db, $key));
    }

    function shh_version()
    {
        return $this->ether_request(__FUNCTION__);
    }

    function shh_post($post)
    {
        if(!is_a($post, WhisperPost::class))
        {
            throw new \ErrorException('Expected a Whisper post');
        }
        else
        {
            return $this->ether_request(__FUNCTION__, $post->toArray());
        }
    }

    function shh_newIdentity()
    {
        return $this->ether_request(__FUNCTION__);
    }

    function shh_hasIdentity($id)
    {
        return $this->ether_request(__FUNCTION__);
    }

    function shh_newFilter($to=NULL, $topics=array())
    {
        return $this->ether_request(__FUNCTION__, array(array('to'=>$to, 'topics'=>$topics)));
    }

    function shh_uninstallFilter($id)
    {
        return $this->ether_request(__FUNCTION__, array($id));
    }

    function shh_getFilterChanges($id)
    {
        return $this->ether_request(__FUNCTION__, array($id));
    }

    function shh_getMessages($id)
    {
        return $this->ether_request(__FUNCTION__, array($id));
    }

    function new_account($passphrase){
        return $this->ether_request('personal_newAccount', array($passphrase));
    }

    function list_account(){
        return $this->ether_request('personal_listAccounts');
    }

    function personal_unlockAccount($address,$passphrase,$duration=300){
        return $this->ether_request(__FUNCTION__, array($address,$passphrase,$duration));
    }

    function personal_lockAccount($address){
        return $this->ether_request(__FUNCTION__, array($address));
    }

    function personal_ecRecover($message, $signature){
        return $this->ether_request(__FUNCTION__, array($message,$signature));
    }

    function personal_importRawKey($keydata, $passphrase){
        return $this->ether_request(__FUNCTION__, array($keydata,$passphrase));
    }

    function personal_sendTransaction(EthereumTransaction $transaction,$passphrase){
        $params=$transaction->toArray();
        array_push($params,$passphrase);
        return $this->ether_request(__FUNCTION__, $params);
    }


    function eth_mining()
    {
        return $this->ether_request(__FUNCTION__);
    }

    function eth_sign($address, $input)
    {
        return $this->ether_request(__FUNCTION__, array($address, $input));
    }

    function eth_sendTransaction($transaction)
    {
        if(!is_a($transaction, EthereumTransaction::class))
        {
            throw new ErrorException('Transaction object expected');
        }
        else
        {
            return $this->ether_request(__FUNCTION__, $transaction->toArray());
        }
    }

    function eth_call($message)
    {
      $params = array($message,'latest');
      return $this->ether_request(__FUNCTION__, $params);
    }


    function eth_estimateGas($message, $block)
    {
        if(!is_a($message, EthereumMessage::class))
        {
            throw new ErrorException('Message object expected');
        }
        else
        {
            return $this->ether_request(__FUNCTION__, $message->toArray());
        }
    }


}
