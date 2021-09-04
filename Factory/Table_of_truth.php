<?php
namespace App\Http\Factory;

class Table_of_truth
{
    public function get_combination_p()
    {
        return [
            ["F","T"],
        ];
    }
    public function get_combination_pq()
    {
        return [
            ["F","F"],
            ["T","F"],
            ["F","T"],
            ["T","T"],
        ];
    }

    public function get_combination_pqr()
    {
        return [
            ["F","T","T"],
            ["F","T","F"],
            ["F","F","T"],
            ["F","F","F"],
            ["T","T","T"],
            ["T","T","F"],
            ["T","F","T"],
            ["T","F","F"],
        ];
    }

    public function test_operation($prop1, $operator, $prop2)
    {
        $left = false;
        $right = false;
        
        switch($prop1)
        {
            case "T":
                $left = true;
                break;
            case "F":
                break;
        }

        switch($prop2)
        {
            case "T":
                $right = true;
                break;
            case "F":
                break;
        }
        
        $result = false;

        if($operator === "V")
        {
            $result = ($left OR $right); 
        }else if($operator === "^")
        {
            $result = ($left AND $right); 
        }else if($operator == ">")
        {
            //imply
            if($right === true){
                $result = true;
            }else if($left === true && $right === false){
                $result = false;
            }else{
                $result = true;
            }
        }else if($operator == "<>")
        {
            //if only if
            if($left === $right){
                $result = true;
            }else{
                $result = false;
            }

        }else if($operator == "X")
        {
            //xor
            $result = $left XOR $right;
        }

        return $result;

    }

    public function test_negation($prop)
    {
        $negation = FALSE;
        switch($prop)
        {
            case "T":
                break;
            case "F":
                $negation = TRUE;
                break;
        }
        return $negation;
    }
}

?>