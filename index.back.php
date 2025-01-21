<?php 
// $response = file_get_contents("https://randomuser.me/api");
// $data = json_decode($response, true);
// echo ( $data["results"][0]["name"]["first"] );

if ( !empty($_GET['name']) ) {
    $response = file_get_contents("https://api.agify.io?name={$_GET['name']}");
    $data = json_decode($response, true);       
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Example</title>
</head>
<body>
    <?php 
        if ( !empty($data['age']) ) {
            echo $data['age'] , " Years";
        }
    ?>
    <form action="" method="get">
    
    <label for="name">Name</label>
    <input type="text" name="name" id="">
    
    <input type="submit" value="Guess Age">
    </form>
</body>
</html>