<?php


include 'Database.php';


$query=$_GET['message'];


$whatsapp=$_GET['whatsapp'];


 $sql="SELECT * FROM `whatsapp_userBase` WHERE Whatsapp='$whatsapp'";

 $result=mysqli_query($conn, $sql);
 $data=mysqli_fetch_assoc($result);




 if ($result->num_rows > 0)
 {
     
     $current_millis = round(microtime(true) * 1000);
     
          if ($current_millis<$data['membershipEnds'])
         {
              getResponse();
         }else if($data['Credit']>0)
         {
              getResponse();
         }
         else
         {
             $sql22="UPDATE whatsapp_userBase SET isMember=0,Credit=0 WHERE Whatsapp='$whatsapp'"; #update email and send data


            if(mysqli_query($conn, $sql22))
            {
                    echo '100' ; #no balance
            }
         }


 }
 else
 {
     echo '200'; #user not found
 }


function getResponse()
{






            $url = 'https://api.openai.com/v1/chat/completions';

            # Initialize cURL session
            $curl = curl_init();



            # Set the request options
           $options = array(

                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode(array(
                               'model' => 'gpt-3.5-turbo', // Change to 'gpt-3.5-turbo' for the GPT-3.5 Turbo model
                                
                                'temperature' => 0.5, // Controls the randomness of the generated response
                                'max_tokens' => 275, // Controls the maximum length of the generated response
                                'n' => 1, // Number of responses to generate
                'messages' => array(
        array(
            'role' => 'user',
            'content' => $GLOBALS['query'], 
        ),
))),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer sk-OPEN_AI_TOKEN'
                ),
            );
                # Set the cURL options
                curl_setopt_array($curl, $options);

                # Execute the cURL request
                $response = curl_exec($curl);

                # Close the cURL session
                curl_close($curl);

                # Handle the response data

                if ($response === false)
                {
                    echo 'Error:affix ' . curl_error($curl);
                } else {
                    # Do something with the response data
                    $responseArr = json_decode($response, true);
                    $responseMsg = $responseArr['choices'][0]['message']['content'];


                    sendResponse($responseMsg);
                    #echo $response;
                    #echo $responseMsg;
                }



}
function sendResponse($responseMsg)
{
     global $whatsapp, $conn;

     $sql2="UPDATE whatsapp_userBase SET Credit=Credit-1,totalCall=totalCall+1 WHERE Whatsapp='$whatsapp'"; #update email and send data


    if(mysqli_query($conn, $sql2))
    {
        $sql3="SELECT * FROM `whatsapp_userBase` WHERE Whatsapp='$whatsapp'";

        $result1=mysqli_query($conn, $sql3);

        $json_array2=array();

         while($row2=mysqli_fetch_assoc($result1))
       {

          $json_array2[]=$row2;
        }

        $newObject = array(
            'Reply' => $responseMsg
        );

        $json_array2[]=$newObject;

    echo json_encode($json_array2);

    }




}




?>
