<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script type="text/javascript" src="https://apis.google.com/js/client.js?onload=handleClientLoad"></script>
<script>
    var clientId = '822104352970-8n0jeuhtqb49bkr8jhi2p8fola6jcei4.apps.googleusercontent.com';
      var apiKey = 'AIzaSyCcDefXSt_7pZkwvfkA8veuwJbfT4Fvk4Q';

      var scopes ='https://www.googleapis.com/auth/drive&access_type=offline';
    
     function handleAuthResult(authResult) {
         if (authResult && !authResult.error) {
			accessToken=authResult.access_token;
			authType="gl";
                        $('div').html(accessToken);
			
        } 

     }
    
    function checkGoogleLoginState(){
	  	gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: false}, handleAuthResult);
	  }
        $(document).ready(function(){  
          //checkGoogleLoginState();
      });
      
      function handleClientLoad() {
        gapi.client.setApiKey(apiKey);
        //gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: false}, handleAuthResult);
      }
    </script>
    
    <button onclick="checkGoogleLoginState()">hit me</button>
    
    <div></div>


 