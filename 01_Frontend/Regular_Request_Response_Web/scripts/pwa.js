var enableNotificationsButtons = document.querySelectorAll('.enable-notifications');


if ('serviceWorker' in navigator) {
    navigator.serviceWorker
        .register('/sw.js')
        .then(function () {
            console.log('Service worker registered!');
        })
        .catch(function(err) {
            console.log(err);
        });
}



if ('Notification' in window && 'serviceWorker' in navigator) {
    for (var i = 0; i < enableNotificationsButtons.length; i++) {
        enableNotificationsButtons[i].style.display = 'inline-block';
        enableNotificationsButtons[i].addEventListener('click', askForNotificationPermission);
    }
}



function askForNotificationPermission() {
    Notification.requestPermission(function(result) {
        console.log('User Choice', result);
        if (result !== 'granted') {
            console.log('No notification permission granted!');
        } else {
            //configurePushSub();
            //displayConfirmNotification();
        }
    });
}



function displayConfirmNotification() {
    if ('serviceWorker' in navigator) {
        var options = {
            body: 'This is description',
            icon: '/core/pwa/icons/app-icon-96x96.png',
            image: '/core/pwa/icons/sf-boat.jpg',
            dir: 'ltr',
            lang: 'en-US', // BCP 47,
            vibrate: [100, 50, 200],
            badge: '/core/pwa/icons/app-icon-96x96.png',
            tag: 'confirm-notification',
            renotify: true,
            actions: [
                { action: 'confirm', title: 'Okay', icon: '/core/pwa/icons/app-icon-96x96.png' },
                { action: 'cancel', title: 'Cancel', icon: '/core/pwa/icons/app-icon-96x96.png' },
            ]
        };
        navigator.serviceWorker.ready
        .then(function(swreg) {
            swreg.showNotification('New lead from andrew!', options);
        });
    }
}









