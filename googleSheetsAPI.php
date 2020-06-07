<?php
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	require __DIR__ . '/vendor/autoload.php';
	echo "500";
	/*Get Data From POST Http Request*/
	$datas = file_get_contents('php://input');
	/*Decode Json From LINE Data Body*/
	$deCode = json_decode($datas,true);

	

	$replyToken = $deCode['events'][0]['replyToken'];
	$userId = $deCode['events'][0]['source']['userId'];
	$type = $deCode['events'][0]['type'];

	$token = "3r2ohGof69ms4hYeSENpaK8E8fBgGV42UkS/a/gzGc88hTHOpNw5+1E3QkAD4E+ENudqYyIIepXAaPZu1pzPcA82PVd0nSyQGA/TQZcAF4BIZt6i8Nhnqp0Uvc9IzqSyg07kI82CK5yUTktOrq6f2AdB04t89/1O/w1cDnyilFU=";

	$LINEProfileDatas['url'] = "https://api.line.me/v2/bot/profile/".$userId;
  	$LINEProfileDatas['token'] = $token;

  	$resultsLineProfile = getLINEProfile($LINEProfileDatas);

  	$LINEUserProfile = json_decode($resultsLineProfile['message'],true);
  	$displayName = $LINEUserProfile['displayName'];
	  echo"200";
	/*
	 * We need to get a Google_Client object first to handle auth and api calls, etc.
	 */
	$client = new \Google_Client();
    $client->setApplicationName('Google Sheets API PHP Quickstart');
    $client->setScopes(\Google_Service_Sheets::SPREADSHEETS);
    $client->setAuthConfig(__DIR__.'/bottest-279517-0dc2d7773a37.json');
    $client->setAccessType('online');
    // $client->setPrompt('select_account consent');
	echo "600";
    $service = new \Google_Service_Sheets($client);

    $spreadsheetId = "1bXbuhVlI11loKILLnIFS3efGds8WMEhYqR5STpyyKxg";

    // updateData($spreadsheetId,$service);
    insertData($spreadsheetId,$service,$displayName);

	function insertData($spreadsheetId,$service,$displayName)
    {
    	// $range = 'congress!D2:F1000000';
	    //INSERT DATA
	    $range = 'a2';
	    $values = [
	    	[$displayName],
	    ];
	    $body = new Google_Service_Sheets_ValueRange([
	    	'values' => $values
	    ]);
	    $params = [
	    	'valueInputOption' => 'RAW'
	    ];
	    $insert = [
	    	'insertDataOption' => 'INSERT_ROWS'
	    ];
	    $result = $service->spreadsheets_values->append(
	    	$spreadsheetId,
	    	$range,
	    	$body,
	    	$params,
	    	$insert
	    );
    }

    function updateData($spreadsheetId,$service)
    {
    	$range = 'a2:b2';
    	$values = [
	    	["Test","Test"],
	    ];
	    $body = new Google_Service_Sheets_ValueRange([
	    	'values' => $values
	    ]);
    	$params = [
	    	'valueInputOption' => 'RAW'
	    ];
	    $result = $service->spreadsheets_values->update(
	    	$spreadsheetId,
	    	$range,
	    	$body,
	    	$params
	    );
    }

    function getData($spreadsheetId,$service)
    {
    	// GET DATA
	    $range = 'congress!D2:F1000000';
		$response = $service->spreadsheets_values->get($spreadsheetId, $range);
		$values = $response->getValues();

		if(empty($values)){
			print "No Data Found.\n";
		}else{
			foreach ($values as $row) {
				echo $row[0]."<br/>";
			}
		}
    }

    function getLINEProfile($datas)
	{
		$datasReturn = [];

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $datas['url'],
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		    "Authorization: Bearer ".$datas['token'],
		    "Postman-Token: 32d99c7d-9f6e-4413-a4d2-fa0a9f1ecf6d",
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
            $datasReturn['result'] = 'E';
            $datasReturn['message'] = $err;
        } else {
            if($response == "{}"){
                $datasReturn['result'] = 'S';
                $datasReturn['message'] = 'Success';
            }else{
                $datasReturn['result'] = 'E';
                $datasReturn['message'] = $response;
            }
        }

        return $datasReturn;
	}

    
    


	

