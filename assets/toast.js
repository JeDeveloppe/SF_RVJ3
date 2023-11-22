const liveToasts = document.getElementsByClassName("liveToast");

console.log(liveToasts);

Array.from(liveToasts).forEach((element) => 
    setTimeout(() => {
        element.classList.add('swing-out-top-bck');
    }, 2000)
)