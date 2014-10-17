<?php
    require_once 'whatsapi/whatsprot.class.php';
    require_once 'Logger.php';
 
    const NL = "\n";
 
    class OnlineChecker
    {
        private $Whatsapp = null;
 
        private $URL = null;
        private $Numbers = null;
 
        function __construct($WP_U, $WP_I, $WP_P, $WP_N, $URL, $N)
        {
            try
            {
                $this->Whatsapp = new WhatsProt($WP_U, $WP_I, $WP_N);
                $this->Whatsapp->connect();
                $this->Whatsapp->loginWithPassword($WP_P);

                if(is_file('nextChallenge.dat'))
                    unlink('nextChallenge.dat');
 
                $this->URL = $URL;
 
                if(!is_array($N))
                    $this->Numbers = array($N);
                else
                    $this->Numbers = $N;

                if(!is_dir('conf/'))
                    if(!mkdir('conf/'))
                        throw new Exception('Can\'t create config folder');

                if(!is_file('conf/enabled.dat'))
                    if(!file_put_contents('conf/enabled.dat', 'true'))
                        throw new Exception('Can\t create enabled config file');

                if(!is_file('conf/sended.dat'))
                    if(!file_put_contents('conf/sended.dat', 'false'))
                        throw new Exception('Can\t create sended flag file');
            }
            catch (Exception $E)
            {
            	Logger::logError($E->getMessage(), $E->getLine(), $E->getFile());
                trigger_error($E->getMessage, E_USER_ERROR);
            }
        }
 
        public function Check()
        {
            try
            {
                $Enabled = self::getConfig('enabled');
                if($Enabled === null)
                    return -2;
 
                if($Enabled)
                {
                    $Headers = get_headers($this->URL, 1);
                    if(!$Headers)
                        return -3;
 
                    $Sended = $this->getConfig('sended');
                    if($Sended === null)
                        return -4;
 
                    if($Headers[0] != 'HTTP/1.0 200 OK' && $Headers[0] != 'HTTP/1.1 200 OK')
                    {
                        if(!$Sended)
                        {
                            $M = self::composeMessage(1, $this->URL, $Headers[0]);
                            if(!$M)
                                return -5;
 
                            if(!self::sendMessages($this->Numbers, $M))
                                return -6;
 
                            if(!self::setConfig('sended', 'true'))
                                return -7;
 
                            return $M;
                        }
                    }
                    else
                    {
                        if($Sended)
                        {
                            $M = self::composeMessage(0, $this->URL);
                            if(!$M)
                                return -5;
 
                            if(!self::sendMessages($this->Numbers, $M))
                                return -6;
 
                            if(!self::setConfig('sended', 'false'))
                                return -7;
 
                            return $M;
                        }
                    }
                }
                else
                    return -1;
                
                return true;
            }
            catch (Exception $E)
            {
            	Logger::logError($E->getMessage(), $E->getLine(), $E->getFile());
                trigger_error($E->getMessage, E_USER_ERROR);
                return false; // If user has set an error handler and it doesn't die()
            }
        }
 
        private function getConfig($Key)
        {
            try
            {
                switch ($Key)
                {
                    case 'enabled':
                        $Status = file_get_contents('conf/enabled.dat');
                        break;
                    case 'sended':
                        $Status = file_get_contents('conf/sended.dat');
                        break;
                    default:
                        return null;
                    break;
                }

                if($Status === 'true')
                    return true;
                else if($Status === 'false')
                    return false;
 
                return null;
            }
            catch (Exception $E)
            {
            	Logger::logError($E->getMessage(), $E->getLine(), $E->getFile());
                return null;
            }
        }
 
        private function setConfig($Key, $Value)
        {
            try
            {
                switch ($Key)
                {
                    case 'enabled':
                        $File = 'conf/enabled.dat';
                        break;
                    case 'sended':
                        $File = 'conf/sended.dat';
                        break;
                    default:
                        return false;
                    break;
                }
 
                $File = fopen($File, 'w');
                fwrite($File, $Value);
                fclose($File);
 
                return true;
            }
            catch (Exception $E)
            {
            	Logger::logError($E->getMessage(), $E->getLine(), $E->getFile());
                return false;
            }
        }
 
        private function composeMessage($Type, $URL, $HTTPCode = null)
        {
            try
            {
                $M = 'Underc0de Online Checker - by fermino';
                $M .= NL . NL;
 
                switch ($Type)
                {
                    case 1:
                        $M .= "Error in {$URL}";
                        $M .= NL . NL;
 
                        $M .= "Error code: {$HTTPCode}";
                        $M .= NL;
 
                        $M .= 'Date: ' . date('d/m/Y H:i:s');
                        break;
                    case 0:
                        $M .= "{$URL} back online";
                        $M .= NL . NL;
 
                        $M .= 'Date: ' . date('d/m/Y H:i:s');
                        break;
                    default:
                        return false;
                        break;
                }
 
                return $M;
            }
            catch (Exception $E)
            {
            	Logger::logError($E->getMessage(), $E->getLine(), $E->getFile());
                return false;
            }
        }
 
        private function sendMessages($Numbers, $Message)
        {
            try
            {
                if(is_array($Numbers))
                {
                    foreach($Numbers as $Number)
                    {
                        $this->Whatsapp->sendMessage($Number, $Message);
                    }
 
                    return true;
                }
 
                return false;
            }
            catch (Exception $E)
            {
            	Logger::logError($E->getMessage(), $E->getLine(), $E->getFile());
                return false;
            }
        }
    }