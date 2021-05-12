/*
Give the service worker access to Firebase Messaging.
Note that you can only use Firebase Messaging here, other Firebase libraries are not available in the service worker.
*/
importScripts('https://www.gstatic.com/firebasejs/7.13.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.13.1/firebase-messaging.js');

var firebaseConfig = {
    apiKey: "AIzaSyBy2p1aKUi-2TnVv30v6TBp6_cVvtGFhaU",
    authDomain: "shop-manager-38674.firebaseapp.com",
    databaseURL: "https://shop-manager-38674.firebaseio.com",
    projectId: "shop-manager-38674",
    storageBucket: "shop-manager-38674.appspot.com",
    messagingSenderId: "170948690489",
    appId: "1:170948690489:web:60ce4953fd142902a4c3e8",
    measurementId: "G-WDMQGXQWDC"
};

firebase.initializeApp(firebaseConfig);

/*
Retrieve an instance of Firebase Messaging so that it can handle background messages.
*/
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here
    const notificationTitle = 'Background Message Title';
    const notificationOptions = {
        body: 'Background Message body.',
        icon: '/firebase-logo.png'
    };

    return self.registration.showNotification(notificationTitle,
        notificationOptions);
});