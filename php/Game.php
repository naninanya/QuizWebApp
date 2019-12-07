<?php
require('AnswerCounter.php');

//** ゲームの表示にかかわるクラス */
class Game
{
    private $votingRate;
    private $chartData;

    public function GetVotingRate()
    {
        return $this->votingRate;
    }

    public function GetChartData()
    {
        return $this->chartData;
    }

    /** ユーザー一覧を取得する。(一回目のみ)*/
    public function GetGameDataForTable()
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $stmt = $pdo->query('
        Select my_session_id,nickname,point 
        from game_user 
        where master_session_id = (select master_session_id from game_master limit 1) 
        order by point desc');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** 特定の投票に対するユーザーの回答数を回答ごとに取得する。*/
    public function GetCountGroupByAnswer($answer_name)
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $stmt = $pdo->prepare("
        Select master_session_id,answer_name,answer,count(1) as 'count' 
        from answer_user 
        group by master_session_id,answer_name,answer
        having master_session_id = (select master_session_id from game_master limit 1)
        and answer_name =:answer_name
        order by answer");
        $stmt->bindParam(':answer_name', $answer_name, PDO::PARAM_STR);
        $stmt->execute();
        $answerCounter = new AnswerCounter();
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row)
            $answerCounter->SetValue($row['answer'], $row['count']);
        $this->votingRate = $answerCounter->GetVotingRate();
        $this->chartData = $answerCounter->PrintChartData();
    }

    /** 特定の投票に対するユーザーの回答一覧を取得する。*/
    public function GetAnswerDataForTable($answer_name)
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $stmt = $pdo->prepare("
        Select a.my_session_id,a.nickname,b.answer,b.bill 
        from game_user a 
        inner join answer_user b 
        on a.master_session_id = b.master_session_id 
        and a.my_session_id = b.my_session_id 
        where a.master_session_id = (select master_session_id from game_master limit 1) 
        and b.answer_name =:answer_name
        order by bill desc");
        $stmt->bindParam(':answer_name', $answer_name, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**ユーザー一覧を取得する。(2回目以降)*/
    public function GetGameDataForTableWithChange($answerName)
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $stmt = $pdo->prepare("
        Select a.my_session_id,a.nickname,a.point,b.bill * c.magnification as 'change'
        from game_user a
        inner join answer_user b
        on a.master_session_id = b.master_session_id
        and a.my_session_id = b.my_session_id
        and b.answer_name =:answer_name 
        inner join magnification c
        on b.magnification_name = c.magnification_name
        where a.master_session_id = (select master_session_id from game_master limit 1) 
        order by a.point desc");
        $stmt->bindParam(':answer_name', $answerName, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //**既にゲームマスターが回答しているか否か */
    public function IsAlredyGameMasterAnswer($answerName)
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $stmt = $pdo->prepare("
        Select 1 from answer_master
        where master_session_id = (select master_session_id from game_master limit 1)
        and answer_name =:answer_name
        and answer <> '-'
        ");
        $stmt->bindParam(':answer_name', $answerName, PDO::PARAM_STR);
        $stmt->execute();
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row)
            return true;
        return false;
    }

    //**特定の回答時点の平均ポイントを取得し、Chart.jsで使用されるdata文字列で返す。 */
    public function GetAverageAtAnswer($answerName)
    {
        $db = new DBAdapter();
        $pdo = $db->GetPDO();
        $stmt = $pdo->prepare("
         Select average
         from answer_master
         where master_session_id = (select master_session_id from game_master limit 1)
         and answer_name <=:answer_name
         order by answer_name
         ");
        $stmt->bindParam(':answer_name', $answerName, PDO::PARAM_STR);
        $stmt->execute();
        $str = '[100,';
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $str = $str . round($row['average'], 1) . ','; //どうやって0を埋める？クラス化?→埋めなくてもよい。
        }
        $str = $str . "]";
        return $str;
    }
}
