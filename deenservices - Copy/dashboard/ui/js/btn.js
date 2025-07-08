var btnEventHandler={
    
    handleNavigation: function(){
        var btnClicked = $(".btn-click");
        btnClicked.on("click", function(){
            var destination = $(this).attr("data-href");
            var target = $(this).attr("data-target");
            
            if(target){
                
                window.open(destination, target).focus()
            }else{
                window.location.href=destination;
            }
        })
    },

    handleClickProcess: function(){
        
    },
    handleExportFile: function(){
        var btnClicked = $(".btn-export");
        btnClicked.on("click", function(){
            var extention=$(this).attr("data-ext");
            $.get(extention, function(data, error){
                console.log(data);
            })
        })
    },
    run: function(){
        btnEventHandler.handleNavigation();
        btnEventHandler.handleClickProcess();  
        btnEventHandler.handleExportFile();
    }
}

document.addEventListener("DOMContentLoaded", function() {    
    btnEventHandler.run();
});
