<script type="text/javascript">
    // Client ID and API key from the Developer Console
    var CLIENT_ID = '{{config('google_sheet.client_id')}}';
    var API_KEY = '{{config('google_sheet.api_key')}}';

    // Array of API discovery doc URLs for APIs used by the quickstart
    var DISCOVERY_DOCS = ["https://sheets.googleapis.com/$discovery/rest?version=v4"];

    // Authorization scopes required by the API; multiple scopes can be
    // included, separated by spaces.
    var SCOPES = "https://www.googleapis.com/auth/spreadsheets.readonly";

    /**
     *  On load, called to load the auth2 library and API client library.
     */
    function handleClientLoad() {
        gapi.load('client:auth2', initClient);
    }

    /**
     *  Initializes the API client library and sets up sign-in state
     *  listeners.
     */
    function initClient() {
        gapi.client.init({
            apiKey: API_KEY,
            clientId: CLIENT_ID,
            discoveryDocs: DISCOVERY_DOCS,
            scope: SCOPES
        }).then(function (result) {
            // Listen for sign-in state changes.
            gapi.auth2.getAuthInstance().isSignedIn.listen(updateSigninStatus);
            updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
        });
    }

    /**
     *  Called when the signed in status changes, to update the UI
     *  appropriately. After a sign-in, the API is called.
     */
    function updateSigninStatus(isSignedIn) {
        if (isSignedIn) {
            gapi.client.sheets.spreadsheets.values.get({
                spreadsheetId: '1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms',
                range: 'Class Data!A2:E',
            }).then(function(response) {
                var range = response.result;
                if (range.values.length > 0) {
                    console.log(gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().access_token);
                }
                console.log('logout');
                handleSignoutClick();
            }, function(response) {
                console.log('logout error response', response);
                handleSignoutClick();
            });
        }
    }

    /**
     *  Sign in the user upon button click.
     */
    $(document).on( 'click','.btn-oauth', function () {
        var sheet_id = $(this).attr('data-sheet-id');
        var column_bot = $(this).attr('data-column-bot');
        var column_user = $(this).attr('data-column-user');
        gapi.auth2.getAuthInstance().signIn();
        updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
    });

    /**
     *  Sign out the user upon button click.
     */
    function handleSignoutClick() {
        gapi.auth2.getAuthInstance().signOut();
    }

</script>

<script async defer src="https://apis.google.com/js/api.js" onload="this.onload=function(){};handleClientLoad()" onreadystatechange="if (this.readyState === 'complete') this.onload()"></script>
