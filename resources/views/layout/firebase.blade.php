<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/7.13.1/firebase-app.js"></script>

<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->
<script src="https://www.gstatic.com/firebasejs/7.13.1/firebase-analytics.js"></script>
<script src='https://www.gstatic.com/firebasejs/7.13.1/firebase-messaging.js'></script>
<script>
  // Your web app's Firebase configuration
  const firebaseConfig = {
    apiKey: "AIzaSyBjz3-Gb0HF1ERJQYi4ccguJwrXijkZ00I",
    authDomain: "web-henry.firebaseapp.com",
    databaseURL: "https://web-henry.firebaseio.com",
    projectId: "web-henry",
    storageBucket: "web-henry.appspot.com",
    messagingSenderId: "973387546989",
    appId: "1:973387546989:web:08df40ec6f03ec97cd5a64",
    measurementId: "G-F24RC9BC89"
  };
  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
  firebase.analytics();

  const messaging = firebase.messaging();
  messaging.usePublicVapidKey("BPxyrCCYmYZOknRo5vzBhnnJtT_p1QTQ_GwRAuelpw7U6Mr3OwEADgaPvD3H8xSyL6VA7WpK7W1rYH71u1dgsc8");

  function requestPermission() {
    messaging.requestPermission().then(function() {
      console.log('Notification permission granted.');
    }).catch(function(err) {
      console.log('Unable to get permission to notify.', err);
    });
  }

  function sendTokenToServer(token) {
    // set-device-token
    $.ajax({
        type: 'POST',
        url: "{{ route('admin.set-device-token') }}",
        data: {
            token: token
        },
        success: function(response) {
        },
        error: function(e) {
        }
    });
  }

  requestPermission();

  messaging.getToken().then((currentToken) => {
    if (currentToken) {
      sendTokenToServer(currentToken);
    }
  }).catch((err) => {
    console.log(err);
  });

  messaging.onMessage((payload) => {
    const noteTitle = payload.notification.title;
    const noteOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon,
    };
    new Notification(noteTitle, noteOptions);
  });

</script>
