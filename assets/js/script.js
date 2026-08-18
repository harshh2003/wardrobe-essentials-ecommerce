document.addEventListener("DOMContentLoaded", function () {

    const sizeButtons = document.querySelectorAll(".size-btn");

    console.log("Buttons found:", sizeButtons.length);

    sizeButtons.forEach(function(button){

        button.onclick = function(){

            sizeButtons.forEach(function(btn){
                btn.classList.remove("active");
            });

            this.classList.add("active");

        };

    });

});