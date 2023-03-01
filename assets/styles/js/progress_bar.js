$(document).ready(function(){
    progress_bar();
})

function progress_bar(){
    let progress_bar = document.querySelector(".js-progress-bar");
    let click = document.querySelector(".js-btn");
    let id = document.getElementById("js-id").value;

    click.addEventListener('click',()=>{
        let a = 0;
        let run = setInterval(frames, 15);
        click.style.visibility = "hidden";
        function frames(){
            a = a+1;
            if(a === 101) {
                clearInterval(run);
                location.replace('/restaurant/hasard/'+id);
            }
            else{
                progress_bar.textContent = a + "%";
                progress_bar.style.width = a + "%";
            }
        }
    })
}