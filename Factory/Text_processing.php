<?php
namespace App\Http\Factory;

use App\Http\Factory\Table_of_truth;

class Text_processing
{
    protected $array_left, $array_right;

    //return the number of prop from both sides
    public function get_number_of_proposition($left, $right)
    {
        //count left
        $this->array_left = str_split($left);
        $count1 = $this->count_of_proposition($this->array_left);
        $left_count = $count1['prop'];
        $variable_left = [$count1['p'],$count1['q'],$count1['r']];
        
        //count right
        $this->array_right = str_split($right);
        $count2 = $this->count_of_proposition($this->array_right);
        $right_count = $count2['prop'];
        $variable_right = [$count2['p'],$count2['q'],$count2['r']];

        $valid_variable = true;
        if($left_count > $right_count){
            $i=0;
            foreach($variable_right as $var){
                if($var > 0){
                    if($variable_left[$i] == 0){
                        $valid_variable = false;
                    }
                }
                $i++;
            }
        }else{
            $i=0;
            foreach($variable_left as $var){
                if($var > 0){
                    if($variable_right[$i] == 0){
                        $valid_variable = false;
                    }
                }
                $i++;
            }
        }

        return [$left_count, $right_count, $valid_variable, $variable_left, $variable_right];
    }

    //get array text
    public function get_array_text($flag)
    {
        switch($flag)
        {
            case "left":
                return $this->array_left;
            case "right":
                return $this->array_right;
        }
    }

    //count the variation of prop
    private function count_of_proposition($array_text)
    {
        $count_p = 0; 
        $count_q = 0; 
        $count_r = 0;
        $count_prop = 0;

        foreach($array_text as $char)
        {
            if(strtoupper($char) === "P")
            {
                $count_p++;
            }else if(strtoupper($char) === "Q")
            {
                $count_q++;
            }else if(strtoupper($char) === "R")
            {
                $count_r++;
            }
        }
        if($count_p >0){
            $count_prop++;
        }
        if($count_q >0){
            $count_prop++;
        }
        if($count_r >0){
            $count_prop++;
        }

        return ['prop' => $count_prop, 'p' => $count_p, 'q' => $count_q, 'r' => $count_r,];
    }

    //replace proposition with truth table
    public function replace_proposition($count, $truth_table, $array_to_proceed)
    {
        //empty bowl for returning value
        $array_text = $array_to_proceed;
        
        $i=0;
        while($i < sizeof($array_text)){
            $array_text[$i] = strtoupper($array_text[$i]);
            $i++;
        }

        $pqr = [];

        //is it left or right side? determine the array text
        /* if($flag == "left")
        {
            $array_text = $this->array_left;
        }else{
            $array_text = $this->array_right;
        } */

        //if count is two then the possible props are p, q, or r. we need to make sure what are those
        if($count === 1)
        {
            $pos_p = array_search("P",$array_text);
            $pos_q = array_search("Q",$array_text);
            $pos_r = array_search("R",$array_text);

            $bowl_for_pqr = [];

            $string1 = "";
            if($pos_p !== FALSE){
                $string1 = "P";
            }else if($pos_q !== FALSE){
                $string1 = "Q";
            }else if($pos_r !== FALSE){
                $string1 = "R";
            }


            //find suitable value inside truth table
            $index_truth_value = array_search($string1, $truth_table);

            $i = 0;
            foreach($array_text as $char)
            {
                if(strtoupper($char) === $string1)
                {
                    $array_text[$i] = $truth_table[$index_truth_value];
                }
                $i++;
            }
            // var_dump($array_text);

            //collecting variabel that used in sentences
            array_push($pqr, $string1);
            
            return $array_text;

        }else if($count === 2)
        {
            $pos_p = array_search("P",$array_text);
            $pos_q = array_search("Q",$array_text);
            $pos_r = array_search("R",$array_text);
            $string1 = "";
            $string2 = "";
            if($pos_p !== FALSE){
                $string1 = "P";
                if($pos_q !== FALSE){
                    $string2 = "Q";
                }else if($pos_r !== FALSE){
                    $string2 = "R";
                }
            }else{
                $string1 = "Q";
                $string2 = "R";
            }

            //find suitable value inside truth table
            $index_truth_value_1 = array_search($string1, $truth_table);
            $index_truth_value_2 = array_search($string2, $truth_table);

            $i = 0;
            foreach($array_text as $char)
            {
                if(strtoupper($char) === $string1)
                {
                    $array_text[$i] = $truth_table[$index_truth_value_1];
                }else if(strtoupper($char) === $string2)
                {
                    $array_text[$i] = $truth_table[$index_truth_value_2];
                }
                $i++;
            }
            // var_dump($array_text);

            //collecting variabel that used in sentences
            array_push($pqr, $string1);
            array_push($pqr, $string2);

            return $array_text;

        //if count is three, so be it, they must be P, Q, and R
        }else if($count === 3)
        {
            $string1 = "P";
            $string2 = "Q";
            $string3 = "R";

            $i = 0;
            foreach($array_text as $char)
            {
                if($char === $string1)
                {
                    $array_text[$i] = $truth_table[0];
                }else if($char === $string2)
                {
                    $array_text[$i] = $truth_table[1];
                }else if($char === $string3)
                {
                    $array_text[$i] = $truth_table[2];
                }
                $i++;
            }

            //collecting variabel that used in sentences
            array_push($pqr, $string1);
            array_push($pqr, $string2);
            array_push($pqr, $string3);

            return $array_text;
        }
    }

