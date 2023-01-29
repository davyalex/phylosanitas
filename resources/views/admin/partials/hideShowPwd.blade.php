<div class="form-check form-switch">
    <input onclick="hideShowPwd()" class="form-check-input" type="checkbox" id="flexSwitchCheckChecked">
    <label class="form-check-label" for="flexSwitchCheckChecked">Afficher le mot de passe</label>
  </div>




  <script>
    function hideShowPwd () { 
        var x =

        document.getElementById("password");

        if (x.type==="password") {
            x.type = "text"
        }else{
            x.type = "password"
        }
     }


     

     
  </script>