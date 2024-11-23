const liveToasts = document.getElementsByClassName("liveToast");

Array.from(liveToasts).forEach((element) => 
    setTimeout(() => {
        element.classList.add('swing-out-top-bck');
    }, 2000)
)