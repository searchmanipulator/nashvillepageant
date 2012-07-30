<?
  //implement PHP5 array_walk_recursive

if (!function_exists('array_walk_recursive'))
{
   function array_walk_recursive(&$input, $funcname, $userdata = "")
   {
       if (!is_callable($funcname))
       {
           return false;
       }
       
       if (!is_array($input))
       {
           return false;
       }
       
       foreach ($input AS $key => $value)
       {
           if (is_array($input[$key]))
           {
               array_walk_recursive($input[$key], $funcname, $userdata);
           }
           else
           {
               $saved_value = $value;
               if (!empty($userdata))
               {
                   $funcname($value, $key, $userdata);
               }
               else
               {
                   $funcname($value, $key);
               }
               
               if ($value != $saved_value)
               {
                   $input[$key] = $value;
               }
           }
       }
       return true;
   }
}
//===========================  

//eliminate commas from array members destined to be displayed in a comma delimited list
  function nocomma(&$item, $key)
  {
    $f = ereg_replace("[,]+", "_", $item);
	$item = $f;
  }


?>