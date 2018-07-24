<?php
    class OnlineStore {
        private $DBConnect = NULL;
        private $storeID = '';
        private $inventory = [];
        private $shoppingCart = [];

        function __construct() {
            include 'online-store-db.php';
            $this->DBConnect = $DBConnect;
        }

        function __destruct() {
            if (!$this->DBConnect->connect_error) {
                $this->DBConnect->close();
            }
        }

        public function setStoreID($storeID) {
            if ($this->storeID != $storeID) {
                $this->storeID = $storeID;
                $sql = "SELECT * FROM inventory WHERE storeID = '".$this->storeID."'";
                $result = @$this->DBConnect->query($sql);
                if (!$result) {
                    $this->storeID = "";    
                } else {
                    $this->inventory = [];
                    $this->shoppingCart = [];
                    while (($row = $result->fetch_assoc()) !== NULL) {
                        $this->inventory[$row['productID']] = [];
                        $this->inventory[$row['productID']]['name'] = $row['name'];
                        $this->inventory[$row['productID']]['description'] = $row['description'];
                        $this->inventory[$row['productID']]['price'] = $row['price'];
                        $this->shoppingCart[$row['productID']] = 0;    
                    }
                }
            }
        }

        public function getStoreInformation() {
            $retval = false;
            if ($this->storeID != "") {
                $sql = "SELECT * FROM store_info WHERE storeID = '".$this->storeID."'";
                $result = @$this->DBConnect->query($sql);
                if ($result) {
                    $retval = $result->fetch_assoc();    
                }
            }
            return $retval;
        }

        public function getProductList() {
            $retval = false;
            $subtotal = 0;
            if (count($this->inventory) > 0) {
                ?>
                <table border="1">
                    <tr>
                        <th>Product</th>
                        <th>Description</th>
                        <th>Price each</th>
                        <th># in Cart</th>
                        <th>Total Price</th>
                        <th>&nbsp;</th>
                    </tr>
                    
                    <?php foreach ($this->inventory as $ID => $Info) { ?>
                        <tr>
                            <td><?php echo htmlentities($Info['name']); ?></td>
                            <td><?php echo htmlentities($Info['description']); ?></td>
                            <td><?php printf("$%.2f", htmlentities($Info['price'])); ?></td>
                            <td><?php echo $this->shoppingCart[$ID]; ?></td>
                            <td><?php printf("$%.2f", $Info['price'] * $this->shoppingCart[$ID]); ?></td>
                            <td>
                                <a href='<?php echo $_SERVER['SCRIPT_NAME']; ?>?PHPSESSID=<?php echo session_id(); ?>&ItemToAdd=<?php echo $ID; ?>'>Add Item</a>
                                <br>
                                <a href='<?php echo $_SERVER['SCRIPT_NAME']; ?>?PHPSESSID=<?php echo session_id(); ?>&ItemToRemove=<?php echo $ID; ?>'>Remove Item</a>
                            </td>
                        </tr>
                        <?php $subtotal += ($Info['price'] * $this->shoppingCart[$ID]); 
                    } ?>
                    <tr>
                        <td colspan='4'>Subtotal</td>
                        <td><?php printf("$%.2f", $subtotal) ?></td>
                        <td><a href='<?php echo $_SERVER['SCRIPT_NAME']; ?>?PHPSESSID=<?php echo session_id(); ?>&EmptyCart=true'>Empty Cart</a></td>
                    </tr>
                </table>
                <p align='center'><a href='Checkout.php?PHPSESSID=<?php echo session_id(); ?>&CheckOut="<?php echo $storeID; ?>'>Checkout</a></p>
                <?php
            }   
            return $retval;
        }
        public $count = 0;

        private function addItem() {
            
            $ProdID = $_GET['ItemToAdd'];
            if (array_key_exists($ProdID, $this->shoppingCart)) {
                $this->shoppingCart[$ProdID] += 1;    
            }
        }

        function __wakeup() {
            include('online-store-db.php');
            $this->DBConnect = $DBConnect;    
        }

        private function removeItem() {
            $ProdID = $_GET['ItemToRemove'];
            if (array_key_exists($ProdID,$this->shoppingCart)) {
                if ($this->shoppingCart[$ProdID] > 0) {
                    $this->shoppingCart[$ProdID] -= 1;                
                }
            }
        }

        private function emptyCart() {
            foreach($this->shoppingCart as $key => $value) {
                $this->shoppingCart[$key] = 0;    
            }  
        }

        public function processUserInput() {
            if(!empty($_GET['ItemToAdd'])) {
                $this->addItem();    
            }
            if(!empty($_GET['ItemToRemove'])) {
                $this->removeItem();    
            }
            if(!empty($_GET['EmptyCart'])) {
                $this->emptyCart();    
            } 
        }

        public function checkout() {
            $ProductsOrdered = 0;
            foreach ($this->shoppingCart as $productID => $quantity) {
                if ($quantity > 0) {
                    ++$ProductsOrdered;
                    $sql = "INSERT INTO orders (orderID, productID, quantity) VALUES ('".session_id()."','".$productID."','".$quantity."')";
                    $QueryResult = $this->DBConnect->query($sql);
                }
            }
            
            echo "<p><strong>Your order has been recorded.</strong></p>";
        }
    }