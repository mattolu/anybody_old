<?php

$in = strtolower($_GET['searchQuery']);

$input = trim($in);

$query=str_replace(" ","+", $_GET['searchQuery']);



//$url= "https://www.googleapis.com/customsearch/v1?key=AIzaSyB5WOpV4_-J6QK2XbldcQ-BVgJl6FotTTo&cx=009130427976801447388:idrpxx1qx8c&q=".$query."&fields=items(title,snippet,pagemap/cse_image)" ;
 $url= "https://www.googleapis.com/customsearch/v1?key=AIzaSyD777ZEvs4UqMZ7kxAv-w98TK1E4hdGoII&cx=018412839258995437894:fgnrg7qhlqy&q=".$query."&fields=items(title,snippet,pagemap/cse_image)" ;
//$url= "https://www.googleapis.com/customsearch/v1?key=AIzaSyATF24vZ97D7lbdQ1zPuxfJcGvJDQhLh0A&cx=009130427976801447388:athkuwtwhli&q=".$query."&fields=items(title,snippet,pagemap/cse_image)" ;

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL,$url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
$result = curl_exec($curl);

//To Calculate the weight of the searches across the returned json from the server
$sum=0; 
//To store the sources of the search
$myArray=[];
$myArrayTitle =[];
$urlImgArray=[];
$mySourceArray = [];
$inputArray = [];
$titleArray = [];
$urlTitle =[];
$titleArray1 = [];
$urlTitle1 =[];
$imageUrl;
$newTit = [];
$resultArray=[];


