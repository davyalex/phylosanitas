 <div class="form-check form-switch">
    <input onclick="hideShowPwd2()" class="form-check-input" type="checkbox" id="flexSwitchCheckChecked">
    <label class="form-check-label" for="flexSwitchCheckChecked">Afficher le mot de passe</label>
  </div>


  <script>

     function hideShowPwd2 () { 
        var x =

        document.getElementById("password2");
        document.getElementById("password");

        if (x.type==="password") {
            x.type = "text"
        }else{
            x.type = "password"
        }
     }


     

     
  </script>