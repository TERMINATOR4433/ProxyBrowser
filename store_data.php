<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the form
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $friendsWithAaron = htmlspecialchars($_POST['friendsWithAaron']);
    $dixonsSuck = htmlspecialchars($_POST['dixonsSuck']);

    // Email settings
    $to = "hib29713@gmail.com";  // Replace with your email address
    $subject = "New Form Submission(dixons)";

    // Email body
    $message = "
    New form submission received:

    Name: $name
    Email: $email
    Friends with Aaron: $friendsWithAaron
    Does Dixons suck?: $dixonsSuck

    Submitted at: " . date('Y-m-d H:i:s') . "
    ";

    // Headers for the email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@gcseisclose.free.nf" . "\r\n";  // Use a no-reply email address (or your own)

    // Send email
    if (mail($to, $subject, $message, $headers)) {
        echo "Form submitted successfully! You'll receive the submission in your inbox.";
    } else {
        echo "Error sending the email.";
    }
} else {
    echo "Invalid request.";
}
?>

