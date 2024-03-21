<?php
namespace App\Libs;

class MyUtil {
  public static function text_to_speech($text, $voice_name) {
    $google_tts_api_url = "https://texttospeech.googleapis.com/v1/text:synthesize?key=".$_ENV["GOOGLE_API_KEY"];
    
    $ch = curl_init();
    curl_setopt_array($ch, [
      CURLOPT_URL => $google_tts_api_url,
      CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POSTFIELDS => json_encode([
        "audioConfig" => [
          "audioEncoding" => "LINEAR16",
          "pitch" => 0,
          "speakingRate" => 1,
          "sampleRateHertz" => 8000
        ],
        "input" => [
          "text" => $text,
        ],
        "voice" => [
          "languageCode" => "ja-JP",
          "name" => $voice_name
        ]
      ])
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $array = json_decode($response, true);
  
    return base64_decode($array["audioContent"]);
  }
}