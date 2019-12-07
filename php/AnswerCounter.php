<?php

class AnswerCounter
{
    private $a;
    private $b;
    private $c;
    private $d;
    private $non;
    private $miniGamer;

    public function __construct()
    {
        $this->a = null;
        $this->b = null;
        $this->c = null;
        $this->d = null;
        $this->non = null;
        $this->miniGamer = null;
    }

    public function SetValue($answerNo, $count)
    {
        if (strcmp($answerNo, 'A') == 0) {
            $this->a = $count;
            return;
        }
        if (strcmp($answerNo, 'B') == 0) {
            $this->b = $count;
            return;
        }
        if (strcmp($answerNo, 'C') == 0) {
            $this->c = $count;
            return;
        }
        if (strcmp($answerNo, 'D') == 0) {
            $this->d = $count;
            return;
        }
        if (strcmp($answerNo, '-') == 0) {
            $this->non = $count;
            return;
        }
        if (strcmp($answerNo, '*') == 0) {
            $this->miniGamer = $count;
            return;
        }
    }

    //** 投票率を返す。小数点第1桁まで */
    public function GetVotingRate()
    {
        $numerator = $this->a +  $this->b + $this->c + $this->d + $this->miniGamer;
        $denominator = $numerator + $this->non;
        return round($numerator / $denominator * 100, 1);
    }

    public function PrintChartData()
    {
        $str = "data: [";
        $str = $str . $this->GetValue($this->a) . ',';
        $str = $str . $this->GetValue($this->b) . ',';
        $str = $str . $this->GetValue($this->c) . ',';
        $str = $str . $this->GetValue($this->d) . ',';
        $str = $str . $this->GetValue($this->miniGamer) . ',';
        $str = $str . $this->GetValue($this->non) . ',';
        $str = $str . "]";
        return $str;
    }

    private function GetValue($value)
    {
        if (is_null($value))
            return '';
        return $value;
    }
}
