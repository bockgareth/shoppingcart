<?php
    session_start();
    include 'online-store.php';
    $storeID = 'COFFEE';
    $storeInfo = [];
    if (class_exists('OnlineStore')) {
        if (isset($_SESSION['currentStore']))
            $store = unserialize($_SESSION['currentStore']);
        else {
            $store = new OnlineStore();
        } 
        $store->setStoreID($storeID);
        $storeInfo = $store->getStoreInformation();
        $store->processUserInput();
    } else {
        $ErrorMsgs[] = 'The OnlineStore class is not available!';
        $Store = NULL;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css"> -->
    <link rel="stylesheet" type="text/css" href="./css/<?php echo $storeInfo['css_file']; ?>" />
    <title><?php echo $storeInfo['name']; ?></title>
</head>
<body>
<h1><?php echo htmlentities($storeInfo['name']); ?></h1>
<h2><?php echo htmlentities($storeInfo['description']); ?></h2>
<p><?php echo htmlentities($storeInfo['welcome']); ?></p>
<p><a href="gosselin-gourmet-goods.php">Home Page</a><br /></p>
<?php
    echo "<p> Calling get Product List </p>";
    $store->getProductList();
    $_SESSION['currentStore'] = serialize($store);
?>
</body>
</html>