if(!empty($result)) {
    $jsonResult = json_decode($result, true);
    $resultArray= $jsonResult['items'];
    if ($resultArray==NULL){
        echo ('Daily limit exceeded. Please try again tomorrow');
        exit;
    }else{
            
            $arrlength = count($resultArray);
            print_r($resultArray);
                if (strpos($input, ' ')){
                $splitr= explode(" ",$input);
                $inputArray[0] = $splitr[1];
                $inputArray[1] = $splitr[0];
                $input1 = implode(' ', $inputArray);
                // if ($input1 !== $input){
                //     echo ($input);
                // }

                //  print_r($inputArray);
                 
                // echo (count($splitr));
                for($x = 0; $x < $arrlength; $x++) {
                    $title=strtolower($resultArray[$x]['title']);
                    $snippet=strtolower($resultArray[$x]['snippet']);

                    $image=(!empty($resultArray[$x]['pagemap']));
    
                    $titleCount = (substr_count($title, $input)); //counting the occurence of the title
                        if ($titleCount==0)
                        $titleCount = (substr_count($title, $input1));
                    $snippetCount =(substr_count($snippet, $input));
                        if ($snippetCount==0)
                        $snippetCount = (substr_count($snippet, $input1));
                        
            //Getting the corresponding image with the title where the image is located
                    if (($titleCount!=0)&&($image)){
                        $imageUrl =  $resultArray[$x]['pagemap']['cse_image'][0]['src'];
                        
                        array_push($urlImgArray, $imageUrl);
                        if  (strpos($title, '·')) {
                        $newTit= explode(' · ', $title);
                        array_push($mySourceArray, $newTit[1]);
                        }elseif  (strpos($title, '|')) {
                        $newTit= explode(' | ', $title);
                        array_push($mySourceArray, $newTit[1]);
                        }elseif  (strpos($title, '-')) {
                            $newTit= explode(' - ', $title);
                            array_push($mySourceArray, $newTit[1]) ;
                        }elseif  (strpos($title, '•')) {
                            $newTit= explode(' • ', $title);
                            array_push($mySourceArray, $newTit[1]) ;
                        }
                       
                    // } else{
                    //     printf ("No perfect Match for ".ucwords($_GET['searchQuery']));
                    }

                    //Getting multiple results
                    if (($titleCount!=0)&&($snippetCount!=0)&&($image)){
                        array_push($titleArray, $title);
                        array_push($urlTitle, $imageUrl);
                    // print_r($title);
                    // print_r($$imageUrl);

                    }

                    //to get the exact searching terms
                    if ((preg_match("~\b$input1\b~",$title))&&($snippetCount!=0)&&($image)){
                        array_push($titleArray1, $title);
                        array_push($urlTitle1, $imageUrl);
                    // print_r($title);
                    // print_r($$imageUrl);

                    }
                    //Getting the source that contains the full search query
                if (($titleCount!=0)&&($snippetCount!=0)){
                    if  (strpos($title, '·')) {
                        $newTit= explode(' · ', $title);
                        array_push($myArray, $newTit[1]) ;
                        
                        }elseif  (strpos($title, '|')) {
                            $newTit= explode(' | ', $title);
                            array_push($myArrayTitle, $newTit[0]) ;
                            array_push($myArray, $newTit[1]);
                            // echo implode(" | ", $myArray);        
                        }elseif  (strpos($title, '-')) {
                            $newTit= explode(' - ', $title);
                            array_push($myArray, $newTit[1]) ;
                        }elseif  (strpos($title, '•')) {
                            $newTit= explode(' • ', $title);
                            array_push($myArray, $newTit[1]) ;
                        }
                        }

                    $titleCount1 = (substr_count($title, $splitr[0]));
                    $titleCount2 = (substr_count($title, $splitr[1])); 
                    $snippetCount1 =(substr_count($snippet, $splitr[0]));
                    $snippetCount2 =(substr_count($snippet, $splitr[1]));
                    

                if (($titleCount!=0)&&($snippetCount!=0)){
                    $sum = $sum + 1.00;
                    }elseif(($titleCount==0)&&($snippetCount!=0)){
                        $sum =$sum + 0.30;
                    }elseif(($titleCount!=0)&&($snippetCount==0)){
                        $sum =$sum + 0.70;
                    }elseif (($titleCount1!=0)&&($snippetCount1!=0)){
                        $sum = $sum + 0.50;
                    }elseif(($titleCount1==0)&&($snippetCount1!=0)){
                        $sum =$sum + 0.15;
                    }elseif(($titleCount1!=0)&&($snippetCount1==0)){
                        $sum =$sum + 0.35;
                    }elseif (($titleCount2!=0)&&($snippetCount2!=0)){
                        $sum = $sum + 0.50;
                    }elseif(($titleCount2==0)&&($snippetCount2!=0)){
                        $sum =$sum + 0.15;
                    }elseif(($titleCount2!=0)&&($snippetCount2==0)){
                        $sum =$sum + 0.35;
                    }elseif (($titleCount1==0)&&($snippetCount1==0)){
                        $sum = $sum + 0.00;
                    }elseif (($titleCount2==0)&&($snippetCount2==0)){
                        $sum = $sum + 0.00;
                    }elseif (($titleCount==0)&&($snippetCount==0)){
                        $sum = $sum + 0.00;
                    }
        }
        
         $sourceArrray = array_unique($myArray);
        //  $titleArrayCount = count($titleArray);
         $divisorCounter = count($titleArray);
         
         var_dump($divisorCounter);
         if ($divisorCounter<=3){
             //if the return array is less than or equal to 3


         foreach( $titleArray as $title1 => $code ) {
            echo "<div class='row' style='height:150px; width:600px; background-color:#ccc; padding:10px; border-radius:10px; margin:50px auto 0 auto;'>
            <div class='col-md-9' style='height:140px; background-color:#c234f2; border-radius:5px; padding:5px;'><p>$code</p></div>
           
            <div class='col-md-3' style='height:140px; background-color:#c24ce2;border-radius:5px;padding:5px;'><img class='img-circle' width=130 height=130 src='$urlTitle[$title1]'></div></div><br>
            "; 
            }
         }else{
            foreach( $titleArray1 as $title2 => $code ) {
                //perhaps if the array returned is greater than 5... do strict searching and calculate the average
                echo "<div class='row' style='height:150px; width:600px; background-color:#ccc; padding:10px; border-radius:10px; margin:50px auto 0 auto;'>
                <div class='col-md-9' style='height:140px; background-color:#c234f2; border-radius:5px; padding:5px;'><p>$code</p></div>
               
                <div class='col-md-3' style='height:140px; background-color:#c24ce2;border-radius:5px;padding:5px;'><img class='img-circle' width=130 height=130 src='$urlTitle1[$title2]'></div></div><br>
                "; 
                }
        //    print_r($titleArray1);
        //    print_r($urlTitle1);
         }
         
      
        // var_dump($divisorCounter);

    }else{
        $arrlength = count($resultArray);
        for($x = 0; $x < $arrlength; $x++) {
            $title=strtolower($resultArray[$x]['title']);
            $snippet=strtolower($resultArray[$x]['snippet']);
            $snippetCount =(substr_count($snippet, $input));
            $image=(!empty($resultArray[$x]['pagemap']));
            
        
            $titleCount = (substr_count($title, $input)); //counting the occurence of the title

            
    //Getting the corresponding image with the title where the image is located
            if (($titleCount!=0)&&($image)){
                
                $imageUrl =  $resultArray[$x]['pagemap']['cse_image'][0]['src'];
                array_push($urlImgArray, $imageUrl);
            if  (strpos($title, '·')) {
                $newTit= explode(' · ', $title);
                array_push($mySourceArray, $newTit[1]);

                }elseif  (strpos($title, '|')) {
                $newTit= explode(' | ', $title);
                array_push($mySourceArray, $newTit[1]);
                }
                if  (strpos($title, '·')) {
                    $newTit= explode(' · ', $title);
                    array_push($myArray, $newTit[1]) ;
                    }elseif  (strpos($title, '|')) {
                        $newTit= explode(' | ', $title);
                        array_push($myArray, $newTit[1]);
                        // echo implode(" | ", $myArray);        
                    }elseif  (strpos($title, '-')) {
                        $newTit= explode(' - ', $title);
                        array_push($myArray, $newTit[1]) ;
                    }if  (strpos($title, '•')) {
                        $newTit= explode(' • ', $title);
                        array_push($myArray, $newTit[1]) ;
                    }
                   
            }
            if (($titleCount!=0)&&($snippetCount!=0)){
                $sum = $sum + 1.00;
                }elseif(($titleCount==0)&&($snippetCount!=0)){
                    $sum =$sum + 0.30;
                }elseif(($titleCount!=0)&&($snippetCount==0)){
                    $sum =$sum + 0.70;
                }elseif (($titleCount==0)&&($snippetCount==0)){
                    $sum = $sum + 0.00;
                }
                
                    //Getting multiple results
                    if (($titleCount!=0)&&($snippetCount!=0)&&($image)){
                        array_push($titleArray, $title);
                        array_push($urlTitle, $imageUrl);
                    // print_r($title);
                    // print_r($imageUrl);
                    }

            
    }
    $sourceArrray = array_unique($myArray);
    if ($titleArrayCount>5){
        //  for ($i=0; $i<$titleArrayCount; $i++){
        // // var_dump(preg_match("~\b$input\b~",$newTit[0]));
        // var_dump($newTit[0]);
     foreach( $titleArray as $title1 => $code ) {
        echo "<div class='row' style='height:150px; width:600px; background-color:#ccc; padding:10px; border-radius:10px; margin:50px auto 0 auto;'>
        <div class='col-md-9' style='height:140px; background-color:#c234f2; border-radius:5px; padding:5px;'><p>$code</p></div>
       
        <div class='col-md-3' style='height:140px; background-color:#c24ce2;border-radius:5px;padding:5px;'><img class='img-circle' width=130 height=130 src='$urlTitle[$title1]'></div></div><br>
        "; 
        }
     }else{
        foreach( $titleArray1 as $title2 => $code ) {
            echo "<div class='row' style='height:150px; width:600px; background-color:#ccc; padding:10px; border-radius:10px; margin:50px auto 0 auto;'>
            <div class='col-md-9' style='height:140px; background-color:#c234f2; border-radius:5px; padding:5px;'><p>$code</p></div>
           
            <div class='col-md-3' style='height:140px; background-color:#c24ce2;border-radius:5px;padding:5px;'><img class='img-circle' width=130 height=130 src='$urlTitle1[$title2]'></div></div><br>
            "; 
            }
    //    print_r($titleArray1);
    //    print_r($urlTitle1);
     }
     $divisorCounter = count($titleArray1);
    // var_dump($myArray);
 }
    }
} else {
    echo "Data not fetched.";
    curl_close($curl);
    exit;
}
//    print_r($urlImgArray);
   ?>

