$(document).ready(function(){
    roundProgressBar();
})

function roundProgressBar() {
    const numb = document.querySelector(".numb");
    let points = document.getElementById("js-points").value;
    let counter = 0;
    setInterval(() => {
        if (counter == points) {
            clearInterval();
        }
        else{
            counter += 1;
            numb.textContent = counter;
        }
    }, 1);
}