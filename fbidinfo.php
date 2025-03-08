<?php
if (isset($_POST['fb_url'])) {
    $fb_url = trim($_POST['fb_url']);
    $api_url = "https://tcsdemonic.vercel.app/api/fbinfo?url=" . urlencode($fb_url);

    $response = file_get_contents($api_url);
    $decodedResponse = json_decode($response, true);

    file_put_contents("response.txt", json_encode($decodedResponse, JSON_PRETTY_PRINT));

    $profilePhoto = isset($decodedResponse['message']['PHOTO']['profile_photo']['url']) ? $decodedResponse['message']['PHOTO']['profile_photo']['url'] : "";
    $coverPhoto = isset($decodedResponse['message']['PHOTO']['cover_photo']['url']) ? $decodedResponse['message']['PHOTO']['cover_photo']['url'] : "";

    echo json_encode([
        "profile_photo" => $profilePhoto,
        "cover_photo" => $coverPhoto,
        "full_response" => $decodedResponse
    ]);
} else {
    echo json_encode(["error" => "No URL provided."]);
}
?>
