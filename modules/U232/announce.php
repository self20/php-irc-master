<?php
class announce extends module
{
    private $announcechan = '#announce';//set the channel you would like the bot to announce to
    private $port = 5002;//set to the port you are listening on
    private $listener;
    private $sockInt;
    private $conns = array();
    public function init() {
        $conn = new connection(NULL, $this->port, 0);
        $conn->setSocketClass($this->socketClass);
        $conn->setIrcClass($this->ircClass);
        $conn->setTimerClass($this->timerClass);
        $conn->setCallbackClass($this);
        $conn->init();
        if ($conn->getError()) {
            echo 'Error connecting: ' . $conn->getErrorMsg();
        }
        $this->sockInt = $conn->getSockInt();
        $this->listener = $conn;
    }
    public function destroy() {
        $this->listener->disconnect();
        foreach ($this->conns as $conn) $this->closeConnection($conn);
    }
    public function onTransferTimeout($conn) {
        if (!$this->closeConnection($conn));
        return false;
    }
    public function onConnectTimeout($conn) {
        if (!$this->closeConnection($conn));
        return false;
    }
    public function onConnect($conn) {
    }
    public function onRead($conn) {
        $connInt = $conn->getSockInt();
        if (!isset($this->conns[$connInt])) {
            $conn->disconnect();
            return false;
        }
        $conn = $this->conns[$connInt];
        $line = $this->socketClass->getQueueLine($connInt);
        $this->ircClass->privMsg($this->announcechan , $line);
        return $this->socketClass->hasLine($connInt);
    }
    public function onWrite($conn) {
    }
    public function onAccept($listener, $newConn) {
        $connInt = $newConn->getSockInt();
        $this->conns[$connInt] = $newConn;
    }
    public function onDead($conn) {
        if (!$this->closeConnection($conn)) return false;
    }
    private function closeConnection($conn) {
        $connInt = $conn->getSockInt();
        if (!isset($this->conns[$connInt])) {
            $conn->disconnect();
            return false;
        }
        $this->conns[$connInt]->disconnect();
        unset($this->conns[$connInt]);
    }
}
?>