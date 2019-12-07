<?php

class GameMaster
{
    private $nickName;
    private $newSessionID;
    private $gameMasterSessionID;

    public function __construct($nickName, $newSessionID)
    {
        $this->nickName = $nickName;
        $this->newSessionID = $newSessionID;
    }

    public function GetMySessionID()
    {
        return $this->newSessionID;
    }

    public function GetGameMasterSessionID()
    {
        return $this->gameMasterSessionID;
    }

    /** ゲームマスターが存在するか否か。 */
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

    //** ゲームマスターを新規作成するトランザクション。成功か否か。 */
    public function CreateNewGameMasterTransaction()
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $pdo->beginTransaction();
        if (!$this->InsertGameMaster($pdo)) {
            $pdo->rollBack();
            return false;
        }
        if (!$this->InsertAnswerMaster($pdo)) {
            $pdo->rollBack();
            return false;
        }
        $pdo->commit();
        $this->gameMasterSessionID = $this->newSessionID;
        return true;
    }

    private function InsertGameMaster($pdo)
    {
        $stmt = $pdo->prepare('
        Insert Into game_master (master_session_id) 
        values (:master_session_id)');
        $stmt->bindParam(':master_session_id', $this->newSessionID, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (is_null($result) || !$result)
            return false;
        return true;
    }

    //** 新規ゲームを作成するトランザクション。成功か否か。 */
    public function CreateNewGameTransaction($regenerateSessionID)
    {
        $this->newSessionID = $regenerateSessionID;
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $pdo->beginTransaction();
        if (!$this->UpdateGameMaster($pdo)) {
            $pdo->rollBack();
            return false;
        }
        if (!$this->InsertAnswerMaster($pdo)) {
            $pdo->rollBack();
            return false;
        }
        $pdo->commit();
        $this->gameMasterSessionID = $this->newSessionID;
        return true;
    }

    /** 新規ゲーム作成時に、game_masterのsession_idを更新することで、今までのgame_userのsession_idと紐づかなくなり、新規ゲームとする。*/
    private function UpdateGameMaster($pdo)
    {
        $stmt = $pdo->prepare('
        Update game_master 
        set master_session_id = :master_session_id');
        $stmt->bindParam(':master_session_id', $this->newSessionID, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (is_null($result) || !$result)
            return false;
        return true;
    }

    /** 新規ゲーム作成時に、answer_masterにレコードを挿入する。*/
    private function InsertAnswerMaster($pdo)
    {
        for ($i = 1; $i <= 5; $i++) {
            $answerName = 'answer' . $i;
            $stmt = $pdo->prepare('
            Insert Into answer_master (master_session_id,answer_name) 
            values (:master_session_id,:answer_name)');
            $stmt->bindParam(':master_session_id', $this->newSessionID, PDO::PARAM_STR);
            $stmt->bindParam(':answer_name', $answerName, PDO::PARAM_STR);
            $result = $stmt->execute();
            if (is_null($result) || !$result)
                return false;
        }
        return true;
    }

    //**ゲームマスターの回答を更新するトランザクション。成功か否か。 */
    public function UpdateAnswerMasterTransaction($answerName, $answer)
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $pdo->beginTransaction();
        if (!$this->UpdateAnswerMaster($pdo, $answerName, $answer)) {
            $pdo->rollBack();
            return false;
        }
        if (!$this->UpdateMagnificationNameWinner($pdo, $answerName)) {
            $pdo->rollBack();
            return false;
        }
        if (!$this->UpdateGameUserPoint($pdo, $answerName)) {
            $pdo->rollBack();
            return false;
        }
        if (!$this->UpdateAverage($pdo, $answerName)) {
            $pdo->rollBack();
            return false;
        }
        $pdo->commit();
        return true;
    }

    /**ゲームマスターの答えを更新する。*/
    private function UpdateAnswerMaster($pdo, $answerName, $answer)
    {
        $stmt = $pdo->prepare('
        Update answer_master 
        set answer =:answer
        where answer_name =:answer_name 
        and master_session_id = (select master_session_id from game_master limit 1)');
        $stmt->bindParam(':answer', $answer, PDO::PARAM_STR);
        $stmt->bindParam(':answer_name', $answerName, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (is_null($result) || !$result)
            return false;
        return true;
    }

    //**予測的中者の倍率名をwinnerに変更する。 */
    private function UpdateMagnificationNameWinner($pdo, $answerName)
    {
        $stmt = $pdo->prepare('
        Update answer_user a
        inner join answer_master b
        on a.master_session_id = b.master_session_id
        and a.answer_name = b.answer_name
        and a.answer = b.answer
        set a.magnification_name =\'winner\'
        where a.answer_name =:answer_name 
        and a.master_session_id = (select master_session_id from game_master limit 1)');
        $stmt->bindParam(':answer_name', $answerName, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (is_null($result) || !$result)
            return false;
        return true;
    }

    //**ミニゲームの結果より全てのユーザーのポイントを更新する。sqlで完結する。*/
    private function UpdateGameUserPoint($pdo, $answerName)
    {
        $stmt = $pdo->prepare('
        Update game_user a
        inner join answer_user b
        on a.master_session_id = b.master_session_id
        and a.my_session_id = b.my_session_id 
        and b.answer_name =:answer_name
        inner join magnification c
        on b.magnification_name = c.magnification_name
        set a.point = a.point + (b.bill * c.magnification)
        where a.master_session_id = (select master_session_id from game_master limit 1) 
        ');
        $stmt->bindParam(':answer_name', $answerName, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (is_null($result) || !$result)
            return false;
        return true;
    }

    //**ミニゲーム後のユーザーの平均ポイントを集計し、answer_masterに格納する。 */
    private function UpdateAverage($pdo, $answerName)
    {
        $stmt = $pdo->prepare('
        Update answer_master a,
        (select master_session_id,avg(point) average from game_user group by master_session_id having master_session_id = (select master_session_id from game_master limit 1)) b
        set a.average = b.average
        where a.master_session_id = b.master_session_id
        and a.answer_name =:answer_name 
        ');
        $stmt->bindParam(':answer_name', $answerName, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (is_null($result) || !$result)
            return false;
        return true;
    }
}