    //scrapping text to determine role of playing
    public function track($array_to_track)
    {
        //set the condition to break the iteration
        //prop => proposition
        //ope => operator
        //neg => negation
        //bo => bracket open
        //bc => bracket close
        $condition1 = "prop+"."ope+"."prop+";
        $condition2 = "neg+"."prop+";
        $condition3 = "bo+prop+bc+";
        $condition4 = "prop+bc+";

        //flag condition to be called, 1 => condition1, 2 => condition2, 3=> condition3
        $flag = "0";

        //empty bowl
        $temp_cond = "";
        $idx = 0;
        $idx_bo = 0;
        $idx_prop = 0;
        $idx_prop_occur = 0;
        $idx_neg = 0;
        $idx_end = 0;

        // echo var_dump($array_to_track) . "</br>";

        //checking if $array_to_track is multiple values or single value
        if(is_array($array_to_track)){
            if((sizeof($array_to_track) === 1)){
                if($array_to_track[0] == "T"){
                    return TRUE;
                }else if($array_to_track[0] == "F"){
                    return FALSE;
                }else{
                    return "Oh God";
                }
            }
        }else{
            if($array_to_track == "T"){
                return TRUE;
            }else if($array_to_track == "F"){
                return FALSE;
            }else{
                return "Oh God";
            }
        }


        //if it's an array then go down to this
        foreach($array_to_track as $char)
        {
            // var_dump($array_to_track);
            // echo "</br>";
            if(($char == "T") OR ($char == "F"))
            {
                $temp_cond = $temp_cond . "prop+";
                $idx_prop_occur++;
                if($idx_prop_occur == 1){
                    $idx_prop = $idx;
                }
            } else if($char == "~")
            {
                $temp_cond = $temp_cond . "neg+";
                $idx_neg = $idx;
            } else if($char == "(")
            {
                $temp_cond = "bo+";
                $idx_prop_occur = 0;
                $idx_bo = $idx;
            }else if ($char == ")")
            {
                $idx_end = $idx;
                $temp_cond = $temp_cond . "bc+";
                $flag = "3";
                if(strpos($temp_cond, "bo+") === false ){
                    $flag = "4";
                }
                
                break;
            }else if(($char == "V") or ($char == "^") or ($char == ">") or ($char == "<") or ($char == "X"))
            {
                //in case found <> then jump 2 indexes
                if(($char == ">") AND ($array_to_track[$idx-1] == "<")){
                }else{
                    $temp_cond = $temp_cond . "ope+";
                }
            }

            //  echo "temp_cond ".$temp_cond. "</br>";
            if(strpos($temp_cond, $condition2) !== false)
            {
                $idx_end = $idx;
                $flag = "2";
                // $temp_cond = "";
                break;
            }else if((strpos($temp_cond, $condition1) !== false))
            {
                
                $idx_end = $idx;
                $flag = "1";
                $idx_prop_occur = 0;
                // echo "condition: ".$temp_cond . "</br>";
                break;
            }

            $idx++;
        }

        /* PERSIAPAN MENGOLAH OPERASI
        DAN MENGITERASI SAMPAI HABIS */

        //from iteration result, act according to condition
        $truth = new Table_of_truth();

        //empty bowl to put return from $truth
        $truth_result = false;

        switch($flag)
        {
            case "1":
                // echo "masuk case 1 </br>";
                // echo $temp_cond ."</br>";
                $prop1="";
                $ope="";
                $prop2="";
                $temp_index = $idx_prop;
                while($temp_index <= $idx_end)
                {
                    if($array_to_track[$temp_index] != "")
                    {
                        if(($array_to_track[$temp_index] != "(") OR ($array_to_track[$temp_index] != ")") )
                        {
                            if(($array_to_track[$temp_index] == "T") OR ($array_to_track[$temp_index] == "F"))
                            {
                                if($prop1==""){
                                    $prop1 = $array_to_track[$temp_index];
                                }else{
                                    $prop2 = $array_to_track[$temp_index];
                                }
                            }else if(($array_to_track[$temp_index] == "V") OR ($array_to_track[$temp_index] == "^") OR ($array_to_track[$temp_index] == ">") OR ($array_to_track[$temp_index] == "<") OR ($array_to_track[$temp_index] == "X"))
                            {
                                // if(($array_to_track[$temp_index] == ">") and ($array_to_track[$temp_index] == "<")){
                                //     $temp_index = $temp_index + 2;
                                //     $ope = $ope . $array_to_track[$temp_index] . $array_to_track[$temp_index+1];
                                //     continue;
                                // }else{
                                // }
                                $ope = $ope . $array_to_track[$temp_index];
                                // else if($array_to_track[$temp_index] == "<"){
                                //     $ope = $ope . $array_to_track[$temp_index] . $array_to_track[$temp_index+1];
                                // }
                            }
                        }
                    }
                    $temp_index++;
                }
                $truth_result = $truth->test_operation($prop1, $ope, $prop2);

                //truth value change back to string
                $result_as_string = "F";
                if($truth_result){
                    $result_as_string = "T";
                }

                $array_tracked = $this->reconstruct_string($idx_prop, $idx_end, $array_to_track, $result_as_string);

                //stop operation when array_to_track is one size left, probably T or F
                $array_to_iterate = $array_tracked;
                // $temp_return_bowl = false;

                // echo "" . var_dump($array_tracked) ."</br>";
                if(sizeof($array_tracked) === 1){
                    if($array_tracked[0] == "T"){
                        return TRUE;
                    }else if($array_tracked[0] == "F"){
                        return FALSE;
                    }else{
                        return "Oh God";
                    }
                }else{
                    return $array_to_iterate;
                }

            case "2":
                // echo "masuk case 2 </br>";
                // echo $temp_cond ."</br>";
                $prop = "";

                $temp_index = $idx_end;
                while($temp_index > $idx_neg)
                {
                    if(($array_to_track[$temp_index] != "") and ($array_to_track[$temp_index] != "~"))
                    {
                        if(($array_to_track[$temp_index] != "(") OR ($array_to_track[$temp_index] != ")") )
                        {
                            if(($array_to_track[$temp_index] == "T") OR ($array_to_track[$temp_index] == "F"))
                            {
                                $prop = $array_to_track[$temp_index];
                                break;
                            }
                        }
                    }
                    $temp_index--;
                }
                $truth_result = $truth->test_negation($prop);

                //truth value change back to string
                $result_as_string = "F";
                if($truth_result === TRUE){
                    $result_as_string = "T";
                }
                
                // echo "neg: " . $idx_neg . "   end: " . $idx_end ."</br>";
                $array_tracked = $this->reconstruct_string($idx_neg, $idx_end, $array_to_track, $result_as_string);

                //stop operation when array_to_track is one size left, probably T or F
                $array_to_iterate = $array_tracked;
                // $temp_return_bowl = false;

                
                if(sizeof($array_tracked) == 1){
                    if($array_tracked[0] === "T"){
                        return TRUE;
                    }else if($array_tracked[0] === "F"){
                        return FALSE;
                    }else{
                        return "Oh God";
                    }
                    break;
                }
                else{
                    // echo "array to itterate:" . var_dump($array_to_iterate) ." </br>";
                    return $array_to_iterate;
                }

            case "3":
                // echo "masuk case 3 </br>";
                // echo $temp_cond ."</br>";
                // $temp_text_check = "";
                // $temp_index = $idx_bo;
                $range_bo_bc = $idx_end - $idx_bo;

                //if inside bracket there is only a composition, then remove the bracket
                //in case inside the braket there are more than one composition or even an operator, then throw text into iteration
                $array_tracked = [];
                if($range_bo_bc <= 2){
                    //remove bracket
                    if(($array_to_track[$idx_bo+1] != "")){
                        $array_tracked = $this->reconstruct_string($idx_bo, $idx_end, $array_to_track, $array_to_track[$idx_bo+1]);
                    }else{
                        $array_tracked = $this->remove_bracket($idx_bo, $idx_end, $array_to_track);
                    }
                }
                // echo "case 3 done </br>";
                //stop operation when array_to_track is one size left, probably T or F
                $array_to_iterate = $array_tracked;
                if(sizeof($array_tracked) == 1){
                    if($array_tracked[0] == "T"){
                        return TRUE;
                    }else if($array_tracked[0] == "F"){
                        return FALSE;
                    }else{
                        return "Oh God";
                    }
                    break;
                }
                else{
                    return $array_to_iterate;
                }
            case "4":
                // echo "masuk case 4 </br>";
                // echo $temp_cond ."</br>";
                // $temp_text_check = "";
                // $temp_index = $idx_bo;
                $range_prop_bc = $idx_end - $idx_prop;

                //if inside bracket there is only a composition, then remove the bracket
                //in case inside the braket there are more than one composition or even an operator, then throw text into iteration
                $array_tracked = [];
                $i=0;
                foreach($array_to_track as $char){
                    if($i == $idx_end){
                        unset($array_to_track[$i]); 
                    }
                    $i++;
                }
                $array_tracked = array_values($array_to_track);
                // echo "result case 4: ".var_dump($array_tracked)."</br>";
                //stop operation when array_to_track is one size left, probably T or F
                $array_to_iterate = $array_tracked;
                if(sizeof($array_tracked) == 1){
                    if($array_tracked[0] == "T"){
                        return TRUE;
                    }else if($array_tracked[0] == "F"){
                        return FALSE;
                    }else{
                        return "Oh God";
                    }
                    break;
                }
                else{
                    return $array_to_iterate;
                }
        }
        
    }

