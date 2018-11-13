<?php

// $compareQuery = preg_replace("/[^a-zA-Z]/","|", $_GET['searchQuery']);


$input = strtolower($_GET['searchQuery']);
// $compare = explode(" ",$input);
// $compareQuery = implode("|",$compare);
// // var_dump($compareQuery);

$query=preg_replace("/[^a-zA-Z]/","+", $_GET['searchQuery']);
$url= "https://www.googleapis.com/customsearch/v1?key=AIzaSyB5WOpV4_-J6QK2XbldcQ-BVgJl6FotTTo&cx=009130427976801447388:idrpxx1qx8c&q=".$query."&fields=items(title,snippet,pagemap/cse_image)" ;

// $url= "https://www.googleapis.com/customsearch/v1?key=AIzaSyATF24vZ97D7lbdQ1zPuxfJcGvJDQhLh0A&cx=009130427976801447388:athkuwtwhli&q=".$query."&fields=items(title,snippet,pagemap/cse_image)" ;

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL,$url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
$result = curl_exec($curl);
if(!empty($result)) {
    $jsonResult = json_decode($result, true);
    $resultArray= $jsonResult['items'];

    $arrlength = count($resultArray);

    $sum=0; 
    // $titleCount=0;
    // $snippetCount=0;
    for($x = 0; $x < $arrlength; $x++) {
        $title=strtolower($resultArray[$x]['title']);
        $snippet=strtolower($resultArray[$x]['snippet']);
        $titleCount = (substr_count($title, $input));
        $snippetCount =(substr_count($snippet, $input));
       if (($titleCount!=0)&&($snippetCount!=0)){
           $sum = $sum + 1.00;
           echo($sum);
           echo"<br>";
       }elseif(($titleCount==0)&&($snippetCount!=0)){
        $sum =$sum + 0.30;
        echo($sum);
        echo"<br>";
    }elseif(($titleCount!=0)&&($snippetCount==0)){
        $sum =$sum + 0.70;
        echo($sum);
        echo"<br>";
    }elseif (($titleCount==0)&&($snippetCount==0)){
        $sum = $sum + 0.00;
        echo($sum);
        echo"<br>";
    }
}
   echo($sum);
   echo"<br>";
   echo($percent=($sum*10) .'% occurence');
   echo"<br>";
} else {
    echo "Data not fetched.";
}
curl_close($curl);
?>