<?php

class GraghParameter
{
    private $array;

    public function __construct()
    {
        $this->array = array();
    }

    //**Chart.js用のデータを分割し、フィールドに格納する。 */
    public function MergeData($data)
    {
        $replace = str_replace('[', '', $data);
        $replace = str_replace(']', '', $replace);
        $datas = explode(",", $replace);
        $this->array = array_merge($this->array, $datas);
    }

    //**グラフの最大値を求める。ユーザーのデータと平均値のデータのうち最大値+50をグラフの最大値とする。 */
    public function GetGraphMax()
    {
        return max($this->array) + 50;
    }
}
