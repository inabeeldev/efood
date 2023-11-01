importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');
firebase.initializeApp({apiKey: "AIzaSyDSTOcrrC82867jIia7z0PEABeJimgOPjM",authDomain: "nature-checkout.firebaseapp.com",projectId: "nature-checkout",storageBucket: "nature-checkout.appspot.com", messagingSenderId: "941798270710", appId: "1:941798270710:web:467468676d027d15d8eed7"});
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (payload) { return self.registration.showNotification(payload.data.title, { body: payload.data.body ? payload.data.body : '', icon: payload.data.icon ? payload.data.icon : '' }); });