<!DOCTYPE html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width" />
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="index.css">
    <title>Anybody</title>
    <style>
        .user-globe-rank{
            text-transform:capitalize;
        }
         @import url('https://fonts.googleapis.com/css?family=Open+Sans|Open+Sans+Condensed:700');
    </style>
 
    
</head>

<body>

<div id="content" style="width:1000px; margin:30px auto 0 auto; height:800px;">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
               <div class="row">
                   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-radius: 16px;">
                       <div class="well profile col-lg-12 col-md-12 col-sm-12 col-xs-12" style="height:250px;">
                           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                               
                               <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 divider text-center row"></div>
                                        <div class="col-md-8 col-xs-8 col-sm-8" >
                                        <h4><p style="text-align: center;"><strong id="user-globe-rank"><?php echo ucwords($_GET['searchQuery'])?></strong></p></h4>
                                       <h4><p style="text-align: center;"><strong id="user-globe-rank"><?php if ($divisorCounter!=0) {echo($percent=(($sum*10)/$divisorCounter) .'% Occurence');}
                                       else{echo 'Not Found! Try again later. Thank you for using ANYBODY';}?> </strong></p></h4> 
                                       <h4><p style="text-align: center;"><strong id="user-globe-rank"><?php echo ucwords(implode(" | ", $sourceArrray));?> </strong></p></h4> 
                                      
                                       <p><small class="label label-success">Percentage Validity Search</small></p>
                                        </div>
                                        <!-- <div class="col-md-4 col-xs-4 col-sm-4" style="height:100px; margin:auto">
                                            <img style="margin:auto;" 
                                            src="<?php 
                                            if(count($urlImgArray)==0){
                                                echo "https://via.placeholder.com/90";
                                            }else{
                                                echo ($urlImgArray[0]);
                                            }?>" class="img-responsive"> -->



                                            <!-- <p style="text-align: center; font-size:10px;"><strong id="user-globe-rank"><?php
                                            if(empty($urlImgArray[0])){
                                                echo "<p class='btn-danger'style='text-align: left; font-size:10px; border-radius:5px;padding:5px;'>Sorry! No Picture found</p>";
                                            }else{
                                                echo ucwords(($mySourceArray[0]));
                                            }
                                            ?> </strong></p>  -->
                                    </div>
                                   
                             </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>    
       </div>
   </div>
</div>

</body>
</html>
   <?php
?>