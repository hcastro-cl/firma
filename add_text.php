<?php
if(isset($_POST['submit'])) {
	    // Set the font file path
    $fontFile1 = __DIR__ . '/files/fonts/WorkSans-Regular.ttf';
    $fontFile2 = __DIR__ . '/files/fonts/WorkSans-SemiBold.ttf';
	$fontSize1 = 90;
	$fontSize2 = 80;
    
    // Text lines to be added
    $text1 = !empty($_POST['text1']) ? $_POST['text1']  : "Nombres Apellido1 Apellido2";
    $text2 = !empty($_POST['text2']) ? $_POST['text2'] : "correo@utalca.cl";
    $text3 = !empty($_POST['text3']) ? $_POST['text3'] : "Instituto de Matemáticas";
    $text4 = !empty($_POST['text4']) ? "Anexo: ".$_POST['text4'] : "Universidad de Talca, Chile";
	
	//width of text
	$bbox1 = imagettfbbox($fontSize1, 0, $fontFile1, $text1);
	$bbox2 = imagettfbbox($fontSize1, 0, $fontFile1, $text2);
	$bbox3 = imagettfbbox($fontSize2, 0, $fontFile2, $text3);
	$bbox4 = imagettfbbox($fontSize2, 0, $fontFile2, $text4);
	
	// Calculate the width of the text
	$textWidth1 = $bbox1[4] - $bbox1[0];
	$textWidth2 = $bbox2[4] - $bbox2[0];
	$textWidth3 = $bbox3[4] - $bbox3[0];
	$textWidth4 = $bbox4[4] - $bbox4[0];
	$textWidth=max($textWidth1,$textWidth2,$textWidth3,$textWidth4);
	
    // Set the position where text will be added (X, Y)
    $x = 1960;
    $y1 = 320; // Y position for the first line of text
    $y2 = 440; // Y position for the second line of text
    $y3 = 700; // Y position for the third line of text
    $y4 = 850; // Y position for the fourth line of text
	
	
    // Load the image
    $varimage = '';
    $varbackcolor = '';


    if(isset($_POST['image']) && !empty($_POST['image'])){
        $imagePath = __DIR__ .'/files/images/logos/' . $_POST['image'];
        $varimage = $_POST['image'];
    }
    else {
        $imagePath = __DIR__ .'/files/images/logos/logo-negro.png'; // Default logo in case no selection is made
        $varimage = "logo-negro.png";
    }
	
	// Load the image
    $image = imagecreatefrompng($imagePath);
	
	// Set the font color (RGB)
    if($varimage == "logo-blanco.png" || $varimage == "logo-blanco2.png") {
        $textColor = imagecolorallocate($image, 255, 255, 255); // White
		$textColor2 = imagecolorallocate($image, 255, 255, 255); // White
    } elseif($varimage == "logo-negro.png" || $varimage == "logo-negro2.png") {
        $textColor = imagecolorallocate($image, 0, 0, 0); // Black
		$textColor2 = imagecolorallocate($image, 0, 0, 0); // Black
    } else {
        $textColor = imagecolorallocate($image, 87, 87, 86); // Color 1
		$textColor2 = imagecolorallocate($image, 29, 113, 184); // Color 2
    }

    // Retrieve the value of the back-color variable
    if(isset($_POST['b-color']) && !empty($_POST['b-color'])) {
        $varbackcolor = $_POST['b-color'];
    } else {
        $varbackcolor = 'trans'; // Default value if back-color is not set
    }
    
    // Set the dimensions of the enlarged canvas
    $original_width = imagesx($image);
    $original_height = imagesy($image);
    $new_width = $original_width + $textWidth + 120; // Increase canvas width
    $new_height = $original_height; // Increase canvas height

    // Create a new image with the enlarged canvas dimensions
    $enlarged_image = imagecreatetruecolor($new_width, $new_height);

    // Fill the enlarged canvas with the specified background color or transparency
if($varbackcolor == "blanco") {
    $bg_color = imagecolorallocate($enlarged_image, 255, 255, 255); // White
    imagefilledrectangle($enlarged_image, 0, 0, $new_width, $new_height, $bg_color); // Fill with white
} elseif($varbackcolor == "negro") {
    $bg_color = imagecolorallocate($enlarged_image, 0, 0, 0); // Black
    imagefilledrectangle($enlarged_image, 0, 0, $new_width, $new_height, $bg_color); // Fill with black
} else {
    // Default to transparent if color is not specified
    imagesavealpha($enlarged_image, true);
    $bg_color = imagecolorallocatealpha($enlarged_image, 0, 0, 0, 127);
    imagefill($enlarged_image, 0, 0, $bg_color);
}

    // Copy the original image onto the enlarged canvas without scaling
    imagecopy($enlarged_image, $image, 0, 0, 0, 0, $original_width, $original_height);
	

	//Draw the vertical line 
	imagesetthickness($enlarged_image, 6);
	imageline($enlarged_image, 1900, 212, 1900, 868, $textColor);
	


    // Add the first line of text to the image
    imagettftext($enlarged_image, $fontSize1, 0, $x, $y1, $textColor, $fontFile2, $text1);

    // Add the second line of text to the image
    imagettftext($enlarged_image, $fontSize1, 0, $x, $y2, $textColor2, $fontFile2, $text2);
    
    // Add the third line of text to the image
    imagettftext($enlarged_image, $fontSize2, 0, $x, $y3, $textColor, $fontFile1, $text3);

    // Add the fourth line of text to the image if it exists
    if (!empty($text4)) {
        imagettftext($enlarged_image, $fontSize2, 0, $x, $y4, $textColor, $fontFile1, $text4);
    }
// Apply the snippet to resize the enlarged image to half its size
$new_width_half = $new_width / 4;
$new_height_half = $new_height / 4;

// Create a new transparent image with the new dimensions
$half_size_image = imagecreatetruecolor($new_width_half, $new_height_half);
imagealphablending($half_size_image, false);
imagesavealpha($half_size_image, true);
$transparent = imagecolorallocatealpha($half_size_image, 0, 0, 0, 127);
imagefill($half_size_image, 0, 0, $transparent);

// Copy and resize the original image onto the new transparent image
imagecopyresampled($half_size_image, $enlarged_image, 0, 0, 0, 0, $new_width_half, $new_height_half, $new_width, $new_height);


// Save the resized image to a variable
ob_start();
imagepng($half_size_image);
$image_data = ob_get_clean();

// Output the image data as a base64-encoded string
$image_base64 = base64_encode($image_data);

// Free up memory
imagedestroy($half_size_image);

    // Free up memory
    imagedestroy($image);
    imagedestroy($enlarged_image);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Firma Generada</title>
    <style>
        body {
            background-color: #ffffff; /* Set the body background color to white */
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
            display: flex; /* Use flexbox */
            justify-content: center; /* Center content horizontally */
            align-items: center; /* Center content vertically */
            height: 100vh; /* Set height to full viewport height */
        }

        #container {
            background-color: #cccccc; /* Set the container background color to grey */
            padding: 20px; /* Add some padding to the container */
        }

        #image-container {
            display: flex; /* Use flexbox */
            justify-content: center; /* Center content horizontally */
        }

        #download-btn {
            display: block; /* Display the button as block to take full width */
            margin-top: 10px; /* Add some space between the image and button */
        }
    </style>
</head>
<body>
    <div id="container">
        <div id="image-container">
            <img src="data:image/png;base64,<?php echo $image_base64; ?>" alt="Firma generada">
        </div>
    </div>
</body>
</html>
