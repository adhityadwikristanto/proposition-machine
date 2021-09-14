<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Factory\Table_of_truth;
use App\Http\Factory\Text_processing;

class PropositionalController extends Controller
{
    public $left, $right;

    public function initiation(Request $req)
    {
        $this->left = str_replace(" ","",$req->left);
        $this->right = str_replace(" ","",$req->right);

        //count number of prop in left
        $text_processing = new text_processing();
        $count_prop = $text_processing->get_number_of_proposition($this->left, $this->right);
        $left_count = $count_prop[0];
        $right_count = $count_prop[1];
        $valid_variable = $count_prop[2];
        $left_variables = $count_prop[3];
        $right_variables = $count_prop[4];
        
        //if varibles are not operatable, then send message and return
        if($valid_variable === false){
            return view ('proposition',['text_message' => "INVALID"]);
        }

        // echo "count left: ".$left_count."  count right: ".$right_count."</br>";

        //creating table of truth for both sides
        // $truth_table = new Table_of_truth();
        // $truth_table_left = [];
        // $truth_table_right = [];
        
        // if($left_count == 2)
        // {
        //     $truth_table_left = $truth_table->get_combination_pq();
        // }else if($left_count == 3)
        // {
        //     $truth_table_left = $truth_table->get_combination_pqr();
        // }else if($left_count == 1)
        // {
        //     $truth_table_left = $truth_table->get_combination_p();
        // }

        // if($right_count == 2)
        // {
        //     $truth_table_right = $truth_table->get_combination_pq();
        // }else if($right_count == 3)
        // {
        //     $truth_table_right = $truth_table->get_combination_pqr();
        // }else if($right_count == 1)
        // {
        //     $truth_table_right = $truth_table->get_combination_p();
        // }

        $max_count = $left_count;
        $variables = [];
        if($right_count > $left_count){
            $max_count = $right_count;
            
            foreach($right_variables as $var){
                array_push($variables, $var);
            }

            // if($right_variables[0] > 0){
            //     array_push($variables, "P");
            // }
            // if($right_variables[1] > 0){
            //     array_push($variables, "Q");
            // }
            // if($right_variables[2] > 0){
            //     array_push($variables, "R");
            // }
        }else{
            foreach($left_variables as $var){
                array_push($variables, $var);
            }
            // if($left_variables[0] > 0){
            //     array_push($variables, "P");
            // }
            // if($left_variables[1] > 0){
            //     array_push($variables, "Q");
            // }
            // if($left_variables[2] > 0){
            //     array_push($variables, "R");
            // }
        }

        $truth_table = new Table_of_truth();
        $truth_table_max = $truth_table->get_combination($max_count);
        // var_dump($truth_table_max);
        


        //LEFT SIDE
        
        $whole_operation_result_left = [];
        $whole_operation_result_right = [];
        
        foreach($truth_table_max as $row)
        {
            //set empty bowl
            $result_left = false;
            $result_right = true;

            //replace proposition with T and F
            $replaced_left_as_array = $text_processing->replace_proposition($left_count, $row, str_split($this->left), $variables);
            $replaced_right_as_array = $text_processing->replace_proposition($right_count, $row, str_split($this->right), $variables);

            //proceed left 
            $array_temp = $replaced_left_as_array;
            while(true){
                $array_temp = $text_processing->track($array_temp);
                if(is_bool($array_temp) == 1){
                    $result_left = $array_temp;
                    break;
                }else if($array_temp === "Oh God"){
                    return view ('proposition',['text_message' => "INVALID"]);
                }
            }
            array_push($whole_operation_result_left, $result_left);

            //proceed right
            $array_temp = $replaced_right_as_array;
            while(true){
                $array_temp = $text_processing->track($array_temp);
                if(is_bool($array_temp) == 1){
                    $result_right = $array_temp;
                    break;
                }else if($array_temp === "Oh God"){
                    echo "here </br>";
                    return view ('proposition',['text_message' => "INVALID"]);
                }
            }
            array_push($whole_operation_result_right, $result_right);
        }

        //check wether two result arrays are equivalent
        $temp_index = 0;
        $is_equivalent = true;
        while ($temp_index < sizeof($whole_operation_result_left))
        {
            // echo "result left: ".$whole_operation_result_left[$temp_index]. "   result right: ".$whole_operation_result_right[$temp_index]."</br>";
            if($whole_operation_result_left[$temp_index] !== $whole_operation_result_right[$temp_index]){
                $is_equivalent = false;
            }
            $temp_index++;
        }

        $text_message = "";
        if($is_equivalent){
            $text_message = "equivalent"; 
        }else{
            $text_message = "not equivalent"; 
        }

        
        return view ('proposition',['text_message' => $text_message,
                                    'left_result' => $whole_operation_result_left,
                                    'right_result' => $whole_operation_result_right,
                                    'truth_table' => $truth_table_max,
                                    'max_size' => $max_count,
                                    'left_sentence' => $this->left,
                                    'right_sentence' => $this->right,
                                    'variables' => $variables
                                    ]);
    }
}
