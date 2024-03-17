<?php

require("../partials/header.php");

$errors=[];

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    renderForm();
    exit;
}


include("../db/conn.php");

if ( !isset($_POST["username"]) || empty($_POST["username"]) ) {
            $errors[]="Username missing";    
}

    if ( !isset($_POST["email"]) || empty($_POST["email"]) ) {
        $errors[]="Email missing";    
}

    if ( !isset($_POST["password"]) || empty($_POST["password"]) ) {
        $errors[]="Password missing";
    
    }
    
    if ( !isset($_POST["confirm_password"]) || empty($_POST["confirm_password"]) ) {
        $errors[]="Password confirmation missing";
    
    }

    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm=$_POST["confirm_password"];
    $email = $_POST["email"];
    

    if (mb_strlen($username)< 3) { 
        $errors[]="Username is too short";
    }

    if (mb_strlen($password)< 8) { 
        $errors[]="Password is too short";
    }

    if ( $password!==$confirm) {
        $errors[]="Passwords don't match";
    }

    if ( !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[]="Invalid email";
    }

    if (count( $errors)> 0) {
        renderForm();
        die();
    }

    registerUser( $username, $password ,$email);



function registerUser($username, $password,$email) {

    global $db;
    global $errors;
    $sql="SELECT 1 FROM users WHERE username=? OR email=?";
	
    $stmt = $db->prepare($sql);

	$stmt->execute([$username,$email]);
    
    if ($stmt->get_result()->num_rows!==0) {
        $errors[]="Käyttäjätunnus on jo käytössä";
        renderForm();
	    die();
	}

    $hash=password_hash($password, PASSWORD_BCRYPT);

    addUser($username, $hash,$email);
}


function addUser($username,$hash,$email) {
    global $db;
    global $errors;
    $sql= "INSERT INTO users (username,password,email) VALUES (?,?,?)";

    $stmt = $db->prepare($sql);

    if (!$stmt->execute([$username,$hash,$email])) {
		$errors[]="Virhe käyttätilin luonnissa";
        renderForm();
	    die();
	}

    header("Location:/user/login.php");
    exit;
}


function renderForm() {
    global $errors;
?>

    <form method="POST" class="user-form">
        <h3></h3>
        <fieldset>
        <legend>Rekisteröityminen</legend>

            <label for="username">Käyttäjätunnus</label>
            <input id="username" name="username" minlength="6" required>

            <label for="email">Sähköposti</label>
            <input id="email" name="email" type="email" required>

            <label for="password">Salasana</label>
            <input id="password" type="password" name="password" minlength="8" required>

            <label for="confirm_password">Salasana uudelleen</label>
            <input id="confirm_password" type="password" name="confirm_password" minlength="8" required>
            

            <?php foreach ($errors as $error): ?>
                    <p class="error">
                        <?= $error ?>
                    </p>

                <?php endforeach ?>

            <button type="submit">Rekisteröidy</button>
        </fieldset>

        <div class="form-message">
        <p class="error" id="error_msg"></p>
        <p>Onko sinulla jo tili?<a href='login.php'>Kirjaudu</a></p>
        </div>
    </form>



    <script>

    function checkPasswordsMatch(event){
        let p1=document.getElementById("password").value;
        let p2=document.getElementById("confirm_password").value;

        console.log(p1,p2);
        if (p1===p2) {
            return true
        }

        event.preventDefault();
        
        document.getElementById("error_msg").innerHTML="Salasanan varmistus ei täsmää";
        setTimeout(()=>document.getElementById("error_msg").innerHTML="",2500);
    }

    let p1=document.querySelector("form button").addEventListener("click",checkPasswordsMatch);


</script>

<?php 
}