(function($){
   $(function(){
    $("#segundopasso").hide();  
    $("#div_contador").hide();  
    buscaEmails();
    enviaEmails();
   });

   let buscaEmails = () =>
   {
    $("#buscarEmails").on('click', function()
    {
        let callAjax = $.ajax({
            url : wems_ex.ajax_url,
            type: 'post',
            data: {'action' : 'buscaClientes'}

        });

        callAjax.done(function(resp){
           let result = JSON.parse(resp);
            $("#quantidade_emails").text(result.count + ' emails encontrados');
            $("#segundopasso").slideDown();
            $("#buscarEmails").attr('disabled', true);
            $("#buscarEmails").css('cursor', 'not-allowed');
        });
     });
    }

     let enviaEmails = () =>
     {
            $("#EnviarEmails").on('click', function()
            {
                $("#quantidade_enviada").text('Enviando Aguarde...');
                $("#EnviarEmails").attr('disabled', true);
                $("#EnviarEmails").css('cursor', 'not-allowed');
                enviaEmail();
                
            });
        }
        let enviaEmail = () =>
        {
            let callAjax = $.ajax({
                url : wems_ex.ajax_url,
                type: 'post',
                data: {'action' : 'enviaEmails'},
                
            });
            callAjax.done(function(backemail)
            {
                let response = JSON.parse(backemail);
                $('#div_contador').show();
                $('#contador').text(`enviado para: ${response.email_sentto}`);

                let callEmail = $.ajax({
                    url : wems_ex.ajax_url,
                    type: 'post',
                    data: {'action' : 'getEmailData'}

                }) ;  
                
                callEmail.done(function(resp)
                {
                    let result = JSON.parse(resp);
                
                if(result.content.length > 0) {
                        enviaEmail();
                    
                }else { 
                    $('#div_contador').removeClass('update-nag notice');
                    $('#div_contador').addClass('updated notice');
                
                    $('#contador').text('Finalizado');
                    $("#quantidade_enviada").hide();
                }
                });  
                callEmail.fail(function(erros)
                {
                    $('#div_contador').addClass('error notice');
                    $('#contador').text(erros);
                }) ;        
        });
        callAjax.fail(function(error) {
               console.log(error); 
        });
     } 
   
})(jQuery);
