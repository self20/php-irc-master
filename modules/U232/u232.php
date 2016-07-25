<?php
class u232 extends module
{
    private $staff = array();
    private $advancedstaff = array();
    private $sitename = "Share The Data";
    private $siteurl = "http://localhost/ircs.php";
    private $ircidleurl = "http://localhost/irc_idle.php";
    private $detailsurl = "http://sharethedata.me/details.php?id=";
    private $botpass = "jaiuemdvJJnqwer8";
    private $bothash = "AjF329ksdfj83jadfjHUI";
    private $staffchannel = "#staff";
    private $ircbonuschannel = "#sharethedata";
    private $helpchannel = "#help";
    private $announcechannel = "#announce";
    
    public function init() {
        $this->timerClass->addTimer("update_staff", $this, "update_staff", "", 15 * 60, true);
        $this->timerClass->addTimer("irc_check", $this, "irc_check", "", 15 * 60, true);
    }
    public function update_staff() {
        require ('/var/www/html/cache/staff_settings3.php');
        $oldstaff = implode(", ", $this->staff);
        $oldadstaff = implode(", ", $this->advancedstaff);
        unset($this->staff);
        $this->staff = array();
        foreach ($staff as $staff2) {
            $this->staff[] = $staff2;
        }
        unset($this->advancedstaff);
        $this->advancedstaff = array();
        foreach ($advanced as $staff2) {
            $this->advancedstaff[] = $staff2;
        }
        $newstaff = implode(", ", $this->staff);
        $newadstaff = implode(", ", $this->advancedstaff);
        if ($newstaff != $oldstaff) $this->ircClass->privMsg($this->staffchannel, "Staff updated to: " . $newstaff);
        if ($newadstaff != $oldadstaff) $this->ircClass->privMsg($this->staffchannel, "Advanced Staff updated to: " . $newadstaff);
        return true;
    }
    public function force_staff_update($line, $args) {
        if ($line['to'] == $this->staffchannel) {
            $this->timerClass->removeTimer("update_staff");
            $this->timerClass->addTimer("update_staff", $this, "update_staff", "", 15 * 60, true);
        }
    }
    public function force_irc_update($line, $args) {
        if ($line['to'] == $this->staffchannel) {
            $this->timerClass->removeTimer("irc_check");
            $this->timerClass->addTimer("irc_check", $this, "irc_check", "", 15 * 60, true);
        }
    }
    public function commands($line, $args) {
        if (in_array($line['fromNick'], $this->staff)) {
            $this->ircClass->notice($line['fromNick'], "The Commands are:");
            if (in_array($line['fromNick'], $this->advancedstaff)) {
                $this->ircClass->notice($line['fromNick'], "Advanced Staff Commands:");
                $this->ircClass->notice($line['fromNick'], "!addbonus <nick> <amount>");
                $this->ircClass->notice($line['fromNick'], "!addfree <nick> <amount>");
                $this->ircClass->notice($line['fromNick'], "!addreputation <nick> <amount>");
                $this->ircClass->notice($line['fromNick'], "!addinvites <nick> <amount>");
                $this->ircClass->notice($line['fromNick'], "!rembonus <nick> <amount>");
                $this->ircClass->notice($line['fromNick'], "!remfreeslots <nick> <amount>");
                $this->ircClass->notice($line['fromNick'], "!remreputation <nick> <amount>");
                $this->ircClass->notice($line['fromNick'], "!reminvites <nick> <amount>");
            }
            $this->ircClass->notice($line['fromNick'], "Staff Commands:");
            $this->ircClass->notice($line['fromNick'], "Doing the following commands without a nick will show your information");
            $this->ircClass->notice($line['fromNick'], "!connectable <nick>");
            $this->ircClass->notice($line['fromNick'], "!online <nick>");
            $this->ircClass->notice($line['fromNick'], "!user <nick>");
            $this->ircClass->notice($line['fromNick'], "!bonus <nick>");
            $this->ircClass->notice($line['fromNick'], "User Commands:");
        } else {
            $this->ircClass->notice($line['fromNick'], "The Commands are:");
            $this->ircClass->notice($line['fromNick'], "!connectable");
            $this->ircClass->notice($line['fromNick'], "!online");
            $this->ircClass->notice($line['fromNick'], "!user");
            $this->ircClass->notice($line['fromNick'], "!bonus");
        }
        $this->ircClass->notice($line['fromNick'], "!torrents");
        $this->ircClass->notice($line['fromNick'], "!search <search terms>");
        $this->ircClass->notice($line['fromNick'], "!pre <search terms>");
        $this->ircClass->notice($line['fromNick'], "!givebonus <nick> <amount>");
        $this->ircClass->notice($line['fromNick'], "!giveinvites <nick> <amount>");
        $this->ircClass->notice($line['fromNick'], "!givefreeslots <nick> <amount>");
        $this->ircClass->notice($line['fromNick'], "!givereputation <nick> <amount>");
    }
    public function addbonus($line, $args) {
        if (in_array($line['fromNick'], $this->staff)) {
            if ($args['nargs'] < 2) {
                $this->ircClass->notice($line['fromNick'], "format is: !addbonus <nick> <amount>");
            } else {
                $whom = $args['arg1'];
                $amount = $args['arg2'];
                $mod = $line['fromNick'];
                $channel = $line['to'];
                $output = file_get_contents($this->siteurl . "?func=add&bonus&amount={$amount}&whom={$whom}&mod={$mod}&pass={$this->botpass}&hash={$this->bothash}");
                $this->ircClass->privMsg($channel, $output);
            }
        } else {
            $this->ircClass->notice($line['fromNick'], "You are not staff");
        }
    }
    public function addfree($line, $args) {
        if (in_array($line['fromNick'], $this->staff)) {
            if ($args['nargs'] < 2) {
                $this->ircClass->notice($line['fromNick'], "format is: !addfree <nick> <amount>");
            } else {
                $whom = $args['arg1'];
                $amount = $args['arg2'];
                $mod = $line['fromNick'];
                $channel = $line['to'];
                $output = file_get_contents($this->siteurl . "?func=add&freeslots&amount={$amount}&whom={$whom}&mod={$mod}&pass={$this->botpass}&hash={$this->bothash}");
                $this->ircClass->privMsg($channel, $output);
            }
        } else {
            $this->ircClass->notice($line['fromNick'], "You are not staff");
        }
    }
    public function addreputation($line, $args) {
        if (in_array($line['fromNick'], $this->staff)) {
            if ($args['nargs'] < 2) {
                $this->ircClass->notice($line['fromNick'], "format is: !addreputation <nick> <amount>");
            } else {
                $whom = $args['arg1'];
                $amount = $args['arg2'];
                $mod = $line['fromNick'];
                $channel = $line['to'];
                $output = file_get_contents($this->siteurl . "?func=add&reputation&amount={$amount}&whom={$whom}&mod={$mod}&pass={$this->botpass}&hash={$this->bothash}");
                $this->ircClass->privMsg($channel, $output);
            }
        } else {
            $this->ircClass->notice($line['fromNick'], "You are not staff");
        }
    }
    public function addinvites($line, $args) {
        if (in_array($line['fromNick'], $this->staff)) {
            if ($args['nargs'] < 2) {
                $this->ircClass->notice($line['fromNick'], "format is: !addinvites <nick> <amount>");
            } else {
                $whom = $args['arg1'];
                $amount = $args['arg2'];
                $mod = $line['fromNick'];
                $channel = $line['to'];
                $output = file_get_contents($this->siteurl . "?func=add&invites&amount={$amount}&whom={$whom}&mod={$mod}&pass={$this->botpass}&hash={$this->bothash}");
                $this->ircClass->privMsg($channel, $output);
            }
        } else {
            $this->ircClass->notice($line['fromNick'], "You are not staff");
        }
    }
    public function rembonus($line, $args) {
        if (in_array($line['fromNick'], $this->staff)) {
            if ($args['nargs'] < 2) {
                $this->ircClass->notice($line['fromNick'], "format is: !rembonus <nick> <amount>");
            } else {
                $whom = $args['arg1'];
                $amount = $args['arg2'];
                $mod = $line['fromNick'];
                $channel = $line['to'];
                $output = file_get_contents($this->siteurl . "?func=rem&bonus&amount={$amount}&whom={$whom}&mod={$mod}&pass={$this->botpass}&hash={$this->bothash}");
                $this->ircClass->privMsg($channel, $output);
            }
        } else {
            $this->ircClass->notice($line['fromNick'], "You are not staff");
        }
    }
    public function remfreeslots($line, $args) {
        if (in_array($line['fromNick'], $this->staff)) {
            if ($args['nargs'] < 2) {
                $this->ircClass->notice($line['fromNick'], "format is: !remfreeslots <nick> <amount>");
            } else {
                $whom = $args['arg1'];
                $amount = $args['arg2'];
                $mod = $line['fromNick'];
                $channel = $line['to'];
                $output = file_get_contents($this->siteurl . "?func=rem&freeslots&amount={$amount}&whom={$whom}&mod={$mod}&pass={$this->botpass}&hash={$this->bothash}");
                $this->ircClass->privMsg($channel, $output);
            }
        } else {
            $this->ircClass->notice($line['fromNick'], "You are not staff");
        }
    }
    public function remreputation($line, $args) {
        if (in_array($line['fromNick'], $this->staff)) {
            if ($args['nargs'] < 2) {
                $this->ircClass->notice($line['fromNick'], "format is: !remreputation <nick> <amount>");
            } else {
                $whom = $args['arg1'];
                $amount = $args['arg2'];
                $mod = $line['fromNick'];
                $channel = $line['to'];
                $output = file_get_contents($this->siteurl . "?func=rem&reputation&amount={$amount}&whom={$whom}&mod={$mod}&pass={$this->botpass}&hash={$this->bothash}");
                $this->ircClass->privMsg($channel, $output);
            }
        } else {
            $this->ircClass->notice($line['fromNick'], "You are not staff");
        }
    }
    public function reminvites($line, $args) {
        if (in_array($line['fromNick'], $this->staff)) {
            if ($args['nargs'] < 2) {
                $this->ircClass->notice($line['fromNick'], "format is: !reminvites <nick> <amount>");
            } else {
                $whom = $args['arg1'];
                $amount = $args['arg2'];
                $mod = $line['fromNick'];
                $channel = $line['to'];
                $output = file_get_contents($this->siteurl . "?func=rem&invites&amount={$amount}&whom={$whom}&mod={$mod}&pass={$this->botpass}&hash={$this->bothash}");
                $this->ircClass->privMsg($channel, $output);
            }
        } else {
            $this->ircClass->notice($line['fromNick'], "You are not staff");
        }
    }
    public function connectable($line, $args) {
        if (in_array($line['fromNick'], $this->staff)) {
            if ($args['nargs'] < 1) {
                $whom = $line['fromNick'];
            } else {
                $whom = $args['arg1'];
            }
        } else {
            $whom = $line['fromNick'];
        }
        $channel = $line['to'];
        $output = file_get_contents($this->siteurl . "?search=$whom&func=connectable&pass=$this->botpass&hash=$this->bothash");
        $this->ircClass->privMsg($channel, $output);
    }
    public function online($line, $args) {
        if (in_array($line['fromNick'], $this->staff)) {
            if ($args['nargs'] < 1) {
                $whom = $line['fromNick'];
            } else {
                $whom = $args['arg1'];
            }
        } else {
            $whom = $line['fromNick'];
        }
        $channel = $line['to'];
        $output = file_get_contents($this->siteurl . "?search=$whom&func=online&pass=$this->botpass&hash=$this->bothash");
        $this->ircClass->privMsg($channel, $output);
    }
    public function torrents($line, $args) {
        $channel = $line['to'];
        $output = file_get_contents($this->siteurl . "?torrents&pass=$this->botpass&hash=$this->bothash");
        $this->ircClass->privMsg($channel, $output);
    }
    public function sitestats($line, $args) {
        if (in_array($line['fromNick'], $this->staff)) {
            if ($args['nargs'] < 1) {
                $whom = $line['fromNick'];
            } else {
                $whom = $args['arg1'];
            }
        } else {
            $whom = $line['fromNick'];
        }
        $channel = $line['to'];
        $output = file_get_contents($this->siteurl . "?func=stats&search=$whom&pass=$this->botpass&hash=$this->bothash");
        $this->ircClass->privMsg($channel, $output);
    }
    public function bonus($line, $args) {
        if (in_array($line['fromNick'], $this->staff)) {
            if ($args['nargs'] < 1) {
                $whom = $line['fromNick'];
            } else {
                $whom = $args['arg1'];
            }
        } else {
            $whom = $line['fromNick'];
        }
        $channel = $line['to'];
        $output = file_get_contents($this->siteurl . "?func=check&search=$whom&pass=$this->botpass&hash=$this->bothash");
        $this->ircClass->privMsg($channel, $output);
    }
    public function givebonus($line, $args) {
        if ($args['nargs'] < 2) {
            $this->ircClass->notice($line['fromNick'], "format is: !givebonus <nick> <amount>");
        } else {
            $whom = $args['arg1'];
            $amount = $args['arg2'];
            $nick = $line['fromNick'];
            $channel = $line['to'];
            $output = file_get_contents($this->siteurl . "?func=give&bonus&amount=$amount&whom=$whom&me=$nick&pass=$this->botpass&hash=$this->bothash");
            $this->ircClass->privMsg($channel, $output);
        }
    }
    public function giveinvites($line, $args) {
        if ($args['nargs'] < 2) {
            $this->ircClass->notice($line['fromNick'], "format is: !giveinvites <nick> <amount>");
        } else {
            $whom = $args['arg1'];
            $amount = $args['arg2'];
            $nick = $line['fromNick'];
            $channel = $line['to'];
            $output = file_get_contents($this->siteurl . "?func=give&invites&amount=$amount&whom=$whom&me=$nick&pass=$this->botpass&hash=$this->bothash");
            $this->ircClass->privMsg($channel, $output);
        }
    }
    public function givefreeslots($line, $args) {
        if ($args['nargs'] < 2) {
            $this->ircClass->notice($line['fromNick'], "format is: !givefreeslots <nick> <amount>");
        } else {
            $whom = $args['arg1'];
            $amount = $args['arg2'];
            $nick = $line['fromNick'];
            $channel = $line['to'];
            $output = file_get_contents($this->siteurl . "?func=give&freeslots&amount=$amount&whom=$whom&me=$nick&pass=$this->botpass&hash=$this->bothash");
            $this->ircClass->privMsg($channel, $output);
        }
    }
    public function givereputation($line, $args) {
        if ($args['nargs'] < 2) {
            $this->ircClass->notice($line['fromNick'], "format is: !givereputation <nick> <amount>");
        } else {
            $whom = $args['arg1'];
            $amount = $args['arg2'];
            $nick = $line['fromNick'];
            $channel = $line['to'];
            $output = file_get_contents($this->siteurl . "?func=give&reputation&amount=$amount&whom=$whom&me=$nick&pass=$this->botpass&hash=$this->bothash");
            $this->ircClass->privMsg($channel, $output);
        }
    }
    private function user_check($user, $chan) {
        $is_user = file_get_contents($this->ircidleurl . "?key=$this->botpass&do=check&username=$user");
        if ($is_user == 1) {
            if (in_array($user, $this->staff)) {
                $this->ircClass->changeMode($this->ircbonuschannel, "+", "o", $user);
                $this->ircClass->sendRaw("INVITE {$user} {$this->staffchannel}");
                $this->ircClass->sendRaw("INVITE {$user} {$this->helpchannel}");
                $this->ircClass->sendRaw("INVITE {$user} {$this->announcechannel}");
            }
            $this->ircClass->changeMode($this->ircbonuschannel, "+", "v", $user);
        } elseif ($user != $this->ircClass->getNick()) {
            $this->ircClass->notice($user, "This is only for $this->sitename members! Please use your site username!");
        }
        return $is_user;
    }
    private function irc_idle($user, $irc_idle, $action) {
        $result = file_get_contents($this->ircidleurl . "?key=$this->botpass&do=idle&username=$user&ircidle=$irc_idle");
        if ($result == 1 && $irc_idle == 1) {
            $this->ircClass->notice($user, "<+> Irc bonus enabled");
        } elseif ($result == 1 && $irc_idle == 0 && $action != "QUIT") {
            $this->ircClass->notice($user, "<-> Irc bonus disabled");
        } else {
            $this->ircClass->privMsg($this->staffchannel, "There was an error while changing irc status to $irc_idle for user $user");
        }
    }
    public function ibleave($line, $args) {
        if ($line['to'] == $this->ircbonuschannel || $line['cmd'] == "QUIT") {
            $this->irc_idle($line['fromNick'], 0, $line['cmd']);
        }
    }
    public function ibjoin($line, $args) {
        if ($line['text'] == $this->ircbonuschannel) {
            $check = $this->user_check($line['fromNick'], $line['to']);
            if ($check == 1) {
                $this->irc_idle($line['fromNick'], 1, $line['cmd']);
            }
        }
    }
    public function ibkick($line, $args) {
        if ($line['to'] == $this->ircbonuschannel || $line['cmd'] == "QUIT") {
            $info = explode(" ", $line['raw']);
            $this->irc_idle($info[3], 0, $line['cmd']);
        }
    }
    public function irc_check() {
        $userslist = file_get_contents($this->ircidleurl . "?key=$this->botpass&do=list&username=a");
        $users = explode(", ", $userslist);
        $ircusers = array();
        $blahs = $this->ircClass->getChannelData($this->ircbonuschannel)->memberList;
        foreach ($blahs as $blah) {
            $ircusers[] = strtolower($blah->nick);
        }
        foreach ($users as $user) {
            if (!in_array($user, $ircusers)) $this->irc_idle($user, 0, "QUIT");
        }
        $userslist = file_get_contents($this->ircidleurl . "?key=$this->botpass&do=list&username=a");
        $users = explode(", ", $userslist);
        foreach ($ircusers as $ircuser) {
            if (!in_array($ircuser, $users)) {
                $check = $this->user_check($ircuser, $this->ircbonuschannel);
                if ($check == 1) {
                    $this->irc_idle($ircuser, 1, "JOIN");
                } else {
                    $this->ircClass->changeMode($this->ircbonuschannel, "-", "v", $ircuser);
                }
            }
        }
        return true;
    }
    public function tsearch($line, $args) {
        if ($this->db->ping()) {
            $searchterms = str_replace(".", " ", $args['query']);
            $searchterms = str_replace(" ", "%' AND name LIKE '%", $searchterms);
            $search = $this->db->query("SELECT name, id FROM torrents WHERE name LIKE '%" . $searchterms . "%' ORDER by id DESC");
            if ($this->db->numRows($search)) {
                if ($this->db->numRows($search) > 1 && $this->db->numRows($search) <= 10) {
                    $this->ircClass->privMsg($line['to'], "Sending " . $this->db->numRows($search) . " torrents matching your query.");
                } elseif ($this->db->numRows($search) > 1 && $this->db->numRows($search) > 10) {
                    $this->ircClass->privMsg($line['to'], "Sending last 10 torrents matching your query.");
                } else {
                    $this->ircClass->privMsg($line['to'], "Sending " . $this->db->numRows($search) . " torrent matching your query.");
                }
                $i = 0;
                while ($result = $this->db->fetchArray($search)) {
                    if ($i < 10) {
                        $noticetxt = "";
                        $noticetxt = $result['name'] . " - " . $this->detailsurl . $result['id'];
                        $this->ircClass->notice($line['fromNick'], $noticetxt);
                    }
                    $i++;
                }
            } else {
                $this->ircClass->privMsg($line['to'], "No torrents found");
            }
        } else {
            $this->ircClass->privMsg($line['to'], "Database connection lost");
        }
    }
    public function pre($line, $args) {
        $data = file_get_contents("http://www.prelist.ws/?search=" . $args['query']);
        $count = preg_match_all('/<tt id="\d+">(.+)<\/tt>/U', $data, $matches);
        if ($count == 1) {
            $this->ircClass->privMsg($line['to'], "Sending " . $count . " match.");
            $max = $count;
        } elseif ($count <= 10) {
            $this->ircClass->privMsg($line['to'], "Sending " . $count . " matches.");
            $max = $count;
        } elseif ($count > 10) {
            $this->ircClass->privMsg($line['to'], "Sending last 10 matches");
            $max = 10;
        } else {
            $this->ircClass->privMsg($line['to'], "No matches found");
            return;
        }
        $i = 0;
        while ($i < $max) {
            preg_match("/\[ (\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}:\d{2} UTC) \]/U", $matches[0][$i], $outputs);
            $doc = new DOMDocument();
            @$doc->loadHTML($matches[0][$i]);
            $time = $outputs[1];
            if ($doc->getElementsByTagName('a')->item(0)->nodeValue == "NUKED" || $doc->getElementsByTagName('a')->item(0)->nodeValue == "UNNUKED") {
                $nuke = $doc->getElementsByTagName('a')->item(0)->nodeValue;
                $cat = $doc->getElementsByTagName('a')->item(1)->nodeValue;
                $name = $doc->getElementsByTagName('a')->item(2)->nodeValue;
                $nukereason = $doc->getElementsByTagName('a')->item(0)->getAttribute('title');
                $this->ircClass->privMsg($line['to'], $time . " - " . $nuke . "({$nukereason}) - " . $cat . " - " . $name);
            } else {
                $cat = $doc->getElementsByTagName('a')->item(0)->nodeValue;
                $name = $doc->getElementsByTagName('a')->item(1)->nodeValue;
                $this->ircClass->privMsg($line['to'], $time . " - " . $cat . " - " . $name);
            }
            $i++;
        }
    }
}
?>
