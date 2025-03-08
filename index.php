<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['fb_url'])) {
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
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facebook Info Lookup</title>
    <link href="https://fonts.googleapis.com/css?family=Iceberg|Wallpoet|Nunito|Poiret+One&display=swap" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(to right, #0f0c29, #302b63, #24243e);
            color: #fff;
            text-align: center;
            overflow: auto;
        }

        h1 {
            font-size: 3em;
            font-family: 'Wallpoet', cursive;
            color: #18FFFF;
            text-shadow: 0 0 30px red;
            margin-top: 50px;
        }

        .container {
            margin-top: 30px;
            position: relative;
            z-index: 2;
        }

        input {
            margin: 10px;
            padding: 10px;
            width: 80%;
            border: none;
            border-radius: 25px;
            font-size: 1.2em;
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        button {
            padding: 10px 15px;
            background-color: #ff004c;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 25px;
            font-size: 1.2em;
            width: 50%;
            box-shadow: 0 0 15px #ff004c;
            transition: all 0.3s ease-in-out;
        }

        button:hover {
            background-color: #ff79a8;
            box-shadow: 0 0 25px #ff79a8;
        }

        #result {
            margin-top: 20px;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: none;
            text-align: center;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }

        .profile-photo, .cover-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
            border: 2px solid white;
            display: block;
            margin: 10px auto;
        }

        .cover-photo {
            width: 100%;
            height: 200px;
        }

        .data-box {
            background: rgba(0, 0, 0, 0.6);
            padding: 15px;
            margin: 10px;
            border-radius: 5px;
            display: inline-block;
            width: 45%;
            min-width: 200px;
            text-align: center;
            font-size: 14px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: cyan;
            font-weight: bold;
            box-shadow: 0 0 10px cyan;
        }

        h3 {
            color: #ff4c4c;
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
            top: 0;
            left: 0;
        }
    </style>
</head>
<body>

    <div id="particles-js"></div>

    <h1>Facebook Info Lookup</h1>

    <div class="container">
        <input type="text" id="fb_url" placeholder="Enter Facebook Profile URL">
        <br>
        <button onclick="fetchData()">Get Info</button>

        <div id="result"></div>
    </div>

    <script>
        function fetchData() {
            let fb_url = document.getElementById("fb_url").value.trim();
            if (fb_url === "") {
                alert("Please enter a valid Facebook profile URL.");
                return;
            }

            let formData = new FormData();
            formData.append("fb_url", fb_url);

            fetch("<?php echo $_SERVER['PHP_SELF']; ?>", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                let resultDiv = document.getElementById("result");
                resultDiv.style.display = "block";

                if (data.error) {
                    resultDiv.innerHTML = `<p style="color: red;">${data.error}</p>`;
                    return;
                }

                let profilePhoto = data.profile_photo || "";
                let coverPhoto = data.cover_photo || "";

                let images = "";
                if (profilePhoto) {
                    images += `<h3>Profile Photo</h3><img class="profile-photo" src="${profilePhoto}" alt="Profile Photo">`;
                }
                if (coverPhoto) {
                    images += `<h3>Cover Photo</h3><img class="cover-photo" src="${coverPhoto}" alt="Cover Photo">`;
                }

                let apiData = "";
                for (let key in data.full_response.message) {
                    if (typeof data.full_response.message[key] === "string" && key !== "PHOTO") {
                        apiData += `<div class="data-box"><strong>${key.replace(/_/g, " ")}:</strong> ${data.full_response.message[key]}</div>`;
                    }
                }

                resultDiv.innerHTML = images + `<h3>Profile Info</h3>` + apiData;
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Failed to fetch data.");
            });
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS('particles-js', {
            "particles": {
                "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": "#ffffff" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.5 },
                "size": { "value": 5, "random": true },
                "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.4, "width": 1 },
                "move": { "enable": true, "speed": 6 }
            }
        });
    </script>

</body>
</html>
