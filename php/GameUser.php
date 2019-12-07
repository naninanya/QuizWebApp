<?php

class GameUser
{
    private $nickName;
    private $mySessionID;
    private $gameMasterSessionID;

    public function __construct($nickName, $mySessionID)
    {
        $this->nickName = $nickName;
        $this->mySessionID = $mySessionID;
    }

    public function GetNickName()
    {
        return $this->nickName;
    }

    public function GetMySessionID()
    {
        return $this->mySessionID;
    }

    public function GetGameMasterSessionID()
    {
        return $this->gameMasterSessionID;
    }

    /**ゲームマスターが存在するか否か。 */
    public function ExistsGameMaster()
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $stmt = $pdo->query('Select master_session_id from game_master limit 1');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->gameMasterSessionID = $row['master_session_id'];
            return true;
        }
        return false;
    }

    //**ゲームユーザーを新規作成するトランザクション。成功か否か。 */
    public function CreateNewGameUserTransaction()
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $pdo->beginTransaction();
        if (!$this->InsertGameUser($pdo)) {
            $pdo->rollBack();
            return false;
        }
        if (!$this->InsertAnswerUser($pdo)) {
            $pdo->rollBack();
            return false;
        }
        $pdo->commit();
        return true;
    }

    /** ユーザーの新規参加時に、game_userにレコードを挿入する。*/
    private function InsertGameUser($pdo)
    {
        $stmt = $pdo->prepare('
        Insert Into game_user (master_session_id,my_session_id,nickname) 
        values (:master_session_id,:my_session_id,:nickname)');
        $stmt->bindParam(':master_session_id', $this->gameMasterSessionID, PDO::PARAM_STR);
        $stmt->bindParam(':my_session_id', $this->mySessionID, PDO::PARAM_STR);
        $stmt->bindParam(':nickname', $this->nickName, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (is_null($result) || !$result)
            return false;
        return true;
    }

    /**ユーザーの新規参加時に、answer_userにレコードを挿入する。*/
    private function InsertAnswerUser($pdo)
    {
        for ($i = 1; $i <= 5; $i++) {
            $answerName = 'answer' . $i;
            $stmt = $pdo->prepare('
            Insert Into answer_user (master_session_id,my_session_id,answer_name) 
            values (:master_session_id,:my_session_id,:answer_name)');
            $stmt->bindParam(':master_session_id', $this->gameMasterSessionID, PDO::PARAM_STR);
            $stmt->bindParam(':my_session_id', $this->mySessionID, PDO::PARAM_STR);
            $stmt->bindParam(':answer_name', $answerName, PDO::PARAM_STR);
            $result = $stmt->execute();
            if (is_null($result) || !$result)
                return false;
        }
        return true;
    }

    /**自身のポイントを取得する。*/
    public function GetPoint()
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $stmt = $pdo->prepare("
        Select point 
        from game_user
        where master_session_id = (select master_session_id from game_master limit 1)
        and my_session_id =:my_session_id
        ");
        $stmt->bindParam(':my_session_id', $this->mySessionID, PDO::PARAM_STR);
        $stmt->execute();
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row)
            return $row['point'];
    }

    //**特定の回答時点のポイントを計算し、Chart.jsで使用されるdata文字列で返す。 */
    public function GetPointAtAnswer($answerName)
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $stmt = $pdo->prepare("
        Select a.bill * b.magnification as 'value'
        from answer_user a
        inner join magnification b
        on a.magnification_name = b.magnification_name
        where a.master_session_id = (select master_session_id from game_master limit 1)
        and a.my_session_id =:my_session_id
        and a.answer_name <=:answer_name
        order by a.answer_name
        ");
        $stmt->bindParam(':my_session_id', $this->mySessionID, PDO::PARAM_STR);
        $stmt->bindParam(':answer_name', $answerName, PDO::PARAM_STR);
        $stmt->execute();
        $point = 100;
        $str = '[100,';
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $point = $point + $row['value'];
            $str = $str . $point . ','; //どうやって0を埋める？クラス化?→埋めなくてもよい。
        }
        $str = $str . "]";
        return $str;
    }

    //** ミニゲーム代表者の回答を更新するトランザクション。成功か否か。*/
    public function UpdateMiniGamerTransaction($answerName)
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $pdo->beginTransaction();
        if (!$this->UpdateMiniGamerAnswer($pdo, $answerName)) {
            $pdo->rollBack();
            return false;
        }
        $pdo->commit();
        return true;
    }

    //**ミニゲーム代表者の回答レコードを更新する。無条件で100ポイント贈呈するため、掛け金を100に設定する。*/
    private function UpdateMiniGamerAnswer($pdo, $answerName)
    {
        $stmt = $pdo->prepare('
        Update answer_user 
        set answer =\'*\',
        bill = 100,
        magnification_name = \'minigamer\'
        where my_session_id=:my_session_id 
        and answer_name =:answer_name 
        and master_session_id = (select master_session_id from game_master limit 1)');
        $stmt->bindParam(':my_session_id', $this->mySessionID, PDO::PARAM_STR);
        $stmt->bindParam(':answer_name', $answerName, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (is_null($result) || !$result)
            return false;
        return true;
    }

    //** ユーザーの回答を更新するトランザクション。成功か否か。*/
    public function UpdateAnswerUserTransaction($answerName, $answer, $bill)
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $pdo->beginTransaction();
        if (!$this->UpdateAnswerUser($pdo, $answerName, $answer, $bill)) {
            $pdo->rollBack();
            return false;
        }
        $pdo->commit();
        return true;
    }

    /**ユーザーの回答を更新する。倍率名はloserに設定する*/
    private function UpdateAnswerUser($pdo, $answerName, $answer, $bill)
    {
        $stmt = $pdo->prepare('
        Update answer_user 
        set answer =:answer,
        bill =:bill,
        magnification_name = \'loser\' 
        where my_session_id=:my_session_id 
        and answer_name =:answer_name 
        and master_session_id = (select master_session_id from game_master limit 1)');
        $stmt->bindParam(':answer', $answer, PDO::PARAM_STR);
        $stmt->bindParam(':bill', $bill, PDO::PARAM_INT);
        $stmt->bindParam(':my_session_id', $this->mySessionID, PDO::PARAM_STR);
        $stmt->bindParam(':answer_name', $answerName, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (is_null($result) || !$result)
            return false;
        return true;
    }

    /**ユーザーのアンケートを登録する。*/
    public function InsertQuestionnaire($contents)
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $stmt = $pdo->prepare('
        Insert Into questionnaire 
        values (:my_session_id,:contents)
       ');
        $stmt->bindParam(':my_session_id', $this->mySessionID, PDO::PARAM_STR);
        $stmt->bindParam(':contents', $contents, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (is_null($result) || !$result)
            return false;
        return true;
    }
}
