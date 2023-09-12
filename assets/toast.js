let toastLiveExample = document.getElementById('liveToast')
let flashMessage = document.getElementById('flash-messages')

if (toastLiveExample) {
    toastLiveExample.classList.add('show');

    //TODO suppression mesage flash
    setTimeout(function() {
        console.log('start')
        toastLiveExample.hide();
    }, 300);
}