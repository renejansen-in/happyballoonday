<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    # BEGIN Setting reCaptcha v3 validation data
    $url = "https://www.google.com/recaptcha/api/siteverify";
    $data = [
        'secret' => "6LdbB6wZAAAAAJ8h7c_vTZ356hkpm6XDgRjzdi3n",
        'response' => $_POST['token'],
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    $options = array(
        'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'POST',
          'content' => http_build_query($data)
        )
      );

    # Creates and returns stream context with options supplied in options preset 
    $context  = stream_context_create($options);
    # file_get_contents() is the preferred way to read the contents of a file into a string
    $response = file_get_contents($url, false, $context);
    # Takes a JSON encoded string and converts it into a PHP variable
    $res = json_decode($response, true);
    # END setting reCaptcha v3 validation data

    // print_r($response); 

    # Post form OR output alert and bypass post if false. NOTE: score conditional is optional, since the successful score default is set at >= 0.5 by Google. Some developers want to be able to control score result conditions, so I included that in this example.
    if ($res['success'] == true && $res['score'] >= 0.5) {

        # Recipient email, split for smart bots and crawlers
        $toname = "info";
        $domain = "happyballoonday";
        $ccode = "nl";
        $mail_to = $toname."@".$domain.".".$ccode;
        
        # Sender form data
        $name = str_replace(array("\r","\n"),array(" "," ") , strip_tags(trim($_POST["name"])));
        $company = trim($_POST["company"]);
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $phone = trim($_POST["phone"]);
        $fsubject = trim($_POST["subject"]);
        $message = trim($_POST["message"]);

        # Generate standard email subject
        $subject = "Je hebt een nieuw bericht van www.".$domain.".".$ccode.".";

        if (empty($name) OR !filter_var($email, FILTER_VALIDATE_EMAIL) OR empty($email) OR empty($subject) OR empty($message)) {
            # Set a 400 (bad request) response code and exit
            http_response_code(400);
            echo '<p class="alert-warning">Vul het formulier volledig in en probeer nog een keer</p>';
            exit;
        }
       
        # Mail content
        $body = "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Express Mail</title></head><body>";
        $body .= "<table style='width: 100%;'>";
        $body .= "<thead style='text-align: center;'><tr><td style='border:none;' colspan='2'>";
        $body .= "</td></tr></thead><tbody><tr>";
        $body .= "<td style='border:none;'><strong>Naam:</strong> {$name}</td>";
        $body .= "<td style='border:none;'><strong>Bedrijf:</strong> {$company}</td>";
        $body .= "</tr>";
        $body .= "<tr><td style='border:none;'><strong>Subject:</strong> {$fsubject}</td>";
        $body .= "<td style='border:none;'><strong>Email:</strong> {$email}</td>";
        $body .= "</tr>";
        $body .= "<tr><td style='border:none;'><strong>&nbsp;</strong></td>";
        $body .= "<td style='border:none;'><strong>Telefoon:</strong> {$phone}</td>";
        $body .= "</tr>";
        $body .= "<tr><td><strong>Inhoud van het bericht:</strong></td></tr>";
        $body .= "<tr><td colspan='2' style='border:none;'>{$message}</td></tr>";
        $body .= "</tbody></table>";
        $body .= "</body></html>";        
      
        # Email headers
        $headers = "From: $name <$email>";
        $headers = "From: " . $name  . "\r\n";
        $headers .= "Reply-To: ". $email . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        

        # Send the email
        $success = mail($mail_to, $subject, $body, $headers);

        if ($success) {
            # Set a 200 (okay) response code
            http_response_code(200);
            echo '<script type="text/javascript">alert("Bedankt voor het bericht. We nemen spoedig contact met je op!");window.location.href = "../../index.html";</script>';
        } else {
            # Set a 500 (internal server error) response code
            http_response_code(500);
            echo '<script type="text/javascript">alert("Er is iets fout gegaan. Je bericht kon niet worden verzonden!");window.location.href = "../../index.html";</script>';
        }   

    } else {
        echo '<script type="text/javascript">alert("Fout! De reCAPTCHA sleutel is verlopen of of je bent een BOT!");window.location.href = "../../index.html";</script>';
    }  

} else {
    # Not a POST request, set a 403 (forbidden) response code
    http_response_code(403);
    echo '<script type="text/javascript">alert("Er ging iets mis met je inzending. Probeer het nog een keer!");window.location.href = "../../index.html";</script>';
} ?>
} ?>