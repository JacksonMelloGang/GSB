<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSB - Définir un mot de passe</title>
    <script src="/public/js/jquery-3.6.0.min.js"></script>
</head>
<body style="background-color: rgb(233, 222, 222)">
    <div>
        <div style='display:flex;justify-content:center;flex-direction:column;text-align:center;'>
            <form style="text-align:center" action="/controller/password_setup_controller.php" method="post" onsubmit="return verifpassword()">
                <h1>Définir un mot de passe</h1>

                <label>Votre Code:</label>
                <br>
                <input type="text" id="code" name="code">
                
                <br><br>

                <label>Mot de passe:</label>
                <br>
                <input type="password" id="password" name="password">

                <br><br>

                <label>Confirmer le mot de passe:</label>
                <br>
                <input type="password" name="cfmpassword">

                <br><br>
                <input type="submit" value="Envoyer">
            </form>

            <span id="info"style="black;"></span>

        </div>
    </div>
    <script>
        function verifpassword(){
            var password = document.getElementsByName("password")[0].value;
            var cfrmpassword = document.getElementsByName("cfmpassword")[0].value;
        
            if(password != cfrmpassword){
                document.getElementById("info").innerText = "Les mot de passe ne sont pas identique.";                
                return false;
            }

            return true;
            
        }

        $("form").submit((e) => {
            e.preventDefault();

            if(verifpassword() == false){
                return;
            }

            var code = $("#code").val();
            var mdp = $("#password").val();

            formdata = {
                code: code,
                password: mdp
            }

            $.ajax({
                type: "POST",
                data: formdata,
                url: "https://gsb-lycee.ga/controller/password_setup_controller.php"
            }).done((data) => {
                if(data == "Code Invalide."){
                    document.getElementById("info").innerText = "Code Invalide/Inexistant.";
                } else {
                    if(data == "Success !"){
                        document.getElementById("info").innerText = "Votre mot de passe a bien été défini.";
                    }
                }
            })

        })

    </script>
</body>

</html>