    //function to reconstruct the array
    private function reconstruct_string($idx_begin, $idx_fin, $array_to_proceed, $new_text)
    {
        //put result back into whole string;
        $temp_index = 0;
        foreach($array_to_proceed as $char)
        {
            if($temp_index == $idx_begin){
                if(($char == "T") OR ($char == "F") ){
                    $array_to_proceed[$temp_index] = $new_text;
                }else if($char == "("){
                    $array_to_proceed[$temp_index] = $new_text;    
                }else if($char == "~"){
                    $array_to_proceed[$temp_index] = $new_text;
                }
            }
            else if(($temp_index > $idx_begin) AND ($temp_index <= $idx_fin)){ 
                unset($array_to_proceed[$temp_index]);
            }else{
            }
            $temp_index++;
        }

        $rearranged_array = array_values($array_to_proceed);
        return $rearranged_array;
    }

    private function remove_bracket($idx_bo, $idx_bc, $array_to_proceed)
    {
        $temp_index = 0;
        foreach($array_to_proceed as $char)
        {
            if(($temp_index >= $idx_bo) AND ($temp_index <= $idx_bc)){
                unset($array_to_proceed[$temp_index]);
            }
            $temp_index++;
        }

        $rearranged_array = array_values($array_to_proceed);
        return $rearranged_array;
    }

}

?>