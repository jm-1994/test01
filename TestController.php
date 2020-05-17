<?php

namespace src\controllers;

use inc\Controller;
use src\lib\Router;
use src\lib\commonClass;
use src\models\CronJob;
use inc\Raise;
use src\lib\walletClass;
use src\inc\transactionArray;

class TestController extends Controller {

    public function __construct(){

        ini_set('max_execution_time', 30000);
        ini_set("memory_limit",-1);
        error_reporting(E_ALL);
        ini_set('display_errors',1);
        $this->cObj  = (new commonClass);
        $this->cron  = (new CronJob);
    }

    public function actionTest(){
        error_reporting(E_ALL);
        $nb = $this->cron->callsql("SELECT * FROM nb_history WHERE update_time >= '1581303600'","rows");
        echo "<table>";
        foreach($nb as $val){
            $name = $this->cron->callsql("SELECT username FROM user WHERE id='$val[user_id]'","value");
            echo "<tr>";
            echo "<td>".$name."</td>";
            echo "<td>".$val['level']."</td>";
            echo "<td>".$val['value']."</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    public function checkRequirement($user,$currentRank){
        $endDate = strtotime('today midnight');
        $startDate = $endDate - (30*24*60*60);

        $totalAmt = $this->cron->callsql("SELECT SUM(value) FROM pairing_history WHERE user_id='$user' AND updated_time BETWEEN $startDate AND $endDate","value");
        $vipGroup = $this->cron->callsql("SELECT * FROM vip_group ORDER BY id DESC","rows");

        echo $user.' - '.$totalAmt.' - '.$vipGroup;

        foreach ($vipGroup as $key => $vip) {
            if($currentRank >= $vip['id'])
                continue;

            $requirement = json_decode($requirement,true);
            $level = $requirement['level'];
            $number = $requirement['number'];

            if($totalAmt < $vip['require_pairing'])
                continue;

            $checkGroup = $this->checkDownlineMember($user,$level);

            if($checkGroup <= $number)
                continue;

            $time = time();
            $ip = $_SERVER['REMOTE_ADDR'];

            // $this->db->callsql("INSERT INTO vip_level_history SET user_id='$user', cur_rank='$currentRank', pre_rank='$vip[id]', update_time='$time', update_ip='$ip'");
            // $this->db->callsql("UPDATE user SET user_vip_level='$vip[id]' WHERE id='$user'");
        }
        echo ' - '.$totalAmt.' - '.$checkGroup.'<br>';
    }

    public function actionFreezeSpecial()
    {
        error_reporting(E_ALL);
        ini_set('display_errors',1);
        
        $user = array('318','322');

        foreach ($user as $key => $value) {
            $this->cron->callsql("UPDATE user SET user_status=3 WHERE id='$value'");
            echo $value . "<br>";
        }
    }

    public function actionMartin(){

        $user = $this->getChild(265).'265';
        $user = explode(',', $user);
        //$user = asort($user);
        //print_r($user);exit;
        echo "<table>";
        foreach ($user as $key => $value) {
            $aUser = $this->cron->callsql("SELECT username FROM user WHERE id='$value'","value"); 
            $CompressNB = $this->cron->callsql("SELECT SUM(value) FROM profit_wallet_history WHERE user_id='$value' AND credit_type=1 AND remarks IN ('CompressNB')",'value');
            $PB = $this->cron->callsql("SELECT SUM(value) FROM profit_wallet_history WHERE user_id='$value' AND credit_type=1 AND remarks IN ('PB')",'value');
            $DS = $this->cron->callsql("SELECT SUM(value) FROM profit_wallet_history WHERE user_id='$value' AND credit_type=1 AND remarks IN ('DS')",'value');
            $ROI = $this->cron->callsql("SELECT SUM(value) FROM profit_wallet_history WHERE user_id='$value' AND credit_type=1 AND remarks IN ('ROI')",'value');
            $NB = $this->cron->callsql("SELECT SUM(value) FROM profit_wallet_history WHERE user_id='$value' AND credit_type=1 AND remarks IN ('NB')",'value');
            if(empty($profit)){
                $profit = 0;
            }
            echo "<tr><td>". $aUser. "</td>";
            echo "<td>". $CompressNB. "</td>";
            echo "<td>". $PB. "</td>";
            echo "<td>". $DS. "</td>";
            echo "<td>". $ROI. "</td>";
            echo "<td>". $NB. "</td></tr>";
        }
        echo "<table>";
    }

    private function getChild($userId)
    {
        $downlineArr=array(); 
        $downlineArr[0]="";
     
        $aUser = $this->cron->callsql("SELECT id,user_status FROM user WHERE sponsor_id='$userId'","rows"); 
    
        foreach ($aUser as $key => $value) { 
            if($value['user_status'] != 5){
              $downlineArr[0].=$value['id'].",";
            }            
            $downlineArr[0].=$this->getChild($value['id']);
        }
        return $downlineArr[0];

    }

    public function actionGroup() {
        $userId = 1907;
        $user = $this->getGroup($userId);
        $user = explode(',', $user);
        foreach ($user as $key => $value) {
            $aUser = $this->cron->callsql("SELECT username FROM user WHERE id='$value'","value"); 
            echo $value . ",";
        }
    }

    private function getGroup($userId){
        $downlineArr=array($userId); 
        $downlineArr[0]=$userId.",";
        $aUser = $this->cron->callsql("SELECT id,user_status FROM user WHERE placing_id='$userId'","rows"); 
        
        foreach ($aUser as $key => $value) { 
            if($value['user_status'] != 5){
              $downlineArr[0].=$value['id'].",";
            }            
            $downlineArr[0].=$this->getGroup($value['id']);
        }
        return $downlineArr[0];
    }   

    public function actionGetSpecialName() {
        $user = $this->cron->callsql("SELECT id,username FROM user","rows"); 
        foreach($user as $key=>$val){
            if(!ctype_alnum($val['username'])){
                
                echo $val['id'].' - '.$val['username'].'<br>';
            }
        }
    }

    public function actionUpdateWallet() {
        $value = 0;
        $pre_value = array();
        $count = array();
        $after_value = array();

        $wallet = $this->cron->callsql("SELECT * FROM profit_wallet_history ORDER BY id ASC;","rows");
        foreach($wallet as $val) {
            if($count[$val['user_id']]==0)
                $pre_value[$val['user_id']] = $val['after_bal'];

            if($count[$val['user_id']]>=1){
                $after_value[$val['user_id']] = $pre_value[$val['user_id']]+$val['value'];

                if($after_value[$val['user_id']]!=$val['before_bal']){
                    echo $pre_value[$val['user_id']].' - '.$after_value[$val['user_id']].' - '.$count[$val['user_id']].'<br>';
                }
                $pre_value[$val['user_id']] = $after_value;
            }

            $count[$val['user_id']]++;
        }
        
    } 

    public function actionUpdateWalletLog() {
        $value = 0;
        $pre_value = 0;
        $count = 0;

        $wallet = $this->cron->callsql("SELECT * FROM wallet_log WHERE user_id='888' AND wallet_type=2 AND id >= 8033470 ORDER BY id ASC","rows");
        foreach($wallet as $val) {
            if($count==0)
                $pre_value = $val['after_bal'];

            if($count>=1){
                $after_value = $pre_value+$val['value'];
                $this->cron->callsql("UPDATE wallet_log SET before_bal='$pre_value', after_bal='$after_value' WHERE user_id='888' AND id='$val[id]'");
                echo $pre_value.' - '.$after_value.' - '.$count.'<br>';
                $pre_value = $after_value;
            }

            $count++;
        }
        
    } 

    public function actionUpdatePairingReward() {
        $user = $this->cron->callsql("SELECT * FROM user","rows");
        $time = time();
        $ip = $_SERVER['REMOTE_ADDR'];

        foreach($user as $val){
            $march_val = $this->cron->callsql("SELECT SUM(value) FROM pairing_history WHERE updated_time BETWEEN 1582473599 AND 1585022400 AND value!=0 AND user_id='$val[id]'","value");

            $jan_val = $this->cron->callsql("SELECT SUM(value) FROM pairing_history WHERE updated_time BETWEEN 1580313600 AND 1580443200 AND value!=0 AND user_id='$val[id]'","value");

            $update_val = $march_val+$jan_val;

            if(empty($update_val))
                $update_val = 0;
            else
                $update_val = number_format($update_val);

            $this->cron->callsql("INSERT INTO pairing_reward_target SET updated_time='$time', monthly_amount=monthly_amount+'$update_val', user_id='$val[id]'");
        }
    }
}

?>
}
}
}
